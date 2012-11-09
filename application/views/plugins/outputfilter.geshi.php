<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.geshi.php
 * Type:     outputfilter
 * Name:     geshi
 * Author:   Eric Hokanson
 * Purpose:  Runs <code></code> segments through the Geshi parser
 * -------------------------------------------------------------
 */
    require_once 'geshi/geshi.php';
    
    function replacer($matches) {
        $language = $matches[1];
        $geshi = new GeSHi(preg_replace('@<br(.*)/?>(\n|\r\n)@iUu', "\n", $matches[2]), $language);
        $geshi->set_header_type(GESHI_HEADER_PRE_VALID);
        $geshi->enable_line_numbers(GESHI_NO_LINE_NUMBERS);
        return $geshi->parse_code();
    }
    
    function smarty_outputfilter_geshi($compiled, &$smarty)
    {
        // follows HTML5 syntax
        $source = preg_replace_callback('@<code class="language\-(.*)">(.*)</code>@isUu', 'replacer', $compiled);
        return $source;
    }
?> 
