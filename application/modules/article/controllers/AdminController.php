<?php
/**
 * This controller manages the article admin interface
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class Article_AdminController extends Zend_Controller_Action
{
    private $_db;
    private $_search;

    public function preDispatch()
    {
        if (!Zend_Registry::get('acl')->isAllowed('admin'))
            $this->_forward('login', 'index', 'admin');
        
        // cancel catch-all
        if ($this->_getParam('cancel') != NULL) {
            $this->_redirect('article/admin');
        }
    }

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_search = GCMS_SearchEngine::getInstance();
        $cfg = Zend_Registry::get('config');

        $this->view->headScript()->appendFile($this->view->baseUrl("themes/default/js/datepicker/Locale.en-US.DatePicker.js"));
        $this->view->headScript()->appendFile($this->view->baseUrl("themes/default/js/datepicker/Picker.js"));
        $this->view->headScript()->appendFile($this->view->baseUrl("themes/default/js/datepicker/Picker.Attach.js"));
        $this->view->headScript()->appendFile($this->view->baseUrl("themes/default/js/datepicker/Picker.Date.js"));
        $this->view->headLink()->appendStylesheet($this->view->baseUrl("themes/default/js/datepicker/datepicker_vista/datepicker_vista.css"));

        /*
        if (!empty($config['forumid'])) {
            $headers->headScript()->captureStart();
            echo <<<CONFIG
            window.addEvent('domready', function() {
                popTopic($('phpbbpath').value, $config[forumid]);			
            });
CONFIG;
            $headers->headScript()->captureEnd();
        }
        */

        // Configure WYSIWYG editor
        if ($cfg['editor'] == 'CKEditor') {
            $this->view->headScript()->captureStart();
            echo <<<FCK
            window.addEvent('domready', function() {
                // replace all of the textareas
                if ($('wysiwyg') != null) {
                    CKEDITOR.replace('wysiwyg', {
                        customConfig : CKEDITOR.basePath + '../ckeconfig.js'
                    });
                }
            });
FCK;
            $this->view->headScript()->captureEnd();
        }
        elseif ($cfg['editor'] == 'TinyMCE') {
            $this->view->headScript()->captureStart();
            echo <<<MCE
            tinyMCE.init({
                mode : "exact",
                elements : "wysiwyg",
                theme : "advanced",
                plugins : "fullscreen,visualchars",
                theme_advanced_buttons3_add : "separator,visualchars,separator,fullscreen",
                entity_encoding : "raw",
                remove_linebreaks : false,
                forced_root_block : ''
            });
MCE;
            $this->view->headScript()->captureEnd();
        }
    }

    public function indexAction()
    {
        $cfg = Zend_Registry::get('config');
        $this->view->headTitle('Manage Articles');
		$data = $this->_db->fetchAll("SELECT * FROM articles ORDER BY pubdate DESC");
		$this->view->assign('data', $data);
    }

    public function addAction()
    {
	    if ($this->_getParam('savecontent') != NULL) {
            $published = $this->_getParam('published')==null?0:1
            $menu = $this->_getParam('menu')==null?0:1
                
            // Save new data
            $data = array('title' => $_POST['title'],
                          'content' => $_POST['content'],
                          'tags' => $_POST['tags'],
                          'comments' => $this->_getParam('comments')==null?0:1,
                          'published' => $published,
                          'pubdate' => new Zend_Db_Expr('NOW()'));
            $this->_db->insert('articles', $data);
            $this->_setParam('id', $this->_db->lastInsertId());
            $needsIntro = true;
            
            // Add to lucene
            if ($published)
                $this->_search->addItem($data['title'], $data['content'], time(), $data['tags'], $view->url(array('module' => 'article', 'controller' => 'index', 'action' => 'index', 'id' => $this->_getParam('id')), null, true)); 

            /* Create a new phpBB topic
            if (isset($phpbb) && isset($needsIntro)) {
                $url = $GLOBALS['config']['siteURL'] . '?mod=' . $_GET['type'] . '&amp;id=' . $_POST['id'];
                $intro = str_replace(array("%t", "%u", "%c"), array($_POST['title'], $url, $_POST['content']), $GLOBALS['config']['threadintro']);
                $topic_id = $phpbb->create_topic($_POST['title'], $intro, $GLOBALS['config']['forumid']);
                $_db->update($table, array('comments' => $topic_id), 'id = ' . $_POST['id']);	
            }
            */
            
            // Add, edit or remove a link on our menu
            $last_weight = $this->_db->fetchOne("SELECT weight FROM menu WHERE parent = 2 ORDER BY weight DESC LIMIT 1");
            if ($published == 1 && $menu == 1) {
                $this->_db->insert('menu', array('name' => $this->_getParam('title'), 'link' => serialize(array('module' => 'article', 'controller' => 'index', 'action' => 'index', 'params' => array('id' => (int)$this->_getParam('id')))), 'parent' => 2, 'weight' => ($last_weight + 1)));
            }

            $this->_forward('index');
        }

        $this->_helper->viewRenderer('edit'); 
    }

    public function deleteAction()
    {
		$this->_db->delete('article', 'id = ' . $this->_db->quote($this->_getParam('id')));
        $this->_search->rebuildIndex();
        $this->_forward('index');
    }
    
    public function editAction()
    {
        if ($this->_getParam('id') == NULL)
            throw new Zend_Controller_Dispatcher_Exception("Missing article ID.");

        // Save
	    if ($this->_getParam('savecontent') != NULL) {
            $published = $this->_getParam('published')==null?0:1
            $menu = $this->_getParam('menu')==null?0:1
            
            $old = $this->_db->fetchRow("SELECT content,comments,moddate FROM articles WHERE id = " . $this->_db->quote($this->_getParam('id')));
            if ($old['content'] != $this->_getParam('content'))
                $moddate = new Zend_Db_Expr('NOW()');
            else
                $moddate = $old['moddate'];
            /* Set phpBB approved flag
            if (isset($phpbb)) {
                if (isset($_POST['comments'])) {
                    // If comments were off before we may need to create a new topic now
                    if ($old['comments'] > 0)
                        $phpbb->set_approved($old['comments']);
                    else
                        $needsIntro = true;
                }
                else
                    $phpbb->set_approved($old['comments'], FALSE);
            }
            else
                $old['comments'] = 0;
            */
            $data = array('title' => $this->_getParam('title'),
                        'content' => $this->_getParam('content'),
                        'tags' => $this->_getParam('tags'),
                        'comments' => $this->_getParam('comments')==null?0:1,
                        'published' => $published,
                        'moddate' => $moddate);
            $this->_db->update('articles', $data, 'id = ' . $this->_db->quote($this->_getParam('id')));
            
            // Rebuild lucene index
            $this->_search->rebuildIndex();
            
            $count = $this->_db->fetchOne("SELECT COUNT(*) FROM menu WHERE parent = 2 AND link = '" . serialize(array('module' => 'article', 'controller' => 'index', 'action' => 'index', 'params' => array('id' => (int)$this->_getParam('id')))) . "'");
            
            // Add, edit or remove a link on our menu
            $last_weight = $this->_db->fetchOne("SELECT weight FROM menu WHERE parent = 2 ORDER BY weight DESC LIMIT 1");
            if ($published == 1 && $menu == 1 && $count < 1) {
                $this->_db->insert('menu', array('name' => $this->_getParam('title'), 'link' => serialize(array('module' => 'article', 'controller' => 'index', 'action' => 'index', 'params' => array('id' => (int)$this->_getParam('id')))), 'parent' => 2, 'weight' => ($last_weight + 1)));
            }
            elseif ($published == 1 && $menu == 1 && $count > 0) {
                $this->_db->update('menu', array('name' => $this->_getParam('title')), "link = '" . serialize(array('module' => 'article', 'controller' => 'index', 'action' => 'index', 'params' => array('id' => (int)$this->_getParam('id')))) . "'");
            }
            elseif ($published == 0 || $menu == 0) {
                $this->_db->delete('menu', "link = '" . serialize(array('module' => 'article', 'controller' => 'index', 'action' => 'index', 'params' => array('id' => (int)$this->_getParam('id')))) . "'");
                //gcmsNav_compact(2);
            }

            $this->_forward('index');
        } else {
            // Edit
            $row = $this->_db->fetchRow("SELECT * FROM articles WHERE id = " . $this->_db->quote($this->_getParam('id')));
            // Try to find a menu link
            $menu = $this->_db->fetchAll("SELECT link FROM menu WHERE link IS NOT NULL");
            $row['menu'] = false;
            foreach ($menu as $v) {
                $v = unserialize($v['link']);
                if ($v['module'] == 'article' && $v['params']['id'] == $this->_getParam('id')) {
                    $row['menu'] = true;
                    break;
                }
            }
            //if ($phpbb)
            //    $row['approved'] = $phpbb->get_approved($row['comments']);
            $this->view->assign('row', $row, false);
            //$this->view->assign('filters', scandir('./filters'));
        }
    }

    public static function adminMenu()
    {
        $view = Zend_Registry::get('view');
        $stack = array();
        $stack[] = Zend_Registry::get('Zend_Translate')->_("Article Config");
        $stack[$view->url(array('module' => 'article', 'controller' => 'admin', 'action' => 'add'), null, true)] = Zend_Registry::get('Zend_Translate')->_("Add New Article");
        $stack[$view->url(array('module' => 'article', 'controller' => 'admin', 'action' => 'index'), null, true)] = Zend_Registry::get('Zend_Translate')->_("Edit Articles");
        return $stack;
    }

}
?>
