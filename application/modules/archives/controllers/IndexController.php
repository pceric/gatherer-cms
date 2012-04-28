<?php
/**
 * This controller manages archives of news and articles
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class Archives_IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->headTitle('Archives');
    }
    
    public function indexAction()
    {
        $db = Zend_Registry::get('db');
        $year = $this->_getParam('year');
        $month = $this->_getParam('month');

        if (!empty($year) && !empty($month)) {
            if ($db instanceof Zend_Db_Adapter_Pdo_Mysql || $db instanceof Zend_Db_Adapter_Mysqli) {
                $rs = $db->query("(SELECT 'reader' as type, id, title, pubdate FROM reader WHERE YEAR(pubdate) = " . $db->quote($year) . " AND MONTH(pubdate) = " . $db->quote($month) . ") UNION (SELECT 'news' as type, id, title, pubdate FROM news WHERE YEAR(pubdate) = " . $db->quote($year) . " AND MONTH(pubdate) = " . $db->quote($month) . ") ORDER BY pubdate DESC");
            } else {
                $rs = $db->query("(SELECT 'reader' as type, id, title, pubdate FROM reader WHERE EXTRACT(YEAR FROM pubdate) = " . $db->quote($year) . " AND EXTRACT(MONTH FROM pubdate) = " . $db->quote($month) . ") UNION (SELECT 'news' as type, id, title, pubdate FROM news WHERE EXTRACT(YEAR FROM pubdate) = " . $db->quote($year) . " AND EXTRACT(MONTH FROM pubdate) = " . $db->quote($month) . ") ORDER BY pubdate DESC");
            }
        } else {
            $rs = $db->query("(SELECT 'reader' as type, id, title, pubdate FROM reader) UNION (SELECT 'news' as type, id, title, pubdate FROM news) ORDER BY pubdate DESC");
        }
        while ($row = $rs->fetch()) {
            $date = new Zend_Date($row['pubdate'], Zend_Date::ISO_8601);
            $archiveArray[] = array('year' => $date->get(Zend_Date::YEAR), 'month' => $date->get(Zend_Date::MONTH_NAME), 'type' => $row['type'], 'id' => $row['id'], 'title' => $row['title']);
        }
	    $this->view->assign('data', $archiveArray);
    }
}
?>
