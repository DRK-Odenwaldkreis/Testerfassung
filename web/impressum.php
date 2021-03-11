<!doctype html>

<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

imprint site

** ************** */



// Include functions
include_once 'menu.php';


// Print html header
echo $GLOBALS['G_html_header'];

// Print html menu
echo $GLOBALS['G_html_menu'];
echo $GLOBALS['G_html_menu2'];

// Print html content part A
echo $GLOBALS['G_html_main_right_a'];



echo '<h3>Impressum und Kontakt</h3>
<a href="https://drk-odenwaldkreis.de/impressum/">Direkt beim DRK Kreisverband Odenwaldkreis e. V.</a>';




// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];






?>