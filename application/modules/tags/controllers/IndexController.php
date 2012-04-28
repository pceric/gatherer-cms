<?php
/**
 * This controller manages the searching of tags
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
require_once "PorterStemmer.php";

class Tags_IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $db = Zend_Registry::get('db');
        $term = $this->_getParam('term');

        if (empty($term) || $term == Zend_Registry::get('Zend_Translate')->_('[Uncategorized]'))
            $queryTerm = "IS NULL";
        else {
            if ($db instanceof Zend_Db_Adapter_Pdo_Pgsql)
                $queryTerm = "ILIKE ";
            else
                $queryTerm = "LIKE ";
            $queryTerm .= $db->quote('%' . PorterStemmer::Stem($term) . '%');
        }
        $data = $db->fetchAll("(SELECT 'news' as type, id, title, pubdate FROM news WHERE published = 1 AND tags $queryTerm) UNION
                               (SELECT 'reader' as type, id, title, pubdate FROM reader WHERE tags $queryTerm) UNION
                               (SELECT 'article' as type, id, title, pubdate from articles WHERE published = 1 AND tags $queryTerm) ORDER BY pubdate DESC");
        $this->view->assign('term', $this->view->escape($term));
        $this->view->assign('data', $data); 
    }
}
?>
