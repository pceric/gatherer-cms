<?php
/*
 * GCMS Filter
 * -------------------------------------------------------------
 * File:     feedfilter.rss.slashdot.org.php
 * Type:     feedfilter
 * Name:     slashdot
 * Author:   Eric Hokanson
 * Purpose:  Hack to clean Slashdot code
 * -------------------------------------------------------------
 */
    function feedfilter_rss_slashdot_org($raw)
    {
        $raw = preg_replace('@^<p>(.*)</p>@sU', '', $raw);
        return preg_replace('@<p>(.*)Read more of this story</a> at Slashdot(.*)>$@s', '', $raw);
    }
?> 
