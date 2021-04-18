<!doctype html>

<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021


error site

** ************** */

include_once 'preload.php';

$errorcode=isset($_GET['e']) ? $_GET['e'] : 0 ;

switch ($errorcode) {
	case 'err10':
		// Pre registration shutdown
		echo "<h1>Error / Fehler (10)</h1>";
		echo '<p><b>Derzeit ist keine Voranmeldung möglich. Bitte zu einem späteren Zeitpunkt nochmal versuchen. Wir bedauern etwaige Unannehmlichkeiten.</b></p>';
	break;
	case 'err80':
		// nosqlconnection
		echo "<h1>Error / Fehler (80)</h1>";
		echo '<p><b>Keine Verbindung zum Server möglich. Bitte zu einem späteren Zeitpunkt nochmal versuchen.</b></p>';
	break;
}

?>