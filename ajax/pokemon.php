<?php
	require('config.php');
	$result = $mysqli->query("SELECT PokemonName, SUM(CaughtSuccess=1) as Caught, SUM(Shiny=1) as Shiny FROM SessionLogEntry WHERE `LogEntryType`=\"Pokemon\" AND `PokemonName`<>\"Missingno\" AND `timestamp` > ADDDATE(NOW(), INTERVAL -1 DAY) GROUP BY `PokemonName`") or die("database error:". mysqli_error($mysqli));

	$data = [];
	while($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$row["ShinyRate"] = round($row["Shiny"] / $row["Caught"] * 100, 2);
		$data[] = $row;
	}

	$results = ["sEcho" => 1,
				"iTotalRecords" => count($data),
				"iTotalDisplayRecords" => count($data),
				"aaData" => $data ];

	echo json_encode($results);
?>