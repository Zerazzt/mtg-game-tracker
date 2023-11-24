<?php
require_once "../includes/library.php";

$pdo = connectDB();

$query = "SELECT id, name FROM players";
$stmt = $pdo->prepare($query);
$stmt->execute();

while($player = $stmt->fetch()) {
	echo $player['id']."\\".$player['name']."/";
}
?>