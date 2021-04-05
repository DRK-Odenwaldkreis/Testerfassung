<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

tools

** ************** */

/****************************************/
/* SQL functions */
/****************************************/

// Open DB connection
function S_open_db () {
	/* Database connection information */
	$gaSql=$GLOBALS['gaSql_server'];
	
	// Connect to DB
	$link=mysqli_connect($gaSql['server'],$gaSql['user'],$gaSql['password'],$gaSql['db']);
	if (!$link) {
		echo "<br>Fehler: konnte nicht mit MySQL verbinden.";
		echo "<br>Debug-Fehlernummer: " . mysqli_connect_errno();
		echo "<br>Debug-Fehlermeldung: " . mysqli_connect_error();
		echo "<br>";
		exit;
	}

	if (!$link) {
		header('Location: error.php?e=err80');
	}

	// Return the database object
	return $link;
}

// Close DB connection
function S_close_db ($Db) {
	mysqli_close($Db);
	return 0;
}


// Return query result from SQL database - first entry only
function S_get_entry ($Db,$sQuery) {
	$result = mysqli_query( $Db, $sQuery );
	$r = mysqli_fetch_all($result);

	// Return result of SQL query
	return $r[0][0];
}
// Only for login
function S_get_entry_login_username ($Db,$username) {
	$stmt=mysqli_prepare($Db,"SELECT id FROM li_user WHERE lower(username)=?;");
	mysqli_stmt_bind_param($stmt, "s", $username);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $id);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);

	// Return result of SQL query
	return $id;
}
// Only for login
function S_get_entry_login_firmencode ($Db,$firmencode) {
	$stmt=mysqli_prepare($Db,"SELECT id FROM Station WHERE Firmencode=?;");
	mysqli_stmt_bind_param($stmt, "s", $firmencode);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $id);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);

	// Return result of SQL query
	return $id;
}
// Return query result from SQL database - all entries
function S_get_multientry ($Db,$sQuery) {
	$result = mysqli_query( $Db, $sQuery );
	$r = mysqli_fetch_all($result);

	// Return result of SQL query
	return $r;
}
// Write data
function S_set_data ($Db,$sQuery) {
    $r = mysqli_query( $Db, $sQuery );
	
	// Return result of SQL query
	return $r;
}


function S_get_entry_vorgang ($Db,$scanevent) {
	$token=substr($scanevent, strrpos($scanevent, 'K' )+1);
	$stmt=mysqli_prepare($Db,"SELECT id, Used FROM Kartennummern WHERE id=?;");
	mysqli_stmt_bind_param($stmt, "i", $token);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $id_kartennummer, $used);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	// Check if number exists in table Kartennummern
	if($used==1) {
		// Karte bereits verwendet mit Ergebnis
		return "Used";
	} elseif($id_kartennummer>0) {
		$stmt=mysqli_prepare($Db,"SELECT id FROM Vorgang WHERE Token=?;");
		mysqli_stmt_bind_param($stmt, "s", $token);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $id);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);

		// Return result of SQL query
		return $id;
	} else {
		return "Not registered";
	}
}

function S_set_entry_voranmeldung ($Db,$array_data) {
	// First, check if Termin_id is already used
	$stmt=mysqli_prepare($Db,"SELECT id, Slot FROM Termine WHERE id=?;");
	mysqli_stmt_bind_param($stmt, "i", $array_data[6]);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $termin_id, $termin_slot);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	if($termin_slot>0) {
		$check_termin_id=S_get_entry($Db,'SELECT id FROM Voranmeldung WHERE Termin_id=CAST('.$termin_id.' as int);');
	} else {
		$check_termin_id=0;
	}

	if($check_termin_id>0) {
		return 0;
	} else {
		// Write data because Termin_id is not used or Termin_id has no slots
		if($termin_slot>0) {
			S_set_data($Db,'UPDATE Termine SET Used=1 WHERE id=CAST('.$termin_id.' as int);');
		}
		$stmt=mysqli_prepare($Db,"INSERT INTO Voranmeldung (Vorname, Nachname, Geburtsdatum, Adresse, Telefon, Mailadresse, Termin_id, Tag) VALUES (?,?,?,?,?,?,?,?);");
		mysqli_stmt_bind_param($stmt, "ssssssis", $array_data[0], $array_data[1], $array_data[2], $array_data[3], $array_data[4], $array_data[5], $array_data[6], $array_data[7]);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $result);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);



		$stmt=mysqli_prepare($Db,"SELECT id FROM Voranmeldung WHERE Vorname=? AND Nachname=? AND Geburtsdatum=? AND Adresse=? AND Telefon=? AND Mailadresse=? AND Termin_id=? AND Tag=?;");
		mysqli_stmt_bind_param($stmt, "ssssssis", $array_data[0], $array_data[1], $array_data[2], $array_data[3], $array_data[4], $array_data[5], $array_data[6], $array_data[7]);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $result2);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
		return $result2; // need id as a return value
	}
}

function S_get_entry_voranmeldung ($Db,$array_data) {
	$stmt=mysqli_prepare($Db,"SELECT id_preregistration FROM Voranmeldung_Verif WHERE id_preregistration=? AND Token=?;");
	mysqli_stmt_bind_param($stmt, "is", $array_data[0], $array_data[1]);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $id);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	return $id;
}


/****************************************/
/* Auxilliary functions */
/****************************************/

// Generate random token
function A_generate_token($length = 8) {
		// without 0, O, o, z, Z, y, Y, l
    $characters = '123456789abcdefghijkmnpqrstuvwxABCDEFGHIJKLMNPQRSTUVWX';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

// Login for user with $uid
function A_login_firmencode($Db,$sid) {
    
	$_SESSION['b2b_id'] = $sid;
	if($_SESSION['b2b_id']=='') { die("Error in database. (Err:102)"); }
	
	$_SESSION['b2b_signedin'] = true;
	$_SESSION['b2b_username'] = S_get_entry($Db,'SELECT Ort FROM Station WHERE id='.$sid.';');

    return true;
}


/****************************************/
/* HTML code snippets */
/****************************************/

function H_build_boxhead( $w, $id, $title, $j=0 ) {
	if($w>0) {
		$w_string='width: '.$w.'px;';
		$w_string50='width: '.($w-50).'px;';
		$w_string10='width: '.($w-10).'px;';
	}
	$class_add='';
	$margin_add='';
	$margin_add='margin-right:15px;';
	$html_result = '<div style="float:none; '.$margin_add.'"><div class="FAIR-box-head'.$class_add.'" style="'.$w_string.' position: relative; z-index:'.(50-$j).';">
  <div class="FAIR-foldbox-head-left" style="display: inline; '. $w_string50.'">
  '.$title.'
  </div>
  <div class="FAIR-foldbox-head-right" style="display: inline; width: 50px;">&nbsp;
  </div></div>
  <div class="FAIR-foldbox-static '.$class_add.'" id="'.$id.'" style="display: block; '.$w_string.' position: relative; z-index: '.(49-$j).';">';
	return $html_result;
}
// return html code for info card after foldbox header
function H_build_boxinfo( $w, $text, $c='red' ) {
	if($c=='red') { $class='alert alert-danger'; }
	elseif($c=='blue') { $class='alert alert-info'; }
	elseif($c=='green') { $class='alert alert-success'; }
	else { $class='alert alert-warning'; }
	if($w==0) {
		$html_result = '<div class="'.$class.'" style="top: -5px">';
	} else {
		$html_result = '<div class="'.$class.'" style="width: '. ($w-20) .'px; left: -10px; top: -5px">';
	}
	$html_result .= '<p style="margin-right: 4px;">'.$text.'</p>';
	$html_result .= '</div>';
	return $html_result;
}
// return html code for box style
function H_build_boxheadinner( $w, $id, $title, $j=0 ) {
	$html_result = '<div style="float: none; margin-right:15px;"><div class="FAIR-box-head-inner" style="width: '.$w.'px; position: relative; z-index:'.(50-$j).';">
  <div class="FAIR-foldbox-head-left" style="display: inline; width: '. ($w-50) .'px;">
  '.$title.'
  </div>
  <div class="FAIR-foldbox-head-right" style="display: inline; width: 50px;">
  </div></div>
  <div class="FAIR-foldbox-static-inner" id="'.$id.'" style="display: block; width: '. ($w-10) .'px; position: relative; z-index: '.(49-$j).';">';
	return $html_result;
}

function H_build_boxfoot( ) {
	$html_result = '</div></div>';
	return $html_result;
}

function H_build_table_testdates( ) {
	$res='';
	$Db=S_open_db();
	$flag_prereg=S_get_entry($Db,'SELECT value FROM website_settings WHERE name="FLAG_Pre_registration";');
	$stations_array=S_get_multientry($Db,'SELECT id, Ort, Adresse FROM Station WHERE Firmencode is null OR Firmencode="";');
	// X ist Anzahl an Tagen für Vorschau in Tabelle
	$X=14;
	// Ohne Terminbuchung für nächste X Tage
	$today=date('Y-m-d');
	$in_x_days=date('Y-m-d', strtotime($today. ' + '.$X.' days'));
	

	// Table
	$res.='
	<table class="FAIR-data">
	<tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-gray"><h4>Ort</h4></td>';
	for($j=0;$j<$X;$j++) {
		$string_date=date('d.m.', strtotime($today. ' + '.$j.' days'));
		$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-gray"><h4>'.$string_date.'</h4></td>';
	}
	$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-gray"></td></tr>';

	$res.='<tr>
    <td class="FAIR-data-height1 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue1" colspan="'.($X+2).'"><b><i>';
	if($flag_prereg!=0) {
		//$res.='Für folgende Teststationen ist keine Terminbuchung möglich, eine Voranmeldung kann gerne gemacht werden';
		$res.='Für folgende Teststationen ist keine Voranmeldung notwendig';
	} else {
		$res.='Für folgende Teststationen ist keine Voranmeldung notwendig';
	}
	$res.='</i></b></td>
	</tr>';
	foreach($stations_array as $st) {
		// check if station has appointed times
		if( S_get_entry($Db,'SELECT id_station FROM Termine WHERE Slot is null AND Date(Tag)>="'.$today.'" AND Date(Tag)<="'.$in_x_days.'" AND id_station='.$st[0].';')==$st[0]) {
			$res.='<tr>';
			$string_location='<b>'.$st[1].'</b><br>'.$st[2].'';
			$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue2">'.$string_location.'</td>';
			for($j=0;$j<$X;$j++) {
				$in_j_days=date('Y-m-d', strtotime($today. ' + '.$j.' days'));
				$array_termine_open=S_get_multientry($Db,'SELECT Startzeit, Endzeit, opt_station, opt_station_adresse FROM Termine WHERE Slot is null AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Startzeit ASC;');
				$string_times='';
				$string_location_opt='';
				foreach($array_termine_open as $te) {
					if($te[2]!='') {
						$string_times.='<span class="text-sm">'.$te[2].',<br>'.$te[3].'</span><br>';
					}
					$string_times.=date('H:i',strtotime($te[0])).' - '.date('H:i',strtotime($te[1])).'<br>';
				}
				if($string_times!='') {
					$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-blue2">'.$string_times.'</td>';
				} else {
					$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-blue3"></td>';
				}
			}
			$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-blue3"></td>';
			$res.='</tr>';
		}
		
	}

	/* if($flag_prereg!=0) {

		$res.='<tr>
		<td class="FAIR-data-height1 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow1" colspan="'.($X+2).'"><b><i>Bei folgenden Teststationen ist eine Voranmeldung und Terminbuchung erforderlich</i></b></td>
		</tr>';
		foreach($stations_array as $st) {
			// check if station has appointed times
			if( S_get_entry($Db,'SELECT id_station FROM Termine WHERE Slot is null AND Date(Tag)>="'.$today.'" AND Date(Tag)<="'.$in_x_days.'" AND id_station='.$st[0].';')==$st[0]) {
				$res.='<tr>';
				$string_location='<b>'.$st[1].'</b><br>'.$st[2].'';
				$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow2">'.$string_location.'</td>';
				for($j=0;$j<$X;$j++) {
					$in_j_days=date('Y-m-d', strtotime($today. ' + '.$j.' days'));
					$array_termine_open=S_get_multientry($Db,'SELECT Startzeit, Endzeit, opt_station, opt_station_adresse FROM Termine WHERE Slot is null AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Startzeit ASC;');
					$string_times='';
					foreach($array_termine_open as $te) {
						if($te[2]!='') {
							$string_times.='<span class="text-sm">'.$te[2].',<br>'.$te[3].'</span><br>';
						}
						$string_times.=date('H:i',strtotime($te[0])).' - '.date('H:i',strtotime($te[1])).'<br>';
					}
					if($string_times!='') {
						$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-yellow2">'.$string_times.'</td>';
					} else {
						$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-yellow3"></td>';
					}
				}
				$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-yellow2">Weitere Termine</td>';
				$res.='</tr>';
			}
			
		}
	} */
	
	$res.='<tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-gray"><h4>Ort</h4></td>';
	for($j=0;$j<$X;$j++) {
		$string_date=date('d.m.', strtotime($today. ' + '.$j.' days'));
		$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-gray"><h4>'.$string_date.'</h4></td>';
	}
	$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-gray"></td></tr>';
    
	$res.='</table>
	';

	S_close_db($Db);
	return $res;
}

function H_build_table_testdates2( $mode ) {
	if($mode=='b2b') {
		$query_b2b='id='.$_SESSION['b2b_id'];
		$path_to_reg='';
	} else {
		$query_b2b='Firmencode is null OR Firmencode=""';
		$path_to_reg='registration/';
		
	}
	$res='';
	$Db=S_open_db();
	$flag_prereg=S_get_entry($Db,'SELECT value FROM website_settings WHERE name="FLAG_Pre_registration";');
	$stations_array=S_get_multientry($Db,'SELECT id, Ort, Adresse FROM Station WHERE '.$query_b2b.';');
	// X ist Anzahl an Tagen für Vorschau in Tabelle
	$X=14;
	// Ohne Terminbuchung für nächste X Tage
	$today=date('Y-m-d');
	$in_x_days=date('Y-m-d', strtotime($today. ' + '.$X.' days'));
	

	// Table
	$res.='
	<table class="FAIR-data">
	<tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-gray"><h4>Ort</h4></td>';
	for($j=0;$j<$X;$j++) {
		$string_date=date('d.m.', strtotime($today. ' + '.$j.' days'));
		$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-gray"><h4>'.$string_date.'</h4></td>';
	}
	$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-gray"></td></tr>';
	if($mode!='b2b') {
		$res.='<tr>
		<td class="FAIR-data-height1 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue1" colspan="'.($X+2).'"><b><i>';
		if($flag_prereg!=0) {
			$res.='Für folgende Teststationen ist keine Terminbuchung notwendig, eine Voranmeldung Ihrer Daten kann gerne gemacht werden, dann geht es vor Ort schneller - bitte dafür einen Termin wählen';
		} else {
			$res.='Für folgende Teststationen ist keine Voranmeldung notwendig';
		}
		$res.='</i></b></td>
		</tr>';
	}
	foreach($stations_array as $st) {
		// check if station has appointed times
		if( S_get_entry($Db,'SELECT id_station FROM Termine WHERE Slot is null AND Date(Tag)>="'.$today.'" AND Date(Tag)<="'.$in_x_days.'" AND id_station='.$st[0].';')==$st[0]) {
			$res.='<tr>';
			$string_location='<b>'.$st[1].'</b><br>'.$st[2].'';
			$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue2">'.$string_location.'</td>';
			for($j=0;$j<$X;$j++) {
				$in_j_days=date('Y-m-d', strtotime($today. ' + '.$j.' days'));
				$array_termine_open=S_get_multientry($Db,'SELECT id,Startzeit, Endzeit, opt_station, opt_station_adresse FROM Termine WHERE Slot is null AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Startzeit ASC;');
				$string_times='';
				foreach($array_termine_open as $te) {
					if($te[3]!='') {
						$string_times.='<span class="text-sm">'.$te[3].',<br>'.$te[4].'</span><br>';
					}
					$string_times.=date('H:i',strtotime($te[1])).' - '.date('H:i',strtotime($te[2])).'<br>';
					if($mode=='b2b') {
						$string_times.='<span class="text-sm">Offener Termin</span><br>';
					}
				}
				
				if($string_times!='') {
					if($flag_prereg==0) {
						$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-blue2">
						'.$string_times.'</td>';
					} else {
						$res.='<td onclick="window.location=\''.$path_to_reg.'index.php?appointment='.$te[0].'\'" class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-blue2 calendarblue">'.$string_times.'</td>';
					}
					
				} else {
					$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-blue3"></td>';
				}
			}
			$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-blue3"></td>';
			$res.='</tr>';
		}
		
	}

	if($flag_prereg!=0) {
		if($mode!='b2b') {
			$res.='<tr>
			<td class="FAIR-data-height1 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow1" colspan="'.($X+2).'"><b><i>Bei folgenden Teststationen ist eine Voranmeldung und Terminbuchung erforderlich - bitte einen Termin wählen</i></b></td>
			</tr>';
		}
		foreach($stations_array as $st) {
			// check if station has appointed times
			if( S_get_entry($Db,'SELECT id_station FROM Termine WHERE Slot>0 AND Date(Tag)>="'.$today.'" AND Date(Tag)<="'.$in_x_days.'" AND id_station='.$st[0].';')==$st[0]) {
				$res.='<tr>';
				$string_location='<b>'.$st[1].'</b><br>'.$st[2].'';
				$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow2">'.$string_location.'</td>';
				for($j=0;$j<$X;$j++) {
					$in_j_days=date('Y-m-d', strtotime($today. ' + '.$j.' days'));
					$array_termine_open=S_get_multientry($Db,'SELECT count(id), count(Used) FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'";');

					$count_free=$array_termine_open[0][0]-$array_termine_open[0][1];
					$display_termine='<div style="display: block; margin-top: 5px;"><span class="label label-success">'.($count_free).'</span></div><span class="text-sm"><div style="display: block; margin-top: 5px;">freie&nbsp;Termine</div></span>';
					if($count_free>0) {
						$string_times='';
						// opt location
						$array_location_opt=S_get_multientry($Db,'SELECT opt_station, opt_station_adresse FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Stunde ASC;');
						if($array_location_opt[0][0]!='') {
							$string_times.='<span class="text-sm">'.$array_location_opt[0][0].',<br>'.$array_location_opt[0][1].'</span><br>';
						}
						// get times
						$value_termine_times1=S_get_entry($Db,'SELECT Stunde FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Stunde ASC;');
						$value_termine_times2=S_get_entry($Db,'SELECT Stunde FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Stunde DESC;');
						$value_termine_id=S_get_entry($Db,'SELECT id FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Stunde ASC;');
						$string_times.='<span class="text-sm"><div style="display: block; margin-top: 5px;">'.sprintf('%02d', $value_termine_times1).':00 - '.sprintf('%02d', $value_termine_times2 + 1).':00</div></span>';

						$res.='<td onclick="window.location=\''.$path_to_reg.'index.php?appointment='.($value_termine_id).'\'" class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-yellow2 calendaryellow">'.$string_times.$display_termine.'</td>';
					} else {
						$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-yellow3"></td>';
					}
				}
				//$res.='<td onclick="window.location=\''.$path_to_reg.'index.php?appointment_more='.($st[0]).'\'" class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-yellow2 calendaryellow">Weitere Termine</td>';
				$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-yellow3"></td>';
				$res.='</tr>';
			}
			
		}
	}
	
	$res.='<tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-gray"><h4>Ort</h4></td>';
	for($j=0;$j<$X;$j++) {
		$string_date=date('d.m.', strtotime($today. ' + '.$j.' days'));
		$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-gray"><h4>'.$string_date.'</h4></td>';
	}
	$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-gray"></td></tr>';
    
	$res.='</table>
	';

	S_close_db($Db);
	return $res;
}

?>