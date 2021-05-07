<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021



** ************** */

include_once 'preload.php';
if( isset($GLOBALS['G_sessionname']) ) { session_name ($GLOBALS['G_sessionname']); }
session_start();
$sec_level=1;
$current_site="testkarten";

// Include functions
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';

// role check
if( A_checkpermission(array(0,2,0,4,5)) ) {

    $errorhtml0 ='';
    $display_creating_testkarten=false;

    // Create testkarten
    $val_report_display=0;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if(isset($_POST['create_testkarten'])) {
            $amount=($_POST['amount']);
            
            $dir="/home/webservice/Testerfassung/QRGeneration/";
            chdir($dir);
            $job="python3 job.py $amount > /dev/null &";
            exec($job,$script_output);
            $errorhtml0 = H_build_boxinfo( 0, "Testkarten werden erstellt und Datei wird in der Downloadliste in wenigen Minuten angezeigt. Dies kann einen Augenblick dauern in Abhängigkeit der angeforderten Karten.", 'green' );
            $display_creating_testkarten=true;
            
        }
    }

    // Print html header
    echo $GLOBALS['G_html_header'];

    // Print html menu
    echo $GLOBALS['G_html_menu'];
    echo $GLOBALS['G_html_menu2'];

    // Print html content part A
    echo $GLOBALS['G_html_main_right_a'];

    echo '<h1>Testkarten</h1>';


    echo '<div class="row">';

    if($display_creating_testkarten) {
      echo '<div class="card">
      <div class="col-sm-6">
      <h3>Neue Testkarten erstellen</h3>
      <p></p>';
      echo $errorhtml0;
      echo '<a class="list-group-item list-group-item-action list-group-item-redtext" href="'.$current_site.'.php">Zurück zur Downloadliste</a>';
      echo '</div></div>';

    } else {

      echo '<div class="card">
      <div class="col-sm-6">
      <h3>Neue Testkarten erstellen</h3>
      <p class="list-group-item-text">Zum Erstellen hier Menge an Testkarten eingeben.</p><p></p>';
      echo '<form action="'.$current_site.'.php" method="post">
          <div class="input-group">
          <span class="input-group-addon" id="basic-addon4">Anzahl</span>
          <input type="number" min="1" max="5000" class="form-control" placeholder="Anzahl" aria-describedby="basic-addon4" name="amount">
          <span class="input-group-btn">
          <input type="submit" class="btn btn-danger" value="Testkarten erstellen" name="create_testkarten" />
          </span>
          </div>
          </form>';
          echo $errorhtml0;
      echo '</div></div>';

      echo '<div class="card">
      <div class="col-sm-6">
      <h3>Download Testkarten</h3>
      <p></p>';
      echo '<p>';
      //Get list of files
      $log_path="/home/webservice/Testkarten/";
      $array_files=scandir($log_path);
      foreach($array_files as $a) {
          if( preg_match('/.pdf/',$a) || preg_match('/.zip/',$a) ) {
              echo '<a class="list-group-item list-group-item-action list-group-item-redtext" href="download.php?dir=t&file='.$a.'">'.$a.'<span class="FAIR-sep-l"></span><span class="FAIR-text-med">(Erstellt: '.date ("d.m.Y H:i", filemtime($log_path.$a)).' / '.(number_format(filesize($log_path.$a)/1024,1,',','.')).' KB)</span></a>';
          }
          
      }
      echo '</p>';
      echo '</div></div>';

        // Number of available Testkarten
        // Open database connection
        $Db=S_open_db();
        $stat_val_unused=S_get_entry($Db,'SELECT count(id) From Kartennummern WHERE Used!=1;');
        $stat_val_highest=S_get_entry($Db,'SELECT id From Kartennummern ORDER BY id DESC;');
        // Close connection to database
        S_close_db($Db);
        echo '<div class="row">';
        echo '<div class="col-sm-4">
        <div class="alert alert-info" role="alert">
        <p>Unbenutzte Testkarten im Umlauf</p>
        <h3>'.$stat_val_unused.' <span class="FAIR-text-sm">Karten</span></h3>
        </div>';
        echo '</div>';
        echo '<div class="col-sm-4">
        <div class="alert alert-info" role="alert">
        <p>Höchste Testkartennummer</p>
        <h3>K'.$stat_val_highest.' <span class="FAIR-text-sm"></span></h3>
        </div>';
        echo '</div></div>';

    }

    echo '</div>';
      
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