<?php

// Generic flight tracking script.  fsACARS periodically sends information
// about the flight to the server.  This script parses that information
// for flight tracking and monitoring.
// Revised September 10, 2025 by Garen Evans


// If you want to save the position reports to a sql table, then
// change the following to true, and set the path to your credentials, if needed.
// Also see about line 49, and edit as necessary to match your sql table.
$use_sql = true;
$sql_credential_file = "sqlfunc.php";



$POSITION_FILE = "posreps.csv";       // name of the file where position reports will be kept.

// These are all the data that are sent, but the example logs only a few of these.
$user      =  $_REQUEST['user'];      //username
$pass      =  $_REQUEST['pass'];      //user password, hashed with SHA1
$fhash     =  $_REQUEST['fhash'];     //flight hash - unique for each flight report.
$auth      =  $_REQUEST['auth'];      //this is the serverpass from the user's org.cfg file
$lat1      =  $_REQUEST['lat1'];      //starting latitude
$lon1      =  $_REQUEST['lon1'];      //starting longitude
$lat2      =  $_REQUEST['lat2'];      //current latitude
$lon2      =  $_REQUEST['lon2'];      //current longitude
$msl       =  $_REQUEST['msl'];       //current altitude , MSL, feet
$gskts     =  $_REQUEST['gskts'];     //current ground speed, knots
$hdgtrue   =  $_REQUEST['hdgtrue'];   //true heading, degrees
$etime     =  $_REQUEST['etime'];     //elapsed time, decimal hours
$aircraft  =  $_REQUEST['aircraft']; //aircraft model from title in aircraft.cfg
$onground  =  $_REQUEST['onground'];  //1=on ground

if(!$lat1 || !$lat2 || !$lon1 || !$lon2 || $user == ""){
	exit;
}else{

	//Replaces commas with periods for numerics
	$lat  = floatval(str_replace(',', '.', $lat2));
	$lon  = floatval(str_replace(',', '.', $lon2));

	$fname = realpath('')."/".$POSITION_FILE;
	updatePostion($fname, $user, $lat, $lon, $msl, $gskts);


	if($use_sql){
		include($sql_credential_file);	 // SQL credentials
		$sql = "INSERT INTO position (user, lat, lon, msl, kts, hdg, aircraft, fhash) VALUES (?,?,?,?,?,?,?,?)";
		$sql = $mySQLi->prepare($sql1);
		$sql->bind_param("ssssssss", $user, $lat, $lon, $msl, $gskts, $hdgtrue, $aircraft, $fhash);
		$sql->execute();
	}	


}

exit;


// Updates position reports file and keeps things tidy.
function updatePostion($fname,$user,$lat,$lon,$msl,$gskts){

	$tmnow = time();
	$newData = "";
	
	if(file_exists($fname)){
		$fdata = file($fname);
		$n = count($fdata);
		$newData = "epochTime,userName,latitude,longitude,altitudeMSL,gsKTS,\n";
		for($i = $flag = 0; $i < $n; $i++){
			$sar = explode(",",$fdata[$i]);
			if($sar[1] == $user){ //update this row with latest info
				if($flag == 0){
					$newData .= "$tmnow,$user,$lat,$lon,$msl,$gskts,\n";
					$flag = 1;
				}
			}else{
				// Prune position reports older than set amount of time
				if( ($tmnow - intval($sar[0])) < 600 ){ // 600 seconds
					$newData .= $fdata[$i]; 
				}
				//implicit else - ignores old position reports
			}
		}
		if($flag == 0){
			$newData .= "$tmnow,$user,$lat,$lon,$msl,$gskts,\n";
		}

		// delete the old position reports file
		unlink($fname);

		// create a new position reports file
		$fp = fopen($fname,"w");
		fwrite($fp,$newData);
		fclose($fp);

	}else{
		$newData .= "$tmnow,$user,$lat,$lon,$msl,$gskts,\n";
	}
	
}



?>
