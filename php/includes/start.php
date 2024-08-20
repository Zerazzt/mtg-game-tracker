<?php
require_once "library.php";
require_once "variables.php";
require_once "validation.php";
session_start();

$page_name = basename($_SERVER['PHP_SELF'], '.php');

$pdo = connectDB();
$query = "SELECT `start_date`, `end_date`, `minimum`, `display max` FROM `settings` WHERE `id` = 1";
$stmt = $pdo->prepare($query);
$stmt->execute();
$settings = $stmt->fetch();

if (isset($_COOKIE["user"]) && isset($_COOKIE["uid"])) {
	$_SESSION["id"] = $_COOKIE["uid"];
	$_SESSION["username"] = $_COOKIE["user"];
}

if ($pages[$page_name]['require_logged_in'] && !isset($_SESSION["id"])) {
	header("location:".$pages['login']['route']);
	die();
}
?>