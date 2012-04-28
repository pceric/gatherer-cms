<?php
/**
 * This controller manages the Admin interface
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class Admin_NavigationController extends Zend_Controller_Action
{
    private $db;
     
    // Compacts the weights in our menu after deleting items
    private function menuCompact($parent='IS NULL') {
        if ($parent != 'IS NULL')
            $parent = '= ' . $this->db->quote($parent);
        $menu = $this->db->fetchAll("SELECT * FROM menu WHERE parent $parent ORDER BY weight");
        $count = 0;
        foreach ($menu as $v) {
            if ($v['weight'] != $count)
                $this->db->update('menu', array('weight' => $count), "parent $parent AND id = " . $v['id']);
            $count++;
        }
    }
    
    public function preDispatch()
    {
        // Check to see if we have logged in
        if (!Zend_Registry::get('acl')->isAllowed('admin'))
            $this->_forward('login');
        
        // cancel catch-all
        if ($this->_getParam('cancel') != NULL) {
            $this->_redirect('admin/navigation');
        }
    }
    
    public function init()
    {
        $this->db = Zend_Registry::get('db');
        
        $this->view->headTitle('Navigation');
        
        if ($this->_getParam('save') != null) {
            if (strpos($_POST['link'], '://') === FALSE) {
                $larray = explode('/', ltrim($_POST['link'], '/'), 4);
                if (count($larray) < 4)
                    $larray[] = NULL;
                $larray = array_combine(array('module', 'controller', 'action', 'params'), $larray);
                // Check to see if we have params
                if ($larray['params'] == NULL) {
                    array_pop($larray);
                } else {
                    $parray = array();
                    foreach(array_chunk(explode('/', $larray['params']), 2) as $v) {
                        $parray[$v[0]] = $v[1];
                    }
                    $larray['params'] = $parray;
                }
                $_POST['link'] = serialize($larray);
            }
            if ($this->_getParam('id') != null) {
                $this->db->update('menu', array('name' => $_POST['name'], 'link' => $_POST['link']), 'id = ' . $this->db->quote($this->_getParam('id')));
            } else {
                $this->db->insert('menu', array('name' => $_POST['name'], 'link' => $_POST['link'], 'parent' => empty($_POST['parent']) ? new Zend_Db_Expr('NULL') : $_POST['parent'], 'weight' => 127));
            }
            $this->_forward('index');
        }
    }

    public function indexAction()
    {
        $this->view->headScript()->appendFile($this->view->baseUrl("/themes/default/js/nav.js"));

        $menu = $this->db->fetchAll("SELECT * FROM menu WHERE parent IS NULL ORDER BY weight,id");
        $cmenu = $this->db->fetchAll("SELECT * FROM menu WHERE parent = 2 ORDER BY weight,id");
        $this->view->assign('menu', $menu);
        $this->view->assign('cmenu', $cmenu);
    }

    public function addAction()
    {
        $this->view->headTitle('Add Link');

        $poptions = array("" => "[No Parent]");
        $menu = $this->db->fetchAll("SELECT * FROM menu WHERE parent IS NULL ORDER BY weight,id");
        foreach ($menu as $v) {
            $poptions[$v['id']] = $v['name'];
        }
        $this->view->assign('poptions', $poptions);
        $this->view->assign('item', array());
        $this->_helper->viewRenderer('edit');
    }
    
    public function deleteAction()
    {
        if ($this->_getParam('item') > 2) {
            $this->db->delete('menu', 'id = ' . $this->db->quote($this->_getParam('item')));
            $this->menuCompact();
        }
        $this->_forward('edit');
    }
    
    public function editAction()
    {
        $this->view->headTitle('Edit Link');
        
        $poptions = array("" => "[No Parent]");
        $menu = $this->db->fetchAll("SELECT * FROM menu WHERE parent IS NULL ORDER BY weight,id");
        foreach ($menu as $v) {
            if ($v['id'] != $this->_getParam('id'))
                $poptions[$v['id']] = $v['name'];
        }
        $this->view->assign('poptions', $poptions);
        
        $item = $this->db->fetchRow("SELECT * FROM menu WHERE id = ?", $this->_getParam('id'));
        $item['link'] = unserialize($item['link']);
        $params = '';
        if (isset($item['link']['params'])) {
            foreach(array_pop($item['link']) as $k => $v)
                $params .= '/' . $k . '/' . $v;
        }
        $item['link'] = implode('/', $item['link']) . $params;
        $this->view->assign('item', $item);
    }
}

