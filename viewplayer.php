<?php
require_once "php/includes/start.php";

$id = $_GET['id'] ?? null;

$ownedDecksQuery = "SELECT
	`decks`.`id`,
	`decks`.`commander`,
	`decks`.`partner`,
	`decks`.`background`,
	`decks`.`owner`,
	`players`.`name`,
	COUNT(DISTINCT `won_games`.`id`) AS `wins`,
	COUNT(DISTINCT `lost_games`.`id`) AS `losses`,
	COUNT(DISTINCT `won_games`.`id`) + COUNT(DISTINCT `lost_games`.`id`) AS `played`,
	COUNT(DISTINCT `won_games`.`id`) / (COUNT(DISTINCT `won_games`.`id`) + COUNT(DISTINCT `lost_games`.`id`)) * 100 AS `win rate`
FROM `decks`
LEFT JOIN `players`
ON `decks`.`owner` = `players`.`id`
LEFT JOIN `game_participation` AS `played_games`
ON `decks`.`id` = `played_games`.`deck_id`
LEFT JOIN `games` AS `won_games`
ON
	`played_games`.`game_id` = `won_games`.`id` AND
	`won_games`.`winning_deck` = `decks`.`id`
LEFT JOIN `games` AS `lost_games`
ON
	`played_games`.`game_id` = `lost_games`.`id` AND
	`lost_games`.`winning_deck` <> `decks`.`id`
WHERE
	`decks`.`owner` = ? AND
	(
		(
			`won_games`.`date` >= ? AND
			`won_games`.`date` <= ?
		) OR
		(
			`lost_games`.`date` >= ? AND
			`lost_games`.`date` <= ?
		) OR
		`won_games`.`date` IS NULL OR
		`lost_games`.`date` IS NULL
	)
GROUP BY `decks`.`id`
ORDER BY
	`win rate` DESC,
	`played` DESC,
	`id` ASC;";

$decks = $pdo->prepare($ownedDecksQuery);
$decks->execute([
	$id,
	$settings['start_date'],
	$settings['end_date'],
	$settings['start_date'],
	$settings['end_date']
]);
$deckData = $decks->fetchAll();
$decks->closeCursor();

$playerResultsQuery = "SELECT
	`players`.`id`,
	`players`.`username`,
	`players`.`name`,
	COUNT(DISTINCT `won games`.`id`) AS `wins`,
	COUNT(DISTINCT `lost games`.`id`) AS `losses`,
	COUNT(DISTINCT `won games`.`id`) / (COUNT(DISTINCT `won games`.`id`) + COUNT(DISTINCT `lost games`.`id`)) * 100 AS `win rate`
FROM `players`
LEFT JOIN `game_participation` AS `played games`
ON `players`.`id` = `played games`.`player_id`
LEFT JOIN `games` AS `won games`
ON `played games`.`game_id` = `won games`.`id` AND
   `won games`.`winning_player` = `players`.`id`
LEFT JOIN `games` AS `lost games`
ON
	`played games`.`game_id` = `lost games`.`id` AND
	`lost games`.`winning_player` <> `players`.`id`
WHERE
	(
		`won games`.`date` >= ? AND
		`won games`.`date` <= ?
	) OR
	(
		`lost games`.`date` >= ? AND
		`lost games`.`date` <= ?
	)
GROUP BY `players`.`id`
HAVING (`wins` > 0 OR
       `losses` > 0) AND
	   `players`.`id` = ?;";

$playerResults = $pdo->prepare($playerResultsQuery);
$playerResults->execute([
	$settings['start_date'],
	$settings['end_date'],
	$settings['start_date'],
	$settings['end_date'],
	$id,
]);
$playerData = $playerResults->fetch();

$gamesPlayedQuery = "SELECT
	`games`.`id`,
	`games`.`date`,
	`decks`.`id` AS `deck id`,
	`decks`.`commander`,
	`decks`.`partner`,
	`games`.`winning_player`
FROM `game_participation`
LEFT JOIN `games`
ON `games`.`id` = `game_participation`.`game_id`
LEFT JOIN `decks` ON `game_participation`.`deck_id` = `decks`.`id`
WHERE `game_participation`.`player_id` = ?
ORDER BY
	`games`.`date` DESC,
	`games`.`id` DESC;";
$gamesPlayed = $pdo->prepare($gamesPlayedQuery);
$gamesPlayed->execute([$id]);

$playedDecksResultsQuery = "SELECT
	`all_decks`.`id`,
	`all_decks`.`commander`,
	`all_decks`.`partner`,
	`all_decks`.`background`,
	COUNT(DISTINCT `won_games`.`id`) AS `wins`,
	COUNT(DISTINCT `lost_games`.`id`) AS `losses`,
	COUNT(DISTINCT `won_games`.`id`) + COUNT(DISTINCT `lost_games`.`id`) AS `played`,
	COUNT(DISTINCT `won_games`.`id`) / (COUNT(DISTINCT `won_games`.`id`) + COUNT(DISTINCT `lost_games`.`id`)) * 100 AS `win rate`
FROM `game_participation` AS `gpA`
LEFT JOIN `game_participation` AS `gp`
ON `gp`.`game_id` = `gpA`.`game_id` AND
   `gp`.`player_id` = ?
LEFT JOIN `decks` AS `all_decks`
ON `all_decks`.`id` = `gp`.`deck_id`
LEFT JOIN `games` AS `won_games`
ON `won_games`.`id` = `gp`.`game_id` AND
   `won_games`.`winning_deck` = `all_decks`.`id`
LEFT JOIN `games` AS `lost_games`
ON `lost_games`.`id` = `gp`.`game_id` AND
   `lost_games`.`winning_deck` <> `all_decks`.`id`
WHERE
	(
		`won_games`.`date` >= ? AND
		`won_games`.`date` <= ?
	) OR
	(
		`lost_games`.`date` >= ? AND
		`lost_games`.`date` <= ?
	)
GROUP BY `all_decks`.`id`
ORDER BY
	`win rate` DESC,
	`played` DESC,
	`id` ASC;";
$playedDecksResults = $pdo->prepare($playedDecksResultsQuery);
$playedDecksResults->execute([
	$id,
	$settings['start_date'],
	$settings['end_date'],
	$settings['start_date'],
	$settings['end_date']
]);

require_once "php/includes/head.php";
?>
<?php
require_once "php/includes/header.php";
?>
<main>
	<div class="left">
		<h1><?= $playerData['username'] ?></h1>
		<div><span><?= $playerData['name'] ?></span></div>
		<div>
			<span><?= $playerData['win rate']."% win rate ".$playerData['wins']."–".$playerData['losses'] ?></span>
		</div>
	</div>
	<div class="middle">
		<section>
			<h2>Owned Decks</h2>
			<table>
				<tr>
					<th>Commander</th>
					<th>Partner or Background</th>
					<th>Wins</th>
					<th>Losses</th>
					<th>Win Rate / %</th>
				</tr>
				<?php foreach($deckData as $deck): ?>
				<tr>
					<td><a href="<?= $pages['viewdeck']['route'].$deck['id']."/" ?>"><?= $deck['commander'] ?></a></td>
					<td><?= $deck['partner'].$deck['background'] ?></td>
					<td><?= $deck['wins'] ?></td>
					<td><?= $deck['losses'] ?></td>
					<td><?= $deck['win rate'] ?></td>
				</tr>
				<?php endforeach; ?>
			</table>
		</section>

		<section>
			<h2>Played Decks</h2>
			<table>
				<tr>
					<th>Commander</th>
					<th>Partner or Background</th>
					<th>Wins</th>
					<th>Losses</th>
					<th>Win Rate / %</th>
				</tr>
				<?php foreach($playedDecksResults as $result): ?>
				<tr>
					<td><a href="<?= $pages['viewdeck']['route'].$result['id']."/" ?>"><?= $result['commander'] ?></a></td>
					<td><?= $result['partner'].$result['background'] ?></td>
					<td><?= $result['wins'] ?></td>
					<td><?= $result['losses'] ?></td>
					<td><?= $result['win rate'] ?></td>
				</tr>
				<?php endforeach; ?>
			</table>
		</section>

		<section>
			<h2>Games Played</h2>
			<table>
				<tr>
					<th>Game #</th>
					<th>Date</th>
					<th>Deck</th>
				</tr>
				<?php while ($game = $gamesPlayed->fetch()): ?>
				<tr class="<?= $game['winning_player'] == $id ? "winner" : "loser" ?>">
					<td><a href="<?= $pages['viewgame']['route'].$game['id']."/" ?>"><?= $game['id'] ?></a></td>
					<td><?= $game['date'] ?></td>
					<td><a href="<?= $pages['viewdeck']['route'].$game['deck id']."/" ?>"><?=$game['partner'] != "" ? $game['commander']." // ".$game['partner'] : $game['commander'] ?></a></td>
				</tr>
				<?php endwhile; ?>
			</table>
		</section>
	</div>
</main>
<?php require "php/includes/footer.php"; ?>