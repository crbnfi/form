<?php

if(!defined('IN_APPLICATION')) die('<h1>Access denied</h1>');

function json_output($output)
{
	return json_encode($output);
}

function clean($in = false) {
	if(empty($in) || !isset($in)) return 0;
	return trim(strip_tags($in));
}

function cleanSQL($in = false) {
	$in = clean($in);
	return mysql_real_escape_string(htmlentities($in));
}

// Validators
$validators = array(
	'email' => '/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/',
	'alnum' => '/^[[:alnum:]]$/',
	'digit' => '/^[[:digit:]]$/',
	'number' => '/^[0-9\.]$/'
);