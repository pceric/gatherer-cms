<?php
/**
 * This controller manages the front page
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class IndexController extends Zend_Controller_Action
{
    private $_config;
    private $_db;

    /**
     * Cleans feeds by stripping most tags and running through a filter
     *
     * @param string $raw Raw HTML from feeds that needs to be cleaned.
     * @param string $host The feed's host name to apply correct filter.
     * @return string Cleaned HTML
     */
    private function cleanFeed($raw, $host) {
        $filterdirlist = scandir(APPLICATION_PATH . '/views/plugins');
        foreach ($filterdirlist as $v) {
            if (preg_match('/^feedfilter\.' . $host . '\.php$/iu',$v)) {
                include_once APPLICATION_PATH . "/views/plugins/$v";
                $orig = $raw;
                $raw = call_user_func('feedfilter_' . str_replace('.', '_', $host), $raw);
                if (empty($raw))
                    $raw = $orig;
            }
        }
        $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                       '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
        );
        $raw = preg_replace($search, '', $raw);
        $raw = strip_tags($raw, '<p><br><b><strong><i><a><ul><li><img><object><param><embed>');
        return $raw;
    }

    public function init()
    {
        /* Initialize action controller here */
        $this->_config = Zend_Registry::get('config');
        $this->_db = Zend_Registry::get('db');
        $zd = new Zend_Date();
        $search = GCMS_SearchEngine::getInstance();

        // Determine if we should fetch new feeds
        if ((time() - $this->_config['lastfetch']) >= $this->_config['fetchfreq']) {
            $fcount = 0;
            $feed_start = microtime(true);
            // Do a Google+ import?
            if (!empty($this->_config['plusapikey'])) {
                try {
                    $client = new Zend_Rest_Client('https://www.googleapis.com');
                    $response = $client->restGet('/plus/v1/people/' . $this->_config['plusid'] . '/activities/public', array('key' => $this->_config['plusapikey']));
                    $json = json_decode($response->getBody(), true);
                    if ($zd->set($json['updated'], Zend_Date::ISO_8601)->get() > $this->_config['lastfetch']) {
                        foreach ($json['items'] as $item) {
                            if ($item['verb'] != "post" || $item['object']['objectType'] != "note")
                                continue;
                            // Check for existing; update if found.
                            $old = $this->_db->fetchRow('SELECT id,title,summary FROM reader WHERE guid = ?', $item['id']);
                            // Is this a re-shared post?
                            if (isset($item['object']['attachments'])) {
                                $data = array('title' => $item['object']['attachments'][0]['displayName'],
                                              'summary' => $item['object']['attachments'][0]['content'],
                                              'source' => $item['object']['attachments'][0]['url'],
                                              'annotation' => $item['object']['content']);
                            } else if ($this->_config['plusmyposts']) {  // Share my original content?
                                $data = array('title' => $item['title'],
                                              'summary' => $item['object']['content'],
                                              'source' => $item['url']);
                            } else {
                                continue;
                            }
                            $data['pubdate'] = $zd->set($item['published'], Zend_Date::ISO_8601)->get(Zend_Date::ISO_8601);
                            $data['guid'] = $item['id'];
                            if ($old) {
                                if ($old['title'] != $data['title'] || $old['summary'] != $data['summary']) {
                                    $this->_db->update('reader', $data, 'id = ' . $old['id']);
                                }
                            } else {
                                $this->_db->insert('reader', $data);
                                $search->addItem($data['title'], $data['summary'], $zd->set($data['pubdate'])->get(), '', 'news/index/index/type/reader/id/' . $this->_db->lastInsertId());
                            }
                            $fcount++;
                        }
                    }
                } catch (Zend_Http_Client_Adapter_Exception $e) {
                    Zend_Registry::get('log')->log('Error connecting to Google+: ' . $e->getMessage(), Zend_Log::ERR);
                }
            } 
            elseif (!empty($this->_config['googlefeed'])) {
                $feed = array();
                $newestFetch = $zd->set($this->_db->fetchOne('SELECT pubdate FROM reader ORDER BY pubdate DESC LIMIT 1'), Zend_Date::ISO_8601);
                try {
                    $feed = Zend_Feed::import($this->_config['googlefeed']);
                } catch (Zend_Feed_Exception $e) {
                    // feed import failed
                    Zend_Registry::get('log')->log('Error importing feed: ' . $e->getMessage(), Zend_Log::ERR);
                } catch (Zend_Http_Client_Adapter_Exception $e) {
                    Zend_Registry::get('log')->log('Error connecting to feed: ' . $e->getMessage(), Zend_Log::ERR);
                }
                // Loop over each channel item and store relevant data
                foreach ($feed as $item) {
                    $data = array();
                    if ($zd->set($item->published(), Zend_Date::ATOM)->get() > $newestFetch) { // Poor Logic. Fetch from last timestamp in DB but sharing an old feed will cause it to be skipped.
                        $feedtags = '';
                        if ($feed instanceof Zend_Feed_Atom) {
                            if (count($item->category) <= 1)
                                $feedtags .= $item->category['term'];
                            else {
                                foreach ($item->category as $cat) {
                                    $feedtags .= $cat['term'] . ',';
                                }
                            }
                            // Fetch the full feed if there is no summary and try to make it a summary
                            $summary = $item->summary();
                            if (empty($summary)) {
                                // Try Google Reader style first
                                $tmp = preg_split("@</blockquote>@isUu", $item->content(), 2);
                                // If that doesn't seem right try more generic
                                if (count($tmp) == 1) {
                                    $tmp = preg_split("@<br(.*)>@isUu", $item->content(), 2);
                                    $summary = $tmp[0];
                                } else {
                                    $ann = preg_split("@<br(.*)>@isUu", $tmp[0]);
                                    $data['annotation'] = $ann[1];
                                    $summary = $tmp[1];
                                }
                            }
                            // We can have more than 1 link
                            if (count($item->link()) > 1)
                                $data['source'] = $item->link[0]['href'];
                            else
                                $data['source'] = $item->link['href'];
                            $data['tags']      = rtrim($feedtags,',');
                            $data['pubdate'] = $zd->set($item->published(), Zend_Date::ATOM)->get(Zend_Date::ISO_8601);
                            $data['guid'] = (empty($item->id)?sha1($data['source']):$item->id);
                        }
                        elseif ($feed instanceof Zend_Feed_Rss) {
                            $summary = $item->description();
                            $data['source']    = $item->link();
                            $data['tags']      = $item->category();
                            $data['pubdate'] = $zd->set($item->pubDate(), Zend_Date::RSS)->get(Zend_Date::ISO_8601);
                            $data['guid'] = (empty($item->guid)?sha1($data['source']):$item->guid);
                        }
                        else
                            trigger_error("Unknown feed type.  Must be valid RSS or ATOM.");
                        $data['title']   = $item->title();
                        $data['summary'] = $this->cleanFeed($summary, parse_url($data['source'], PHP_URL_HOST));
                        //print_r($data);
                        // Check for existing; update if found.
                        $old = $this->_db->fetchOne('SELECT id FROM reader WHERE source = ?', $data['source']);
                        if ($old) {
                            $this->_db->update('reader', $data, 'id = ' . $old);
                        } else {
                            $this->_db->insert('reader', $data);
                            $search->addItem($data['title'], $data['summary'], $data['pubdate'], $data['tags'], 'news/index/index/type/reader/id/' . $this->_db->lastInsertId());
                        }
                        $fcount++;
                    }
                }
            }
            $this->_config->write('lastfetch', time());
            //$this->view->getEngine()->clear_cache('feeds.tpl');
            $feed_end = microtime(true);
            Zend_Registry::get('log')->log('Fetched ' . $fcount . ' feed(s) in ' . ($feed_end - $feed_start) . ' seconds.', Zend_Log::INFO);
        }
    }

    public function indexAction()
    {
        //if (!empty($this->_config['forumdir']))
            //$phpbb = new Zend_PHPBB3($config['forumdir']);
        $sticky = array();
        $feed = array();
        // First fetch our sticky posts
        $rs = $this->_db->query("SELECT * FROM news WHERE sticky = 1 AND published = 1 ORDER BY pubdate DESC");
        while ($row = $rs->fetch()) {
            if (!empty($row['tags']))
                $row['tags'] = $row['tags'];
            if (isset($phpbb) && $phpbb->get_approved($row['comments']))
                $row['comments'] = array('fid' => $this->_config['forumid'], 'tid' => $row['comments'], 'count' => $phpbb->get_count($row['comments']));
            $sticky[] = $row;
        }
        $this->view->assign('sticky', $sticky);
        // Union our regular news posts and RSS feeds
        $rs = $this->_db->query("(SELECT id,title,content,NULL as source,pubdate,moddate,tags,comments,NULL as annotation,'news' as type FROM news WHERE sticky = 0 AND published = 1)
                           UNION (SELECT id,title,summary as content,source,pubdate,NULL as moddate,tags,NULL as comments,annotation,'reader' as type FROM reader) ORDER BY pubdate DESC LIMIT 5");
        while ($row = $rs->fetch()) {
            if ($row['type'] == 'reader') {
                $source = parse_url($row['source'], PHP_URL_HOST);
                $content = $row['content'] . " <a href=\"$row[source]\" target=\"_blank\">" . Zend_Registry::get('Zend_Translate')->_("[Read More..]") . "</a>";
                $feed[] = array("id" => $row['id'], "title" => $row['title'], "source" => $source, "content" => $content, "pubdate" => $row['pubdate'], "annotation" => $row['annotation'], "tags" => $row['tags'], "type" => $row['type']);
            } else {
                if (isset($phpbb) && $phpbb->get_approved($row['comments']))
                    $row['comments'] = array('fid' => $this->_config['forumid'], 'tid' => $row['comments'], 'count' => $phpbb->get_count($row['comments']));
                $feed[] = array("id" => $row['id'], "title" => $row['title'], "content" => $row['content'], "pubdate" => $row['pubdate'], "moddate" => $row['moddate'], "tags" => $row['tags'], "type" => $row['type'], "comments" => $row['comments']);
            }
        }
        $this->view->assign('feed', $feed);           
    }
    
}

