<?php
require_once "php/includes/start.php";

$pdo = connectDB();

$playerQuery = "SELECT * FROM `player_win_rates`";
$players = $pdo->prepare($playerQuery);
$players->execute();

$deckQuery = "SELECT * FROM `deck_win_rates`";
$decks = $pdo->prepare($deckQuery);
$decks->execute();

$gamesQuery = "SELECT `games`.`id`, `games`.`date`, `players`.`name`, `decks`.`commander` FROM `games` LEFT JOIN `players` on `games`.`winning_player` = `players`.`id` LEFT JOIN `decks` on `games`.`winning_deck` = `decks`.`id` ORDER BY id DESC LIMIT 25";
$games = $pdo->prepare($gamesQuery);
$games->execute();

require_once "php/includes/head.php";
?>
<!-- <script defer src="js/index.js"></script> -->
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
				<td><?= $player['name'] ?></td>
				<td><?= $player['wins'] ?></td>
				<td><?= $player['losses'] ?></td>
				<td><?= $player['win rate'] ?></td>
			</tr>
			<?php endwhile; ?>
		</table>

		<table id="deckWinRates">
			<tr>
				<th>Owner</th>
				<th>Commander</th>
				<th>Partner</th>
				<th>Wins</th>
				<th>Losses</th>
				<th>Win Rate</th>
			</tr>
			<?php while ($deck = $decks->fetch()): ?>
			<tr>
				<td><?= $deck['name'] ?></td>
				<td><?= $deck['commander'] ?></td>
				<td><?= $deck['partner'] ?></td>
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
				<td><a href="/viewgame.php?id=<?= $game['id'] ?>"><?= $game['id'] ?></a></td>
				<td><?= $game['date'] ?></td>
				<td><?= $game['name'] ?></td>
				<td><?= $game['commander'] ?></td>
			</tr>
			<?php endwhile; ?>
		</table>
	</div>
</main>
<?php require "php/includes/footer.php"; ?>