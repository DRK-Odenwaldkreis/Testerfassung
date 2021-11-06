<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

file with HTML elements
to construct website frame
and some global used values
** ************** */



// HTML header with complete <head> element
$G_html_header='<html lang="en">
  <head>
    ';
    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
      $G_html_header.='
      <title>DRK Covid-19 Testzentrum Odenwaldkreis</title>
      ';
    } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
      $G_html_header.='
      <title>DRK Covid-19 Impfzentrum Odenwaldkreis</title>
      ';
    } elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
      $G_html_header.='
      <title>DRK Covid-19 Antikörpertests Odenwaldkreis</title>
      ';
    }
    $G_html_header.='
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
<link rel="shortcut icon" href="img/favicon.png" type="image/x-ico; charset=binary" />
<link rel="icon" href="img/favicon.png" type="image/x-ico; charset=binary" />

';

if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
  $G_html_header.='
  <link href="css/bootstrap_red.css" rel="stylesheet">
  <!-- Custom styles for this template -->
  <link href="css/dashboard_red.css" rel="stylesheet">';
} else {
  $G_html_header.='
  <link href="css/bootstrap.css" rel="stylesheet">
  <!-- Custom styles for this template -->
  <link href="css/dashboard.css" rel="stylesheet">';
}

$G_html_header.='
<link href="css/symbols-fair.css" rel="stylesheet">

<script type="text/javascript" src="lib/datatables/jQuery-3.3.1/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="lib/datatables/Bootstrap-3.3.7/js/bootstrap.min.js"></script>
    
  </head>';


// Menu
if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
  $_module_array1=array(
    0=>array("text"=>'<h4 class="list-group-item-heading">Kunden-Registrierung / Test-Auswertung</h4><p class="list-group-item-text">TESTKARTE QR Code scannen</p>',"text_s"=>'<span class="icon-qrcode"></span>&nbsp;Scannen',"link"=>"scan.php","role"=>array(1,0,0,4,0),"role-disabled"=>array(0,2,0,0,5)),
    2=>array("text"=>'<h4 class="list-group-item-heading">Voranmeldungen</h4><p class="list-group-item-text">Liste der Voranmeldungen und Übernahme in Reg-Prozess</p>',"text_s"=>'<span class="icon-ticket"></span>&nbsp;Voranmeldungen',"link"=>"prereglist.php","role"=>array(1,2,0,4,5),"role-disabled"=>array(0,0,0,0,0)),
    10=>array("text"=>'<h4 class="list-group-item-heading">Liste an Tests</h4><p class="list-group-item-text">Aktive Tests und Export CSV</p>',"link"=>"testlist.php","text_s"=>'<span class="icon-lab"></span>&nbsp;Testliste',"role"=>array(1,2,0,4,5),"role-disabled"=>array(0,0,0,0,0)),
    15=>array("text"=>'<h4 class="list-group-item-heading">Liste für Gesundheitsamt</h4><p class="list-group-item-text">PCR-Tests & Positivmeldungen und Export CSV</p>',"text_s"=>'<span class="icon-lab"></span>&nbsp;Gesundheitsamt',"link"=>"gesundheitsamt.php","role"=>array(0,0,3,4,0),"role-disabled"=>array(0,0,0,0,0)),
    99=>array("text"=>'<h4 class="list-group-item-heading">Öffentliche Startseite Testzentrum</h4><p class="list-group-item-text"></p>',"text_s"=>'',"link"=>"../index.php","role"=>array(1,2,3,4,5),"role-disabled"=>array(0,0,0,0,0))
  );
  $_module_array2=array(
    20=>array("text"=>'<h4 class="list-group-item-heading">Stationen</h4><p class="list-group-item-text">Stations-Management</p>',"text_s"=>'<span class="icon-office"></span>&nbsp;Stationen',"link"=>"station_admin.php","role"=>array(0,2,0,4,0),"role-disabled"=>array(0,0,0,0,0)),
    22=>array("text"=>'<h4 class="list-group-item-heading">Testkarten</h4><p class="list-group-item-text">Erstellung von neuen Testkarten</p>',"text_s"=>'<span class="icon-print"></span>&nbsp;Testkarten',"link"=>"testkarten.php","role"=>array(0,2,0,4,5),"role-disabled"=>array(0,0,0,0,0)),
    25=>array("text"=>'<h4 class="list-group-item-heading">Termine</h4><p class="list-group-item-text">Übersicht der angelegten Termine</p>',"text_s"=>'<span class="icon-calendar2"></span>&nbsp;Terminübersicht',"link"=>"terminlist.php","role"=>array(1,2,0,4,5),"role-disabled"=>array(0,0,0,0,0)),
    26=>array("text"=>'<h4 class="list-group-item-heading">Termine erstellen</h4><p class="list-group-item-text">Neue Termine für eine Teststation erstellen</p>',"text_s"=>'<span class="icon-cogs"></span>&nbsp;Termin-Verwaltung',"link"=>"terminerstellung.php","role"=>array(0,2,0,4,5),"role-disabled"=>array(1,0,0,0,0)),
    28=>array("text"=>'<h4 class="list-group-item-heading">Sammel-Testung</h4><p class="list-group-item-text">Für Sammel-Testung Daten importieren und Ergebnis-Abruf</p>',"text_s"=>'<span class="icon-stack"></span>&nbsp;Sammel-Testung',"link"=>"sammeltestung.php","role"=>array(0,2,0,4,0),"role-disabled"=>array(0,0,0,0,0)),
    30=>array("text"=>'<h4 class="list-group-item-heading">Admin: Web user</h4><p class="list-group-item-text">User-Management</p>',"text_s"=>'<span class="icon-users"></span>&nbsp;User-Management',"link"=>"user_admin.php","role"=>array(0,0,0,4,0),"role-disabled"=>array(0,2,0,0,0)),
    33=>array("text"=>'<h4 class="list-group-item-heading">Admin: Files</h4><p class="list-group-item-text">Dateien</p>',"text_s"=>'',"link"=>"downloadlist.php","role"=>array(0,0,0,4,0),"role-disabled"=>array(0,0,0,0,0)),
    34=>array("text"=>'<h4 class="list-group-item-heading">Admin: Logs</h4><p class="list-group-item-text">Übersicht der Logs</p>',"text_s"=>'',"link"=>"log.php","role"=>array(0,0,0,4,0),"role-disabled"=>array(0,0,0,0,0)),
    98=>array("text"=>'<h4 class="list-group-item-heading">Support, Datenschutz, Impressum</h4><p class="list-group-item-text"></p>',"text_s"=>'',"link"=>"impressum.php","role"=>array(1,2,3,4,5),"role-disabled"=>array(0,0,0,0,0))
  );
} else {
  $_module_array1=array(
    2=>array("text"=>'<h4 class="list-group-item-heading">Voranmeldungen</h4><p class="list-group-item-text">Liste der Voranmeldungen</p>',"text_s"=>'<span class="icon-ticket"></span>&nbsp;Voranmeldungen',"link"=>"prereglist.php","role"=>array(1,2,0,4,5),"role-disabled"=>array(0,0,0,0,0)),
    99=>array("text"=>'<h4 class="list-group-item-heading">Öffentliche Startseite Impfzentrum</h4><p class="list-group-item-text"></p>',"text_s"=>'',"link"=>"../index.php","role"=>array(1,2,3,4,5),"role-disabled"=>array(0,0,0,0,0))
  );
  $_module_array2=array(
    20=>array("text"=>'<h4 class="list-group-item-heading">Stationen</h4><p class="list-group-item-text">Stations-Management</p>',"text_s"=>'<span class="icon-office"></span>&nbsp;Stationen',"link"=>"station_admin.php","role"=>array(0,2,0,4,0),"role-disabled"=>array(0,0,0,0,0)),
    25=>array("text"=>'<h4 class="list-group-item-heading">Termine</h4><p class="list-group-item-text">Übersicht der angelegten Termine</p>',"text_s"=>'<span class="icon-calendar2"></span>&nbsp;Terminübersicht',"link"=>"terminlist.php","role"=>array(1,2,0,4,5),"role-disabled"=>array(0,0,0,0,0)),
    26=>array("text"=>'<h4 class="list-group-item-heading">Termine erstellen</h4><p class="list-group-item-text">Neue Termine für einen Impfstoff erstellen</p>',"text_s"=>'<span class="icon-cogs"></span>&nbsp;Termin-Verwaltung',"link"=>"terminerstellung.php","role"=>array(0,2,0,4,5),"role-disabled"=>array(1,0,0,0,0)),
    30=>array("text"=>'<h4 class="list-group-item-heading">Admin: Web user</h4><p class="list-group-item-text">User-Management</p>',"text_s"=>'<span class="icon-users"></span>&nbsp;User-Management',"link"=>"user_admin.php","role"=>array(0,0,0,4,0),"role-disabled"=>array(0,2,0,0,0)),
    33=>array("text"=>'<h4 class="list-group-item-heading">Admin: Files</h4><p class="list-group-item-text">Dateien</p>',"text_s"=>'',"link"=>"downloadlist.php","role"=>array(0,0,0,4,0),"role-disabled"=>array(0,0,0,0,0)),
    34=>array("text"=>'<h4 class="list-group-item-heading">Admin: Logs</h4><p class="list-group-item-text">Übersicht der Logs</p>',"text_s"=>'',"link"=>"log.php","role"=>array(0,0,0,4,0),"role-disabled"=>array(0,0,0,0,0)),
    98=>array("text"=>'<h4 class="list-group-item-heading">Support, Datenschutz, Impressum</h4><p class="list-group-item-text"></p>',"text_s"=>'',"link"=>"impressum.php","role"=>array(1,2,3,4,5),"role-disabled"=>array(0,0,0,0,0))
  );
}



// HTML body with menu
// contains start of <body> element
$G_html_menu='<body>';
$G_html_menu_login='<body style="background-color:#ccc;">';
$G_html_menu2='<nav class="navbar navbar-inverse navbar-fixed-top FAIR-navbar">
      <div class="container-fluid">
        <div class="navbar-header">';

if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
  $G_html_menu2.='<a class="navbar-brand" href="index.php"><span class="shorten">DRK Covid-19 Testzentrum Odenwaldkreis </span><span style="color:#eee;">Start</span></a>';
} elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
  $G_html_menu2.='<a class="navbar-brand" href="index.php"><span class="shorten">DRK Covid-19 Impfzentrum Odenwaldkreis </span><span style="color:#eee;">Start</span></a>';
} elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
  $G_html_menu2.='<a class="navbar-brand" href="index.php"><span class="shorten">DRK Covid-19 Antikörpertests Odenwaldkreis </span><span style="color:#eee;">Start</span></a>';
}


if($_SESSION['uid']>0) {
	$G_html_menu2.='<ul class="nav navbar-nav navbar-left">';

  $G_html_menu2.='
  <li class="dropdown">
  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Menü <b class="caret"></b></a>
  <ul class="dropdown-menu">
  ';
  foreach($_module_array1 as $key=>$a) {
    $show_entry=false;
    foreach($a["role"] as $b) {
        if($b>0 && $_SESSION['roles'][$b]==1) { 
            $show_entry=true;
        }
    }
    if($show_entry && $a["text_s"]!='') { 
      $G_html_menu2.= '<li><a href="'.$a["link"].'">'.$a["text_s"].'</a></li>';
    }
  }
  foreach($_module_array2 as $key=>$a) {
    $show_entry=false;
    foreach($a["role"] as $b) {
        if($b>0 && $_SESSION['roles'][$b]==1) { 
            $show_entry=true;
        }
    }
    if($show_entry && $a["text_s"]!='') { 
      $G_html_menu2.= '<li><a href="'.$a["link"].'">'.$a["text_s"].'</a></li>';
    }
  }
    
    $G_html_menu2.='</ul>
</li>';

	if($_SESSION['station_id']>0) {
    $display_station='S'.$_SESSION['station_id'].'/'.$_SESSION['station_name'];
  } else {
    $display_station=$_SESSION['username'];
  }
	$G_html_menu2.='<li title="Station"><a style="color:#fff; font-size:85%;">'.$display_station.'</a></li>';

	$G_html_menu2.='</div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">';

	// Logged in / expiration of cookie
	$cookievalue=json_decode($_COOKIE['drk-cookie']);
	$expiry=$cookievalue->expiry;
	$expiry_diff=($expiry-time())/60; // in minutes
	if($expiry_diff<20) {$expiry_diff=20;}
	if( floor($expiry_diff / 60) < 2 ) { $expiry_text=ceil($expiry_diff).' Min.'; } // ceil = round up
	else { $expiry_text=ceil($expiry_diff / 60).' Std.'; } // ceil = round up
	$G_html_menu2.='<li title="Eingeloggt für '.$expiry_text.'" data-toggle="tooltip" data-placement="bottom" class="shorten"><a style="color:#fff; font-size:85%;">Eingeloggt für '.$expiry_text.'</a></li>';
	
	$G_html_menu2.='<li><a href="logout.php" style="color: #fff; background-color: #9f0000;">Logout</a></li>';
} else {
	$G_html_menu2.='</div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
			<li><a href="impressum.php">Impressum</a></li>
			<li><a href="login.php" style="color: #fff; background-color: #419f00;">Login</a></li>';
}
$G_html_menu2.='</ul>
        
          </ul>
        </div>
      </div>
    </nav>
';

// HTML element for content
$G_html_main_right_a='<main role="main" class="FAIR-main-col">';

// HTML section for database table and its content
// Content is produced with JS after initialisation of site
$G_html_main_right_b='
		  <div class="table-responsive">
		  <table id="main-tab" class="table table-striped display" width="100%"></table>
		  </div>
		  
		  <div class="table-responsive" style="visibility: hidden; position: fixed;">
		  <table id="comment-tab" class="table table-striped display" width="100%"></table>
		  </div>
';

// HTML closure elements before footer
$G_html_main_right_c='
        </main>
      </div>
    </div>';

// HTML footer section with closure of <body> and <html> elements
$G_html_footer='
  </body>
</html>';


// HTML closure elements before footer
$G_html_no_permission='
        <div style="padding-top:8px;"><h2 class="FAIR-redgrey">Keine Berechtigung</h2></div>';
	
?>