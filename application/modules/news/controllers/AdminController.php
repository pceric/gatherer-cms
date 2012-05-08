<?php
/**
 * This controller manages the saving and editing of news posts
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class News_AdminController extends Zend_Controller_Action
{
    private $_db;
    private $_search;
    
    public function preDispatch()
    {
        if (!Zend_Registry::get('acl')->isAllowed('admin'))
            $this->_forward('login', 'index', 'admin');
        
        // cancel catch-all
        if ($this->_getParam('cancel') != NULL) {
            $this->_redirect('news/admin');
        }
    }

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $cfg = Zend_Registry::get('config');
        $this->_search = GCMS_SearchEngine::getInstance();
        
        // Setup MooTools datepicker
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
        $this->view->headTitle('Manage News');
		$data = $this->_db->fetchAll("SELECT * FROM news ORDER BY pubdate DESC");
		$this->view->assign('data', $data);
    }

    public function addAction()
    {
        // Save data
	    if ($this->_getParam('savecontent') != NULL) {
            $published = $this->_getParam('published')==null?0:1;
                
            // Save new data
            $data = array('title' => $_POST['title'],
                          'content' => $_POST['content'],
                          'tags' => $_POST['tags'],
                          'comments' => $this->_getParam('comments')==null?0:1,
                          'published' => $published,
                          'pubdate' => new Zend_Db_Expr('NOW()'));
            $data['sticky'] = $this->_getParam('sticky')==null?0:1;
            $this->_db->insert('news', $data);
            $this->_setParam('id', $this->_db->lastInsertId());
            $needsIntro = true;

            // Add to lucene
            if ($published)
                $this->_search->addItem($data['title'], $data['content'], time(), $data['tags'], $this->view->url(array('module' => 'news', 'controller' => 'index', 'action' => 'index', 'id' => $this->_getParam('id')), null, true));
            
            /* Create a new phpBB topic
            if (isset($phpbb) && isset($needsIntro)) {
                $url = $GLOBALS['config']['siteURL'] . '?mod=' . $_GET['type'] . '&amp;id=' . $_POST['id'];
                $intro = str_replace(array("%t", "%u", "%c"), array($_POST['title'], $url, $_POST['content']), $GLOBALS['config']['threadintro']);
                $topic_id = $phpbb->create_topic($_POST['title'], $intro, $GLOBALS['config']['forumid']);
                $__db->update($table, array('comments' => $topic_id), 'id = ' . $_POST['id']);	
            }
            */
            
            //$this->_request->clearParams();
            $this->_setParam('msg', '<div class="alert alert-success">' . Zend_Registry::get('Zend_Translate')->_('Post added successfully') . '</div>');
            $this->_forward('index');
        }

        $this->view->headTitle('Add Article');
        $this->_helper->viewRenderer('edit'); 
    }

    public function deleteAction()
    {
        if ($this->_getParam('id') != NULL) {
            $this->_db->delete('news', 'id = ' . $this->_db->quote($this->_getParam('id')));
            $this->_search->deleteItem($this->view->url(array('module' => 'news', 'controller' => 'index', 'action' => 'index', 'id' => $this->_getParam('id')), null, true));
            //$this->_request->clearParams();
            $this->_setParam('msg', '<div class="alert alert-success">' . Zend_Registry::get('Zend_Translate')->_('Post deleted successfully') . '</div>');
        }
        $this->_forward('index');
    }
    
    public function editAction()
    {
        if ($this->_getParam('id') == NULL)
            throw new Zend_Controller_Dispatcher_Exception("Missing news ID.");

        $this->view->headTitle('Edit News');
        
        if ($this->_getParam('savecontent') != NULL) {
            $published = $this->_getParam('published')==null?0:1;

            // Did we update the content?
            $old = $this->_db->fetchRow("SELECT content,comments,published,moddate FROM news WHERE id = " . $this->_db->quote($this->_getParam('id')));
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
                        'sticky' => $this->_getParam('sticky')==null?0:1,
                        'published' => $published,
                        'moddate' => $moddate);
            $this->_db->update('news', $data, 'id = ' . $this->_db->quote($this->_getParam('id')));

            // Update lucene index if the content changed
            if (!$published) {
                $this->_search->deleteItem($this->view->url(array('module' => 'news', 'controller' => 'index', 'action' => 'index', 'id' => $this->_getParam('id')), null, true));
            } else if ($published != $old['published']) {
                $this->_search->addItem($data['title'], $data['content'], time(), $data['tags'], $this->view->url(array('module' => 'news', 'controller' => 'index', 'action' => 'index', 'id' => $this->_getParam('id')), null, true));
            } else {
                $this->_search->updateItem($data['title'], $data['content'], time(), $data['tags'], $this->view->url(array('module' => 'news', 'controller' => 'index', 'action' => 'index', 'id' => $this->_getParam('id')), null, true));
            }
            
            //$this->_request->clearParams();
            $this->_setParam('msg', '<div class="alert alert-success">' . Zend_Registry::get('Zend_Translate')->_('Post edited successfully') . '</div>');
            $this->_forward('index');
        }

        // Edit
        $row = $this->_db->fetchRow("SELECT * FROM news WHERE id = " . $this->_db->quote($this->_getParam('id')));
        //if ($phpbb)
        //    $row['approved'] = $phpbb->get_approved($row['comments']);
        $this->view->assign('row', $row, false);
        //$this->view->assign('filters', scandir('./filters'));
    }

    public static function adminMenu()
    {
        $view = Zend_Registry::get('view');
        $stack = array();
        $stack[] = Zend_Registry::get('Zend_Translate')->_("News Config");
        $stack[$view->url(array('module' => 'news', 'controller' => 'admin', 'action' => 'add'), null, true)] = Zend_Registry::get('Zend_Translate')->_("Add New News Post");
        $stack[$view->url(array('module' => 'news', 'controller' => 'admin', 'action' => 'index'), null, true)] = Zend_Registry::get('Zend_Translate')->_("Edit News Posts");
        return $stack;
    }

}
?>
