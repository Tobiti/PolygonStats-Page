<?php
	require('config.php');

	$sql = "SELECT * FROM (SELECT AccountId, SUM(CaughtPokemon) AS CaughtPokemon, SUM(FleetPokemon) AS FleetPokemon, SUM(ShinyPokemon) AS ShinyPokemon, SUM(100IV) AS 100IV, SUM(FortSpins) AS FortSpins, SUM(Rockets) AS Rockets, SUM(Raids) AS Raids, SUM(TotalXp) AS TotalXp, SUM(TotalStardust) AS TotalStardust, SUM(TotalMinutes) AS TotalMinutes FROM (SELECT `SessionId`, SUM(CaughtSuccess=1) AS CaughtPokemon, SUM(CaughtSuccess=0 AND LogEntryType=\"Pokemon\") AS FleetPokemon, SUM(Shiny) AS ShinyPokemon, SUM(Attack=15 AND Defense=15 AND Stamina=15 AND NOT LogEntryType<>\"EvolvePokemon\") AS 100IV, SUM(LogEntryType=\"Fort\") AS FortSpins, SUM(LogEntryType=\"Rocket\") AS Rockets, SUM(LogEntryType=\"Raid\") AS Raids, SUM(XpReward) AS TotalXp, SUM(StardustReward) AS TotalStardust, TIMESTAMPDIFF(MINUTE, MIN(`timestamp`), MAX(`timestamp`)) AS TotalMinutes FROM SessionLogEntry GROUP BY `SessionId`) l JOIN `Session` s ON s.Id=l.SessionId GROUP BY AccountId) t JOIN Account ON t.AccountId=Id;";
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