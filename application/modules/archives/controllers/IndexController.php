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
        $zd = new Zend_Date();
        $db = Zend_Registry::get('db');
        $year = (int)$this->_getParam('year', $zd->get(Zend_Date::YEAR));
        $month = null;

        // Extract years
        if ($db instanceof Zend_Db_Adapter_Pdo_Mysql || $db instanceof Zend_Db_Adapter_Mysqli) {
            $years = $db->fetchCol("(SELECT DISTINCT YEAR(pubdate) as year FROM reader) UNION (SELECT DISTINCT YEAR(pubdate) as year FROM news) ORDER BY year DESC");
        } else {
            $years = $db->fetchCol("(SELECT DISTINCT EXTRACT(YEAR FROM pubdate) as year FROM reader) UNION (SELECT DISTINCT EXTRACT(YEAR FROM pubdate) as year FROM news) ORDER BY year DESC");
        }
        
        if (!empty($year)) {
            if ($db instanceof Zend_Db_Adapter_Pdo_Mysql || $db instanceof Zend_Db_Adapter_Mysqli) {
                if (!empty($month)) {
                    $andmonth = ' AND MONTH(pubdate) = ' . $db->quote($month);
                } else {
                    $andmonth = '';
                }
                $rs = $db->query("(SELECT 'reader' as type, id, title, pubdate FROM reader WHERE YEAR(pubdate) = " . $db->quote($year) . $andmonth . ") UNION (SELECT 'news' as type, id, title, pubdate FROM news WHERE YEAR(pubdate) = " . $db->quote($year) . $andmonth . ") ORDER BY pubdate DESC");
            } else {
                if (!empty($month)) {
                    $andmonth = ' AND EXTRACT(MONTH FROM pubdate) = ' . $db->quote($month);
                } else {
                    $andmonth = '';
                }
                $rs = $db->query("(SELECT 'reader' as type, id, title, pubdate FROM reader WHERE EXTRACT(YEAR FROM pubdate) = " . $db->quote($year) . $andmonth . ") UNION (SELECT 'news' as type, id, title, pubdate FROM news WHERE EXTRACT(YEAR FROM pubdate) = " . $db->quote($year) . $andmonth . ") ORDER BY pubdate DESC");
            }
        } else {
            $rs = $db->query("(SELECT 'reader' as type, id, title, pubdate FROM reader) UNION (SELECT 'news' as type, id, title, pubdate FROM news) ORDER BY pubdate DESC");
        }
       
        $archiveArray = array();
        while ($row = $rs->fetch()) {
            $date = $zd->set($row['pubdate'], Zend_Date::ISO_8601);
            $archiveArray[] = array('year' => $date->get(Zend_Date::YEAR), 'month' => $date->get(Zend_Date::MONTH), 'month_name' => $date->get(Zend_Date::MONTH_NAME), 'type' => $row['type'], 'id' => $row['id'], 'title' => $row['title']);
        }

        $this->view->assign('data', $archiveArray);
        $this->view->assign('year', $year);
        $this->view->assign('years', $years);
    }
}
?>
