<?php
/*
 * GCMS Filter
 * -------------------------------------------------------------
 * File:     feedfilter.lifehacker.com.php
 * Type:     feedfilter
 * Name:     lifehacker
 * Author:   Eric Hokanson
 * Purpose:  Hack to clean lifehacker code
 * -------------------------------------------------------------
 */
	function feedfilter_lifehacker_com($raw)
	{
		return preg_replace('@(\t){4}<a(.*)$@s', '', $raw);
	}
?> 
