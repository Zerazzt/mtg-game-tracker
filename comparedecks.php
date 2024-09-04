<?php
require_once "php/includes/start.php";

$id1 = $_POST['deck1'] ?? null;
$id2 = $_POST['deck2'] ?? null;

if (isset($id1) && isset($id2)) {
	header("location:".$pages['comparedecks']['route']."$id1/$id2/");
	die();
}

$decksQuery = "SELECT
	`decks`.`id`,
	`commander`,
	`partner`,
	`background`,
	`name`
FROM `decks`
LEFT JOIN `players`
ON `decks`.`owner` = `players`.`id`
WHERE `players`.`priority` > 0";
$decksStmt = $pdo->prepare($decksQuery);
$decksStmt->execute();

$decks = array();

foreach ($decksStmt as $deck) {
	$decks[] = $deck;
}

$id1 = $_GET['deck1'] ?? null;
$id2 = $_GET['deck2'] ?? null;

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
	`all_decks`.*,
	COUNT(DISTINCT `won_games`.`id`) AS `wins`,
	COUNT(DISTINCT `lost_games`.`id`) AS `losses`,
	COUNT(DISTINCT `won_games`.`id`) / (COUNT(DISTINCT `won_games`.`id`) + COUNT(DISTINCT `lost_games`.`id`)) * 100 AS `win rate`
FROM `game_participation` AS `gpA`
INNER JOIN `game_participation` AS `gpB`
ON 	`gpA`.`game_id` = `gpB`.`game_id` AND
	`gpA`.`deck_id` = ? AND
	`gpB`.`deck_id` = ?
LEFT JOIN `game_participation` AS `gp`
ON	`gp`.`game_id` = `gpA`.`game_id`
LEFT JOIN `decks` AS `all_decks`
ON	`all_decks`.`id` = `gp`.`deck_id`
LEFT JOIN `games` AS `won_games`
ON	`won_games`.`id` = `gp`.`game_id` AND
	`won_games`.`winning_deck` = `all_decks`.`id`
LEFT JOIN `games` AS `lost_games`
ON	`lost_games`.`id` = `gp`.`game_id` AND
	`lost_games`.`winning_deck` <> `all_decks`.`id`
WHERE
	(`won_games`.`date` >= ? AND
	`won_games`.`date` <= ?) OR
	(`lost_games`.`date` >= ? AND
	`lost_games`.`date` <= ?)
GROUP BY `all_decks`.`id`
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
LEFT JOIN `decks`
ON `games`.`winning_deck` = `decks`.`id`
LEFT JOIN `players`
ON `games`.`winning_player` = `players`.`id`
LEFT JOIN `game_participation` AS `gpA`
ON `games`.`id` = `gpA`.`game_id`
LEFT JOIN `game_participation` AS `gpB`
ON `games`.`id` = `gpB`.`game_id`
WHERE `gpA`.`deck_id` = ? AND
      `gpB`.`deck_id` = ? 
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
		<h2>Compare Decks</h2>
		<form method="post" class="container user">
			<div>
				<label for="deck1">Deck 1:</label>
				<select class="deckSelect" id="deck1" name="deck1">
					<option <?= isset($id1) ? "" : "selected" ?>></option>
					<?php foreach ($decks as $deck): ?>
					<option value=<?= $deck['id'] ?> <?= $deck['id'] == $id1 ? "selected" : "" ?>><?= $deck['partner'] != "" ? $deck['commander']." // ".$deck['partner'] : $deck['commander']," (".$deck['name'].")" ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div>
				<label for="deck2">Deck 2:</label>
				<select class="deckSelect" id="deck2" name="deck2">
					<option <?= isset($id2) ? "" : "selected" ?>></option>
					<?php foreach ($decks as $deck): ?>
					<option value=<?= $deck['id'] ?> <?= $deck['id'] == $id2 ? "selected" : "" ?>><?= $deck['partner'] != "" ? $deck['commander']." // ".$deck['partner'] : $deck['commander']," (".$deck['name'].")" ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<button type="submit">Submit</button>
		</form>

		<table>
			<tr>
				<th>Commander</th>
				<th>Partner</th>
				<th>Wins</th>
				<th>Losses</th>
				<th>Win Rate / %</th>
			</tr>
			<?php foreach($matchup as $r): ?>
			<tr>
				<td><a href="<?= $pages['viewdeck']['route'].$r['id']."/" ?>"><?= $r['commander'] ?></a></td>
				<td><?= $r['partner'] ?></td>
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