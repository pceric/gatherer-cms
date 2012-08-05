<?php
/**
 * This controller redirects users who click on a banner
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class Banner_RedirectController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $db = Zend_Registry::get('db');
        $url = $db->fetchOne('SELECT url FROM banners WHERE id = ?', $this->_getParam('id'));
        if (!empty($url)) {
            $db->update('banners', array('clicks' => new Zend_Db_Expr('(clicks+1)')), 'id = ' . $db->quote($this->_getParam('id')));
            $this->_redirect($url);
        }
        $this->_redirect('index');
    }
}    
?>
