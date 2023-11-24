<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "php/includes/start.php";

$pdo = connectDB();

$playersQuery = "SELECT id, name FROM `players`";
$playersStmt = $pdo->prepare($playersQuery);
$playersStmt->execute();

$players = array();

foreach ($playersStmt as $player) {
	$players[] = $player;
}

$id1 = $_GET['player1'] ?? null;
$id2 = $_GET['player2'] ?? null;

$matchup = array();

$games = array();

if (isset($_GET['viewMatchup'])) {
	$query = "CALL `PlayerHeadToHead`(?, ?)";
	$results = $pdo->prepare($query);
	$results->execute([$id1, $id2]);

	foreach ($results as $r) {
		$matchup[] = $r;
	}
	
	$results->closeCursor();

	$gamesQuery = "SELECT `id` FROM `games` WHERE `games`.`id` IN (SELECT `game_id` FROM `game_participation` WHERE `player_id` = ? INTERSECT SELECT `game_id` FROM `game_participation` WHERE `player_id` = ?) ORDER BY id ASC";
	$games = $pdo->prepare($gamesQuery);
	$games->execute([$id1, $id2]);
}

require_once "php/includes/head.php";
?>
<?php
require_once "php/includes/header.php";
?>
<main>
	<div class="middle">
		<h2>Compare Players</h2>
		<form method="get" class="container user">
			<div>
				<label for="player1">Player 1:</label>
				<select class="playerSelect" id="player1" name="player1">
					<option value="-1" <?= isset($id1) ? "" : "selected" ?>></option>
					<?php foreach ($players as $player): ?>
					<option value=<?= $player['id'] ?> <?= $player['id'] == $id1 ? "selected" : "" ?>><?= $player['name'] ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div>
				<label for="player2">Player 2:</label>
				<select class="playerSelect" id="player2" name="player2">
					<option value="-1" <?= isset($id2) ? "" : "selected" ?>></option>
					<?php foreach ($players as $player): ?>
					<option value=<?= $player['id'] ?> <?= $player['id'] == $id2 ? "selected" : "" ?>><?= $player['name'] ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<button type="submit" name="viewMatchup">Submit</button>
		</form>

		<table>
			<tr>
				<th>Name</th>
				<th>Wins</th>
				<th>Losses</th>
				<th>Win Rate</th>
			</tr>
			<?php foreach ($matchup as $r):?>
			<tr>
				<td><?= $r['name'] ?></td>
				<td><?= $r['wins'] ?></td>
				<td><?= $r['losses'] ?></td>
				<td><?= $r['win rate'] ?></td>
			</tr>
			<?php endforeach; ?>
		</table>

		<?php foreach ($games as $game): ?>
		<a href="/viewgame.php?id=<?= $game['id'] ?>">Game <?= $game['id'] ?></a>
		<?php endforeach; ?>
	</div>
</main>
<?php require "php/includes/footer.php"; ?>