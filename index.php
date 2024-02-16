<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "php/includes/start.php";

$pdo = connectDB();

$playerQuery = "SELECT * FROM `player_win_rates`";
$players = $pdo->prepare($playerQuery);
$players->execute();

$deckQuery = "SELECT `decks`.`id`, `players`.`id` AS `player id`, `players`.`name`, `decks`.`commander`, `decks`.`partner`, `deck_win_rates`.`wins`, `deck_win_rates`.`losses`, `deck_win_rates`.`win rate` FROM `decks` LEFT JOIN `deck_win_rates` ON `decks`.`id` = `deck_win_rates`.`id` LEFT JOIN `players` ON `decks`.`owner` = `players`.`id` ORDER BY `win rate` DESC";
$decks = $pdo->prepare($deckQuery);
$decks->execute();

$gamesQuery = "SELECT `games`.`id`, `games`.`date`, `players`.`id` AS `player id`, `players`.`name`, `decks`.`id` AS `deck id`, `decks`.`commander`, `decks`.`partner` FROM `games` LEFT JOIN `players` on `games`.`winning_player` = `players`.`id` LEFT JOIN `decks` on `games`.`winning_deck` = `decks`.`id` ORDER BY id DESC LIMIT 25";
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
				<td><a href="<?= $pages['viewplayer']['route'].$player['id']."/" ?>"><?= $player['name'] ?></a></td>
				<td><?= $player['wins'] ?></td>
				<td><?= $player['losses'] ?></td>
				<td><?= $player['win rate'] ?></td>
			</tr>
			<?php endwhile; ?>
		</table>

		<table id="deckWinRates">
			<tr>
				<th>Owner</th>
				<th>Deck</th>
				<th>Wins</th>
				<th>Losses</th>
				<th>Win Rate</th>
			</tr>
			<?php while ($deck = $decks->fetch()): ?>
			<tr>
				<td><a href="<?= $pages['viewplayer']['route'].$deck['player id']."/" ?>"><?= $deck['name'] ?></a></td>
				<td><a href="<?= $pages['viewdeck']['route'].$deck['id']."/"?>"><?= $deck['partner'] != "" ? $deck['commander']." // ".$deck['partner'] : $deck['commander'] ?></a></td>
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