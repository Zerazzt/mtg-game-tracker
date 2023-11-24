<?php
require_once "../includes/library.php";

$id = $_GET['id'] ?? null;

$pdo = connectDB();

$query = "SELECT id, commander FROM decks where owner = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id]);

while($deck = $stmt->fetch()) {
	echo $deck['id']."\\".$deck['commander']."/";
}
?>