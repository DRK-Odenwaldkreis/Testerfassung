<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

** ************** */

include_once 'preload.php';
if( isset($GLOBALS['G_sessionname']) ) { session_name ($GLOBALS['G_sessionname']); }
session_start();
$sec_level=1;
$current_site="download";

// Include functions
include_once 'tools.php';
include_once 'auth.php';

// role check
$bool_no_permission=false;
if( A_checkpermission(array(0,2,0,4,5)) ) {

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $file=($_GET['file']);
        if(isset($_GET['dir'])) { $dir_id=$_GET['dir']; } else {$dir_id=0;}
        switch($dir_id) {
            case "t":
                $dir="Testkarten";
                break;
            case "zip":
                $dir_extra=$_GET['t'];
                $dir="Zertifikate/$dir_extra";
                break;
            case "r":
                if( A_checkpermission(array(0,2,0,4,0)) ) {
                    $dir="Reports";
                } else {
                    $bool_no_permission=true;
                }
                break;
            default:
            if( A_checkpermission(array(0,2,0,4,0)) ) {
                $dir="Reports";
            } else {
                $bool_no_permission=true;
            }
                break; 
        }
        if(!$bool_no_permission) {
            if( file_exists("/home/webservice/$dir/$file") ) {
                //header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($file).'"');
                header('Pragma: no-cache');
                header('Expires: 0');
                //header('Content-Length: ' . filesize($file));
                readfile("/home/webservice/$dir/$file");
                exit;
            }
        }
    }
} else {
    $bool_no_permission=true;
}

if($bool_no_permission) {
    // Print html header
    echo $GLOBALS['G_html_header'];
  
    // Print html menu
    echo $GLOBALS['G_html_menu'];
    echo $GLOBALS['G_html_menu2'];
  
    // Print html content part A
    echo $GLOBALS['G_html_main_right_a'];
    echo '<h1>KEINE BERECHTIGUNG</h1>';
  }
?>