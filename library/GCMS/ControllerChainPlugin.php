<?php
/**
 * Plugin that provides various utilities to GCMS's controller chain
 *
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class GCMS_ControllerChainPlugin extends Zend_Controller_Plugin_Abstract
{
    // does an install sanity check
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $cfg = Zend_Registry::get('config');
        // If no site username or password is set we need to redirect to install
        if (empty($cfg['rootuser']) || empty($cfg['rootpassword'])) {
            $request->setModuleName('install');
            $request->setControllerName('index');
            $request->setActionName('index');
        }
        // Check for correct DB schema
        if ($cfg['schema'] != 1) {
            // Upgrade DB
        }
        // These are some traditional request params
        if ($request->getParam('module') == 'default' && !empty($_GET['module']))
            $request->setModuleName($_GET['module']);
        elseif ($request->getParam('module') == 'default' && !empty($_GET['mod']))
            $request->setModuleName($_GET['mod']);
        if ($request->getParam('controller') == 'index' && !empty($_GET['controller']))
            $request->setControllerName($_GET['controller']);
        if ($request->getParam('action') == 'index' && !empty($_GET['action']))
            $request->setActionName($_GET['action']);
    }
    
    // provides the request params to Smarty templates
    public function preDispatch(Zend_Controller_Request_Abstract $request) 
    {
        Zend_Registry::get('view')->append('gcms', array('param' => $request->getParams()), true); 
    }
}
?>
