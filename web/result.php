<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

get result for customer

** ************** */

$current_site="result";

// Include functions
include_once 'admin01.php';
if(!$GLOBALS['FLAG_SHUTDOWN_MAIN']) {
	include_once 'zentral/tools.php';
	//include_once 'zentral/auth.php';
	include_once('zentral/server_settings.php');
}

// Include secondary files
include 'menu.php';

// Get Server and Path values
$hostname = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['PHP_SELF']);

$refererURL=$_SERVER['HTTP_REFERER'];
$refererPATH=parse_url($refererURL, PHP_URL_PATH);
$refererARG=parse_url($refererURL, PHP_URL_QUERY);


if(!$GLOBALS['FLAG_SHUTDOWN_MAIN']) {
	// Open database connection
	$Db=S_open_db();

	$errorhtml1 = '';

	// Case: shutdown active -> show message
	$FLAG_SHUTDOWN=S_get_entry($Db,'SELECT value FROM website_settings WHERE name="FLAG_SHUTDOWN";');

	$ALLOWANCE_RESULT=0;

	if ( $_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['internal_download']) ) {

		if(isset($_POST['button'])) {
			// Check birth date to show result
			
			$token = A_sanitize_input($_POST['token']);
			$customer_key = A_sanitize_input($_POST['customer_key']);
			//$gebdatum = $_POST['gebdatum'];
			$gebdatum_d = A_sanitize_input($_POST['gebdatum_d']);
			$gebdatum_m = A_sanitize_input($_POST['gebdatum_m']);
			$gebdatum_y = A_sanitize_input($_POST['gebdatum_y']);
			$gebdatum=sprintf('%04d',$gebdatum_y).'-'.sprintf('%02d',$gebdatum_m).'-'.sprintf('%02d',$gebdatum_d);

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
			
		} elseif(isset($_POST['download_pdf']) || isset($_GET['internal_download'])) {
			if(isset($_POST['download_pdf'])) {
				$token=A_sanitize_input($_POST['token']);
				$customer_key=A_sanitize_input($_POST['customer_key']);
				$gebdatum=A_sanitize_input($_POST['gebdat']);
			} elseif(isset($_GET['internal_download'])) {
				$token=A_sanitize_input($_GET['i']);
				$customer_key=A_sanitize_input($_GET['t']);
				$gebdatum=A_sanitize_input($_GET['g']);
			}

			// Daten werde überprüft
			$stmt=mysqli_prepare($Db,"SELECT id, Geburtsdatum, Customer_lock, Token FROM Vorgang WHERE Token=? AND Customer_key=? AND (Customer_lock is null OR Customer_lock<10);");
			mysqli_stmt_bind_param($stmt, "ss", $token, $customer_key);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $k_vorgang_id, $k_geb, $val_lock, $k_token);
			mysqli_stmt_fetch($stmt);
			mysqli_stmt_close($stmt);

			if($k_vorgang_id>0) {
				if($gebdatum==$k_geb) {
					$dir="/home/webservice/Testerfassung/Certificate2PDF/";
					chdir($dir);
					$job="python3 job.py $k_token";
					exec($job,$script_output);
					$file=$script_output[0];

					if( file_exists("/home/webservice/Zertifikate/$k_token.pdf") ) {
						header('Content-Type: application/octet-stream');
						header('Content-Disposition: attachment; filename="'.basename("$k_token.pdf").'"');
						header('Pragma: no-cache');
						header('Expires: 0');
						readfile("/home/webservice/Zertifikate/$k_token.pdf");
						exit;
					}
				}
			}
		}

		
	}
		
	if($ALLOWANCE_RESULT>0) {
		$result_array=S_get_multientry($Db,'SELECT Vorgang.Teststation, Vorgang.Registrierungszeitpunkt, Vorgang.Vorname, Vorgang.Nachname, Vorgang.Geburtsdatum, Vorgang.Ergebnis, Testtyp.Name, Testtyp.IsPCR FROM Vorgang LEFT OUTER JOIN Testtyp ON Testtyp.id=Vorgang.Testtyp_id WHERE Vorgang.id=CAST('.$ALLOWANCE_RESULT.' as int);');



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
		if($result_array[0][7] == 1) {
			$testtyp='RT-PCR Labortest';
		} else {
			$testtyp='SARS-CoV-2 PoC Ag-Test';
		}
		$display_result=str_replace('[[VORNAME]]', $result_array[0][2], $display_result);
		$display_result=str_replace('[[NACHNAME]]', $result_array[0][3], $display_result);
		$display_result=str_replace('[[TESTTYPE]]', $testtyp, $display_result);
		$display_result=str_replace('[[MANUFACTURER]]', $result_array[0][6], $display_result);
		$display_result=str_replace('[[GEBDATUM]]', date('d.m.Y',strtotime($result_array[0][4])), $display_result);
		$display_result=str_replace('[[DATE]]', date('d.m.Y',strtotime($result_array[0][1])).' um '.date('H:i',strtotime($result_array[0][1])).' Uhr', $display_result);

		echo '<div style="margin-top:20px;">
		<form action="'.$current_site.'.php" method="post">
		
		<input type="text" value="'.$token.'" name="token" style="display:none;">
		<input type="text" value="'.$customer_key.'" name="customer_key" style="display:none;">
		<input type="text" value="'.$gebdatum.'" name="gebdat" style="display:none;">
		<div style="margin-top: 18px;
		margin-bottom: 25px;">
		<input type="submit" style="display: inline-block;
		padding: 6px 12px;
		margin-bottom: 0;
		font-size: 14px;
		font-weight: normal;
		line-height: 1.42857143;
		text-align: center;
		white-space: nowrap;
		vertical-align: middle;
		-ms-touch-action: manipulation;
		touch-action: manipulation;
		cursor: pointer;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
		background-image: none;
		border: 1px solid transparent;
			border-top-color: transparent;
			border-right-color: transparent;
			border-bottom-color: transparent;
			border-left-color: transparent;
		border-radius: 4px;
		color: #fff;
background-color: #286090;
border-color: #204d74;" value="PDF herunterladen" name="download_pdf" />
		</div></form>
		</div>';

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

		// Print html content part A
		echo $GLOBALS['G_html_main_right_a'];


		if ($FLAG_SHUTDOWN==0) {

			if( !( isset($_GET['i']) && A_sanitize_input($_GET['i'])!='' && isset($_GET['t']) && A_sanitize_input($_GET['t'])!='' ) ) {
				$errorhtml1 =  H_build_boxinfo( 322, 'Falsche Dateneingabe.', 'red' );
			}
			
			// //////////////
			// Form with birth date
			
			$html_box_login.='<div class="panel panel-primary">
			<div class="panel-heading">
			<b>Ihr Testergebnis kann abgerufen werden</b>
			</div>
			<div class="panel-body">
			<div class="row">
			
			';
			
			if($errorhtml1!='') {
				$html_box_login.='<div class="col-sm-12">';
				$html_box_login.=$errorhtml1;
			} else {
				$html_box_login.='<div class="col-sm-6"><p>Bitte geben Sie zur Verifizierung <b>das Geburtsdatum der getesteten Person</b> ein.</p>
				<p>Ihr Ergebnis kann hierüber nur bis maximal 48 Stunden nach der Testung abgerufen werden. Anschließend werden die Daten gelöscht.</p></div>
				
				<div class="col-sm-6"><p><i>For verification, please confirm <b>the date of birth of the tested person</b>.<br>Format: dd mm yyyy</i></p>
				<p><i>The test result is available only for max. 48 hours after your swab test. Your data will be deleted after this time.</i></p></div></div>
				<div class="row"><div class="col-sm-12">
				<form action="'.$current_site.'.php" method="post">
				
				<input type="text" value="'.A_sanitize_input($_GET['i']).'" name="token" style="display:none;">
				<input type="text" value="'.A_sanitize_input($_GET['t']).'" name="customer_key" style="display:none;">';
				$html_box_login.='
				<div class="input-group">
				<span class="input-group-addon" id="basic-addon1">Geburtsdatum / <i>Date Of Birth</i></span>
				</div>
				<div class="input-group">
				<span class="input-group-addon" id="basic-addon1">Tag</span>
				<input type="number" min="1" max="31" placeholder="TT" class="form-control" name="gebdatum_d" required>
				</div><div class="input-group">
				<span class="input-group-addon" id="basic-addon1">Monat</span>
				<input type="number" min="1" max="12" placeholder="MM" class="form-control" name="gebdatum_m" required>
				</div><div class="input-group">
				<span class="input-group-addon" id="basic-addon1">Jahr</span>
				<input type="number" min="1900" max="2999" placeholder="JJJJ" class="form-control" name="gebdatum_y" required>
				</div>';

				$html_box_login.='<div class="FAIR-si-button">';
				$html_box_login.='<input type="submit" class="btn btn-primary" value="Testergebnis anzeigen" name="button" />';
				$html_box_login.='</div></form>';
				$html_box_login.='</div>';
			}
			
			$html_box_login.='</div>
			</div>
			</div>
			</div>';
		
				
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
} else {

	// Print html header
	echo $GLOBALS['G_html_header'];

		
	// Print html menu
	echo $GLOBALS['G_html_menu_login'];
	echo $GLOBALS['G_html_menu2'];
	echo '<div class="row">
	<div class="col-sm-12"><div class="card">
	<h2>Abfrage Ihres Testergebnis</h2>
	<div class="alert alert-danger" role="alert">
    <h3>Wartungsarbeiten</h3>
    <p>Derzeit finden Arbeiten an dieser Seite statt, die Ergebnisabfrage steht momentan nicht zur Verfügung. Bald geht es wieder weiter...wir bitten um etwas Geduld.</p>
    <div class="FAIRsepdown"></div>
    <div class="FAIRsep"></div>
</div>
</div></div></div>';
	// Print html footer
	echo $GLOBALS['G_html_footer'];
}

?>