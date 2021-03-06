<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

login procedure

** ************** */

include_once 'preload.php';
if( isset($GLOBALS['G_sessionname']) ) { session_name ($GLOBALS['G_sessionname']); }
session_start();
$current_site="login";
$sec_level=0;

// Include functions
include_once 'tools.php';
include_once 'auth.php';
require_once 'lib/passwordLib.php';

// Include secondary files
include 'menu.php';

// Get Server and Path values
$hostname = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['PHP_SELF']);

$refererURL=$_SERVER['HTTP_REFERER'];
$refererPATH=parse_url($refererURL, PHP_URL_PATH);
$refererARG=parse_url($refererURL, PHP_URL_QUERY);

// b=1 is for password reset
if( isset($_GET['b']) && $_GET['b']==1 ) { $pwd_reset=true; } else { $pwd_reset=false; }
// qr=1 is for qr code login
if( isset($_GET['qr']) && $_GET['qr']==1 ) { $qr_login=true; } else { $qr_login=false; }
// scan=? is for qr code login
if( isset($_GET['scan']) ) { $qr_login_val=$_GET['scan']; } else { $qr_login_val=false; }

// pwd=lock is for information about locked account
if( isset($_GET['pwd']) && $_GET['pwd']=='lock' ) { $pwd_lock=true; } else { $pwd_lock=false; }


// Get user login id for password reset - with token
if( isset($_GET['u']) ) {
	$login_user_id=intval($_GET['u']);
	if( isset($_GET['t']) ) {
		$tokenreceived=$_GET['t'];
		if( isset($_GET['tid']) ) {
			$token_id=intval($_GET['tid']);
		} else {
			$token_id=0;
		}
	} else {
		$tokenreceived='';
		$token_id=0;
	}
} else {
	$login_user_id=0;
	$tokenreceived='';
	$token_id=0;
}

// Open database connection
$Db=S_open_db();



// Case: shutdown active -> show message
$FLAG_SHUTDOWN=S_get_entry($Db,'SELECT value FROM website_settings WHERE name="FLAG_SHUTDOWN";');



// Forward after login to refering site
	// Is referer url not a login site
	 if( !preg_match('/login/',$refererPATH) ) {
		$_SESSION['refURL'] = $refererURL;
	} 


if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$pwd_lock) {

	if(isset($_POST['button'])) {
		// ----------------------------- //
		// -- Begin: PASSWORD SIGN IN -- //
		// ----------------------------- //
		$username = strtolower($_POST['username']);
		$password = $_POST['password'];

		// Benutzername und Passwort werden überprüft
		if($username!='') {
			//$uid=S_get_entry($Db,'SELECT id FROM li_user WHERE lower(username)=\''.$username.'\';');
			$uid=S_get_entry_login_username($Db,$username);
			if($uid>0) {
				$db_hash=S_get_entry($Db,'SELECT password_hash FROM li_user WHERE id='.$uid.';');
			} else {
				$db_hash='';
			}
		} else {
			$uid=0;
		}
		
		if($uid>0) {
			// Check for failed login attempts and if value is lower than threshold go to log-in
			$lock_value=S_get_entry($Db,'SELECT login_attempts FROM li_user WHERE id='.$uid.';');
			if($lock_value>=$GLOBALS["LOCK_VALUE_THRESHOLD"]) {
				header('Location: '.$FLAG_http.'://'.$hostname.($path == '/' ? '' : $path).'/login.php?pwd=lock');
				exit;
			}
			
			// Check correct password hash
			if (!password_verify($password, $db_hash)) {
				$errorhtml1 =  H_build_boxinfo( 322, 'Daten nicht korrekt.', 'red' );
				S_set_data($Db,'UPDATE li_user SET login_attempts=login_attempts+1 WHERE id='.$uid.';');
			} else {
			
				A_login($Db,$uid,'password');

				// Weiterleitung zur geschützten Startseite
				if ($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') {
					if (php_sapi_name() == 'cgi') {
						header('Status: 303 See Other');
					} else {
						header('HTTP/1.1 303 See Other');
					}
				}
					
				if( isset($_SESSION['linkURL']) ) {
					$link=$_SESSION['linkURL'];
					unset($_SESSION['linkURL']);
					header('Location: '.$link);
				} else {
					header('Location: '.$FLAG_http.'://'.$hostname.($path == '/' ? '' : $path).'/index.php');
				}
					
				exit;
			}
			
		} else {
			$errorhtml1 =  H_build_boxinfo( 322, 'Daten nicht korrekt.', 'red' );
		}
		
	}
	// --------------------------- //
	// -- End: PASSWORD SIGN IN -- //
	// --------------------------- //
	
}
	
if ( $FLAG_SHUTDOWN==0 && $login_user_id>0 && $tokenreceived!='' ) {

	// ------------------------------------ //
	// -- Begin: CODE SIGN IN - RESET PWD - //
	// ------------------------------------ //

	// Check if received token is correct
	$db_hash=S_get_entry($Db,'SELECT token FROM li_token WHERE id='.$token_id.'');
	if (!password_verify($tokenreceived, $db_hash)) {
		$errorhtml1 =  H_build_boxinfo( 322, 'Daten nicht korrekt.', 'red' );
		// increase failed login counter
		S_set_data($Db,'UPDATE li_user SET login_attempts=login_attempts+1 WHERE id='.$login_user_id.';');

	} else {
		
		// Check for failed login attempts and if value is lower than threshold
		$lock_value=S_get_entry($Db,'SELECT login_attempts FROM li_user WHERE id='.$login_user_id.';');
		if($lock_value>=$GLOBALS["LOCK_VALUE_THRESHOLD"]) {
			header('Location: '.$FLAG_http.'://'.$hostname.($path == '/' ? '' : $path).'/login.php?pwd=lock');
			exit;
		}
		
		// Delete all login tokens linked to this user
		S_set_data($Db,'DELETE FROM li_token WHERE id_user='.$login_user_id.';');
		
		A_login($Db,$login_user_id,'code');

			// Weiterleitung zur geschützten Startseite
			if ($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') {
				if (php_sapi_name() == 'cgi') {
					header('Status: 303 See Other');
				} else {
					header('HTTP/1.1 303 See Other');
				}
			}
			// Forward to change password site
			header('Location: '.$FLAG_http.'://'.$hostname.($path == '/' ? '' : $path).'/user.php?m=chgpwd');
			exit;
	}

	// ---------------------------------- //
	// -- End: CODE SIGN IN - RESET PWD - //
	// ---------------------------------- //

}

if($qr_login_val) {
	// ----------------------------- //
	// -- Begin: QR CODE SIGN IN  -- //
	// ----------------------------- //
	$arr=explode('/password/',$qr_login_val);
	$username=substr($arr[0], strpos($arr[0], '/user/' )+6);
	$password=$arr[1];

	// Benutzername und Passwort werden überprüft
	if($username!='') {
		//$uid=S_get_entry($Db,'SELECT id FROM li_user WHERE lower(username)=\''.$username.'\';');
		$uid=S_get_entry_login_username($Db,$username);
		if($uid>0) {
			$db_hash=S_get_entry($Db,'SELECT password_hash FROM li_user WHERE id='.$uid.';');
		} else {
			$db_hash='';
		}
	} else {
		$uid=0;
	}
	
	if($uid>0) {
		// Check for failed login attempts and if value is lower than threshold go to log-in
		$lock_value=S_get_entry($Db,'SELECT login_attempts FROM li_user WHERE id='.$uid.';');
		if($lock_value>=$GLOBALS["LOCK_VALUE_THRESHOLD"]) {
			header('Location: '.$FLAG_http.'://'.$hostname.($path == '/' ? '' : $path).'/login.php?pwd=lock');
			exit;
		}
		
		// Check correct password hash
		if (!password_verify($password, $db_hash)) {
			$errorhtml1 =  H_build_boxinfo( 322, 'Daten nicht korrekt.', 'red' );
			S_set_data($Db,'UPDATE li_user SET login_attempts=login_attempts+1 WHERE id='.$uid.';');
		} else {
		
			A_login($Db,$uid,'password');

			// Weiterleitung zur geschützten Startseite
			if ($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') {
				if (php_sapi_name() == 'cgi') {
					header('Status: 303 See Other');
				} else {
					header('HTTP/1.1 303 See Other');
				}
			}
				
			if( isset($_SESSION['linkURL']) ) {
				$link=$_SESSION['linkURL'];
				unset($_SESSION['linkURL']);
				header('Location: '.$link);
			} else {
				header('Location: '.$FLAG_http.'://'.$hostname.($path == '/' ? '' : $path).'/index.php');
			}
				
			exit;
		}
		
	} else {
		$errorhtml1 =  H_build_boxinfo( 322, 'Daten nicht korrekt.', 'red' );
	}
	
}
// --------------------------- //
// -- End: QR CODE SIGN IN  -- //
// --------------------------- //
	
	
if( isset($_POST['button-reset']) ) {
      
	// ---------------------- //
	// -- Begin: SEND CODE -- //
	// -- for password reset  //
	// ---------------------- //
	
	$username_reset = strtolower($_POST['username-reset']);
	
      	
	$uid=S_get_entry_login_username($Db,$username_reset);
	$email=S_get_entry($Db,'SELECT email FROM li_user WHERE id='.$uid.';');
	if($uid>0) {
		
		// Check for failed login attempts and if value is lower than threshold
		$lock_value=S_get_entry($Db,'SELECT login_attempts FROM li_user WHERE id='.$uid.';');
		if($lock_value>=$GLOBALS["LOCK_VALUE_THRESHOLD"]) {
			header('Location: '.$FLAG_http.'://'.$hostname.($path == '/' ? '' : $path).'/login.php?pwd=lock');
		}
		
		// Create new login token
		$token=A_generate_token(32);
		$tokenhash = password_hash($token, PASSWORD_BCRYPT);
		$timestamp=date('Y-m-d H:i:s', time());
		S_set_data($Db,'INSERT INTO li_token (id_user,timestamp,token) VALUES ('.$uid.',\''.$timestamp.'\',\''.$tokenhash.'\');');
		$token_id=S_get_entry($Db,'SELECT id FROM li_token WHERE id_user='.$uid.' AND timestamp=\''.$timestamp.'\' AND token=\''.$tokenhash.'\';');
		
		$header = "From: info@testzentrum-odenwald.de\r\n";
		$header .= "Content-Type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 8bit";
		$content="Lieber Nutzer, liebe Nutzerin,\n
es wurde eine Anfrage zum Zurücksetzen Ihres Passwortes für das Websystem des DRK Covid-19 Testzentrum gestellt. Falls diese Anfrage von Ihnen nicht initiiert wurde, können Sie diese Nachricht ignorieren.\n
Bitte mit diesem Link das Passwort neu setzen:\n";
		$content.=$FLAG_http.'://'.$hostname.($path == '/' ? '' : $path)."/login.php?u=$uid&t=$token&tid=$token_id";
		$content.="\n\n
Mit freundlichen Grüßen\n
Das Team vom DRK";
		$title='DRK Covid-19 Testzentrum Odenwaldkreis - Passwort zurücksetzen';
		$res=mail($email, $title, $content, $header, "-r info@testzentrum-odenwald.de");
		
		
	}
	
	$errorhtml1 =  H_build_boxinfo( 0, 'Eine Nachricht wurde verschickt an (*):<br><strong>' . $username_reset . '</strong><br>Bitte Ihr Postfach prüfen.<br><br>(*) Nur wenn der Benutzername registriert ist.', 'green' );
      	
		
	// -------------------- //
	// -- End: SEND CODE -- //
	// -------------------- //

}

// Print html header
echo $GLOBALS['G_html_header'];

 
// Print html menu
echo $GLOBALS['G_html_menu'];
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


if($pwd_lock) {
	
	// //////////////
	// LOGIN ATTEMPTS
	
	$html_box_login .= '<div style="text-align: left; display: inline-block; vertical-align: top;">';
	$html_box_login .= H_build_boxhead( $box_width, 'boxlock', 'Account gesperrt', 0 );
	$html_box_login .= '<div class="FAIR-ed-label">Achtung: Ihr Account ist aufgrund einer hohen Zahl fehlgeschlagener Anmeldeversuche gesperrt. Wenden Sie sich bitte an den <a title="Impressum und Administration" href="impressum.php">Administrator</a>.</div>';
	$html_box_login .= H_build_boxfoot( );
	$html_box_login .= '</div>';
	
} elseif(isset( $_SESSION['uid']) && $_SESSION['uid']>=0 ) {
	
	// /////////////////
	// ALREADY LOGGED IN
	$html_box_login .='<div style="text-align: left; display: inline-block; vertical-align: top;">
	<div style="float:none; margin-right:50px;margin-top: 8px;">';
	$html_box_login .='<div class="FAIR-box-rc-outer">
	<div style="font-size:180%; position: relative;top: 17px;left: 10px;">Sie sind bereits angemeldet</div>
	<div style="font-size:130%; position: relative;top: 50px;left: 18px;"><a href="index.php" id="entersite" class="btn btn-default" name="entersite">Zur Startseite</a></div></div></div>';
	$html_box_login .= '</div></div>';

} elseif( $qr_login && $FLAG_SHUTDOWN==0 ) {

	// //////////////
	// QR LOGIN
	/* echo '
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/3.3.3/adapter.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.min.js"></script>
    <script type="text/javascript" src="lib/instascan-master/instascan.min.js"></script>
    '; */
    echo '
    <script type="text/javascript" src="lib/qrscan-lib/html5-qrcode.min.js"></script>
    ';
	


    echo '<div class="row">';
    echo '<div class="col-sm-12">
    <h3>Bitte QR-Code für Login scannen</h3>';

	echo '<div style="width: 500px" id="reader"></div>';


	echo '<script>
const html5QrCode = new Html5Qrcode("reader");
const qrCodeSuccessCallback = message => { window.location.href=`?scan=${message}`; }
const config = { fps: 10, qrbox: 350 };

html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback);

	</script>
	';

    echo '</div></div>';

	
} elseif( $pwd_reset && $FLAG_SHUTDOWN==0 ) {
	
	// //////////////
	// PASSWORD RESET
	
	$html_box_login.= '<div style="text-align: left; display: inline-block; vertical-align: top;">';
	$html_box_login.= H_build_boxhead( $box_width, 'boxl1', 'Bitte Benutzername eintragen' );
	$html_box_login.= '<div class="FAIR-foldbox-static-part">';
	$html_box_login.= '<p>Nach Eingabe von Benutzername wird eine Nachricht an Sie versandt mit einem Anmeldecode zum Zurücksetzen des Passwortes.</p>';
	$html_box_login.= '
	<form action="'.$current_site.'.php" method="post">
	<div class="FAIR-si-box">';
	$html_box_login.= '<input type="text" class="FAIR-textbox-large" name="username-reset" placeholder="Benutzername"/>';
	$html_box_login.= '</div>';
	$html_box_login.= $errorhtml1;
	$html_box_login.= '<div class="FAIR-si-button">';
	$html_box_login.= '<input type="submit" class="btn btn-danger" value="Passwort zurücksetzen" name="button-reset" />';
	$html_box_login.= '</div></form>';
	$html_box_login.= '</div>';
	
	$html_box_login.= '<p></p>';
	$html_box_login.='<ul class="FAIR-editmenu-ul">';
	$html_box_login.='<li class="FAIR-editmenu-sep"></li>';
	$html_box_login.= '<li class="FAIR-editmenu-ul"><a class="FAIR-editmenu-td" href="'.$current_site.'.php">
	<div class="FAIR-editmenu-left"><span style="margin-left:10px;"></span>Zurück</div><div class="FAIR-editmenu-right"></div>
	</a></li>';
	$html_box_login.='<li class="FAIR-editmenu-sep"></li>';
	$html_box_login.= '</ul>';
	
	$html_box_login.= H_build_boxfoot( );
	$html_box_login.= '</div>';

} elseif ($FLAG_SHUTDOWN==0) {
		
	// //////////////
	// PASSWORD LOGIN
	
	$html_box_login.='<div style="text-align: left; display: inline-block; vertical-align: top;">';
	$html_box_login.=H_build_boxhead( $box_width, 'boxl1', 'Bitte melden Sie sich an' );
	
	if($FLAG_SHUTDOWN==1) {
		$html_box_login.=H_build_boxinfo( 0, 'Diese Website und die Datenbank sind derzeit geschlossen.<br>Diese Anmeldung ist nur für Administratoren.', 'blue' );	
	}
	
	$html_box_login.='<div class="FAIR-foldbox-static-part">';
	$html_box_login.='
	<form action="'.$current_site.'.php" method="post">
	<div class="FAIR-si-box">';
	$html_box_login.='<input type="text" class="FAIR-textbox-large" name="username" placeholder="Benutzername" autofocus="autofocus"/>';
	$html_box_login.='</div>';
	$html_box_login.='<div class="FAIR-si-box">';
	$html_box_login.='<input type="password" class="FAIR-textbox-large" name="password" placeholder="Passwort" />';
	$html_box_login.='</div>';
	$html_box_login.=$errorhtml1;
	$html_box_login.='<div class="FAIR-si-button">';
	$html_box_login.='<input type="submit" class="btn btn-danger" value="Anmelden" name="button" />';
	$html_box_login.='</div></form>';
	$html_box_login.='<p><span class="text-sm">Mit Klick auf Anmelden stimme ich der Verwendung von technisch notwendigen Cookies zu.</span></p>';
	$html_box_login.='</div>';
	
	$html_box_login.='<p></p>';
	$html_box_login.='<ul class="FAIR-editmenu-ul">';
	$html_box_login.='<li class="FAIR-editmenu-sep"></li>';
	$html_box_login.='<li class="FAIR-editmenu-ul"><a class="FAIR-editmenu-td" href="'.$current_site.'.php?qr=1">
	<div class="FAIR-editmenu-left">Mit QR-Code einloggen</div><div class="FAIR-editmenu-right"></div>
	</a></li>';
	$html_box_login.='<li class="FAIR-editmenu-sep"></li>';
	$html_box_login.='<li class="FAIR-editmenu-ul"><a class="FAIR-editmenu-td" href="'.$current_site.'.php?b=1">
	<div class="FAIR-editmenu-left">Passwort vergessen?</div><div class="FAIR-editmenu-right"></div>
	</a></li>';
	if($FLAG_SHUTDOWN==1) {
		$html_box_login.='<li class="FAIR-editmenu-sep"></li>';
		$html_box_login.='<li class="FAIR-editmenu-ul"><a class="FAIR-editmenu-td" href="'.$current_site.'.php">
		<div class="FAIR-editmenu-left"><span style="margin-left:10px;"></span>Zurück</div><div class="FAIR-editmenu-right"></div>
		</a></li>';
	}
	$html_box_login.='<li class="FAIR-editmenu-sep"></li>';
	$html_box_login.='</ul>';
	
	$html_box_login.=H_build_boxfoot( );
	$html_box_login.='</div>';
		
} elseif($FLAG_SHUTDOWN==1) {
	
	// //////////////
	// SITE CLOSED
	
	$html_box_login.='<div style="text-align: left; display: inline-block; vertical-align: top;">';
	$html_box_login.=H_build_boxhead( $box_width, 'boxl1', 'Dienst vorübergehend nicht verfügbar' );
	$html_box_login.=H_build_boxinfo( 0, 'Diese Website ist derzeit geschlossen. Bitte versuchen Sie es zu einem späteren Zeitpunkt noch einmal.', 'red' );		
	$html_box_login.= '<p></p>';
	/* $html_box_login.='<ul class="FAIR-editmenu-ul">';
	$html_box_login.='<li class="FAIR-editmenu-sep"></li>';
	$html_box_login.= '<li class="FAIR-editmenu-ul"><a class="FAIR-editmenu-td" href="'.$current_site.'.php?a=1">
	<div class="FAIR-editmenu-left">Administrator Login</div><div class="FAIR-editmenu-right"></div>
	</a></li>';
	$html_box_login.='<li class="FAIR-editmenu-sep"></li>';
	$html_box_login.= '</ul>'; */
	
	$html_box_login.= H_build_boxfoot( );
	$html_box_login.= '</div>';
	
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