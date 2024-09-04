<?php
$direx = explode('/', getcwd());
define('DOCROOT', "/$direx[1]/$direx[2]/");
define('WEBROOT', "/$direx[1]/$direx[2]/$direx[3]/");

function connectDB() {
	$config = explode(PHP_EOL, file_get_contents(DOCROOT."pwd/mtgtracker.ini"));

	$dsn = "mysql:host=$config[0];port=3306;dbname=$config[1];charset=utf8mb4";
	try {
		$pdo = new PDO($dsn, $config[2], $config[3], [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false
		]);
	}
	catch (\PDOException $e) {
		echo "\n".$e->getMessage().(int)$e->getCode();
	}
	return $pdo;
}

function getPlayerID($username) {
	$pdo = connectDB();
	$query = "SELECT id FROM players WHERE username = ?";
	$stmt = $pdo->prepare($query);
	$stmt->execute([$username]);
	return $stmt->fetch()["id"];
}

function getDeckID($commander) {
	$pdo = connectDB();
	$query = "SELECT id FROM decks WHERE commander = ?";
	$stmt = $pdo->prepare($query);
	$stmt->execute([$commander]);
	return $stmt->fetch()["id"];
}

function getLastGameID() {
	$pdo = connectDB();
	$query = "SELECT MAX(id) FROM games";
	$stmt = $pdo->prepare($query);
	$stmt->execute();
	return $stmt->fetch()["MAX(id)"];
}
?>