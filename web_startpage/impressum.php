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



echo '<h3 class="imprint">Impressum und Kontakt</h3>
<a href="https://drk-odenwaldkreis.de/impressum/">Direkt beim DRK Kreisverband Odenwaldkreis e. V.</a>';

echo '<h3 class="imprint">Rückfragen</h3>
<p>Bei Fragen zum Testergebnis oder organisatorischen Anfragen bitte eine E-Mail schreiben an:</p>
<p><a href="mailto:testzentrum@drk-odenwaldkreis.de">testzentrum@drk-odenwaldkreis.de</a></p>
<p>&nbsp;</p>';

echo '<h3 class="imprint">Technischer Support für die Teams vor Ort und Behörden</h3>
<p><a target="_blank" href="https://github.com/DRK-Odenwaldkreis/Testerfassung">Für die Dokumentation der Web-Anwendung hier klicken</a></p>
<p>&nbsp;</p>
<p>Bei technischen Fragen zum Erfassungssystem bitte eine E-Mail schreiben an den Support:</p>
<p><a href="mailto:info@testzentrum-odenwald.de">info@testzentrum-odenwald.de</a></p>
<p>&nbsp;</p>

<div id="datenschutz" class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
<h3 class="imprint">Datenschutzerklärung</h3>
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
<h5><b>Bei Nutzung des Testzentrums</b></h5>
<p>Mit der Anmeldung zu einem Corona-Schnelltest erfassen wir personenbezogene Daten von Ihnen. Wir erheben Ihren Namen, Anschrift, Geburtsdatum, Telefonnummer, Emailadresse und Testergebnis, um im Falle eines positiven Testergebnis das zuständige Gesundheitsamt nach §7,8 IfSG zu informieren. Im Falle eines negativen Testergebnis werden Ihre Daten nach einer Woche gelöscht. Im Fall eines positiven Testergebnis werden die Daten an das zuständige Gesundheitsamt weitergeleitet und von uns nach 3 Monaten gelöscht.</p>
<p>Bitte beachten Sie, dass wir Ihnen Ihr Testergebnis per Email mitteilen. Im E-Mail-Verkehr sind Ihre Daten ohne weitere Vorkehrungen nicht sicher und können unter Umständen von Dritten erfasst werden.</p>

<h5><b>Bei Nutzung des Impfzentrums</b></h5>
<p>Mit der Anmeldung zu einer Impfung erfassen wir personenbezogene Daten von Ihnen. Wir erheben Ihren Namen, Telefonnummer, Emailadresse und Impfstoff. Ihre Daten werden nach einer Woche nach dem gewählten Impftermin gelöscht.</p>
<p>&nbsp;</p>

<h4>Auskunft, Löschung, Sperrung</h4>
<p>Sie erhalten jederzeit unentgeltlich Auskunft über die von uns gespeicherten personenbezogenen Daten zu Ihrer Person sowie zum Zweck von Datenerhebung sowie Datenverarbeitung. Außerdem haben Sie das Recht, die Berichtigung, die Sperrung oder Löschung Ihrer Daten zu verlangen. Ausgenommen davon sind Daten, die aufgrund gesetzlicher Vorschriften aufbewahrt oder zur ordnungsgemäßen Geschäftsabwicklung benötigt werden.Für alle Fragen und Anliegen zur Berichtigung, Sperrung oder Löschung von personenbezogenen Daten wenden Sie sich bitte an unseren Datenschutzbeauftragten.</p>


<div id="datenschutz_cwa" class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
<h3 class="imprint">Datenschutzhinweis bei Verwendung der Corona-Warn-App (CWA)</h3>

<p>
„Hinweise zum Datenschutz: Sie* möchten die Corona Warn App („App“) des Robert Koch Instituts („RKI“) zum Abruf Ihres Testergebnisses eines Antigentests verwenden. Um Ihr Testergebnis über die App abrufen zu können ist es notwendig, dass Ihr Testergebnis von der Teststelle an das Serversystem des RKI übermittelt wird.
</p><p>
Verkürzt dargestellt erfolgt dies, indem die Teststelle Ihr Testergebnis, verknüpft mit einem maschinenlesbaren Code, auf einem hierfür bestimmten Server des RKI ablegt. Der Code ist Ihr Pseudonym, weitere Angaben zu Ihrer Person sind für die Anzeige des Testergebnisses in der App nicht erforderlich. Sie können die Anzeige des Testergebnisses jedoch für sich durch Angabe Ihres Namens, Vornamens und Geburtsdatums personalisieren lassen.
</p><p>
Der Code wird aus dem vorgesehenen Zeitpunkt des Tests und einer Zufallszahl gebildet. Die Bildung des Codes erfolgt, indem die vorgenannten Daten so miteinander verrechnet werden, dass ein Zurückrechnen der Daten aus dem Code nicht mehr möglich ist.
</p><p>
Sie erhalten eine Kopie des Codes in der Darstellung eines QR Codes, der durch die Kamerafunktion Ihres Smartphones in die App eingelesen werden kann. Alternativ können Sie den pseudonymen Code auch als Internetverweis erhalten („App Link“), der von der App geöffnet und verarbeitet werden kann. Nur hierdurch ist eine Verknüpfung des Testergebnisses mit Ihrer App möglich. Mit Ihrer Einwilligung können Sie dann Ihr Testergebnis mit Hilfe der App abrufen. Ihr Testergebnis wird automatisch nach 21 Tagen auf dem Server gelöscht.
</p><p>
Wenn Sie mit der Übermittlung Ihres pseudonymen Testergebnisses mittels des Codes an die App Infrastruktur zum Zweck des Testabrufs einverstanden sind, bestätigen Sie dies bitte gegenüber den Mitarbeitern der Teststelle. Sie können Ihre Einwilligung jederzeit mit Wirkung für die Zukunft widerrufen. Bitte beachten Sie jedoch, dass aufgrund der vorhandenen Pseudonymisierung eine Zuordnung zu Ihrer Person nicht erfolgen kann und daher eine Löschung Ihrer Daten erst mit Ablauf der 21 tägigen Speicherfrist automatisiert erfolgt.
</p><p>
Einzelheiten hierzu finden Sie zudem in den »Datenschutzhinweisen« der Corona Warn App des RKI.“
</p><p>
*Wenn Sie jünger als 16 Jahre alt sind, besprechen Sie die Nutzung der App bitte mit Ihren Eltern oder Ihrer sorgeberechtigten Person.
</p>

<div class="FAIRsepdown"></div>

';



// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];






?>
