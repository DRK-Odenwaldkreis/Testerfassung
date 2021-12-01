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
$current_site="index";


// Include functions
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';


// Print html header
echo $GLOBALS['G_html_header'];

// Print html menu
echo $GLOBALS['G_html_menu'];
echo $GLOBALS['G_html_menu2'];

// Print html content part A
echo $GLOBALS['G_html_main_right_a'];

// Select station for statistics
if($_SESSION['station_id']>0) {
    $station=$_SESSION['station_id'];
} else {
    $station=1;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['select_station'])) {
        $station=$_POST['station_id'];
    }
}




echo '<div class="row">';
echo '<div class="col-sm-6">
<h3>Vor Ort</h3>
<div class="list-group">';
foreach($_module_array1 as $key=>$a) {
    $show_entry=false;
    $show_entry_disabled=false;
    foreach($a["role"] as $b) {
        if($b>0 && $_SESSION['roles'][$b]==1) { 
            $show_entry=true;
        }
    }
    foreach($a["role-disabled"] as $b) {
        if($b>0 && $_SESSION['roles'][$b]==1) { 
            $show_entry_disabled=true;
        }
    }
    if($show_entry) { 
        echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" id="module-'.$key.'" href="'.$a["link"].'">'.$a["text"].'</a>';
    } elseif($show_entry_disabled) {
        echo '<a class="list-group-item list-group-item-action list-group-item-FAIR disabled" id="module-'.$key.'" >'.$a["text"].'</a>';
    }
}
echo '</div></div>';
echo '<div class="col-sm-6">
<h3>Verwaltung</h3>
<div class="list-group">';
foreach($_module_array2 as $key=>$a) {
    $show_entry=false;
    $show_entry_disabled=false;
    foreach($a["role"] as $b) {
        if($b>0 && $_SESSION['roles'][$b]==1) { 
            $show_entry=true;
        }
    }
    foreach($a["role-disabled"] as $b) {
        if($b>0 && $_SESSION['roles'][$b]==1) { 
            $show_entry_disabled=true;
        }
    }
    if($show_entry) { 
        echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" id="module-'.$key.'" href="'.$a["link"].'">'.$a["text"].'</a>';
    } elseif($show_entry_disabled) {
        echo '<a class="list-group-item list-group-item-action list-group-item-FAIR disabled" id="module-'.$key.'" >'.$a["text"].'</a>';
    }
}
echo '</div></div>';
echo '</div>';


// ////////////////////////////
// Test statistics

if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
  // Open database connection
  $Db=S_open_db();
  $stations_array=S_get_multientry($Db,'SELECT id, Ort FROM Station;');
  $today=date("Y-m-d",time());
  $yesterday=date("Y-m-d",time() - 60 * 60 * 24);
  $beforetwodays=date("Y-m-d",time() - 2 * 60 * 60 * 24);
  $tomorrow=date("Y-m-d",time() + 60 * 60 * 24);

  $stat_val_total_fday=S_get_entry($Db,'SELECT count(id) From Voranmeldung WHERE Date(Tag)=\''.$tomorrow.'\';');
  $stat_val_total_cwanreg_fday=S_get_entry($Db,'SELECT count(id) From Voranmeldung WHERE Date(Tag)=\''.$tomorrow.'\' AND CWA_request=1;');
  $stat_val_total_cwaareg_fday=S_get_entry($Db,'SELECT count(id) From Voranmeldung WHERE Date(Tag)=\''.$tomorrow.'\' AND CWA_request=2;');

  $stat_val_total_day=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$today.'\';');
  $stat_val_total_pocreg_day=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$today.'\' AND reg_type=\'POCREG\';');
  $stat_val_total_cwanreg_day=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$today.'\' AND CWA_request=1;');
  $stat_val_total_cwaareg_day=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$today.'\' AND CWA_request=2;');
  $stat_val_neg_day=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$today.'\' AND Ergebnis=2;');
  $stat_val_pos_day=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$today.'\' AND Ergebnis=1;');

  $stat_val_total_yday=S_get_entry($Db,'SELECT sum(Amount) From Abrechnung WHERE Date(Date)=\''.$yesterday.'\';');
  $stat_val_total_pocreg_yday=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$yesterday.'\' AND reg_type=\'POCREG\';');
  $stat_val_total_cwanreg_yday=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$yesterday.'\' AND CWA_request=1;');
  $stat_val_total_cwaareg_yday=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$yesterday.'\' AND CWA_request=2;');
  $stat_val_neg_yday=S_get_entry($Db,'SELECT sum(Negativ) From Abrechnung WHERE Date(Date)=\''.$yesterday.'\';');
  $stat_val_pos_yday=S_get_entry($Db,'SELECT sum(Positiv) From Abrechnung WHERE Date(Date)=\''.$yesterday.'\';');

  $stat_val_total_2day=S_get_entry($Db,'SELECT sum(Amount) From Abrechnung WHERE Date(Date)=\''.$beforetwodays.'\';');
  $stat_val_total_pocreg_2day=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$beforetwodays.'\' AND reg_type=\'POCREG\';');
  $stat_val_total_cwanreg_2day=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$beforetwodays.'\' AND CWA_request=1;');
  $stat_val_total_cwaareg_2day=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$beforetwodays.'\' AND CWA_request=2;');
  $stat_val_neg_2day=S_get_entry($Db,'SELECT sum(Negativ) From Abrechnung WHERE Date(Date)=\''.$beforetwodays.'\';');
  $stat_val_pos_2day=S_get_entry($Db,'SELECT sum(Positiv) From Abrechnung WHERE Date(Date)=\''.$beforetwodays.'\';');

  $stat_val_total_day_st=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$today.'\' AND Teststation='.$station.';');
  $stat_val_total_pocreg_day_st=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$today.'\' AND Teststation='.$station.' AND reg_type=\'POCREG\';');
  $stat_val_neg_day_st=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$today.'\' AND Teststation='.$station.' AND Ergebnis=2;');
  $stat_val_pos_day_st=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$today.'\' AND Teststation='.$station.' AND Ergebnis=1;');

  $stat_val_total_yday_st=S_get_entry($Db,'SELECT sum(Amount) From Abrechnung WHERE Date(Date)=\''.$yesterday.'\' AND Teststation='.$station.';');
  $stat_val_total_pocreg_yday_st=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$yesterday.'\' AND Teststation='.$station.' AND reg_type=\'POCREG\';');
  $stat_val_neg_yday_st=S_get_entry($Db,'SELECT sum(Negativ) From Abrechnung WHERE Date(Date)=\''.$yesterday.'\' AND Teststation='.$station.';');
  $stat_val_pos_yday_st=S_get_entry($Db,'SELECT sum(Positiv) From Abrechnung WHERE Date(Date)=\''.$yesterday.'\' AND Teststation='.$station.';');

  $stat_val_total_2day_st=S_get_entry($Db,'SELECT sum(Amount) From Abrechnung WHERE Date(Date)=\''.$beforetwodays.'\' AND Teststation='.$station.';');
  $stat_val_total_pocreg_2day_st=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$beforetwodays.'\' AND Teststation='.$station.' AND reg_type=\'POCREG\';');
  $stat_val_neg_2day_st=S_get_entry($Db,'SELECT sum(Negativ) From Abrechnung WHERE Date(Date)=\''.$beforetwodays.'\' AND Teststation='.$station.';');
  $stat_val_pos_2day_st=S_get_entry($Db,'SELECT sum(Positiv) From Abrechnung WHERE Date(Date)=\''.$beforetwodays.'\' AND Teststation='.$station.';');

  // Close connection to database
  S_close_db($Db);

  echo '<div class="row">';
  echo '<div class="col-md-2">
  <div class="alert alert-info" role="alert">
  <p>Getestete Personen</p>
  <h3><span class="FAIR-text-sm">heute</span> '.$stat_val_total_day.'</h3>
  <h3><span class="FAIR-text-sm">gestern</span> '.$stat_val_total_yday.'</h3>
  <h3><span class="FAIR-text-sm">vorgestern</span> '.$stat_val_total_2day.'</h3>
  </div>';

  $reg_cwan=(number_format(($stat_val_total_cwanreg_fday/$stat_val_total_fday*100),1,'.','.'));
  $reg_cwaa=(number_format(($stat_val_total_cwaareg_fday/$stat_val_total_fday*100),1,'.','.'));
  $poc_rate=($stat_val_total_pocreg_day+$stat_val_total_pocreg_yday+$stat_val_total_pocreg_2day)/($stat_val_total_day+$stat_val_total_yday+$stat_val_total_2day);
  $reg_fday_estimation=number_format(($stat_val_total_fday/(1-$poc_rate)),0,',','.');
  $reg_pre=(number_format((100-$reg_cwan-$reg_cwaa),1,'.','.'));

  echo '</div>';
  echo '<div class="col-md-2">
  <div class="alert alert-info" role="alert">
  <p>Registrierungsart</p>
  <h3><span class="FAIR-text-sm">morgen registr.'.$stat_val_total_fday.' / erwartet '.$reg_fday_estimation.'</span></h3>
  <div class="progress">
    <div title="CWA-namentlich '.$reg_cwan.' %" class="progress-bar progress-bar-success" style="width: '.$reg_cwan.'%">
      <span>CWA-n</span>
    </div>
    <div title="CWA-anonym '.$reg_cwaa.' %" class="progress-bar progress-bar-info" style="width: '.$reg_cwaa.'%">
      <span>CWA-a</span>
    </div>
    <div title="PreReg ohne CWA '.$reg_pre.' %" class="progress-bar progress-bar-danger" style="width: '.$reg_pre.'%">
      <span>PreReg</span>
    </div>
  </div>
  ';
  $reg_poc=(number_format(($stat_val_total_pocreg_day/$stat_val_total_day*100),1,'.','.'));
  $reg_cwan=(number_format(($stat_val_total_cwanreg_day/$stat_val_total_day*100),1,'.','.'));
  $reg_cwaa=(number_format(($stat_val_total_cwaareg_day/$stat_val_total_day*100),1,'.','.'));
  $reg_pre=(number_format((100-$reg_poc-$reg_cwan-$reg_cwaa),1,'.','.'));
  echo '
  <h3><span class="FAIR-text-sm">heute</span></h3>
  <div class="progress">
    <div title="PoC Reg '.$reg_poc.' %" class="progress-bar progress-bar-warning" style="width: '.$reg_poc.'%">
      <span>PoC</span>
    </div>
    <div title="CWA-namentlich '.$reg_cwan.' %" class="progress-bar progress-bar-success" style="width: '.$reg_cwan.'%">
      <span>CWA-n</span>
    </div>
    <div title="CWA-anonym '.$reg_cwaa.' %" class="progress-bar progress-bar-info" style="width: '.$reg_cwaa.'%">
      <span>CWA-a</span>
    </div>
    <div title="PreReg ohne CWA '.$reg_pre.' %" class="progress-bar progress-bar-danger" style="width: '.$reg_pre.'%">
      <span>PreReg</span>
    </div>
  </div>
  ';
  $reg_poc=(number_format(($stat_val_total_pocreg_yday/$stat_val_total_yday*100),1,'.','.'));
  $reg_cwan=(number_format(($stat_val_total_cwanreg_yday/$stat_val_total_yday*100),1,'.','.'));
  $reg_cwaa=(number_format(($stat_val_total_cwaareg_yday/$stat_val_total_yday*100),1,'.','.'));
  $reg_pre=(number_format((100-$reg_poc-$reg_cwan-$reg_cwaa),1,'.','.'));
  echo '
  <h3><span class="FAIR-text-sm">gestern</span></h3>
  <div class="progress">
    <div title="PoC Reg '.$reg_poc.' %" class="progress-bar progress-bar-warning" style="width: '.$reg_poc.'%">
      <span>PoC</span>
    </div>
    <div title="CWA-namentlich '.$reg_cwan.' %" class="progress-bar progress-bar-success" style="width: '.$reg_cwan.'%">
      <span>CWA-n</span>
    </div>
    <div title="CWA-anonym '.$reg_cwaa.' %" class="progress-bar progress-bar-info" style="width: '.$reg_cwaa.'%">
      <span>CWA-a</span>
    </div>
    <div title="PreReg ohne CWA '.$reg_pre.' %" class="progress-bar progress-bar-danger" style="width: '.$reg_pre.'%">
      <span>PreReg</span>
    </div>
  </div>
  ';
  $reg_poc=(number_format(($stat_val_total_pocreg_2day/$stat_val_total_2day*100),1,'.','.'));
  $reg_cwan=(number_format(($stat_val_total_cwanreg_2day/$stat_val_total_2day*100),1,'.','.'));
  $reg_cwaa=(number_format(($stat_val_total_cwaareg_2day/$stat_val_total_2day*100),1,'.','.'));
  $reg_pre=(number_format((100-$reg_poc-$reg_cwan-$reg_cwaa),1,'.','.'));
  echo '
  <h3><span class="FAIR-text-sm">vorgestern</span></h3>
  <div class="progress">
    <div title="PoC Reg '.$reg_poc.' %" class="progress-bar progress-bar-warning" style="width: '.$reg_poc.'%">
      <span>PoC</span>
    </div>
    <div title="CWA-namentlich '.$reg_cwan.' %" class="progress-bar progress-bar-success" style="width: '.$reg_cwan.'%">
      <span>CWA-n</span>
    </div>
    <div title="CWA-anonym '.$reg_cwaa.' %" class="progress-bar progress-bar-info" style="width: '.$reg_cwaa.'%">
      <span>CWA-a</span>
    </div>
    <div title="PreReg ohne CWA '.$reg_pre.' %" class="progress-bar progress-bar-danger" style="width: '.$reg_pre.'%">
      <span>PreReg</span>
    </div>
  </div>
  ';

  echo '</div></div>';
  echo '<div class="col-md-2">
  <div class="alert alert-success" role="alert">
  <p>Negative Fälle</p>
  <h3><span class="FAIR-text-sm">heute</span> '.$stat_val_neg_day.'</h3>
  <h3><span class="FAIR-text-sm">gestern</span> '.$stat_val_neg_yday.'</h3>
  <h3><span class="FAIR-text-sm">vorgestern</span> '.$stat_val_neg_2day.'</h3>
  </div>';

  echo '</div>';
  echo '<div class="col-md-2">
  <div class="alert alert-danger" role="alert">
  <p>Positive Fälle</p>
  <h3><span class="FAIR-text-sm">heute</span> '.$stat_val_pos_day.' ('.(number_format(($stat_val_pos_day/$stat_val_total_day*100),1,',','.')).' %)</h3>
  <h3><span class="FAIR-text-sm">gestern</span> '.$stat_val_pos_yday.' ('.(number_format(($stat_val_pos_yday/$stat_val_total_yday*100),1,',','.')).' %)</h3>
  <h3><span class="FAIR-text-sm">vorgestern</span> '.$stat_val_pos_2day.' ('.(number_format(($stat_val_pos_2day/$stat_val_total_2day*100),1,',','.')).' %)</h3>
  </div>';

  echo '</div>';

  echo '<div class="col-md-4">
      <div class="alert alert-warning" role="alert">';

  if($_SESSION['station_id']>0) {
      echo '<p>Eigene Station S'.$_SESSION['station_id'].'/'.$_SESSION['station_name'].'</p>';
  } else {
      echo '<form action="'.$current_site.'.php" method="post">
      <div class="input-group">
      <span class="input-group-addon" id="basic-addon1">Station</span>
      <select id="select-state" placeholder="Wähle eine Station..." class="custom-select" style="margin-top:0px;" name="station_id">
      <option value="">Wähle Station...</option>
          ';
          foreach($stations_array as $i) {
              $display=$i[1].' / S'.$i[0];
              if($i[0]==$station) {$selected="selected";} else {$selected="";}
              echo '<option value="'.$i[0].'" '.$selected.'>'.$display.'</option>';
          }
          echo '
      </select>
      <span class="input-group-btn">
      <input type="submit" class="btn btn-danger" value="Anzeigen" name="select_station" />
      </span>
      </div>
      </form>';
  }

  if($stat_val_total_day_st>0) {
      echo '<h3><span class="FAIR-text-sm">heute</span> '.$stat_val_total_day_st.', <span class="FAIR-text-sm">PoC Reg: '.(number_format(($stat_val_total_pocreg_day_st/$stat_val_total_day_st*100),0,',','.')).' %</span>, <span class="FAIR-text-sm">Pos:</span> '.$stat_val_pos_day_st.' ('.(number_format(($stat_val_pos_day_st/$stat_val_total_day_st*100),1,',','.')).' %)</h3>';
  } else {
      echo '<h3><span class="FAIR-text-sm">(heute keine Tests durchgeführt)</span></h3>';
  }
  if($stat_val_total_yday_st>0) {
      echo '<h3><span class="FAIR-text-sm">gestern</span> '.$stat_val_total_yday_st.', <span class="FAIR-text-sm">PoC Reg: '.(number_format(($stat_val_total_pocreg_yday_st/$stat_val_total_yday_st*100),0,',','.')).' %</span>, <span class="FAIR-text-sm">Pos:</span> '.$stat_val_pos_yday_st.' ('.(number_format(($stat_val_pos_yday_st/$stat_val_total_yday_st*100),1,',','.')).' %)</h3>';
  } else {
      echo '<h3><span class="FAIR-text-sm">(gestern keine Tests durchgeführt)</span></h3>';
  }
  if($stat_val_total_2day_st>0) {
      echo '<h3><span class="FAIR-text-sm">vorgestern</span> '.$stat_val_total_2day_st.', <span class="FAIR-text-sm">PoC Reg: '.(number_format(($stat_val_total_pocreg_2day_st/$stat_val_total_2day_st*100),0,',','.')).' %</span>, <span class="FAIR-text-sm">Pos:</span> '.$stat_val_pos_2day_st.' ('.(number_format(($stat_val_pos_2day_st/$stat_val_total_2day_st*100),1,',','.')).' %)</h3>';
  } else {
      echo '<h3><span class="FAIR-text-sm">(vorgestern keine Tests durchgeführt)</span></h3>';
  }

  echo '</div>
  </div>';
} elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
  // Open database connection
  $Db=S_open_db();
  $stations_array=S_get_multientry($Db,'SELECT Station.id, Station.Ort,Impfstoff.Kurzbezeichnung FROM Station JOIN Impfstoff ON Impfstoff.id=Station.Impfstoff_id;');
  $today=date("Y-m-d",time());
  $tomorrow=date("Y-m-d",time() + 60 * 60 * 24);
  $hourNow=date('H',time());

  $stat_val_total_fday=S_get_entry($Db,'SELECT count(id) From Voranmeldung WHERE Date(Tag)=\''.$tomorrow.'\';');
  $stat_val_total_day=S_get_entry($Db,'SELECT count(id) From Voranmeldung WHERE Date(Tag)=\''.$today.'\';');

  $stat_val_total_day_used=S_get_entry($Db,'SELECT count(id) From Voranmeldung WHERE Date(Tag)=\''.$today.'\' and Used=1;');
  $stat_val_total_day_unused=S_get_entry($Db,'SELECT count(Voranmeldung.id) From Voranmeldung JOIN Termine ON Termin_id=Termine.id WHERE Date(Voranmeldung.Tag)=\''.$today.'\' AND Voranmeldung.Used=0 AND Termine.Stunde < '.$hourNow.';');

  $stat_val_total_day_st=S_get_entry($Db,'SELECT count(Voranmeldung.id) From Voranmeldung JOIN Termine ON Termine.id=Voranmeldung.Termin_id WHERE Date(Voranmeldung.Tag)=\''.$today.'\' AND Termine.id_station='.$station.';');
  $stat_val_total_fday_st=S_get_entry($Db,'SELECT count(Voranmeldung.id) From Voranmeldung JOIN Termine ON Termine.id=Voranmeldung.Termin_id WHERE Date(Voranmeldung.Tag)=\''.$tomorrow.'\' AND Termine.id_station='.$station.';');

  $stat_val_total_day_st_used=S_get_entry($Db,'SELECT count(Voranmeldung.id) From Voranmeldung JOIN Termine ON Termine.id=Voranmeldung.Termin_id WHERE Date(Voranmeldung.Tag)=\''.$today.'\' AND Termine.id_station='.$station.' and Voranmeldung.Used=1;');
  $stat_val_total_day_st_unused=S_get_entry($Db,'SELECT count(Voranmeldung.id) From Voranmeldung JOIN Termine ON Termine.id=Voranmeldung.Termin_id WHERE Date(Voranmeldung.Tag)=\''.$today.'\' AND Termine.id_station='.$station.' AND Voranmeldung.Used=0 AND Termine.Stunde < '.$hourNow.';');


  // Close connection to database
  S_close_db($Db);

  echo '<div class="row">';
  echo '<div class="col-md-4">
  <div class="alert alert-info" role="alert">
  <p>Vorgemeldete Personen Insgesamt</p>
  <h3><span class="FAIR-text-sm">heute</span> '.$stat_val_total_day.' / Erledigt: '.$stat_val_total_day_used.'</h3>
  <h3><span class="FAIR-text-sm">Nicht erschienen bis '.$hourNow.' Uhr: </span> '.$stat_val_total_day_unused.'</h3>
  <h3><span class="FAIR-text-sm">morgen</span> '.$stat_val_total_fday.'</h3>
  </div>';

  echo '</div>';

  echo '<div class="col-md-4">
      <div class="alert alert-warning" role="alert">';

  if($_SESSION['station_id']>0) {
      echo '<p>Eigene Station S'.$_SESSION['station_id'].'/'.$_SESSION['station_name'].'</p>';
  } else {
      echo '<form action="'.$current_site.'.php" method="post">
      <div class="input-group">
      <span class="input-group-addon" id="basic-addon1">Station</span>
      <select id="select-state" placeholder="Wähle eine Station..." class="custom-select" style="margin-top:0px;" name="station_id">
      <option value="">Wähle Station...</option>
          ';
          foreach($stations_array as $i) {
              $display=$i[1].' - '.$i[2];
              if($i[0]==$station) {$selected="selected";} else {$selected="";}
              echo '<option value="'.$i[0].'" '.$selected.'>'.$display.'</option>';
          }
          echo '
      </select>
      <span class="input-group-btn">
      <input type="submit" class="btn btn-danger" value="Anzeigen" name="select_station" />
      </span>
      </div>
      </form>';
  }

  if($stat_val_total_day_st>0) {
      echo '<h3><span class="FAIR-text-sm">heute</span> '.$stat_val_total_day_st.' / Erledigt: '.$stat_val_total_day_st_used.'</h3>';
      echo '<h3><span class="FAIR-text-sm">Nicht erschienen bis '.$hourNow.' Uhr: </span> '.$stat_val_total_day_st_unused.'</h3>';
  } else {
      echo '<h3><span class="FAIR-text-sm">(heute keine Impfungen geplant)</span></h3>';
  }

  if($stat_val_total_fday_st>0) {
      echo '<h3><span class="FAIR-text-sm">morgen</span> '.$stat_val_total_fday_st.'</h3>';
  } else {
      echo '<h3><span class="FAIR-text-sm">(Morgen keine Impfungen geplant)</span></h3>';
  }

  echo '</div>
  </div>';
} elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
  // Open database connection
  $Db=S_open_db();
  $stations_array=S_get_multientry($Db,'SELECT Station.id, Station.Ort FROM Station;');
  $today=date("Y-m-d",time());
  $tomorrow=date("Y-m-d",time() + 60 * 60 * 24);
  $hourNow=date('H',time());

  $stat_val_total_fday=S_get_entry($Db,'SELECT count(id) From Voranmeldung WHERE Date(Tag)=\''.$tomorrow.'\';');
  $stat_val_total_day=S_get_entry($Db,'SELECT count(id) From Voranmeldung WHERE Date(Tag)=\''.$today.'\';');

  $stat_val_total_day_used=S_get_entry($Db,'SELECT count(id) From Voranmeldung WHERE Date(Tag)=\''.$today.'\' and Used=1;');
  $stat_val_total_day_unused=S_get_entry($Db,'SELECT count(Voranmeldung.id) From Voranmeldung JOIN Termine ON Termin_id=Termine.id WHERE Date(Voranmeldung.Tag)=\''.$today.'\' AND Voranmeldung.Used=0 AND Termine.Stunde < '.$hourNow.';');

  $stat_val_total_day_st=S_get_entry($Db,'SELECT count(Voranmeldung.id) From Voranmeldung JOIN Termine ON Termine.id=Voranmeldung.Termin_id WHERE Date(Voranmeldung.Tag)=\''.$today.'\' AND Termine.id_station='.$station.';');
  $stat_val_total_fday_st=S_get_entry($Db,'SELECT count(Voranmeldung.id) From Voranmeldung JOIN Termine ON Termine.id=Voranmeldung.Termin_id WHERE Date(Voranmeldung.Tag)=\''.$tomorrow.'\' AND Termine.id_station='.$station.';');

  $stat_val_total_day_st_used=S_get_entry($Db,'SELECT count(Voranmeldung.id) From Voranmeldung JOIN Termine ON Termine.id=Voranmeldung.Termin_id WHERE Date(Voranmeldung.Tag)=\''.$today.'\' AND Termine.id_station='.$station.' and Voranmeldung.Used=1;');
  $stat_val_total_day_st_unused=S_get_entry($Db,'SELECT count(Voranmeldung.id) From Voranmeldung JOIN Termine ON Termine.id=Voranmeldung.Termin_id WHERE Date(Voranmeldung.Tag)=\''.$today.'\' AND Termine.id_station='.$station.' AND Voranmeldung.Used=0 AND Termine.Stunde < '.$hourNow.';');


  // Close connection to database
  S_close_db($Db);

  echo '<div class="row">';
  echo '<div class="col-md-4">
  <div class="alert alert-info" role="alert">
  <p>Vorgemeldete Personen</p>
  <h3><span class="FAIR-text-sm">heute</span> '.$stat_val_total_day.' / Erledigt: '.$stat_val_total_day_used.'</h3>
  <h3><span class="FAIR-text-sm">Nicht erschienen bis '.$hourNow.' Uhr: </span> '.$stat_val_total_day_unused.'</h3>
  <h3><span class="FAIR-text-sm">morgen</span> '.$stat_val_total_fday.'</h3>
  </div>';

  echo '</div>';

  echo '<div class="col-md-4">
      <div class="alert alert-warning" role="alert">';

  if($_SESSION['station_id']>0) {
      echo '<p>Eigene Station S'.$_SESSION['station_id'].'/'.$_SESSION['station_name'].'</p>';
  } else {
      echo '<form action="'.$current_site.'.php" method="post">
      <div class="input-group">
      <span class="input-group-addon" id="basic-addon1">Station</span>
      <select id="select-state" placeholder="Wähle eine Station..." class="custom-select" style="margin-top:0px;" name="station_id">
      <option value="">Wähle Station...</option>
          ';
          foreach($stations_array as $i) {
              $display=$i[1].'';
              if($i[0]==$station) {$selected="selected";} else {$selected="";}
              echo '<option value="'.$i[0].'" '.$selected.'>'.$display.'</option>';
          }
          echo '
      </select>
      <span class="input-group-btn">
      <input type="submit" class="btn btn-danger" value="Anzeigen" name="select_station" />
      </span>
      </div>
      </form>';
  }

  if($stat_val_total_day_st>0) {
      echo '<h3><span class="FAIR-text-sm">heute</span> '.$stat_val_total_day_st.' / Erledigt: '.$stat_val_total_day_st_used.'</h3>';
      echo '<h3><span class="FAIR-text-sm">Nicht erschienen bis '.$hourNow.' Uhr: </span> '.$stat_val_total_day_st_unused.'</h3>';
  } else {
      echo '<h3><span class="FAIR-text-sm">(heute keine Antikoerpertests geplant)</span></h3>';
  }

  if($stat_val_total_fday_st>0) {
      echo '<h3><span class="FAIR-text-sm">morgen</span> '.$stat_val_total_fday_st.'</h3>';
  } else {
      echo '<h3><span class="FAIR-text-sm">(Morgen keine Antikoerpertests geplant)</span></h3>';
  }

  echo '</div>
  </div>';
}







// Test statistics
// ////////////////////////////


echo '</div></div>';



// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];

?>