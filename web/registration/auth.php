<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021


authentication frame

** ************** */



$FLAG_http='https';

$hostname = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['PHP_SELF']);

include_once('server_settings.php');
include_once('tools.php');
 
$linked_URL=$_SERVER['REQUEST_URI'];

if( $sec_level>0 ) {
	// Case: shutdown active -> go to error site
	$Db=S_open_db();
	if(isset($Db)) {
		$FLAG_Pre_registration=S_get_entry($Db,'SELECT value FROM website_settings WHERE name="FLAG_Pre_registration";');
		if ($FLAG_Pre_registration!=1 ) {
			header('Location: '.$FLAG_http.'://'.$hostname.($path == '/' ? '' : $path).'/error.php?e=err10');
		exit;
		}
	S_close_db($Db);
	}
}
$html_usermenubar='';


?>