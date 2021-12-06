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


  // Create XLSX export file
  $val_report_display=0;
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if(isset($_POST['create_export_csv'])) {
          $date=($_POST['date']);
          if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
            $dir="/home/webservice/Impfterminerfassung/CSVExport/";
          } elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
            $dir="/home/webservice/Antikoerpererfassung/CSVExport/";
          }
          chdir($dir);
          $job="python3 job.py $date";
          exec($job,$script_output);
          $file=$script_output[0];
          if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
            $filename="/home/webservice/Reports/Impfzentrum/$file";
          } elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
            $filename="/home/webservice/Reports/Antikoerper/$file";
          }
          if( file_exists($filename) ) {
              header('Content-Type: application/octet-stream');
              header('Content-Disposition: attachment; filename="'.basename($file).'"');
              header('Pragma: no-cache');
              header('Expires: 0');
              readfile($filename);
              exit;
          }
          
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


    ';

    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
      echo '
      <link href="css/bootstrap_red.css" rel="stylesheet">
      <!-- Custom styles for this template -->
      <link href="css/dashboard_red.css" rel="stylesheet">';
    } else {
      echo '
      <link href="css/bootstrap.css" rel="stylesheet">
      <!-- Custom styles for this template -->
      <link href="css/dashboard.css" rel="stylesheet">';
    }
    
    echo '
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
  if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
    $array_tests=S_get_multientry($Db,'SELECT Voranmeldung.id, Voranmeldung.Nachname, Voranmeldung.Vorname, Voranmeldung.Adresse, Voranmeldung.Wohnort, Voranmeldung.Tag, Termine.id_station, Termine.Stunde, Termine.Slot, Station.Ort, Voranmeldung.Token, Voranmeldung.Mailadresse, Voranmeldung.Telefon, Kosten_PCR.Kurzbezeichnung, Voranmeldung.CWA_request FROM Voranmeldung JOIN Termine ON Voranmeldung.Termin_id=Termine.id JOIN Station ON Station.id=Termine.id_station LEFT OUTER JOIN Kosten_PCR ON Kosten_PCR.id=Voranmeldung.PCR_Grund WHERE Date(Voranmeldung.Tag)="'.$today.'" AND Voranmeldung.Used=0 AND Token IS NOT NULL ORDER BY Voranmeldung.Anmeldezeitpunkt DESC;');
  } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
    $array_tests=S_get_multientry($Db,'SELECT Voranmeldung.id, Voranmeldung.Nachname, Voranmeldung.Vorname, Voranmeldung.Geburtsdatum, Voranmeldung.Booster, Voranmeldung.Tag, Termine.id_station, Termine.Stunde, Termine.Slot, Station.Ort, Voranmeldung.Token, Voranmeldung.Mailadresse, Voranmeldung.Telefon, Voranmeldung.Used, Impfstoff.Kurzbezeichnung, Voranmeldung.Anmeldezeitpunkt, ROW_NUMBER() OVER() FROM Voranmeldung JOIN Termine ON Voranmeldung.Termin_id=Termine.id JOIN Station ON Station.id=Termine.id_station JOIN Impfstoff ON Impfstoff.id=Station.Impfstoff_id WHERE Date(Voranmeldung.Tag)="'.$today.'" AND Token IS NOT NULL ORDER BY Termine.Stunde,Termine.Slot ASC;');
  } elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
    $array_tests=S_get_multientry($Db,'SELECT Voranmeldung.id, Voranmeldung.Nachname, Voranmeldung.Vorname, 0, 0, Voranmeldung.Tag, Termine.id_station, Termine.Stunde, Termine.Slot, Station.Ort, Voranmeldung.Token, Voranmeldung.Mailadresse, Voranmeldung.Telefon, Voranmeldung.Used, 0, ROW_NUMBER() OVER() FROM Voranmeldung JOIN Termine ON Voranmeldung.Termin_id=Termine.id JOIN Station ON Station.id=Termine.id_station WHERE Date(Voranmeldung.Tag)="'.$today.'" AND Token IS NOT NULL ORDER BY Termine.Stunde,Termine.Slot ASC;');
  }


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

  
  
  if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
    echo '
    <div class="col-sm-12">
    <table class="FAIR-data" id="maintable" data-order=\'[[ 7, "asc" ]]\' data-page-length=\'1000\'>';
    echo '<thead>
    <tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Reg Transfer</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Stations-ID</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Termin</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Name</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Adresse</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Kontakt</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">Info: PCR / CWA</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">(Sortiert)</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"></td>
    </tr>
    </thead><tbody>';
  } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
    echo '
    <div class="col-sm-12">
    <table class="FAIR-data" id="maintable" data-order=\'[[ 6, "asc" ]]\' data-page-length=\'1000\'>';
    echo '<thead>
    <tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Person bestätigen</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Station</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Termin</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Name</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Kontakt</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Impfdaten</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">(Sortiert)</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"></td>
    </tr>
    </thead><tbody>';
  } else {
    echo '
    <div class="col-sm-12">
    <table class="FAIR-data" id="maintable" data-order=\'[[ 5, "asc" ]]\' data-page-length=\'1000\'>';
    echo '<thead>
    <tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Person bestätigen</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Station</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Termin</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Name</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Kontakt</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">(Sortiert)</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">Impfdaten</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"></td>
    </tr>
    </thead><tbody>';
  }
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
    
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">';
    
    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
      echo '<a class="list-group-item list-group-item-action list-group-item-redtext" href="scan.php?scan='.$i[10].'" title="Transfer in Registrierung"><span class="icon-forward"></span>&nbsp;#'.$i[0].'</a></td>';
    } else {
      if($i[13]==0) {
        echo '<a class="list-group-item list-group-item-action list-group-item-redtext" target="_blank" href="confirm.php?id='.$i[10].'" title="Person bestätigen"><span class="icon-forward"></span>&nbsp;#'.$i[0].'</a></td>';
      } else {
        echo '<span class="icon-checkmark2"></span>&nbsp;Bestätigt</td>';
      }
    }
    if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
      $vaccination_label=$i[14].'/';
    }  else {
      $vaccination_label='';
    }
    echo'<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><abbr title="'.$i[9].'">'.$vaccination_label.'S'.$i[6].'/'.substr($i[9],0,16).'</abbr></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$display_appointment.'</td>
    ';
    if($_SESSION['display_sensitive']==0) {
      echo '
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><span class="FAIR-sep-l-black"></span></td>';
      if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        echo '
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><span class="FAIR-sep-l-black"></span></td>';
      }
      echo '
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><span class="FAIR-sep-l-black"></span></td>
      ';
      if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
        echo '
        <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><span class="FAIR-sep-l-black"></span></td>
      ';
      }
    } else {
      echo '
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[1].', '.$i[2];
      if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
        echo '<br>geb. '.$birthDate;
      }
      echo '</td>';
      if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        echo '
        <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[3].'<br>'.$i[4].'</td>';
      }
      echo '
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[11].'<br>'.$i[12].'</td>
      ';
      if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
        // Impfdaten
        // if registered before this date - show no specific data
        $cutoff="2021-12-27";
        if( $cutoff<=substr($i[15],0,10) ) {
          $birthDate=explode("-",$i[3]);
          $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[2], $birthDate[0]))) > date("md")
            ? ((date("Y") - $birthDate[0]) - 1)
            : (date("Y") - $birthDate[0]));
          $booster = ($i[4]) ? "<span class=\"FAIR-text-blue\">Booster</span>" : "<span class=\"FAIR-text-green\">Grundimmun.</span>";
          echo '
          <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">Alter: '.$age.'<br>Impfstoff: '.$i[14].'<br>'.$booster.'</td>';
        } else {
          echo '
          <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">Alter: --<br>Impfstoff: '.$i[14].'<br>--</td>';
        }
        
      }
    }
    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
      if($i[14]==1) {$display_CWA='CWA-n';} elseif($i[14]==1) {$display_CWA='CWA-a';} else {$display_CWA='';}
      if($i[13]!=null) {$display_PCR='PCR: '.$i[13];} else {$display_PCR='';}
      echo '
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$display_PCR.''.$display_CWA.'</td>';
    }
    echo '
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><span class="text-sm">'.sprintf('%02d', $i[7]).'-'.sprintf('%01d', $i[8]).'-'.sprintf('%06d', $i[0]).'</span></td>';
    
    if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
      echo '<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">
      <a target="_blank" class="list-group-item list-group-item-action list-group-item-redtext" href="../registration/index.php?cancel=cancel&t='.$i[10].'&i='.$i[0].'" title="Registrierung löschen"><span class="icon-remove2"></span>&nbsp;Löschen</a><a class="list-group-item list-group-item-action list-group-item-redtext" href="edit_person.php?id='.$i[0].'"><span class="icon-pencil"></span>&nbsp;Ändern</a></td>
      ';
    } elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
      echo '
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">
      <a target="_blank" class="list-group-item list-group-item-action list-group-item-redtext" href="../registration/index.php?cancel=cancel&t='.$i[10].'&i='.$i[0].'" title="Registrierung löschen"><span class="icon-remove2"></span>&nbsp;Löschen</a><a class="list-group-item list-group-item-action list-group-item-redtext" href="edit_person.php?id='.$i[0].'"><span class="icon-pencil"></span>&nbsp;Ändern</a></td>
        ';
    } else {
      echo '<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">
      <a target="_blank" class="list-group-item list-group-item-action list-group-item-redtext" href="../registration/index.php?cancel=cancel&t='.$i[10].'&i='.$i[0].'" title="Registrierung löschen"><span class="icon-remove2"></span>&nbsp;Löschen</a></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"></td>
      ';
    }
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



if($GLOBALS['FLAG_MODE_MAIN'] == 2 or $GLOBALS['FLAG_MODE_MAIN'] == 3) {
  // Get XLSX file
  echo '<div class="card">
      <div class="col-sm-4">
      <h3>Export</h3>
      <p></p>';
      echo '<form action="'.$current_site.'.php" method="post">
      <div class="input-group">
        <span class="input-group-addon" id="basic-addonA2">Tag auswählen</span>
        <input type="date" class="form-control" placeholder="Tag wählen" aria-describedby="basic-addonA2" value="'.$today.'" name="date">
        </div>
          <input type="submit" class="btn btn-danger" value="Export Excel-Datei" name="create_export_csv" />
          </span>
          </div>
          </form>';
      echo $errorhtml0;
    
      echo '</div></div>';

  echo '</div>';
}


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
