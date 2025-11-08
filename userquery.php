<?php

// Updated on July 1, 2025

$username = $_REQUEST['user']; //entered by user in fsACARS
$sha1pass = $_REQUEST['pass']; //user password hashed with SHA1
$acars = $_REQUEST['acars']; // the version of acars the user is running.


// For now, we are going to assume the user sent a correct password hash.
// However if you know the SHA1 hash of the user's password then
// you should compare that to sha1pass.

//User and password validation (you probably want to use hashed passwords)
//As a demo we allow anything except for empty name and password fields.
if(($username == "") || ($sha1pass == "")){
	exit("NOUSR");
}


exit("USEROK");

?>

