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
        $cfg = Zend_Registry::get('config');
        $this->view->headTitle('Install');
        $this->view->headMeta()->appendHttpEquiv('pragma', 'no-cache')->appendHttpEquiv('Cache-Control', 'no-cache');
        if (isset($_POST['submit'])) {
            if (!empty($_POST['username']) && !empty($_POST['password']) && $_POST['password'] == $_POST['passcheck']) {
                $cfg->write('rootuser', $_POST['username']);
                $cfg->write('rootpassword', sha1(md5($_POST['username']) . $_POST['password']));
                $_POST['submit'] = null;
                $this->_forward('login', 'index', 'admin');
            } else {
                $this->view->assign('error_msg', 'Error: Passwords did not match');
            }
        }
    }
}
