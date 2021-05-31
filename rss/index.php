<?php
	include('../ajax/config.php');


	$attack = !isset($_GET["attack"]) ? 0 : $_GET["attack"];
	$defense = !isset($_GET["defense"]) ? 0 : $_GET["defense"];
	$stamina = !isset($_GET["stamina"]) ? 0 : $_GET["stamina"];

	$deleteStmt = $mysqli->prepare("DELETE FROM `Encounter` WHERE `timestamp` < DATE_SUB( CURRENT_TIME(), INTERVAL 20 MINUTE)");
	$deleteStmt->execute();


	if (isset($_GET["latitude"]) && isset($_GET["longitude"]) && isset($_GET["distance"])) {
		$stmt = $mysqli->prepare("SELECT * FROM `Encounter` WHERE `timestamp` >= DATE_SUB( CURRENT_TIME(), INTERVAL 10 MINUTE) AND `Attack`>=? AND `Defense`>=? AND `Stamina`>=? AND ST_Distance_Sphere(point(`Longitude`, `Latitude`), point(?,?)) <= ?");
		$stmt->bind_param("iiiddd", $attack, $defense, $stamina, $_GET["longitude"], $_GET["latitude"], $_GET["distance"]);
	} else {
		$stmt = $mysqli->prepare("SELECT * FROM `Encounter` WHERE `timestamp` >= DATE_SUB( CURRENT_TIME(), INTERVAL 10 MINUTE) AND `Attack`>=? AND `Defense`>=? AND `Stamina`>=?");
		$stmt->bind_param("iii", $attack, $defense, $stamina);
	}
	

	$stmt->execute();
	$result = $stmt->get_result() or die("database error:". mysqli_error($conn));

	header("Content-type: text/xml"); 

	echo "<?xml version='1.0' encoding='UTF-8'?>
	<rss version='2.0'>
	<channel>
	<title>PolyStats Encounter Feed</title>
	<link>https://pokestats.euve265206.serverprofi24.de/rss/</link>
	<description>A feed for pokemon encounters</description>
	<language>de</language>"; 

	while($row = $result->fetch_array(MYSQLI_ASSOC))
	{
		$title=$row['PokemonName']; 
		$link=$row['Latitude'] . ", " . $row['Longitude'];
		$date=new DateTime($row['timestamp']);
		$description= "Attack:" . $row['Attack'] . ", Defense:" . $row['Defense'] . ", Stamina:" . $row['Stamina'] . " | Time: ". $date->format("H:i:s d.m.y");
		$guid=$row['EncounterId'];

		echo "<item>
		<title>$title</title>
		<link>$link</link>
		<description>$description</description>
      	<guid>$guid</guid>
		</item>"; 
	} 
	echo "</channel></rss>"; 
?>