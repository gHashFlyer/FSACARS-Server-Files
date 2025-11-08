<?php

// mySQL Credentials
include("sql_creds.php");

//Initialize mySQL
//===================================================================
$hostname = $sql_creds_HOSTNAME;
$database = $sql_creds_DATABASE;
$username = $sql_creds_USERNAME;
$password = $sql_creds_PASSWORD;
$sqlicnxn = mysqli_connect($hostname, $username, $password, $database);

if (mysqli_connect_errno()) {
   die("Connect failed: %s\n" + mysqli_connect_error());
   exit;
}

// Get the username sent from acars
$user = $_REQUEST['user'];
$orig = $_REQUEST['orig'];
$dest = $_REQUEST['dest'];
$fnum = $_REQUEST['fnum'];

$ip = $_SERVER['REMOTE_ADDR'];

// For testing purposes only
$booking_time = gmdate("Y-m-d H:i:s");
exit("$user submitted flight dispatch request at $booking_time. [ORIG: $orig]  [DEST: $dest]  [FNUM: $fnum] IP address: $ip");

// The following code retrieves the most recent active booking for the user and 
// returns the details to the acars system
// ============================================================

// Get the database row id of the username
$sqlcmd = "SELECT * FROM pilots WHERE pilot_id=?";
$sqli = $sqlicnxn->prepare($sqlcmd);
$sqli->bind_param("s", $user);
$sqli->execute();
$result = $sqli->get_result();
$row = $result->fetch_assoc();
$sqli->close();

$row_id = $row['id'];


// Get the most recent active booking
$sqlcmd = "SELECT * FROM bookings WHERE `status`='active' AND pilot_id=? ORDER BY booking_time DESC LIMIT 1";
$sqli = $sqlicnxn->prepare($sqlcmd);
$sqli->bind_param("s", $row_id);
$sqli->execute();
$result = $sqli->get_result();
$row = $result->fetch_assoc();
$sqli->close();

if($row){
	$orig = $row['departure_icao'];
	$dest = $row['arrival_icao'];
	$callsign = $row['callsign'];
	$booking_time = $row['booking_time'];
	$booking_id = $row['id'];
	exit("$orig|$dest|Flight $callsign booked at $booking_time. Have a good flight.|$booking_id|RESERVED|");
}



// $user = $_REQUEST['user'];

// if($user == ""){
// 	$user = "Unknown";
// }

// $ip = $_SERVER['REMOTE_ADDR'];


// $orig = "KMEM";
// $dest = "EDKK";
// $flight_number = "FSA210B";
// $message = sprintf("Hello $user your IP address is $ip. Your flight number is $flight_number");
// exit("$orig|$dest|$message|$flight_number|RESERVED|");


?>

