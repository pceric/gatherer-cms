<?php
/**
 * This controller manages the Admin interface
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class Admin_IndexController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        // Check to see if we have logged in
        if (!Zend_Registry::get('acl')->isAllowed('admin'))
            $this->_forward('login');
        
        // cancel catch-all
        if ($this->_getParam('cancel') != NULL) {
            $this->_redirect('admin');
        }
    }
    
    public function chgpassAction()
    {
        $cfg = Zend_Registry::get('config');
        $this->view->headTitle('Change Password');
        if ($this->_getParam('save')) {
            if (sha1(md5($cfg['rootuser']) . $this->_getParam('oldpass')) == $cfg['rootpassword']) {
                if ($this->_getParam('newpass') == $this->_getParam('conpass') && $this->_getParam('newpass') != '') {
                    $cfg->write('rootpassword', sha1(md5($cfg['rootuser']) . $this->_getParam('newpass')));
                    $this->_setParam('alert', 'success');
                    $this->_setParam('msg', 'pass');
                    $this->_forward('index');
                } else {
                    $this->view->assign('error', 'New passwords did not match or were empty.');
                }
            } else {
                $this->view->assign('error', 'Old password was incorrect.');
            }
        }
    }

    public function configAction()
    {
        // Get active config
        $cfg = Zend_Registry::get('config');
        $t = Zend_Registry::get('Zend_Translate');
        
        if ($this->_getParam('configsubmit') != NULL) {
            $cfg->write('sitename', $_POST['sitename']); 
            $cfg->write('siteauthor', $_POST['siteauthor']);
            $cfg->write('sitecontact', $_POST['sitecontact']);
            $cfg->write('siteURL', urldecode($_POST['siteURL']));
            $cfg->write('sitedesc', $_POST['sitedesc']);
            $cfg->write('sitekeywords', $_POST['sitekeywords']);
            $cfg->write('siteslogan', $_POST['siteslogan']);
            $cfg->write('sitetheme', $_POST['sitetheme']);
            $cfg->write('editor', $_POST['editor']);
            $cfg->write('googlefeed', urldecode($_POST['googlefeed']));
            $cfg->write('imagedir', $_POST['imagedir']);
            $cfg->write('filedir', $_POST['filedir']);
            $cfg->write('meta1name', $_POST['meta1name']);
            $cfg->write('meta1value', $_POST['meta1value']);
            $cfg->write('meta2name', $_POST['meta2name']);
            $cfg->write('meta2value', $_POST['meta2value']);
            $cfg->write('meta3name', $_POST['meta3name']);
            $cfg->write('meta3value', $_POST['meta3value']);
            $cfg->write('republickey', $_POST['publickey']);
            $cfg->write('reprivatekey', $_POST['privatekey']);
            // In case the theme changes we need to wipe Smarty clean
            $this->view->getEngine()->clearCompiledTemplate();
            $this->view->getEngine()->clearAllCache();
            $this->_redirect('admin/index/index/alert/success/msg/config');
        }

        $this->view->headTitle('Admin Config');
        $this->view->assign('eOptions', array('[None]',
                                        'CKEditor',
                                        'TinyMCE'));
        $this->view->assign('importOptions', array('disabled' => $t->_('Disabled'),
                                                   'googleplus' => $t->_('Google+'),
                                                   'feed' => $t->_('RSS/Atom Feed')));
        $this->view->assign('themelist', scandir('./themes'));
    }
    
    public function indexAction()
    {
        $this->view->headTitle('Admin');
        $menu_stack = array();
        foreach (scandir(APPLICATION_PATH . '/modules') as $v) {
            if (is_dir(APPLICATION_PATH . '/modules/' . $v) && preg_match('/^([A-Z]|[a-z]|_)/',$v) && $v != 'admin') {
                if (is_readable(APPLICATION_PATH . '/modules/' . $v . '/controllers/AdminController.php')) {
                    include_once APPLICATION_PATH . '/modules/' . $v. '/controllers/AdminController.php';
                    // PHP BUG: call_user_func returns NULL and not FALSE as the manual states
                    if (($stack = @call_user_func(ucfirst($v).'_AdminController::adminMenu')) !== NULL) {
                        $menu_stack[] = $stack;
                    }
                }
            }
        }
        $this->view->assign('menu_stack_array', $menu_stack);
        $this->view->assign('alert', $this->_getParam('alert', 'info'));
        $this->view->assign('msg', $this->_getParam('msg'));
    }

    public function loginAction()
    {
        $authNamespace = new Zend_Session_Namespace('gcmsauth');
        $cfg = Zend_Registry::get('config');
        $this->view->headTitle('Admin Login');
        if (isset($_POST['submit'])) {
            if ($cfg['rootuser'] == $_POST['login'] && $cfg['rootpassword'] == sha1(md5($_POST['login']) . $_POST['pass'])) {
                // Generate a CSRF token
                if (!isset($authNamespace->csrf))
                    $authNamespace->csrf = md5(mt_rand());
                // Set & save the ACL
                Zend_Registry::get('acl')->allow('admin');
                $authNamespace->acl = serialize(Zend_Registry::get('acl'));
                Zend_Registry::get('log')->log('Successful admin login from ' . $_SERVER['REMOTE_ADDR'] , Zend_Log::NOTICE);
                $this->_forward('index');
            } else {
                $this->view->assign('error_msg', 'Wrong username or password.');
                Zend_Registry::get('log')->log('Unsuccessful admin login from ' . $_SERVER['REMOTE_ADDR'] , Zend_Log::WARN);
            }
        }
    }

    public function logoutAction()
    {
        $authNamespace = new Zend_Session_Namespace('gcmsauth');
        Zend_Registry::get('acl')->deny('admin');
        Zend_Session::namespaceUnset('gcmsauth');
        Zend_Session::writeClose();
        $this->view->assign('gcms.isAdmin', false);
        //$this->_redirect('admin');
    }
}

