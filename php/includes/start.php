<?php
require_once "library.php";
require_once "variables.php";
require_once "validation.php";
session_start();

$page_name = basename($_SERVER['PHP_SELF'], '.php');

if (isset($_COOKIE["user"]) && isset($_COOKIE["uid"])) {
	$_SESSION["id"] = $_COOKIE["uid"];
	$_SESSION["username"] = $_COOKIE["user"];
}

if ($pages[$page_name]['require_logged_in'] && !isset($_SESSION["id"])) {
	header("location:".$pages['login']['route']);
	die();
}
?>