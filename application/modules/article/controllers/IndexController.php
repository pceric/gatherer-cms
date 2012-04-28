<?php
/**
 * This controller manages articles
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class Article_IndexController extends Zend_Controller_Action
{
    private $_db;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');

        if ($this->_getParam('id') == NULL)
            throw new Zend_Controller_Dispatcher_Exception("Missing article ID.");

        // Get page tags and title for meta tag
        $data = $this->_db->fetchRow("SELECT id,title,tags FROM articles WHERE id = " . $this->_db->quote($this->_getParam('id')) . " AND published = 1");
        if ($data == null)
            throw new Zend_Controller_Dispatcher_Exception("Content is not available.");
        // Increase hit count but only once per session
        $visitorNamespace = new Zend_Session_Namespace('gcmsvisitor');
        if (!isset($visitorNamespace->visited['article'][(int)$this->_getParam('id')])) {
            $this->_db->update('articles', array('hits' => new Zend_Db_Expr('(hits+1)')), 'id = '.$this->_db->quote($this->_getParam('id')));
            $visitorNamespace->visited['article'][(int)$this->_getParam('id')] = true;
        }
        $this->view->headTitle($data['title']);
        if (!empty($data['tags']))
            $this->view->headMeta()->setName('keywords', $data['tags']); // Override default keywords
    }

    public function indexAction()
    {
        $cfg = Zend_Registry::get('config');

        //if (!empty($config['forumdir']))
            //$phpbb = new Zend_PHPBB3($config['forumdir']);
        $this->view->getEngine()->loadFilter('output', 'geshi');
        $data = $this->_db->fetchRow("SELECT * FROM articles WHERE id = " . $this->_db->quote($this->_getParam('id')) . " AND published = 1");
        $page = preg_split('/<!\-\-pagebreak.*\-\->/iU', $data['content'], -1, PREG_SPLIT_NO_EMPTY);
        $matches = array();
        $matches2 = array();
        $titles = array($data['title']);
        if (preg_match_all('/<!\-\-pagebreak(.*)\-\->/iU', $data['content'], $matches) > 0) {
            foreach ($matches[1] as $match) {
                if (preg_match('/\s*title="(.*)"\s*/iU', $match, $matches2) > 0) {
                    $titles[] = $matches2[1];
                } else {
                    $titles[] = $data['title'];
                }
            }
        }
        if (isset($phpbb) && $phpbb->get_approved($data['comments']))
            $data['comments'] = array('fid' => $config['forumid'], 'tid' => $data['comments'], 'count' => $phpbb->get_count($data['comments']));
        $this->view->assign('ptitle', $titles);
        $this->view->assign('page', $page);
        $this->view->assign('pagecount', count($page));
        $this->view->assign('data', $data);
    }

    public function printAction() 
    {
        $data = $this->_db->fetchRow("SELECT * FROM articles WHERE id = " . $this->_db->quote($this->_getParam('id')) . " AND published = 1");
        $this->view->assign('title', $data['title']);
        $this->view->assign('content', preg_replace('/<!\-\-pagebreak(.*)\-\->/iU', '<br />', $data['content']));
        $this->view->assign('published', $data['pubdate']);
        $this->view->assign('modified', $data['moddate']);
        $this->_helper->layout->disableLayout();
    }

}
?>
