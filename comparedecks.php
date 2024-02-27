<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "php/includes/start.php";

$id1 = $_POST['deck1'] ?? null;
$id2 = $_POST['deck2'] ?? null;

if (isset($id1) && isset($id2)) {
	header("location:".$pages['comparedecks']['route']."$id1/$id2/");
	die();
}

$pdo = connectDB();

$decksQuery = "SELECT `id`, `commander`, `partner` FROM `decks`";
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
	$query = "CALL `DeckHeadToHead`(?, ?)";
	$results = $pdo->prepare($query);
	$results->execute([$id1, $id2]);

	foreach ($results as $r) {
		$matchup[] = $r;
	}

	$results->closeCursor();

	$gamesQuery = "SELECT `games`.`id`, `games`.`date`, `players`.`name`, `decks`.`commander`, `decks`.`partner` FROM `games` LEFT JOIN `decks` ON `games`.`winning_deck` = `decks`.`id` LEFT JOIN `players` ON `games`.`winning_player` = `players`.`id` LEFT JOIN `game_participation` AS `gpA` ON `games`.`id` = `gpA`.`game_id` LEFT JOIN `game_participation` AS `gpB` ON `games`.`id` = `gpB`.`game_id` WHERE `gpA`.`deck_id` = ? AND `gpB`.`deck_id` = ? ORDER BY `games`.`id` DESC";
	$games = $pdo->prepare($gamesQuery);
	$games->execute([$id1, $id2]);
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
					<option value=<?= $deck['id'] ?> <?= $deck['id'] == $id1 ? "selected" : "" ?>><?= $deck['partner'] != "" ? $deck['commander']." // ".$deck['partner'] : $deck['commander'] ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div>
				<label for="deck2">Deck 2:</label>
				<select class="deckSelect" id="deck2" name="deck2">
					<option <?= isset($id2) ? "" : "selected" ?>></option>
					<?php foreach ($decks as $deck): ?>
					<option value=<?= $deck['id'] ?> <?= $deck['id'] == $id2 ? "selected" : "" ?>><?= $deck['partner'] != "" ? $deck['commander']." // ".$deck['partner'] : $deck['commander'] ?></option>
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