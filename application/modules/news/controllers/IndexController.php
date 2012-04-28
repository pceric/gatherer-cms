<?php
/**
 * This controller manages displaying of news posts
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class News_IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $db = Zend_Registry::get('db');

        if ($this->_getParam('id') == NULL)
            throw new Zend_Controller_Dispatcher_Exception("Missing news ID.");
        
        // Get page tags and title for meta tag
        if ($this->_getParam('type') == 'reader')
            $data = $db->fetchRow("SELECT id,title,tags FROM reader WHERE id = " . $db->quote($this->_getParam('id')));
        else
            $data = $db->fetchRow("SELECT id,title,tags,'news' as type FROM news WHERE id = " . $db->quote($this->_getParam('id')) . " AND published = 1");
        if ($data == null)
            throw new Zend_Controller_Dispatcher_Exception("Content is not available.");
        // Increase hit count but only once per session
        if ($this->_getParam('type') != 'reader') {
            $visitorNamespace = new Zend_Session_Namespace('gcmsvisitor');
            if (!isset($visitorNamespace->visited['news'][(int)$this->_getParam('id')])) {
                $db->update('news', array('hits' => new Zend_Db_Expr('(hits+1)')), 'id = '.$db->quote($this->_getParam('id')));
                $visitorNamespace->visited['news'][(int)$this->_getParam('id')] = true;
            }
        }
        $this->view->headTitle($data['title']);
        if (!empty($data['tags']))
            $this->view->headMeta()->setName('keywords', $data['tags']);
    }

    public function indexAction()
    {
        //require_once './libs/phpbb.php';
        $db = Zend_Registry::get('db');
        
        if (!empty($config['forumdir']))
            $phpbb = new Zend_PHPBB3($config['forumdir']);
        if ($this->_getParam('type') == 'reader') {
            $data = $db->fetchRow("SELECT id,title,summary as content,source,tags,pubdate,annotation,'reader' as type FROM reader WHERE id = ".$db->quote($this->_getParam('id')));
            $data['content'] .= " <a href=\"$data[source]\" target=\"_blank\">" . Zend_Registry::get('Zend_Translate')->_("[Read More..]") . "</a>";
            $data['source'] = parse_url($data['source'], PHP_URL_HOST);
        } else {
            $data = $db->fetchRow("SELECT *,'news' as type FROM news WHERE id = ".$db->quote($this->_getParam('id'))." AND published = 1");
            if (isset($phpbb) && $phpbb->get_approved($data['comments']))
                $data['comments'] = array('fid' => $config['forumid'], 'tid' => $data['comments'], 'count' => $phpbb->get_count($data['comments']));
        }
        $this->view->assign('data', $data);
    }
}
?>
