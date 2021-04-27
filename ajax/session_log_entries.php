<?php
	require('config.php');

	$id = $_GET["session_id"];

	$stmt = $mysqli->prepare("SELECT * FROM SessionLogEntry WHERE SessionId=?");
	$stmt->bind_param("s", $id);
	$stmt->execute();
	$result = $stmt->get_result() or die("database error:". mysqli_error($conn));

	while($row = $result->fetch_array(MYSQLI_ASSOC)) {
		if ($row["LogEntryType"] == "Fort") {
			$row["LogEntryType"] = "Pokestop";
		}
		$row["Iv"] = number_format(($row["Attack"] + $row["Defense"] + $row["Stamina"]) / 45 * 100, 1);
		$data[] = $row;
	}

	$results = ["sEcho" => 1,
				"iTotalRecords" => count($data),
				"iTotalDisplayRecords" => count($data),
				"aaData" => $data ];

	echo json_encode($results);
?>