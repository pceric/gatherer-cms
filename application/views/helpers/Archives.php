<?php
/**
 * This helper displays archives from the last 6 months
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class My_View_Helper_Archives extends Zend_View_Helper_Abstract
{
    public function archives() {
        $archiveArray = array();
        
        if (Zend_Registry::get('db') instanceof Zend_Db_Adapter_Pdo_Mysql ||
            Zend_Registry::get('db') instanceof Zend_Db_Adapter_Mysqli) {
            $rs = Zend_Registry::get('db')->query("(SELECT UNIX_TIMESTAMP(pubdate) as timestamp FROM reader WHERE pubdate > NOW() - INTERVAL 6 MONTH) UNION (SELECT UNIX_TIMESTAMP(pubdate) as timestamp FROM news WHERE pubdate > NOW() - INTERVAL 6 MONTH) ORDER BY timestamp DESC");
        } else {
            $rs = Zend_Registry::get('db')->query("(SELECT extract(epoch FROM pubdate) as timestamp FROM reader WHERE pubdate > NOW() - INTERVAL '6 months') UNION (SELECT extract(epoch FROM pubdate) as timestamp FROM news WHERE pubdate > NOW() - INTERVAL '6 months') ORDER BY timestamp DESC");
        }

        while ($row = $rs->fetch()) {
            $date = new Zend_Date($row['timestamp'], Zend_Date::TIMESTAMP);
            @$archiveArray[$date->get(Zend_Date::YEAR)][$date->get(Zend_Date::MONTH)] += 1;
        }

        $this->view->assign('archive', $archiveArray);

        return $this->view->getEngine()->fetch('archives.tpl');
    }
}
?>
