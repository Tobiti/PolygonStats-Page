<?php
	require('config.php');

	$sql = "SELECT Name, Id, CaughtPokemon, EscapedPokemon AS FleetPokemon, ShinyPokemon, MaxIV AS 100IV, Pokestops AS FortSpins, Rockets, Raids, TotalGainedXp AS TotalXp, TotalGainedStardust AS TotalStardust, TotalMinutes FROM Account";
	$result = $mysqli->query($sql) or die("database error:". mysqli_error($mysqli));

	$data = [];
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		$row["Name"] = hideAccountName($row["Name"]);
		$row["TotalMinutes"] = max(1, $row["TotalMinutes"]);
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