<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "php/includes/start.php";

$pdo = connectDB();

$decksQuery = "SELECT id, commander FROM `decks`";
$decksStmt = $pdo->prepare($decksQuery);
$decksStmt->execute();

$decks = array();

foreach($decksStmt as $deck) {
	$decks[] = $deck;
}

$id1 = $_GET['deck1'] ?? null;
$id2 = $_GET['deck2'] ?? null;

$matchup = array();

$games = array();

if (isset($_GET['viewMatchup'])) {
	$query = "CALL `DeckHeadToHead`(?, ?)";
	$results = $pdo->prepare($query);
	$results->execute([$id1, $id2]);

	foreach ($results as $r) {
		$matchup[] = $r;
	}

	$results->closeCursor();

	$gameQuery = "SELECT `id` FROM `games` WHERE `games`.`id` IN (SELECT `game_id` FROM `game_participation` WHERE `deck_id` = ? INTERSECT SELECT `game_id` FROM `game_participation` WHERE `deck_id` = ?) ORDER BY id ASC";
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
				<label for="deck1">Deck 1:</label>
				<select class="deckSelect" id="deck1" name="deck1">
					<option value="-1" <?isset($id1) ? "" : "selected" ?>></option>
					<?php foreach ($decks as $deck): ?>
					<option value=<?= $player['id'] ?> <?= $deck['id'] == $id1 ? "selected" : "" ?>><?= $deck['commander'] ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div>
				<label for="deck2">Deck 2:</label>
				<select class="deckSelect" id="deck2" name="deck2">
					<option value="-1" <?= isset($id2) ? "" : "selected" ?>></option>
					<?php foreach ($decks as $deck): ?>
					<option value=<?= $deck['id'] ?> <?= $deck['id'] == $id2 ? "selected" : "" ?>><?= $deck['commander'] ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<button type="submit" name="viewMatchup">Submit</button>
		</form>

		<table>
			<tr>
				<th>Commander</th>
				<th>Wins</th>
				<th>Losses</th>
				<th>Win Rate</th>
			</tr>
			<?php foreach ($matchup as $r): ?>
			<tr>
				<td><?= $r['commander'] ?></td>
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