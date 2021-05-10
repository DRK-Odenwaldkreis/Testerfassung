<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
April 2021


_- **************
DataTables is available under the MIT license. In short, this means that you are free to use DataTables as you wish, including modifying and redistributing the code, as long as the original copyright notice is retained.
MIT license

Copyright (C) 2008-2021, SpryMedia Ltd.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
_- **************



** ************** */

include_once 'preload.php';
if( isset($GLOBALS['G_sessionname']) ) { session_name ($GLOBALS['G_sessionname']); }
session_start();
$sec_level=1;
$current_site="prereglist";

// Include functions
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';

// role check
if( A_checkpermission(array(1,2,0,4,5)) ) {





  $today=date("Y-m-d",time());

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // show different date
    if(isset($_POST['show_times'])) {
      $u_date=($_POST['date']);
      $today=$u_date; // for overwriting default search value in box
    } elseif(isset($_POST['show_times_minus1'])){
      $u_date=($_POST['date']);
      $today=date("Y-m-d",strtotime($u_date.' - 1 days'));
    } elseif(isset($_POST['show_times_plus1'])){
      $u_date=($_POST['date']);
      $today=date("Y-m-d",strtotime($u_date.' + 1 days'));
    } elseif(isset($_POST['show_times_today'])){
      $today=$today;
    } elseif(isset($_POST['show_sensitive_data'])) {
      $today=$_POST['date'];
      $_SESSION['display_sensitive']=1;
    } elseif(isset($_POST['unshow_sensitive_data'])) {
      $today=$_POST['date'];
      $_SESSION['display_sensitive']=0;
    }
  }




    // pre settings for DataTables
    $local_dt_language="'language': {
			'info': 'Zeige _START_ bis _END_ von _TOTAL_ Einträgen',
			'infoFiltered': '(gefiltert aus _MAX_ Einträgen)',
			'search': 'Suchen:',
			select: {
				rows: {
					_: '%d Zeilen ausgewählt',
					0: '',
					1: '1 Zeile ausgewählt'
				}
			}
        },";



    // Print html header
    //echo $GLOBALS['G_html_header'];
    echo '<head>
    <title>DRK Covid-19 Testzentrum Odenwaldkreis</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-ico; charset=binary" />
    <link rel="icon" href="img/favicon.png" type="image/x-ico; charset=binary" />


    <link href="css/bootstrap.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/dashboard.css" rel="stylesheet">
    <link href="css/symbols-fair.css" rel="stylesheet">
        
    <script type="text/javascript" src="lib/datatables/jQuery-3.3.1/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="lib/datatables/Bootstrap-3.3.7/js/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css" href="lib/datatables/datatables.min.css"/>
    <script type="text/javascript" src="lib/datatables/datatables.min.js"></script>
    
    </head>';

    // Print html menu
    echo $GLOBALS['G_html_menu'];
    echo $GLOBALS['G_html_menu2'];
  
    // Print html content part A
    echo $GLOBALS['G_html_main_right_a'];

  // Open database connection
  $Db=S_open_db();

  // Get all pre registrations for today or another day
  // for all stations
  $array_tests=S_get_multientry($Db,'SELECT Voranmeldung.id, Voranmeldung.Nachname, Voranmeldung.Vorname, Voranmeldung.Adresse, Voranmeldung.Wohnort, Voranmeldung.Tag, Termine.id_station, Termine.Stunde, Termine.Slot, Station.Ort, Voranmeldung.Token, Voranmeldung.Mailadresse, Voranmeldung.Telefon FROM Voranmeldung JOIN Termine ON Voranmeldung.Termin_id=Termine.id JOIN Station ON Station.id=Termine.id_station WHERE Date(Voranmeldung.Tag)="'.$today.'" AND Voranmeldung.Used=0 AND Token IS NOT NULL ORDER BY Voranmeldung.Anmeldezeitpunkt DESC;');


  echo '<h1>Ansicht der Voranmeldungen</h1>';

  echo '<div class="card">
  <div class="row">';

  echo '<div class="col-sm-4">';
  echo '<p></p>';
  if( A_checkpermission(array(1,0,0,0,0)) && !A_checkpermission(array(0,2,0,4,5)) ) {
    // only today
    echo '<input type="date" class="form-control" placeholder="Tag wählen" aria-describedby="basic-addonA2" value="'.$today.'" name="date" disabled>';
  } else {
    // choose a day
    echo'<form action="'.$current_site.'.php" method="post">
    <div class="input-group">
    <span class="input-group-addon" id="basic-addonA2">Tag auswählen</span>
    <input type="date" class="form-control" placeholder="Tag wählen" aria-describedby="basic-addonA2" value="'.$today.'" name="date">
    <span class="input-group-btn">
    <input type="submit" class="btn btn-primary" value="Liste anzeigen" name="show_times" />
    <input type="submit" class="btn btn-default" value="- 1 Tag" name="show_times_minus1" />
    <input type="submit" class="btn btn-default" value="Heute" name="show_times_today" />
    <input type="submit" class="btn btn-default" value="+ 1 Tag" name="show_times_plus1" />
    </span>
    </div></form>';
  }

  // Button to switch between sensitive data to display
  echo'<form action="'.$current_site.'.php" method="post">
  <input type="date" value="'.$today.'" name="date" style="display: none;">
  <div class="FAIR-si-button">';
  if($_SESSION['display_sensitive']==0) {
    echo '<button type="submit" class="btn btn-primary" name="show_sensitive_data"><span class="icon-eye"></span>&nbsp;Zeige personenbezogene Daten</button>';
  } else {
    echo '<button type="submit" class="btn btn-primary active" name="unshow_sensitive_data"><span class="icon-eye-blocked"></span>&nbsp;Blende personenbezogene Daten aus</button>';
  }
  echo'
    </div></form>
  </div>';

  echo '</div>';

  echo '
  <div class="col-sm-12">
  <table class="FAIR-data" id="maintable" data-order=\'[[ 6, "asc" ]]\' data-page-length=\'1000\'>';
  
  
    echo '<thead>
    <tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Reg Transfer</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Stations-ID</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Termin</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Name</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Adresse</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Kontakt</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">(Sortiert)</td>
    </tr>
    </thead><tbody>';

  //Get list of times
  foreach($array_tests as $i) {

    // Show appointment
    if($i[7]>0) {
      // Slot was booked
      $display_appointment=sprintf('%02d', $i[7]).':'.sprintf('%02d', ( ($i[8]-1)*15 ));
    } else {
      $display_appointment='';
    }
    

    echo '
    
    <tr>
    
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">
    <a class="list-group-item list-group-item-action list-group-item-redtext" href="scan.php?scan='.$i[10].'" title="Transfer in Registrierung"><span class="icon-forward"></span>&nbsp;#'.$i[0].'</a></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><abbr title="'.$i[9].'">S'.$i[6].'/'.substr($i[9],0,16).'</abbr></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$display_appointment.'</td>
    ';
    if($_SESSION['display_sensitive']==0) {
      echo '
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><span class="FAIR-sep-l-black"></span></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><span class="FAIR-sep-l-black"></span></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><span class="FAIR-sep-l-black"></span></td>
      ';
    } else {
      echo '
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[1].', '.$i[2].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[3].'<br>'.$i[4].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[11].'<br>'.$i[12].'</td>
      ';
    }
    echo '
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><span class="text-sm">'.sprintf('%02d', $i[7]).'-'.sprintf('%01d', $i[8]).'-'.sprintf('%06d', $i[0]).'</span></td>
    ';
    echo '</tr>';
  }
  echo '</tbody></table></div></div>';


  // Initialize DataTables JavaScript
  echo "
  <script>
  $(document).ready( function () {
    $('#maintable').DataTable( {
      dom: \"frti\",
      $local_dt_language
    });
} );
  </script>
  ";



  // Close connection to database
  S_close_db($Db);

} else {
  // Print html header
  echo $GLOBALS['G_html_header'];

  // Print html menu
  echo $GLOBALS['G_html_menu'];
  echo $GLOBALS['G_html_menu2'];

  // Print html content part A
  echo $GLOBALS['G_html_main_right_a'];
  echo '<h1>KEINE BERECHTIGUNG</h1>';
}

// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];

?>
