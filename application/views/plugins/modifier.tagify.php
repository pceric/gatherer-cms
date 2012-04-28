<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     modifier.tagify.php
 * Type:     modifier
 * Name:     tagify
 * Purpose:  Turns a comma seperated tag list into clickable tags
 * -------------------------------------------------------------
 */
function smarty_modifier_tagify($list) {
    $tag_links = '';
    if (empty($list))
        $list = Zend_Registry::get('Zend_Translate')->_('[Uncategorized]');
    $tag_array = explode(',', $list);
    foreach($tag_array as $v)
        $tag_links .= "<a href=\"" . Zend_Registry::get('view')->url(array('module' => 'tags', 'term' => trim($v)),NULL,true) . "\" class=\"tags\">" . trim($v) . "</a>, ";
    return rtrim($tag_links,', ');
}
?>
