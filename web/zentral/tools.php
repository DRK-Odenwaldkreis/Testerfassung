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

function S_get_entry_voranmeldung ($Db,$scanevent) {
	$stmt=mysqli_prepare($Db,"SELECT id, Used FROM Voranmeldung WHERE Token=?;");
	mysqli_stmt_bind_param($stmt, "s", $scanevent);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $id_voranmeldung, $used);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	// Check if number exists in table Voranmeldung
	if($used==1) {
		// bereits verwendet
		return "Used";
	} elseif($id_voranmeldung>0) {
		// Return result of SQL query
		return $id_voranmeldung;
	} else {
		return "Not registered";
	}
}

function S_get_cwa_qr_code ($Db,$test_id) {
	$test_array=S_get_multientry($Db,'SELECT Geburtsdatum, Vorname, Nachname, Registrierungszeitpunkt, Salt, Token, CWA_request FROM Vorgang WHERE id=CAST('.$test_id.' AS int);');
	if($test_array[0][6]==1) {
		// // personalized CWA
		// build hash
		$pre_hash=$test_array[0][0].'#'.$test_array[0][1].'#'.$test_array[0][2].'#'.strtotime($test_array[0][3]).'#'.$test_array[0][5].'#'.$test_array[0][4];
		$hash=hash("sha256",$pre_hash);
		// build json
		$json='{"dob":"'.$test_array[0][0].'","fn":"'.$test_array[0][1].'","ln":"'.$test_array[0][2].'","testid":"'.$test_array[0][5].'","timestamp":'.strtotime($test_array[0][3]).',"salt":"'.$test_array[0][4].'","hash":"'.$hash.'"}';
	} elseif($test_array[0][6]==2) {
		// // anonymous CWA
		// build hash
		$pre_hash=strtotime($test_array[0][3]).'#'.$test_array[0][4];
		$hash=hash("sha256",$pre_hash);
		// build json
		$json='{"timestamp":'.strtotime($test_array[0][3]).',"salt":"'.$test_array[0][4].'","hash":"'.$hash.'"}';
	}
	// build base64coded
	$base64=rtrim( strtr( base64_encode( $json ), '+/', '-_'), '=');
	return $base64;
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
// Generate random salt
function A_generate_cwa_salt($length = 32) {
	// Generierte 128-Bit Zufallszahl in Hexadezimal-Darstellung, nur mit Großbuchstaben und fester Breite von 32 Stellen
$characters = '1234567890ABCDEF';
$randomString = '';
for ($i = 0; $i < $length; $i++) {
	$randomString .= $characters[rand(0, strlen($characters) - 1)];
}
return $randomString;
}

// Login for user with $uid
// $mode is for 'password', 'code', ...
function A_login($Db,$uid,$mode) {
    
	$_SESSION['uid'] = $uid;
	if($_SESSION['uid']=='') { die("Error in database. (Err:102)"); }
	
	$_SESSION['signedin'] = true;
	$_SESSION['username'] = S_get_entry($Db,'SELECT username FROM li_user WHERE id='.$uid.';');
	$_SESSION['station_id'] = S_get_entry($Db,'SELECT Station FROM li_user WHERE id='.$uid.';');
	if($_SESSION['station_id']>0) {
		$_SESSION['station_name'] = S_get_entry($Db,'SELECT Ort FROM Station WHERE id='.$_SESSION['station_id'].';');
		$business_code=S_get_entry($Db,'SELECT Firmencode FROM Station WHERE id='.$_SESSION['station_id'].';');
		if($business_code!='') { $_SESSION['station_business']=true; } else { $_SESSION['station_business']=false; }
	} else {
		$_SESSION['station_name']="";
		$_SESSION['station_business']=false;
	}

	/* Rollen
		1 - Teststation
		2 - Office
		3 - Gesundheitsamt
		4 - Admin
		5 - Gruppenleitung */
	$t = S_get_multientry($Db,'SELECT 0, role_1, role_2, role_3, role_4, role_5 FROM li_user WHERE id='.$uid.';');
	$_SESSION['roles']=$t[0];
	$_SESSION['display_sensitive']=0;

	if($mode!='check' && $mode!='chguserid') {
		// Cookie will expire after 12 hrs after log-in
		// PHP session will expire earlier if no site request
		$expiry = time() + 12*60*60;
		$data = (object) array( "un" => $_SESSION['username'], "pw" => S_get_entry($Db,'SELECT password_hash FROM li_user WHERE id='.$uid.'') );
		$cookieData = (object) array( "data" => $data, "expiry" => $expiry );
		setcookie('drk-cookie', json_encode( $cookieData ), $expiry);
	}
	
	//login lock reset > set failed attempts to 0
	S_set_data($Db,'UPDATE li_user SET login_attempts=0 WHERE id='.$uid.';');
	
	// Delete older login tokens
	S_set_data($Db,'DELETE FROM li_token WHERE id_user='.$uid.';');

    return true;
}

// Check if user with $uid is already logged in after standard session time is expired
function A_checkloggedin($Db,$username,$hash) {

	// Benutzername und Hash werden überprüft
	if($username!='') {
		$uid=S_get_entry($Db,'SELECT id FROM li_user WHERE lower(username)=\''.$username.'\'');
		if($uid>0) {
			$db_hash=S_get_entry($Db,'SELECT password_hash FROM li_user WHERE id='.$uid.'');
		} else {
			$db_hash='';
		}
		// Check correct password hash
		if ( $hash === $db_hash ) {
			A_login($Db,$uid,'check');
			return true;
		}
	}

    return false;
}

// Check if user role fits to requirements
function A_checkpermission($requirement) {
	$bool_permission=false;
	foreach($requirement as $b) {
		if($b>0 && $_SESSION['roles'][$b]==1) { 
			$bool_permission=true;
		}
	}
return $bool_permission;
}

function A_sanitize_input($input) {
	// strips any HTML and PHP tags
	strip_tags($input);

	// validate white listed chars in input (alphanumeric)
	$validated = "";
	if(preg_match("/^[a-zA-Z0-9\-]+$/", $input)) {
		$validated = $input;
	}
	
	return $validated;
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

function H_build_table_testdates_all($mode) {
	
	$res='';
	$Db=S_open_db();
	if($mode == 'vaccinate') {
		$stations_array=S_get_multientry($Db,'SELECT Station.id, Station.Ort, Station.Adresse, Impfstoff.Kurzbezeichnung FROM Station
		JOIN Impfstoff ON Impfstoff.id=Station.Impfstoff_id;');
	} else {
		$stations_array=S_get_multientry($Db,'SELECT id, Ort, Adresse FROM Station;');
	}
	// X ist Anzahl an Tagen für Vorschau in Tabelle
	if($mode == 'vaccinate') {
		$X=35;
	} else {
		$X=21;
	}
	// Ohne Terminbuchung für nächste X Tage / free2come
	$today=date('Y-m-d');
	$in_x_days=date('Y-m-d', strtotime($today. ' + '.$X.' days'));
	$yesterday=date('Y-m-d', strtotime($today. ' -1 days'));
	

	// Table
	$res.='
	<table class="FAIR-data">
	<tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-gray"><h4>Ort</h4></td>';
	$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-gray"><h4>Gestern</h4></td>';
	for($j=0;$j<$X;$j++) {
		$string_date=date('d.m.', strtotime($today. ' + '.$j.' days'));
		$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-gray"><h4>'.$string_date.'</h4></td>';
	}
	$res.='</tr>';

	foreach($stations_array as $st) {
		// check if station has appointed times
		if( S_get_entry($Db,'SELECT id_station FROM Termine WHERE Slot is null AND Date(Tag)>="'.$yesterday.'" AND Date(Tag)<="'.$in_x_days.'" AND id_station='.$st[0].';')==$st[0]) {
			$location_thirdline_val=S_get_entry($Db,'SELECT Oeffnungszeiten FROM Station WHERE id='.$st[0].';');
			if($location_thirdline_val!='') {
				$display_location_thirdline='<br><span class="text-sm">'.$location_thirdline_val.'</span>';
			} else {
				$display_location_thirdline='';
			}
			$res.='<tr>';
			if($mode == 'vaccinate') {
				$string_location='<b>'.$st[3].'</b><br>'.$st[1].', '.$st[2].'';
			} else {
				$string_location='<b>'.$st[1].'</b><br>'.$st[2].'';
			}
			$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue2">'.$string_location.$display_location_thirdline.'</td>';
			for($j=0;$j<=$X;$j++) {
				$in_j_days=date('Y-m-d', strtotime($yesterday. ' + '.$j.' days'));
				$array_termine_open=S_get_multientry($Db,'SELECT id,Startzeit, Endzeit, opt_station, opt_station_adresse FROM Termine WHERE Slot is null AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Startzeit ASC;');
				$string_times='';

				

				foreach($array_termine_open as $te) {
					if($te[3]!='') {
						$string_times.='<span class="text-sm">'.$te[3].',<br>'.$te[4].'</span><br>';
					}
					if($mode!='get_id_for_zip') {
						// How many have registered for this free2come appointment
						$value_reservation=S_get_entry($Db,'SELECT count(id) FROM Voranmeldung WHERE Termin_id='.$te[0].';');
						if($j>0) {
							$display_termine='<br><div style="display: block; margin-top: 5px;"><span class="label label-primary">'.sprintf('%01d',$value_reservation).'</span></div><span class="text-sm"><div style="display: block; margin-top: 5px;">Reservierungen</div></span>';
						} else {
							$display_termine='';
						}
						
						// How many have registered and not shown up
						if($j<2) {
							$value_reservation_used=S_get_entry($Db,'SELECT count(id) FROM Voranmeldung WHERE Termin_id='.$te[0].' AND Used=1;');
							$display_termine.='<div style="display: block; margin-top: 5px;"><span class="label label-danger">'.sprintf('%01d',$value_reservation-$value_reservation_used).'</span></div>
							<span class="text-sm"><div style="display: block; margin-top: 5px;">no-show</div></span>';
						}
					}
					if($mode=='get_id_for_zip') { $click_event='<div onclick="window.location=\'sammeltestung.php?termin='.$te[0].'\'">'; }  else { $click_event=''; }
					$string_times.=$click_event.date('H:i',strtotime($te[1])).' - '.date('H:i',strtotime($te[2])).$display_termine.'<br>';
					if($mode=='get_id_for_zip') { $click_event='</div>'; }  else { $click_event=''; }
					$string_times.=$click_event;
				}
				
				if($string_times!='') {
					
					$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-blue2 calendarblue">'.$string_times.'</td>';

					
				} else {
					$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-blue3"></td>';
				}
			}
			
			$res.='</tr>';
		}
		
	}

	if($mode!='get_id_for_zip') {
		foreach($stations_array as $st) {
			// check if station has appointed times
			if( S_get_entry($Db,'SELECT id_station FROM Termine WHERE Slot>0 AND Date(Tag)>="'.$yesterday.'" AND Date(Tag)<="'.$in_x_days.'" AND id_station='.$st[0].';')==$st[0]) {
				$location_thirdline_val=S_get_entry($Db,'SELECT Oeffnungszeiten FROM Station WHERE id='.$st[0].';');
				if($location_thirdline_val!='') {
					$display_location_thirdline='<br><span class="text-sm">'.$location_thirdline_val.'</span>';
				} else {
					$display_location_thirdline='';
				}
				$res.='<tr>';
				if($mode == 'vaccinate') {
					$string_location='<b>'.$st[3].'</b><br>'.$st[1].', '.$st[2].'';
				} else {
					$string_location='<b>'.$st[1].'</b><br>'.$st[2].'';
				}
				$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow2">'.$string_location.$display_location_thirdline.'</td>';
				for($j=0;$j<=$X;$j++) {
					$in_j_days=date('Y-m-d', strtotime($yesterday. ' + '.$j.' days'));
					$array_termine_open=S_get_multientry($Db,'SELECT count(id), count(Used) FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'";');
					$count_free=$array_termine_open[0][0]-$array_termine_open[0][1];
					if( $count_free==0 ) {
						$label_free='default';
					} elseif( ($count_free/$array_termine_open[0][0])<0.5 ) {
						$label_free='warning';
					} else {
						$label_free='success';
					}
					$display_termine='<div style="display: block; margin-top: 5px;"><span class="label label-'.$label_free.'">'.($count_free).' von '.$array_termine_open[0][0].'</span></div><span class="text-sm"><div style="display: block; margin-top: 5px;">freie&nbsp;Termine</div></span>';
					// How many have registered and not shown up
					if($j<2) {
						if($j==0) {
							$current_hour=24;
						} else {
							$current_hour=date('G');
						}
						$value_reservation_unused=S_get_entry($Db,'SELECT count(Voranmeldung.id) FROM Voranmeldung JOIN Termine ON Termine.id=Voranmeldung.Termin_id WHERE Termine.Slot>0 AND Termine.id_station='.$st[0].' AND Date(Termine.Tag)="'.$in_j_days.'" AND Termine.Stunde<'.$current_hour.' AND Voranmeldung.Used=0;');
						$display_termine.='<div style="display: block; margin-top: 5px;"><span class="label label-danger">'.sprintf('%01d',$value_reservation_unused).'</span></div>
						<span class="text-sm"><div style="display: block; margin-top: 5px;">no-show</div></span>';
					}
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
						//$value_termine_id=S_get_entry($Db,'SELECT id FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Stunde ASC;');
						$string_times.='<span class="text-sm"><div style="display: block; margin-top: 5px;">'.sprintf('%02d', $value_termine_times1).':00 - '.sprintf('%02d', $value_termine_times2 + 1).':00</div></span>';

						$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-yellow2 calendaryellow">'.$string_times.$display_termine.'</td>';
					} elseif($array_termine_open[0][0]>0) {
						$string_times='';
						// opt location
						$array_location_opt=S_get_multientry($Db,'SELECT opt_station, opt_station_adresse FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Stunde ASC;');
						if($array_location_opt[0][0]!='') {
							$string_times.='<span class="text-sm">'.$array_location_opt[0][0].',<br>'.$array_location_opt[0][1].'</span><br>';
						}
						// get times
						$value_termine_times1=S_get_entry($Db,'SELECT Stunde FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Stunde ASC;');
						$value_termine_times2=S_get_entry($Db,'SELECT Stunde FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Stunde DESC;');
						//$value_termine_id=S_get_entry($Db,'SELECT id FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Stunde ASC;');
						$string_times.='<span class="text-sm"><div style="display: block; margin-top: 5px;">'.sprintf('%02d', $value_termine_times1).':00 - '.sprintf('%02d', $value_termine_times2 + 1).':00</div></span>';

						$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-yellow2 calendaryellow">'.$string_times.$display_termine.'</td>';
					} else {
						$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-yellow3"></td>';
					}
				}
				
				$res.='</tr>';
			}
				

		}
	}
	
	$res.='<tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-gray"><h4>Ort</h4></td>';
	$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-gray"><h4>Gestern</h4></td>';
	for($j=0;$j<$X;$j++) {
		$string_date=date('d.m.', strtotime($today. ' + '.$j.' days'));
		$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-gray"><h4>'.$string_date.'</h4></td>';
	}
	$res.='</tr>';
    
	$res.='</table>
	';

	S_close_db($Db);
	return $res;
}

?>