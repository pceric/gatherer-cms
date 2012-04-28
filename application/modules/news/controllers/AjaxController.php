<?php
/**
 * This controller manages AJAX functions for news posts
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class News_AjaxController extends Zend_Controller_Action
{
    public function init()
    {
        // Turn off views and layouts
        $front = Zend_Controller_Front::getInstance();
        $front->setParam('noErrorHandler', true);
        $front->setParam('noViewRenderer', true);
        $this->_helper->layout->disableLayout();
    }
    
    public function annotateAction()
    {
        if (!Zend_Registry::get('acl')->isAllowed('admin'))
            return;
        
        $db = Zend_Registry::get('db');
        
        // Save reader annotation.  TODO: edit whole article
        if (!empty($_POST['table']) && !empty($_POST['id']) && !empty($_POST['data'])) {
            $db->update($_POST['table'], array('annotation' => $_POST['data']), 'id = ' . $db->quote(ltrim($_POST['id'],'i')));
        }
   
    }
}
?>
