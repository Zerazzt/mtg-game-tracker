<?php
require_once "../includes/library.php";

$pdo = connectDB();

$query = "SELECT id, name FROM players WHERE `priority` > 0 ORDER BY `priority` DESC, `id` ASC";
$stmt = $pdo->prepare($query);
$stmt->execute();

while($player = $stmt->fetch()) {
	echo $player['id']."\\".$player['name']."/";
}
?>