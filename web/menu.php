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
  $G_html_header.= '
  <link href="css/bootstrap_red.css" rel="stylesheet">
  <!-- Custom styles for this template -->
  <link href="css/dashboard_red.css" rel="stylesheet">';
} else {
  $G_html_header.= '
  <link href="css/bootstrap.css" rel="stylesheet">
  <!-- Custom styles for this template -->
  <link href="css/dashboard.css" rel="stylesheet">';
}

$G_html_header.= '
<link href="css/symbols-fair.css" rel="stylesheet">
  </head>';

// HTML body with menu
// contains start of <body> element
$G_html_menu='<body>';
$G_html_menu_login='<body style="background-color:#ccc;">';
$G_html_menu2='<nav class="navbar navbar-inverse navbar-fixed-top FAIR-navbar">
      <div class="container-fluid">
        <div class="navbar-header">';

if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
  $G_html_menu2.='<a class="navbar-brand" href="index.php"><span style="color:#eee;">DRK</span><span class="shorten"> Covid-19 Testzentrum Odenwaldkreis</span></a>';
} elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
  $G_html_menu2.='<a class="navbar-brand" href="index.php"><span style="color:#eee;">DRK</span><span class="shorten"> Covid-19 Impfzentrum Odenwaldkreis</span></a>';
} elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
  $G_html_menu2.='<a class="navbar-brand" href="index.php"><span style="color:#eee;">DRK</span><span class="shorten"> Covid-19 Antikörpertests Odenwaldkreis</span></a>';
}
          

$G_html_menu2.='</div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
          <li><a href="https://www.drk-coronazentrum.de/">DRK Cononazentrum<span class="shorten"> ODW</span></a></li>';

$G_html_menu2.='<li><a href="impressum.php">Impressum / Datenschutz<span class="shorten"> / Kontakt</span></a></li>';


$G_html_menu2.='</ul>
        
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