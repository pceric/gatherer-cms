<?php
/**
 * This controller administers the banner module
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class Banner_AdminController extends Zend_Controller_Action
{
    private $sizes = array('728x90', '468x60', '234x60', '120x600', '160x600', '120x240', '250x250', '200x200', '125x125');
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
        $this->view->headLink()->appendStylesheet($this->view->baseUrl("themes/default/js/datepicker/datepicker_vista/datepicker_vista.css"));
    }

    public function indexAction()
    {
        $this->_forward('clients');
    }
    
    public function clientsAction()
    {
        $cfg = Zend_Registry::get('config');
        $this->view->headTitle('Manage Clients');
        
        $data = $this->db->query($this->db->select()->from('clients')->joinLeft('banners', 'banners.client = clients.id', array('count' => 'COUNT(banners.id)')))->fetchAll();
        $this->view->assign('data', $data);
    }

    public function bannersAction()
    {
        $cfg = Zend_Registry::get('config');
        $this->view->headTitle('Manage Banners');
        
        if ($this->_getParam('cid') == NULL)
            throw new Zend_Controller_Dispatcher_Exception('Missing client ID.');
        
        $data = $this->db->fetchRow('SELECT * FROM clients WHERE id = ?', $this->_getParam('cid'));
        
        if ($data == null)
            throw new Zend_Controller_Dispatcher_Exception("Client not found.");
        
        $banners = $this->db->fetchAll('SELECT * FROM banners WHERE client = ?', $this->_getParam('cid'));
        
        $this->view->assign('data', $data);
        $this->view->assign('banners', $banners);
    }

    public function addAction()
    {
	    if ($this->_getParam('save') != NULL) {
            $validatorChain = new Zend_Validate();
            if ($this->_getParam('type') == 'client') {
                $validatorChain->addValidator(new Zend_Validate_NotEmpty())->
                                 addValidator(new Zend_Validate_Db_NoRecordExists(
                                                  array('adapter' => $this->db,
                                                        'table' => 'clients',
                                                        'field' => 'name')));
                // Save new data
                $data = array('name' => $this->_getParam('name'),
                              'contact' => $this->_getParam('phone'),
                              'email' => $this->_getParam('email'),
                              'extrainfo' => $this->_getParam('notes'),
                              'createdate' => new Zend_Db_Expr('NOW()'));
                // Validate
                if ($validatorChain->isValid($data['name'])) {
                    $this->db->insert('clients', $data); 
                    $this->_forward('clients');
                } else {
                    $this->view->assign('alert_class', 'error');
                    $this->view->assign('alert_msg', $validatorChain->getMessages());
                }
            } else {
                // Save new data
                $data = array('client' => $this->_getParam('cid'),
                              'size' => $this->_getParam('size'),
                              'image' => $this->_getParam('image'),
                              'url' => $this->_getParam('url'),
                              'code' => $this->_getParam('code'),
                              'active' => $this->_getParam('active'),
                              'startdate' => new Zend_Db_Expr('NOW()'));
                $this->db->insert('banners', $data); 
                $this->_forward('banners');
            }
        }
        
        if ($this->_getParam('type') == 'client') { 
            $this->_helper->viewRenderer('edit_client');
        } else {
            $this->view->assign('sizes', $this->sizes);
            $this->view->assign('cid', $this->_getParam('cid'));
            $this->_helper->viewRenderer('edit_banner');
        }
    }

    public function deleteAction()
    {
        if ($this->_getParam('type') == 'client' && $this->_getParam('cid') != NULL) {
    		$this->db->delete('clients', 'id = ' . $this->db->quote($this->_getParam('cid')));
            $this->_redirect('banner/admin/clients');
        } 
        else if ($this->_getParam('type') == 'banner' && $this->_getParam('bid') != NULL) {
    		$this->db->delete('banners', 'id = ' . $this->db->quote($this->_getParam('bid')));
            $this->_redirect('banner/admin/banners/cid/' . $this->_getParam('cid'));
        }
    }
    
    public function editAction()
    {
        if ($this->_getParam('cid') == NULL && $this->_getParam('bid') == NULL)
            throw new Zend_Controller_Dispatcher_Exception('Missing client or banner ID.');
        
        $this->view->assign('cid', $this->_getParam('cid'));

        // Save
	    if ($this->_getParam('save') != NULL) {
            $validatorChain = new Zend_Validate();
            if ($this->_getParam('type') == 'client') {
                $validatorChain->addValidator(new Zend_Validate_NotEmpty())->
                                 addValidator(new Zend_Validate_Db_NoRecordExists(
                                                  array('adapter' => $this->db,
                                                        'table' => 'clients',
                                                        'field' => 'name',
                                                        'exclude' => $this->db->quoteInto('name != (SELECT name FROM clients WHERE id = ?)', $this->_getParam('cid')))));
                // Update data
                $data = array('name' => $this->_getParam('name'),
                              'contact' => $this->_getParam('phone'),
                              'email' => $this->_getParam('email'),
                              'extrainfo' => $this->_getParam('notes'),
                              'createdate' => new Zend_Db_Expr('NOW()'));
                // Validate
                if ($validatorChain->isValid($data['name'])) {
                    $this->db->update('clients', $data, 'id = ' . $this->db->quote($this->_getParam('cid')));
                    $this->_forward('clients');
                } else {
                    $this->view->assign('alert_class', 'error');
                    $this->view->assign('alert_msg', $validatorChain->getMessages());
                }
            } else {
                if ($this->_getParam('bantype') == 'img') {
                    $this->_setParam('code', '');
                }
                // Update data
                $data = array('client' => $this->_getParam('cid'),
                              'size' => $this->_getParam('size'),
                              'image' => $this->_getParam('image'),
                              'url' => $this->_getParam('url'),
                              'code' => $this->_getParam('code'),
                              'active' => $this->_getParam('active'),
                              'startdate' => new Zend_Db_Expr('NOW()'));
                $this->db->update('banners', $data, 'id = ' . $this->db->quote($this->_getParam('bid'))); 
                $this->_forward('banners');
            }
        }
        
        if ($this->_getParam('type') == 'client') {
            $data = $this->db->fetchRow("SELECT * FROM clients WHERE id = ?", $this->_getParam('cid'));
            $this->view->assign('data', $data);
            $this->_helper->viewRenderer('edit_client');
        } else {
            $this->view->assign('sizes', $this->sizes);
            $data = $this->db->fetchRow("SELECT * FROM banners WHERE id = ?", $this->_getParam('bid'));
            $this->view->assign('data', $data);
            $this->_helper->viewRenderer('edit_banner');
        }
    }

    public static function adminMenu()
    {
        $view = Zend_Registry::get('view');
        $stack = array();
        $stack[] = Zend_Registry::get('Zend_Translate')->_('Banner Management');
        $stack[$view->url(array('module' => 'banner', 'controller' => 'admin', 'action' => 'clients'), null, true)] = Zend_Registry::get('Zend_Translate')->_('Manage Clients');
        return $stack;
    }
}
?>
