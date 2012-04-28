<?php
/**
 * Admin AJAX Controller
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class Admin_AjaxController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        if (!Zend_Registry::get('acl')->isAllowed('admin')) {
            $this->getResponse()->clearHeaders()->setHttpResponseCode(403)->sendResponse();
            die('Forbidden');
        }
    }
    
    public function init()
    {
        // Turn off views and layouts
        $front = Zend_Controller_Front::getInstance();
        $front->setParam('noErrorHandler', true);
        $front->setParam('noViewRenderer', true);
        $this->_helper->layout->disableLayout();
    }
    
    /**
     * Rebuilds the Lucene index for the site
     */
    public function reindexAction()
    {
        $search = GCMS_SearchEngine::getInstance();
        $search->rebuildIndex();
        echo $this->view->json(array(true));
    }

    /**
     * Used by the navigation controller to set the weights of our menu items
     */
    public function sortAction()
    {
        $db = Zend_Registry::get('db');
		
        $list = explode(',', $_POST['nav']);
		// Check parent links first
		$old = $db->fetchAll("SELECT id,weight FROM menu WHERE parent IS NULL ORDER BY weight");
		$weight = 0;
		foreach ($list as $v) {
			if (substr($v,0,2) == 'nl') {
				if ($old[$weight] != substr($v,2))
					$db->update('menu', array('weight' => $weight), 'id = ' . substr($v,2));
				$weight++;
			}
		}
		// Now do content
		$old = $db->fetchAll("SELECT id,weight FROM menu WHERE parent = 2 ORDER BY weight");
		$weight = 0;
		foreach ($list as $v) {
			if (substr($v,0,2) == 'cl') {
				if ($old[$weight] != substr($v,2))
					$db->update('menu', array('weight' => $weight), 'id = ' . substr($v,2));
				$weight++;
			}
		}
    }

    public function topicsAction()
    {
        // Fetch topics
        if (isset($_POST['path'])) {
            if (!file_exists($_POST['path'] . '/config.php')) {
                echo Zend_Json::encode(array(array('forum_id' => '', 'forum_name' => '[phpBB3 not found]')));
                exit();
            }
            $phpbb = new Zend_PHPBB3($_POST['path']);
            echo $this->view->json($phpbb->get_forums());
        }
    }

    public function importDropdownAction()
    {
        $cfg = Zend_Registry::get('config');
        

        // sends a JSON header and data
        echo $this->view->json(array());
    }

}
?>
