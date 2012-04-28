<?php
/**
 * This controller manages AJAX functions for our search engine
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class Search_AjaxController extends Zend_Controller_Action
{
    public function init()
    {
        // Turn off views and layouts
        $front = Zend_Controller_Front::getInstance();
        $front->setParam('noErrorHandler', true);
        $front->setParam('noViewRenderer', true);
        $this->_helper->layout->disableLayout();
    }

    public function searchAction()
    {
        $json_array = array();
        $engine = GCMS_SearchEngine::getInstance();
        if ($this->_getParam('query') != null) {
            try {
                $results = $engine->search(rawurldecode($this->_getParam('query')));
                foreach ($results as $v) {
                    $json_array[] = array('id' => $v->id, 'title' => $v->title, 'url' => $v->url, 'timestamp' => $v->timestamp, 'score' => $v->score);   
                }
            } catch (Exception $e) {
                
            }
        }
        echo $this->view->json(array_slice($json_array, 0, 20));
    }
}
?>
