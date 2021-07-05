<?php
	require('config.php');
	
	$sql = "UPDATE `Session` s SET EndTime=COALESCE((SELECT MAX(TIMESTAMP) FROM SessionLogEntry WHERE SessionId=s.Id), s.EndTime) WHERE EndTime=\"0001-01-01 00:00:00.000000\";";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET TotalMinutes=COALESCE((SELECT SUM(TIMESTAMPDIFF(MINUTE, StartTime, EndTime)) FROM `Session` s WHERE s.AccountId=a.Id AND EndTime<>\"0001-01-01 00:00:00.000000\"), 1);";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET CaughtPokemon=COALESCE((SELECT COUNT(*) FROM SessionLogEntry WHERE CaughtSuccess=1 AND SessionId IN (SELECT s.Id FROM `Session` s WHERE s.AccountId=a.Id)), 0);";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET EscapedPokemon=COALESCE((SELECT COUNT(*) FROM SessionLogEntry WHERE LogEntryType=\"Pokemon\" AND CaughtSuccess=0 AND SessionId IN (SELECT s.Id FROM `Session` s WHERE s.AccountId=a.Id)), 0);";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET Pokestops=COALESCE((SELECT COUNT(*) FROM SessionLogEntry WHERE LogEntryType=\"Fort\" AND SessionId IN (SELECT s.Id FROM `Session` s WHERE s.AccountId=a.Id)), 0);";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET Raids=COALESCE((SELECT COUNT(*) FROM SessionLogEntry WHERE LogEntryType=\"Raid\" AND SessionId IN (SELECT s.Id FROM `Session` s WHERE s.AccountId=a.Id)), 0);";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET Rockets=COALESCE((SELECT COUNT(*) FROM SessionLogEntry WHERE LogEntryType=\"Rocket\" AND SessionId IN (SELECT s.Id FROM `Session` s WHERE s.AccountId=a.Id)), 0);";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET TotalGainedStardust=COALESCE((SELECT SUM(StardustReward) FROM SessionLogEntry WHERE SessionId IN (SELECT s.Id FROM `Session` s WHERE s.AccountId=a.Id)), 0);";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET TotalGainedXp=COALESCE((SELECT SUM(XpReward) FROM SessionLogEntry WHERE SessionId IN (SELECT s.Id FROM `Session` s WHERE s.AccountId=a.Id)), 0);";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET ShinyPokemon=COALESCE((SELECT SUM(Shiny) FROM SessionLogEntry WHERE SessionId IN (SELECT s.Id FROM `Session` s WHERE s.AccountId=a.Id)), 0);";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
	
	$sql = "UPDATE `Account` a SET MaxIV=COALESCE((SELECT SUM(Attack=15 AND Defense=15 AND Stamina=15) FROM SessionLogEntry WHERE LogEntryType<>\"EvolvePokemon\" AND SessionId IN (SELECT s.Id FROM `Session` s WHERE s.AccountId=a.Id)), 0);";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));
?>