<?php
	require('config.php');

	$id = $_GET["account_id"];

	$stmt = $mysqli->prepare("SELECT PokemonName AS Pokemon, SUM(CaughtSuccess=1) AS Caught, SUM(CaughtSuccess=0 AND `LogEntryType`=\"Pokemon\") AS Fleet, SUM(Shiny) AS Shiny, SUM(Attack=15 AND Defense=15 AND Stamina=15 AND LogEntryType<>\"EvolvePokemon\") AS 100IV, SUM(CandyAwarded) AS Candy FROM SessionLogEntry WHERE SessionId IN (SELECT Id FROM `Session` s WHERE AccountId=?) AND `PokemonName`<>\"Missingno\" AND `LogEntryType`<>\"EvolvePokemon\" GROUP BY `PokemonName`");

	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result() or die("database error:". mysqli_error($mysqli));

	$data = [];
	while($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$row["ShinyRate"] = round(max($row["Shiny"], 0) / max($row["Caught"], 1) * 100, 2);
		$data[] = $row;
	}

	$results = ["sEcho" => 1,
				"iTotalRecords" => count($data),
				"iTotalDisplayRecords" => count($data),
				"aaData" => $data ];

	echo json_encode($results);
?>