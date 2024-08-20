<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "php/includes/start.php";

$playerQuery = "SELECT
	`players`.`id`, `players`.`name`,
	COUNT(DISTINCT `won_games`.`id`) AS `wins`,
	COUNT(DISTINCT `lost_games`.`id`) AS `losses`,
	COUNT(DISTINCT `won_games`.`id`) / (COUNT(DISTINCT `won_games`.`id`) + COUNT(DISTINCT `lost_games`.`id`)) * 100 AS `win rate`
FROM `players`
LEFT JOIN `game_participation` AS `played_games`
ON `players`.`id` = `played_games`.`player_id`
LEFT JOIN `games` AS `won_games`
ON `played_games`.`game_id` = `won_games`.`id` AND
	`won_games`.`winning_player` = `players`.`id`
LEFT JOIN `games` AS `lost_games`
ON `played_games`.`game_id` = `lost_games`.`id` AND
	`lost_games`.`winning_player` <> `players`.`id`
WHERE
	(`won_games`.`date` >= ? AND
	`won_games`.`date` <= ?) OR
	(`lost_games`.`date` >= ? AND
	`lost_games`.`date` <= ?)
GROUP BY `players`.`id`
HAVING `wins` + `losses` >= ?
ORDER BY `win rate` DESC
LIMIT ?";
$players = $pdo->prepare($playerQuery);
$players->execute([
	$settings['start_date'],
	$settings['end_date'],
	$settings['start_date'],
	$settings['end_date'],
	$settings['minimum'],
	$settings['display max']
]);

$deckQuery = "SELECT
	`decks`.`id`,
	`decks`.`commander`,
	`decks`.`partner`,
	`decks`.`owner`,
	`players`.`name`,
	COUNT(DISTINCT `won_games`.`id`) AS `wins`,
	COUNT(DISTINCT `lost_games`.`id`) AS `losses`,
	COUNT(DISTINCT `won_games`.`id`) / (COUNT(DISTINCT `won_games`.`id`) + COUNT(DISTINCT `lost_games`.`id`)) * 100 AS `win rate`
FROM `decks`
LEFT JOIN `players`
ON `decks`.`owner` = `players`.`id`
LEFT JOIN `game_participation` AS `played_games`
ON `decks`.`id` = `played_games`.`deck_id`
LEFT JOIN `games` AS `won_games`
ON `played_games`.`game_id` = `won_games`.`id` AND
	`won_games`.`winning_deck` = `decks`.`id`
LEFT JOIN `games` AS `lost_games`
ON `played_games`.`game_id` = `lost_games`.`id` AND
	`lost_games`.`winning_deck` <> `decks`.`id`
WHERE
	(`won_games`.`date` >= ? AND
	`won_games`.`date` <= ?) OR
	(`lost_games`.`date` >= ? AND
	`lost_games`.`date` <= ?)
GROUP BY `decks`.`id`
HAVING `wins` + `losses` >= ?
ORDER BY `win rate` DESC
LIMIT ?";
$decks = $pdo->prepare($deckQuery);
$decks->execute([
	$settings['start_date'],
	$settings['end_date'],
	$settings['start_date'],
	$settings['end_date'],
	$settings['minimum'],
	$settings['display max']
]);

$gamesQuery = "SELECT
	`games`.`id`,
	`games`.`date`,
	`players`.`id` AS `player id`,
	`players`.`name`,
	`decks`.`id` AS `deck id`,
	`decks`.`commander`,
	`decks`.`partner`
FROM `games`
LEFT JOIN `players`
ON `games`.`winning_player` = `players`.`id`
LEFT JOIN `decks`
ON `games`.`winning_deck` = `decks`.`id`
ORDER BY id DESC
LIMIT ?";
$games = $pdo->prepare($gamesQuery);
$games->execute([
	$settings['display max']
]);

require_once "php/includes/head.php";
?>
<?php
require_once "php/includes/header.php";
?>
<main>
	<div class="middle">
		<table id="playerWinRates">
			<tr>
				<th>Name</th>
				<th>Wins</th>
				<th>Losses</th>
				<th>Win Rate</th>
			</tr>
			<?php while ($player = $players->fetch()): ?>
			<tr>
				<td><a href="<?= $pages['viewplayer']['route'].$player['id']."/" ?>"><?= $player['name'] ?></a></td>
				<td><?= $player['wins'] ?></td>
				<td><?= $player['losses'] ?></td>
				<td><?= $player['win rate'] ?></td>
			</tr>
			<?php endwhile; ?>
		</table>

		<table id="deckWinRates">
			<tr>
				<th>Deck</th>
				<th>Owner</th>
				<th>Wins</th>
				<th>Losses</th>
				<th>Win Rate / %</th>
			</tr>
			<?php while ($deck = $decks->fetch()): ?>
			<tr>
				<td><a href="<?= $pages['viewdeck']['route'].$deck['id']."/"?>"><?= $deck['partner'] != "" ? $deck['commander']." // ".$deck['partner'] : $deck['commander'] ?></a></td>
				<td><a href="<?= $pages['viewplayer']['route'].$deck['owner']."/" ?>"><?= $deck['name'] ?></a></td>
				<td><?= $deck['wins'] ?></td>
				<td><?= $deck['losses'] ?></td>
				<td><?= $deck['win rate'] ?></td>
			</tr>
			<?php endwhile; ?>
		</table>

		<table id="gamesList">
			<tr>
				<th>Game #</th>
				<th>Date</th>
				<th>Winner</th>
				<th>Deck</th>
			</tr>
			<?php while ($game = $games->fetch()): ?>
			<tr>
				<td><a href="<?= $pages['viewgame']['route'].$game['id']."/"?>"><?= $game['id'] ?></a></td>
				<td><?= $game['date'] ?></td>
				<td><a href="<?= $pages['viewplayer']['route'].$game['player id']."/" ?>"><?= $game['name'] ?></a></td>
				<td><a href="<?= $pages['viewdeck']['route'].$game['deck id']."/" ?>"><?= $game['partner'] != "" ? $game['commander']." // ".$game['partner'] : $game['commander'] ?></a></td>
			</tr>
			<?php endwhile; ?>
		</table>
	</div>
</main>
<?php require "php/includes/footer.php"; ?>