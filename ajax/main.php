<?php
	require('config.php');

	$sql = "SELECT Id, `Name`, TotalXp, TotalStardust, CaughtPokemon, FleetPokemon, ShinyPokemon, FortSpins, TotalMinutes FROM Account AS a JOIN (SELECT s.AccountId, SUM(t.XpTotal) AS TotalXp, SUM(t.StardustTotal) AS TotalStardust, SUM((SELECT COUNT(*) FROM SessionLogEntry WHERE SessionId=s.Id AND LogEntryType=\"Pokemon\" AND CaughtSuccess=1)) AS CaughtPokemon,SUM((SELECT COUNT(*) FROM SessionLogEntry WHERE SessionId=s.Id AND Shiny=1)) AS ShinyPokemon, SUM((SELECT COUNT(*) FROM SessionLogEntry WHERE SessionId=s.Id AND LogEntryType=\"Pokemon\" AND CaughtSuccess=0)) AS FleetPokemon, SUM((SELECT COUNT(*) FROM SessionLogEntry WHERE SessionId=s.Id AND LogEntryType=\"Fort\")) AS FortSpins, SUM(TIMESTAMPDIFF(MINUTE, StartTime, t.EndTime)) AS TotalMinutes FROM `Session` AS s JOIN(SELECT MAX(TIMESTAMP) AS EndTime, SessionId, SUM(XpReward) AS XpTotal, SUM(StardustReward) AS StardustTotal FROM SessionLogEntry GROUP BY SessionId) t ON t.SessionId=s.Id GROUP BY s.AccountId) AS b ON a.Id=b.AccountId";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($conn));

	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		$row["Name"] = substr($row["Name"], 0, 2) . "XXXXX" . substr($row["Name"], strlen($row["Name"])-2);
		$row["XpHour1"] = (int) ($row["TotalXp"] / ($row["TotalMinutes"] / 60));
		$row["XpHour24"] = (int) ($row["TotalXp"] / ($row["TotalMinutes"] / 60 / 24));
		$row["StardustHour1"] = (int) ($row["TotalStardust"] / ($row["TotalMinutes"] / 60));
		$row["StardustHour24"] = (int) ($row["TotalStardust"] / ($row["TotalMinutes"] / 60 / 24));
		$row["Caught24"] = (int) ($row["CaughtPokemon"] / ($row["TotalMinutes"] / 60 / 24));
		$row["Spinned24"] = (int) ($row["FortSpins"] / ($row["TotalMinutes"] / 60 / 24));
		$data[] = $row;
	}

	$results = ["sEcho" => 1,
				"iTotalRecords" => count($data),
				"iTotalDisplayRecords" => count($data),
				"aaData" => $data ];

	echo json_encode($results);
?>