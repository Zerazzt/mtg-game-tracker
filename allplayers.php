<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "php/includes/start.php";

$pdo = connectDB();

$execQuery = "SELECT
	`id`,
	`username`,
	`name`
FROM `players`
WHERE `priority` >= 3
ORDER BY
	`id` ASC;";
$execs = $pdo->prepare($execQuery);
$execs->execute();

$playersQuery = "SELECT
	`id`,
	`username`,
	`name`
FROM `players`
WHERE `priority` < 3
ORDER BY
	`id` ASC";
$players = $pdo->prepare($playersQuery);
$players->execute();

require_once "php/includes/head.php";
?>
<?php
require_once "php/includes/header.php";
?>
<main>
	<div class="middle">
		<h1>Executives</h1>
		<div class="list">
			<?php while ($exec = $execs->fetch()): ?>
			<a href="<?= $pages['viewplayer']['route'].$exec['id']."/" ?>"><?= $exec['name'] ?></a>
			<?php endwhile; ?>
		</div>

		<h1>Members</h1>
		<div class="list">
			<?php while ($player = $players->fetch()): ?>
			<a href="<?= $pages['viewplayer']['route'].$player['id']."/" ?>"><?= $player['name'] ?></a>
			<?php endwhile; ?>
		</div>
	</div>
</main>
<?php require "php/includes/footer.php"; ?>