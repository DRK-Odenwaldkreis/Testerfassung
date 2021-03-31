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
<p>Bei technischen Fragen zum Erfassungssystem bitte eine E-Mail schreiben an den Support:</p>
<p><a href="mailto:info@testzentrum-odenwald.de">info@testzentrum-odenwald.de</a></p>
<p>&nbsp;</p>

<h3>Datenschutzerklärung</h3>
<h4>Die Kontaktdaten des Unternehmens:</h4>
<p>Name: Frank Sauer</p>
<p>Telefonnummer: 06062 6070</p>
<p>E-Mail Adresse: info@drk-odenwaldkreis.de</p>
<p>Unternehmensbezeichnung: DRK Kreisverband Odenwaldkreis e.V.</p>
<p>&nbsp;</p>

<h4>Die Kontaktdaten des Datenschutzbeauftragten:</h4>
<p>Name: Mathias Bojahr</p>
<p>Telefonnummer: 06062 6070</p>
<p>E-Mail Adresse: datenschutz@drk-odenwaldkreis.de</p>
<p>Unternehmensbezeichnung: extern benannter Datenschutzbeauftragter</p>
<p>&nbsp;</p>

<h4>Server Logfiles</h4>
<p>In den Server Logfiles werden Daten festgehalten, die bei Ihrem Zugriff auf unsere Webseite erfasst werden. Diese Informationen sind aus technischen Gründen für die Auslieferung und Darstellung unserer Inhalte unverzichtbar. Typische Einträge in Logfiles sind das Datum und die Zeit des Zugriffs, die Datenmenge und Name der Domainname der Webseite.</p>
<p>&nbsp;</p>

<h4>Personenbezogene Daten</h4>
<p>Mit der Anmeldung zu einem Corona-Schnelltest erfassen wir personenbezogene Daten von Ihnen. Wir erheben Ihren Namen, Anschrift, Geburtsdatum, Telefonnummer, Emailadresse und Testergebnis, um im Falle eines positiven Testergebnis das zuständige Gesundheitsamt nach §7,8 IfSG zu informieren. Im Falle eines negativen Testergebnis werden Ihre Daten nach einer Woche gelöscht. Im Fall eines positiven Testergebnis werden die Daten an das zuständige Gesundheitsamt weitergeleitet und von uns nach 3 Monaten gelöscht.</p>
<p>Bitte beachten Sie, dass wir Ihnen Ihr Testergebnis per Email mitteilen. Im E-Mail-Verkehr sind Ihre Daten ohne weitere Vorkehrungen nicht sicher und können unter Umständen von Dritten erfasst werden.</p>
<p>&nbsp;</p>

<h4>Auskunft, Löschung, Sperrung</h4>
<p>Sie erhalten jederzeit unentgeltlich Auskunft über die von uns gespeicherten personenbezogenen Daten zu Ihrer Person sowie zum Zweck von Datenerhebung sowie Datenverarbeitung. Außerdem haben Sie das Recht, die Berichtigung, die Sperrung oder Löschung Ihrer Daten zu verlangen. Ausgenommen davon sind Daten, die aufgrund gesetzlicher Vorschriften aufbewahrt oder zur ordnungsgemäßen Geschäftsabwicklung benötigt werden.Für alle Fragen und Anliegen zur Berichtigung, Sperrung oder Löschung von personenbezogenen Daten wenden Sie sich bitte an unseren Datenschutzbeauftragten.</p>








';



// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];






?>
