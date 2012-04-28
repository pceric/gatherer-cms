<?php
/**
 * This helper shows the most recent posts from the admin
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class My_View_Helper_MyPosts extends Zend_View_Helper_Abstract
{
    /**
     * Displays author's most recent posts.
     *
     * @param array $args Arguments to pass to the module in the form of an assoc array.
     */
    public function myPosts($args = NULL) {
        if (!isset($args['count']) || !is_numeric($args['count']))
            $args['count'] = 5;
        $data = Zend_Registry::get('db')->fetchAll("SELECT id,title FROM news WHERE published = 1 ORDER BY pubdate DESC LIMIT " . (int)$args['count']);
        $this->view->assign('data', $data);
        return $this->view->getEngine()->fetch('myposts.tpl');
    }
}
?>
