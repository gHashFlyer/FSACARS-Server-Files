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

// Get info from acars
//===================================================================

$username = $_REQUEST['user'];  // entered by user in fsACARS
$sha1pass = $_REQUEST['pass'];  // user password hashed with SHA1
$acars    = $_REQUEST['acars']; // the version of acars the user is running.

// Compare info from acars to what is in the database
$sqlcmd = "SELECT * FROM pilots WHERE pilot_id=? AND password=?"
$sqli = $sqlicnxn->prepare($sqlcmd);
$sqli->bind_param("ss", $username, $sha1pass);
$sqli->execute();
$result = $sqli->get_result();
$sqli->close();

if($result->num_rows > 0){
	exit("USEROK");
}else{
	exit("NOUSR");
}


?>
