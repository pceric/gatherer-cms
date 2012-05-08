<?php
/**
 * This controller manages the install system
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class Install_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $cfg = Zend_Registry::get('config');
        if (!empty($cfg['rootuser']) && !empty($cfg['rootpassword']))
            throw new Exception("Install already run.");
    }

    public function indexAction()
    {
        $authNamespace = new Zend_Session_Namespace('gcmsauth');
        $cfg = Zend_Registry::get('config');
        $this->view->headTitle('Install');
        $this->view->headMeta()->appendHttpEquiv('pragma', 'no-cache')->appendHttpEquiv('Cache-Control', 'no-cache');
        if (isset($_POST['submit'])) {
            if (!empty($_POST['username']) && !empty($_POST['password']) && $_POST['password'] == $_POST['passcheck']) {
                $cfg->write('rootuser', $_POST['username']);
                $cfg->write('rootpassword', sha1(md5($_POST['username']) . $_POST['password']));
                GCMS_SearchEngine::getInstance()->rebuildIndex();
                // Generate a CSRF token
                if (!isset($authNamespace->csrf))
                    $authNamespace->csrf = md5(mt_rand());
                // Set & save the ACL
                Zend_Registry::get('acl')->allow('admin');
                $authNamespace->acl = serialize(Zend_Registry::get('acl'));
                $_POST['submit'] = null;
                $this->_forward('index', 'index', 'admin');
            } else {
                $this->view->assign('error_msg', 'Error: Passwords did not match');
            }
        }
    }
}
