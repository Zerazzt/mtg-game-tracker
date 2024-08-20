<?php
require_once "php/includes/start.php";

$id1 = $_POST['player1'] ?? null;
$id2 = $_POST['player2'] ?? null;

if (isset($id1) && isset($id2)) {
	header("location:".$pages['compareplayers']['route']."$id1/$id2/");
	die();
}

$playersQuery = "SELECT
	`id`,
	`name`
FROM `players`
WHERE `priority` > 0";
$playersStmt = $pdo->prepare($playersQuery);
$playersStmt->execute();

$players = array();

foreach ($playersStmt as $player) {
	$players[] = $player;
}

$id1 = $_GET['player1'] ?? null;
$id2 = $_GET['player2'] ?? null;

if ($id1 == "") {
	$id1 = null;
}

if ($id2 == "") {
	$id2 = null;
}

$matchup = array();

$games = array();

if (isset($id1) && isset($id2)) {
	$query = "SELECT
	`all_players`.`id`,
	`all_players`.`name`,
	COUNT(DISTINCT `won_games`.`id`) AS `wins`,
	COUNT(DISTINCT `lost_games`.`id`) AS `losses`,
	COUNT(DISTINCT `won_games`.`id`) / (COUNT(DISTINCT `won_games`.`id`) + COUNT(DISTINCT `lost_games`.`id`)) * 100 AS `win rate`
FROM `game_participation` AS `gpA`
INNER JOIN `game_participation` AS `gpB`
ON 	`gpA`.`game_id` = `gpB`.`game_id` AND
	`gpA`.`player_id` = ? AND
	`gpB`.`player_id` = ?
LEFT JOIN `game_participation` AS `gp`
ON	`gp`.`game_id` = `gpA`.`game_id`
LEFT JOIN `players` AS `all_players`
ON	`all_players`.`id` = `gp`.`player_id`
LEFT JOIN `games` AS `won_games`
ON	`won_games`.`id` = `gp`.`game_id` AND
	`won_games`.`winning_player` = `all_players`.`id`
LEFT JOIN `games` AS `lost_games`
ON	`lost_games`.`id` = `gp`.`game_id` AND
	`lost_games`.`winning_player` <> `all_players`.`id`
WHERE
	(`won_games`.`date` >= ? AND
	`won_games`.`date` <= ?) OR
	(`lost_games`.`date` >= ? AND
	`lost_games`.`date` <= ?)
GROUP BY `all_players`.`id`
HAVING `wins` > 0 OR `losses` > 0
ORDER BY `win rate` DESC;";
	$results = $pdo->prepare($query);
	$results->execute([
		$id1,
		$id2,
		$settings['start_date'],
		$settings['end_date'],
		$settings['start_date'],
		$settings['end_date']
	]);

	foreach ($results as $r) {
		$matchup[] = $r;
	}
	
	$results->closeCursor();

	$gamesQuery = "SELECT
	`games`.`id`,
	`games`.`date`,
	`players`.`name`,
	`decks`.`commander`,
	`decks`.`partner`
FROM `games`
LEFT JOIN `players`
ON `games`.`winning_player` = `players`.`id`
LEFT JOIN `decks`
ON `games`.`winning_deck` = `decks`.`id`
LEFT JOIN `game_participation` AS `gpA`
ON `games`.`id` = `gpA`.`game_id`
LEFT JOIN `game_participation` AS `gpB`
ON `games`.`id` = `gpB`.`game_id`
WHERE `gpA`.`player_id` = ? AND
      `gpB`.`player_id` = ?
ORDER BY `games`.`id` DESC";
	$games = $pdo->prepare($gamesQuery);
	$games->execute([
		$id1,
		$id2
	]);
}

require_once "php/includes/head.php";
?>
<?php
require_once "php/includes/header.php";
?>
<main>
	<div class="single">
		<h2>Compare Players</h2>
		<form method="post" class="container user">
			<div>
				<label for="player1">Player 1:</label>
				<select class="playerSelect" id="player1" name="player1">
					<option <?= isset($id1) ? "" : "selected" ?>></option>
					<?php foreach ($players as $player): ?>
					<option value=<?= $player['id'] ?> <?= $player['id'] == $id1 ? "selected" : "" ?>><?= $player['name'] ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div>
				<label for="player2">Player 2:</label>
				<select class="playerSelect" id="player2" name="player2">
					<option <?= isset($id2) ? "" : "selected" ?>></option>
					<?php foreach ($players as $player): ?>
					<option value=<?= $player['id'] ?> <?= $player['id'] == $id2 ? "selected" : "" ?>><?= $player['name'] ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<button type="submit">Submit</button>
		</form>

		<table>
			<tr>
				<th>Name</th>
				<th>Wins</th>
				<th>Losses</th>
				<th>Win Rate / %</th>
			</tr>
			<?php foreach ($matchup as $r):?>
			<tr>
				<td><a href="<?= $pages['viewplayer']['route'].$r['id']."/" ?>"><?= $r['name'] ?></a></td>
				<td><?= $r['wins'] ?></td>
				<td><?= $r['losses'] ?></td>
				<td><?= $r['win rate'] ?></td>
			</tr>
			<?php endforeach; ?>
		</table>

		<table>
			<tr>
				<th>Game #</th>
				<th>Date</th>
				<th>Winning Player</th>
				<th>Winning Deck</th>
			</tr>
			<?php foreach ($games as $game): ?>
			<tr>
				<td><a href="<?= $pages['viewgame']['route'].$game['id']."/" ?>">Game <?= $game['id'] ?></a></td>
				<td><?= $game['date'] ?></td>
				<td><?= $game['name'] ?></td>
				<td><?= $game['partner'] != "" ? $game['commander']." // ".$game['partner'] : $game['commander'] ?></td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
</main>
<?php require "php/includes/footer.php"; ?>