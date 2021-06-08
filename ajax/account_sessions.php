<?php
	require('config.php');

	$id = $_GET["account_id"];
	$daily = $_GET["daily"];

	if (isset($_GET["daily"]) && $daily==1) {
		$stmt = $mysqli->prepare("SELECT SUM(CaughtSuccess=1) AS CaughtPokemon, SUM(CaughtSuccess=0 AND LogEntryType=\"Pokemon\") AS FleetPokemon, SUM(Shiny) AS ShinyPokemon, SUM(Attack=15 AND Defense=15 AND Stamina=15 AND LogEntryType<>\"EvolvePokemon\") AS 100IV, SUM(LogEntryType=\"Fort\") AS FortSpins, SUM(LogEntryType=\"Rocket\") AS Rockets, SUM(LogEntryType=\"Raid\") AS Raids, SUM(XpReward) AS TotalXp, SUM(StardustReward) AS TotalStardust, DATE(`timestamp`) as StartTime, TIMESTAMPDIFF(MINUTE, MIN(`timestamp`), MAX(`timestamp`)) AS TotalMinutes FROM SessionLogEntry WHERE SessionId IN (SELECT Id FROM `Session` s WHERE AccountId=?) GROUP BY DATE(`timestamp`) LIMIT 30");
	} else {
		$stmt = $mysqli->prepare("SELECT SessionId, SUM(CaughtSuccess=1) AS CaughtPokemon, SUM(CaughtSuccess=0 AND LogEntryType=\"Pokemon\") AS FleetPokemon, SUM(Shiny) AS ShinyPokemon, SUM(Attack=15 AND Defense=15 AND Stamina=15 AND LogEntryType<>\"EvolvePokemon\") AS 100IV, SUM(LogEntryType=\"Fort\") AS FortSpins, SUM(LogEntryType=\"Rocket\") AS Rockets, SUM(LogEntryType=\"Raid\") AS Raids, SUM(XpReward) AS TotalXp, SUM(StardustReward) AS TotalStardust, MIN(`timestamp`) as StartTime, TIMESTAMPDIFF(MINUTE, MIN(`timestamp`), MAX(`timestamp`)) AS TotalMinutes FROM SessionLogEntry WHERE SessionId IN (SELECT Id FROM `Session` s WHERE AccountId=?) GROUP BY SessionId");
	}

	$stmt->bind_param("s", $id);
	$stmt->execute();
	$result = $stmt->get_result() or die("database error:". mysqli_error($mysqli));

	$data = [];
	while($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$row["TotalMinutes"] = max(1, $row["TotalMinutes"]);
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