<?php
/**
 * This helper displays banners from our banners module
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class My_View_Helper_Banner extends Zend_View_Helper_Abstract
{
    public function banner($args=null) {
        $db = Zend_Registry::get('db');
        
        if (!isset($args['width']))
            $args['width'] = 468;
        if (!isset($args['height']))
            $args['height'] = 60;
        
        /*
        if ($db instanceof Zend_Db_Adapter_Pdo_Mysql || $db instanceof Zend_Db_Adapter_Mysqli) {
            $rs = $db->query("(SELECT UNIX_TIMESTAMP(pubdate) as timestamp FROM reader WHERE pubdate > NOW() - INTERVAL 6 MONTH) UNION (SELECT UNIX_TIMESTAMP(pubdate) as timestamp FROM news WHERE pubdate > NOW() - INTERVAL 6 MONTH) ORDER BY timestamp DESC");
        } else {
            $rs = $db->query("(SELECT extract(epoch FROM pubdate) as timestamp FROM reader WHERE pubdate > NOW() - INTERVAL '6 months') UNION (SELECT extract(epoch FROM pubdate) as timestamp FROM news WHERE pubdate > NOW() - INTERVAL '6 months') ORDER BY timestamp DESC");
        }
        */
        
        // Only select banners that match the correct size
        $banner = $db->fetchAll("SELECT * FROM banners WHERE size = '$args[width]x$args[height]' AND active = 1");
        if (count($banner) < 1)
            return;
        $rand = rand(0, count($banner) - 1);
        $db->update('banners', array('impressions' => new Zend_Db_Expr('(impressions+1)')), 'id = '.$banner[$rand]['id']);
        if (!empty($banner[$rand]['code']))
            return $banner[$rand]['code']; 
        else
            return "<a href=" . Zend_Registry::get('view')->url(array('module' => 'banner', 'controller' => 'redirect', 'id' => $banner[$rand]['id'], 'url' => urlencode($banner[$rand]['url'])),null,true) . " target=\"_blank\"><img src=\"" . $banner[$rand]['image'] . "\" width=\"" . $args['width'] . "\" height=\"" . $args['height'] . "\" alt=\"Ad\" /></a>\n";
    }
}
?>
