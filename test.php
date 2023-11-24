<?php
error_reporting(E_ALL);
require "php/includes/library.php";

$pdo = connectDB();

// var_dump($pdo);

$query1 = "SELECT * FROM player_win_rates;";

$stmt1 = $pdo->prepare($query1);

var_dump($stmt1);

$stmt1->execute();

echo '<br><br>';

var_dump($stmt1);

$stmt1->closeCursor();

echo '<br><br>';

var_dump($stmt1);

$query2 = "SELECT * FROM deck_wins_rates;";

try {
	$stmt2 = $pdo->prepare($query2);	
}
catch (PDOException $e) {
	echo $e->getMessage();
}

?>