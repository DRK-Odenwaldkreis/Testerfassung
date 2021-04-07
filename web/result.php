<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

get result for customer

** ************** */

$current_site="result";

// Include functions
include_once 'zentral/tools.php';
//include_once 'zentral/auth.php';
include_once('zentral/server_settings.php');

// Include secondary files
include 'menu.php';

// Get Server and Path values
$hostname = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['PHP_SELF']);

$refererURL=$_SERVER['HTTP_REFERER'];
$refererPATH=parse_url($refererURL, PHP_URL_PATH);
$refererARG=parse_url($refererURL, PHP_URL_QUERY);



// Open database connection
$Db=S_open_db();

$errorhtml1 = '';

// Case: shutdown active -> show message
$FLAG_SHUTDOWN=S_get_entry($Db,'SELECT value FROM website_settings WHERE name="FLAG_SHUTDOWN";');

$ALLOWANCE_RESULT=0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if(isset($_POST['button'])) {
		// Check birth date to show result
		
		$token = $_POST['token'];
		$customer_key = $_POST['customer_key'];
		$gebdatum = $_POST['gebdatum'];

		// Daten werde überprüft
		if($gebdatum!='') {
			$stmt=mysqli_prepare($Db,"SELECT id, Geburtsdatum, Customer_lock FROM Vorgang WHERE Token=? AND Customer_key=? AND (Customer_lock is null OR Customer_lock<10);");
			mysqli_stmt_bind_param($stmt, "ss", $token, $customer_key);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $k_vorgang_id, $k_geb, $val_lock);
			mysqli_stmt_fetch($stmt);
			mysqli_stmt_close($stmt);
		} else {
			$k_vorgang_id=0;
		}

		if($k_vorgang_id>0) {
			if($gebdatum==$k_geb) {
				// Allowed to show result
				$ALLOWANCE_RESULT=$k_vorgang_id;
				S_set_data($Db,'UPDATE Vorgang SET Customer_lock=0 WHERE id=CAST('.$k_vorgang_id.' AS int)');
			} else {
				// wrong birth date - increase lock value
				S_set_data($Db,'UPDATE Vorgang SET Customer_lock=CAST('.($val_lock+1).' as INT) WHERE id=CAST('.$k_vorgang_id.' AS int)');
				$errorhtml1 =  H_build_boxinfo( 322, 'Daten nicht korrekt.', 'red' );
			}
			
		} else {
			$errorhtml1 =  H_build_boxinfo( 322, 'Daten nicht korrekt.', 'red' );
		}
		
	}

	
}
	
if($ALLOWANCE_RESULT>0) {
	$result_array=S_get_multientry($Db,'SELECT Teststation, Ergebniszeitpunkt, Vorname, Nachname, Geburtsdatum, Ergebnis FROM Vorgang WHERE id=CAST('.$ALLOWANCE_RESULT.' as int);');

	switch ($result_array[0][5]) {
		case "2":
		  // Test NEGATIV
		  $display_result=file_get_contents('result_html/Negative_Result.html');
		  break;
		case "1":
		  // Test POSITIV
		  $display_result=file_get_contents('result_html/Positive_Result.html');
		  break;
		case "9":
		  // Test FEHLERHAFT
		  $display_result=file_get_contents('result_html/Indistinct_Result.html');
		  break;
	  }
	  $display_result=str_replace('[[VORNAME]]', $result_array[0][2], $display_result);
	  $display_result=str_replace('[[NACHNAME]]', $result_array[0][3], $display_result);
	  $display_result=str_replace('[[GEBDATUM]]', date('d.m.Y',strtotime($result_array[0][4])), $display_result);
	  $display_result=str_replace('[[DATE]]', date('d.m.Y',strtotime($result_array[0][1])).' um '.date('H:m',strtotime($result_array[0][1])).' Uhr', $display_result);

	echo $display_result;
	

} else {

	// Print html header
	echo $GLOBALS['G_html_header'];

	
	// Print html menu
	echo $GLOBALS['G_html_menu_login'];
	echo $GLOBALS['G_html_menu2'];

	$html_box_login='';
	$html_box_login_title='';
	$html_box_splogin='';
	$html_box_splogin_title='';

	$box_width=400;



	// /////////////////
	// LOGIN
	echo '<div style="text-align: center;">';

	echo '<div style="margin-bottom:45px; margin-top:0px;">';


	if ($FLAG_SHUTDOWN==0) {

		if( !( isset($_GET['i']) && isset($_GET['t']) ) ) {
			$errorhtml1 =  H_build_boxinfo( 322, 'Falsche Dateneingabe.', 'red' );
		}
		
		// //////////////
		// Form with birth date
		
		$html_box_login.='<div style="text-align: left; display: inline-block; vertical-align: top;">';
		$html_box_login.=H_build_boxhead( $box_width, 'boxl1', 'Ihr Testergebnis kann abgerufen werden' );
		
		if($errorhtml1!='') {
			$html_box_login.=$errorhtml1;
		} else {
			$html_box_login.='<div class="FAIR-foldbox-static-part">';
			$html_box_login.='<p>Bitte geben Sie zur Verifizierung<br><b>das Geburtsdatum der getesten Person</b><br>ein.</p>';
			$html_box_login.='<p><i>For verification, please confirm<br><b>the date of birth of the tested person</b>.<br>Format: dd mm yyyy</i></p>';
			$html_box_login.='
			<form action="'.$current_site.'.php" method="post">
			<div class="FAIR-si-box">
			<input type="text" value="'.$_GET['i'].'" name="token" style="display:none;">
			<input type="text" value="'.$_GET['t'].'" name="customer_key" style="display:none;">';
			$html_box_login.='<input type="date" class="FAIR-textbox-large" name="gebdatum" />';
			$html_box_login.='</div>';
			
			$html_box_login.='<div class="FAIR-si-button">';
			$html_box_login.='<input type="submit" class="btn btn-primary" value="Testergebnis anzeigen" name="button" />';
			$html_box_login.='</div></form>';
			$html_box_login.='</div>';
		}
		
		$html_box_login.=H_build_boxfoot( );
		$html_box_login.='</div>';
	
			
	} elseif($FLAG_SHUTDOWN==1) {
		
		// //////////////
		// SITE CLOSED
		
		$html_box_login.='<div style="text-align: left; display: inline-block; vertical-align: top;">';
		$html_box_login.=H_build_boxhead( $box_width, 'boxl1', 'Dienst vorübergehend nicht verfügbar' );
		$html_box_login.=H_build_boxinfo( 0, 'Diese Website ist derzeit geschlossen. Bitte versuchen Sie es zu einem späteren Zeitpunkt noch einmal.', 'red' );		
		$html_box_login.= '<p></p>';
		
		$html_box_login.= H_build_boxfoot( );
		$html_box_login.= '</div>';
		
	}

	echo '<div style="padding-top:25px;"></div>';

	// /////////
	//   Boxes
	// /////////

	// No. 1 - Form
	echo $html_box_login;
	echo '</div>';

	echo '<div><img style="display: block; margin-left: auto; margin-right: auto; width: 40%;" src="../img/logo.png"></img></div>';

	echo '</div>';
	echo '</div>';

	// Print html footer
	echo $GLOBALS['G_html_footer'];
}

// Close connection to database
S_close_db($Db);

?>