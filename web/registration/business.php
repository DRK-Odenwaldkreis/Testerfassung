<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

login procedure for business accounts

** ************** */

include_once 'preload.php';
if( isset($GLOBALS['G_sessionname']) ) { session_name ($GLOBALS['G_sessionname']); }
session_start();
$current_site="business";
$sec_level=0;

// Include functions
include_once 'tools.php';
include_once 'auth.php';

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



// Case: shutdown active -> show message
$FLAG_SHUTDOWN=S_get_entry($Db,'SELECT value FROM website_settings WHERE name="FLAG_SHUTDOWN";');



// Forward after login to refering site
	// Is referer url not a login site
	 if( !preg_match('/login/',$refererPATH) ) {
		$_SESSION['refURL'] = $refererURL;
	} 


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if(isset($_POST['button'])) {
		// ----------------------------- //
		// -- Begin: PASSWORD SIGN IN -- //
		// ----------------------------- //
		$firmencode = strtolower($_POST['firmencode']);

		// Passwort wird überprüft
		if($firmencode!='') {
			//$uid=S_get_entry($Db,'SELECT id FROM li_user WHERE lower(username)=\''.$username.'\';');
			$sid=S_get_entry_login_firmencode($Db,$firmencode);
		} else {
			$sid=0;
		}
		
		if($sid>0) {
			
			A_login_firmencode($Db,$sid);

			// Weiterleitung zur geschützten Startseite
			header('Location: '.$FLAG_http.'://'.$hostname.($path == '/' ? '' : $path).'/business.php');
			exit;

			
		} else {
			$errorhtml1 =  H_build_boxinfo( 322, 'Daten nicht korrekt.', 'red' );
		}
		
	}
	// --------------------------- //
	// -- End: PASSWORD SIGN IN -- //
	// --------------------------- //
	
}
	


// Print html header
echo $GLOBALS['G_html_header'];

 
// Print html menu
if($_SESSION['b2b_signedin']) {
	echo $GLOBALS['G_html_menu'];
} else {
	echo $GLOBALS['G_html_menu_login'];
}
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


if ($FLAG_SHUTDOWN==0 && !$_SESSION['b2b_signedin']) {
		
	// //////////////
	// PASSWORD LOGIN
	
	$html_box_login.='<div style="text-align: left; display: inline-block; vertical-align: top;">';
	$html_box_login.=H_build_boxhead( $box_width, 'boxl1', 'Bitte melden Sie sich an' );
	
	
	$html_box_login.='<div class="FAIR-foldbox-static-part">';
	$html_box_login.='<p>Bitte geben Sie Ihren Firmencode ein, den Sie von Ihrer Organisation erhalten haben. Mit diesem können Sie sich für einen Covid-19 Schnelltest registrieren. Bei Fragen hierzu kontaktieren Sie bitte Ihre/n Vorgesetzte/n.</p>';
	$html_box_login.='
	<form action="'.$current_site.'.php" method="post">
	<div class="FAIR-si-box">';
	$html_box_login.='<input type="text" class="FAIR-textbox-large" name="firmencode" placeholder="Firmencode" autofocus="autofocus"/>';
	$html_box_login.='</div>';
	$html_box_login.=$errorhtml1;
	$html_box_login.='<div class="FAIR-si-button">';
	$html_box_login.='<input type="submit" class="btn btn-primary" value="Anmelden" name="button" />';
	$html_box_login.='</div></form>';
	$html_box_login.='<p><span class="text-sm">Mit Klick auf Anmelden stimme ich der Verwendung von technisch notwendigen Cookies zu.</span></p>';
	$html_box_login.='</div>';
	
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
	
} elseif($_SESSION['b2b_signedin']) {
	// //////////////
	// SHOW CALENDAR FOR B2B

	echo '<div class="row">
    
    <div class="col-sm-12">
    <h2 style="text-align: center;">Covid-19 Schnelltest - Termine und Orte für Ihr Unternehmen</h2>
    </div>
    <div class="col-sm-12"><div class="card">
	';

	// Show table of available dates
	echo H_build_table_testdates2('b2b');

	echo '</div></div>

    </div>
</div>';

}

echo '<div style="padding-top:25px;"></div>';

// /////////
//   Boxes
// /////////

// No. 1 - Login prompt
echo $html_box_login;
echo '</div>';

echo '<div><img style="display: block; margin-left: auto; margin-right: auto; width: 40%;" src="../img/logo.png"></img></div>';

echo '</div>';
echo '</div>';

// Print html footer
echo $GLOBALS['G_html_footer'];

// Close connection to database
S_close_db($Db);

?>