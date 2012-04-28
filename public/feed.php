<?php
    /* 
        RSS Feed Generator for GCMS
        Copyright © 2007-2012 by Eric Hokanson

        This program is free software: you can redistribute it and/or modify
        it under the terms of the GNU Lesser General Public License as published by
        the Free Software Foundation, either version 3 of the License, or
        (at your option) any later version.
        
        This program is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        GNU Lesser General Public License for more details.
        
        You should have received a copy of the GNU Lesser General Public License
        along with this program.  If not, see <http://www.gnu.org/licenses/>.
    */

    // A couple requirement checks
    if (version_compare(PHP_VERSION, '5.2.4', '<') || !class_exists('DOMDocument'))
        throw new Exception('Error: This software requires PHP 5.2.4 and the libxml extension.');
    
    // Define path to application directory
    defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

    // Define application environment
    defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

    // Ensure library/ is on include_path
    set_include_path(implode(PATH_SEPARATOR, array(
        realpath(APPLICATION_PATH . '/../library'),
        get_include_path(),
    )));
    
    require_once 'Zend/Loader/Autoloader.php';
    
    $autoloader = Zend_Loader_Autoloader::getInstance();
    $autoloader->registerNamespace('GCMS_');
    
    // Configure our Zend Framework cache
    $frontendOptions = array('lifetime' => 300);
    $backendOptions = array('cache_dir' => APPLICATION_PATH . '/../data/cache/');
    $cache = Zend_Cache::factory('Output', 'File', $frontendOptions, $backendOptions);

    // Get feed type
    if (isset($_GET['RSS']) || (isset($_GET['type']) && $_GET['type'] == 'RSS2.0'))
        $type = 'rss';
    else
        $type = 'atom';

    // Cloned from Google's feed headers
    header("Content-Type: text/xml; charset=UTF-8");
    header("Cache-Control: private");
    
    // Check if we have a valid cache
    if(!$cache->start($type)) {
        // Setup
        $zd = new Zend_Date();
        $ini = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $db = Zend_Db::factory($ini->resources->db);
        $config = new GCMS_Config($db);

        // Figure out the site URL
        if (empty($config['siteURL'])) {
            $proto = (!empty($_SERVER['HTTPS']))?'https://':'http://';
            $file = (!empty($_SERVER['HTTP_MOD_REWRITE']))?'':'index.php';
            $siteURL = $proto . $_SERVER['SERVER_NAME'] . substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], "/")+1) . $file;
        } else {
            $siteURL = $config['siteURL'];
        }

        // Union our regular news posts and RSS feeds
        $rs = $db->query("(SELECT id,title,content,NULL as source,pubdate,tags,NULL as annotation,'news' as type FROM news WHERE published = 1)
                           UNION (SELECT id,title,summary as content,source,pubdate,tags,annotation,'reader' as type FROM reader) ORDER BY pubdate DESC LIMIT 20");

        // Build feed array
        $feed = array();
        while ($row = $rs->fetch())
            $feed[] = array("id" => $row['id'], "title" => $row['title'], "source" => $row['source'], "content" => $row['content'], "pubdate" => $row['pubdate'], "annotation" => $row['annotation'], "tags" => $row['tags'], "type" => $row['type']);

        // Make proper Zend_Feed
        $array = array('title' => $config['sitename'], 'link' => $siteURL, 'lastUpdate' => time(), 'charset' => 'utf-8', 'description' => $config['sitedesc'], 'author' => $config['siteauthor'], 'entries' => array());
        $entry = 0;
        foreach($feed as $v) {
            // HTML title's don't appear to be supported
            // HTML is also not allowed in description
            $array['entries'][$entry] = array('title' => html_entity_decode($v['title'], ENT_QUOTES, 'UTF-8'),
                                              'link' => rtrim($siteURL, '/') . '/news/index/index/type/' . $v['type'] . '/id/' . $v['id'],
                                              'description' => html_entity_decode(strip_tags($v['content']), ENT_QUOTES, 'UTF-8'),
                                              'content' => $v['content'],
                                              'lastUpdate' => $zd->set($v['pubdate'],Zend_Date::ISO_8601)->get());
            if ($v['type'] == 'reader')
                $array['entries'][$entry]['source'] = array('title' => $v['title'], 'url' => $v['source']);
            if (!empty($v['tags'])) {
                $array['entries'][$entry]['category'] = array();
                foreach(explode(',', $v['tags']) as $t)
                    $array['entries'][$entry]['category'][] = array('term' => trim($t));
            }
            if ($type == 'rss') // Possible bug in Zend_Feed?
                $array['entries'][$entry]['link'] = htmlspecialchars($array['entries'][$entry]['link']);
            $entry++;
        }

        $feedFromArray = @Zend_Feed::importArray($array, $type);
    
        if ($feedFromArray instanceof Zend_Feed_Abstract)
            print $feedFromArray->saveXML();  // send feed to client
        $cache->end();
    }
?>
