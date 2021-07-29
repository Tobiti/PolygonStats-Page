<?php
	require('config.php');
		
	$sql = "UPDATE `Account` a SET TotalMinutes=COALESCE((SELECT SUM(TotalMinutes) FROM `Session` s WHERE s.AccountId=a.Id), 1) ORDER BY a.Id;";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET CaughtPokemon=COALESCE((SELECT SUM(CaughtPokemon) FROM `Session` s WHERE s.AccountId=a.Id), 0) ORDER BY a.Id;";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET EscapedPokemon=COALESCE((SELECT SUM(EscapedPokemon) FROM `Session` s WHERE s.AccountId=a.Id), 0) ORDER BY a.Id;";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET Pokestops=COALESCE((SELECT SUM(Pokestops) FROM `Session` s WHERE s.AccountId=a.Id), 0) ORDER BY a.Id;";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET Raids=COALESCE((SELECT SUM(Raids) FROM `Session` s WHERE s.AccountId=a.Id), 0) ORDER BY a.Id;";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET Rockets=COALESCE((SELECT SUM(Rockets) FROM `Session` s WHERE s.AccountId=a.Id), 0) ORDER BY a.Id;";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET TotalGainedStardust=COALESCE((SELECT SUM(TotalGainedStardust) FROM `Session` s WHERE s.AccountId=a.Id), 0) ORDER BY a.Id;";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET TotalGainedXp=COALESCE((SELECT SUM(TotalGainedXp) FROM `Session` s WHERE s.AccountId=a.Id), 0) ORDER BY a.Id;";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET MaxIV=COALESCE((SELECT SUM(MaxIV) FROM `Session` s WHERE s.AccountId=a.Id), 0) ORDER BY a.Id;";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET ShinyPokemon=COALESCE((SELECT SUM(ShinyPokemon) FROM `Session` s WHERE s.AccountId=a.Id), 0) ORDER BY a.Id;";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET LastUpdate=UTC_TIMESTAMP() ORDER BY a.Id;";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
?>