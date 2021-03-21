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

echo '<h3>Rückfragen</h3>
<p>Bei Fragen zum Testergebnis oder organisatorischen Anfragen bitte eine E-Mail schreiben an:</p>
<p><a href="mailto:testzentrum@drk-odenwaldkreis.de">testzentrum@drk-odenwaldkreis.de</a></p>
<p>&nbsp;</p>';

echo '<h3>Technischer Support für die Teams vor Ort</h3>
<p><a target="_blank" href="https://github.com/DRK-Odenwaldkreis/Testerfassung">Für die Dokumentation der Web-Anwendung hier klicken</a></p>
<p>&nbsp;</p>
<p>Bei technischen Fragen zum Zeiterfassungssystem bitte eine E-Mail schreiben an den Support:</p>
<p><a href="mailto:info@testzentrum-odenwald.">info@testzentrum-odenwald.</a></p>
<p>&nbsp;</p>';



// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];






?>
