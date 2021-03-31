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
	$stmt=mysqli_prepare($Db,"INSERT INTO Voranmeldung (Vorname, Nachname, Geburtsdatum, Adresse, Telefon, Mailadresse, Slot) VALUES (?,?,?,?,?,?,?);");
	mysqli_stmt_bind_param($stmt, "s", $array_data[0], $array_data[1], $array_data[2], $array_data[3], $array_data[4], $array_data[5], $array_data[6]);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $result);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	return $result; // TODO need id as a return value
}

function S_get_entry_voranmeldung ($Db,$array_data) {
	$stmt=mysqli_prepare($Db,"SELECT id_preregistration FROM Voranmeldung_Verif WHERE id=? AND Token=?;");
	mysqli_stmt_bind_param($stmt, "s", $array_data[0], $array_data[1]);
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
		// without 0, O, o, z, Z, y, Y
    $characters = '123456789abcdefghijklmnpqrstuvwxABCDEFGHIJKLMNPQRSTUVWX';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
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
		$res.='Für folgende Teststationen ist keine Terminbuchung möglich, eine Voranmeldung kann gerne gemacht werden';
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

?>