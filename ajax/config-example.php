<?php
	require('functions.php');
	
	$mysqli = mysqli_init();
	if (!$mysqli) {
		die("mysqli_init failed");
	}
	// Specify connection timeout
	mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, 10);

	mysqli_real_connect($mysqli, "IP", "USER", "PW", "DB");
?>
