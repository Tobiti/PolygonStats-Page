<?php
	require('config.php');

	$id = $_GET["account_id"];
	$daily = $_GET["daily"];

	if (isset($_GET["daily"]) && $daily==1) {
		$stmt = $mysqli->prepare("SELECT SUM(CaughtSuccess=1) AS CaughtPokemon, SUM(CaughtSuccess=0 AND LogEntryType=\"Pokemon\") AS FleetPokemon, SUM(Shiny) AS ShinyPokemon, SUM(Attack=15 AND Defense=15 AND Stamina=15) AS 100IV, SUM(LogEntryType=\"Fort\") AS FortSpins, SUM(XpReward) AS TotalXp, SUM(StardustReward) AS TotalStardust, DATE(`timestamp`) as StartTime, TIMESTAMPDIFF(MINUTE, MIN(`timestamp`), MAX(`timestamp`)) AS TotalMinutes FROM SessionLogEntry WHERE SessionId IN (SELECT Id FROM `Session` s WHERE AccountId=?) GROUP BY DATE(`timestamp`) LIMIT 30");
	} else {
		$stmt = $mysqli->prepare("SELECT s.Id AS SessionId, t.XpTotal AS TotalXp, t.StardustTotal AS TotalStardust, t.cp AS CaughtPokemon, t.iv AS 100IV, t.shiny AS ShinyPokemon, t.ep AS FleetPokemon, t.forts AS FortSpins, s.StartTime AS StartTime, t.EndTime AS EndTime, TIMESTAMPDIFF(MINUTE, StartTime, t.EndTime) AS TotalMinutes FROM `Session` AS s JOIN(SELECT MAX(TIMESTAMP) AS EndTime, SessionId, SUM(XpReward) AS XpTotal, SUM(StardustReward) AS StardustTotal, SUM(CaughtSuccess=1) AS cp, SUM(CaughtSuccess=0 AND LogEntryType=\"Pokemon\") as ep, SUM(Shiny) as shiny, SUM(LogEntryType=\"Fort\") as forts, SUM(Attack=15 AND Defense=15 AND Stamina=15) as iv FROM SessionLogEntry GROUP BY SessionId) t ON t.SessionId=s.Id WHERE s.AccountId=16 ORDER BY s.Id DESC");
	}

	$stmt->bind_param("s", $id);
	$stmt->execute();
	$result = $stmt->get_result() or die("database error:". mysqli_error($conn));

	while($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$row["XpHour1"] = (int) ($row["TotalXp"] / ($row["TotalMinutes"] / 60));
		$row["XpHour24"] = (int) ($row["TotalXp"] / ($row["TotalMinutes"] / 60 / 24));
		$row["StardustHour1"] = (int) ($row["TotalStardust"] / ($row["TotalMinutes"] / 60));
		$row["StardustHour24"] = (int) ($row["TotalStardust"] / ($row["TotalMinutes"] / 60 / 24));
		$data[] = $row;
	}

	$results = ["sEcho" => 1,
				"iTotalRecords" => count($data),
				"iTotalDisplayRecords" => count($data),
				"aaData" => $data ];

	echo json_encode($results);
?>