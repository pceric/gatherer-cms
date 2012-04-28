<?php
/**
 * This controller administers the banner module
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class Banner_AdminController extends Zend_Controller_Action
{
    private $db;

    public function preDispatch()
    {
        if (!Zend_Registry::get('acl')->isAllowed('admin'))
            $this->_forward('login', 'index', 'admin');
        
        // cancel catch-all
        if ($this->_getParam('cancel') != NULL) {
            $this->_redirect('banner/admin');
        }
    }

    public function init()
    {
        $this->db = Zend_Registry::get('db');
        $cfg = Zend_Registry::get('config');

        $this->view->headScript()->appendFile($this->view->baseUrl("themes/default/js/datepicker/Locale.en-US.DatePicker.js"));
        $this->view->headScript()->appendFile($this->view->baseUrl("themes/default/js/datepicker/Picker.js"));
        $this->view->headScript()->appendFile($this->view->baseUrl("themes/default/js/datepicker/Picker.Attach.js"));
        $this->view->headScript()->appendFile($this->view->baseUrl("themes/default/js/datepicker/Picker.Date.js"));
        $this->view->headLink()->appendStylesheet($this->view->baseUrl("themes/default/js/datepicker/datepicker_dashboard/datepicker_dashboard.css"));

        // Configure WYSIWYG editor
        if ($cfg['editor'] == 'CKEditor') {
            $this->view->headScript()->captureStart();
            echo <<<FCK
            window.addEvent('domready', function() {
                // replace all of the textareas
                CKEDITOR.replace('wysiwyg',
                    {
                        customConfig : CKEDITOR.basePath + '../ckeconfig.js'
                    }
                );
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
        $this->view->headTitle('Manage Banners');
        
        $this->view->assign('sizes', array('728x90', '468x60', '234x60', '120x600', '160x600', '120x240', '250x250', '200x200', '125x125'));
        $data = $this->db->fetchRow("SELECT * FROM banners WHERE id = ?", $_GET['bid']);
		$this->view->assign('data', $data);
    }
    
    public function clientsAction()
    {
        $cfg = Zend_Registry::get('config');
        $this->view->headTitle('Manage Clients');
        
        $data = $this->db->fetchAll("SELECT * FROM clients");
        $this->view->assign('data', $data);
    }

    public function bannersAction()
    {
        $this->view->headTitle('Manage Banners');
        $data = $this->db->query($this->db->select()->from('banners')->join('clients', 'clients.id = banners.client', array('name'))->where('banners.client = ?', $_GET['client']))->fetchAll();
        $this->view->assign('data', $data);
    }

    public function addAction()
    {
	    if ($this->_getParam('savecontent') != NULL) {
            if ($this->_getParam('published'))
                $published = 1;
            else
                $published = 0;
            if ($this->_getParam('menu'))
                $menu = 1;
            else
                $menu = 0;
                
            // Save new data
            $data = array('title' => $_POST['title'],
                          'content' => $_POST['content'],
                          'tags' => $_POST['tags'],
                          'published' => $published,
                          'pubdate' => new Zend_Db_Expr('NOW()'));
            $this->db->insert('articles', $data);
            $this->_setParam('id', $this->db->lastInsertId());
            
            $this->_forward('index');
        }

        $this->_helper->viewRenderer('edit'); 
    }

    public function deleteAction()
    {
		$this->db->delete('banners', 'id = ' . $this->db->quote($this->_getParam('bid')));
        $this->_forward('index');
    }
    
    public function editAction()
    {
        if ($this->_getParam('id') == NULL)
            throw new Zend_Controller_Dispatcher_Exception("Missing article ID.");

        // Save
	    if ($this->_getParam('savecontent') != NULL) {
        }
    }

    /*
    public static function adminMenu()
    {
        $view = Zend_Registry::get('view');
        $stack = array();
        $stack[] = Zend_Registry::get('Zend_Translate')->_("Banner Config");
        $stack[$view->url(array('module' => 'banner', 'controller' => 'admin', 'action' => 'clients'), null, true)] = Zend_Registry::get('Zend_Translate')->_("Manage Clients");
        $stack[$view->url(array('module' => 'banner', 'controller' => 'admin', 'action' => 'index'), null, true)] = Zend_Registry::get('Zend_Translate')->_("Manage Banners");
        return $stack;
    }
    */

}
?>
