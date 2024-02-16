<?php
require_once "php/includes/start.php";

$pdo = connectDB();

$id = $_GET['id'] ?? null;

$decksQuery = "SELECT `decks`.`id`, `decks`.`commander`, `decks`.`partner`, `deck_win_rates`.`wins`, `deck_win_rates`.`losses`, `deck_win_rates`.`win rate` FROM `deck_win_rates` LEFT JOIN `decks` ON `deck_win_rates`.`id` = `decks`.`id` WHERE `decks`.`owner` = ? ORDER BY `deck_win_rates`.`win rate` DESC";
$decks = $pdo->prepare($decksQuery);
$decks->execute([$id]);
$deckData = $decks->fetchAll();
$decks->closeCursor();

$playerQuery = "SELECT `players`.`id`, `players`.`username`, `players`.`name`, `player_win_rates`.`wins`, `player_win_rates`.`losses`, `player_win_rates`.`win rate` FROM `players` LEFT JOIN `player_win_rates` ON `players`.`id` = `player_win_rates`.`id` WHERE `players`.`id` = ?";
$player = $pdo->prepare($playerQuery);
$player->execute([$id]);
$playerData = $player->fetch();

$gamesQuery = "SELECT `games`.`id`, `games`.`date`, `decks`.`id` AS `deck id`, `decks`.`commander`, `decks`.`partner`, `games`.`winning_player` FROM `game_participation` LEFT JOIN `games` ON `games`.`id` = `game_participation`.`game_id` LEFT JOIN `decks` ON `game_participation`.`deck_id` = `decks`.`id` WHERE `game_participation`.`player_id` = ? ORDER BY `games`.`date` DESC, `games`.`id` DESC";
$games = $pdo->prepare($gamesQuery);
$games->execute([$id]);

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
					<th>Partner</th>
					<th>Wins</th>
					<th>Losses</th>
					<th>Win Rate</th>
				</tr>
				<?php foreach($deckData as $deck): ?>
				<tr>
					<td><a href="<?= $pages['viewdeck']['route'].$deck['id']."/" ?>"><?= $deck['commander'] ?></a></td>
					<td><?= $deck['partner'] ?></td>
					<td><?= $deck['wins'] ?></td>
					<td><?= $deck['losses'] ?></td>
					<td><?= $deck['win rate'] ?></td>
				</tr>
				<?php endforeach; ?>
			</table>
		</section>

		<section>
			<h2>Played Decks</h2>
			<span>Under development.</span>
		</section>

		<section>
			<h2>Games Played</h2>
			<table>
				<tr>
					<th>Game #</th>
					<th>Date</th>
					<th>Deck</th>
				</tr>
				<?php while ($game = $games->fetch()): ?>
				<tr class="<?= $game['winning_player'] == $id ? "winner" : "loser" ?>">
					<td><a href="<?= $pages['viewgame']['route'].$game['id']."/" ?>"?><?= $game['id'] ?></a></td>
					<td><?= $game['date'] ?></td>
					<td><a href="<?= $pages['viewdeck']['route'].$game['deck id']."/" ?>"><?=$game['partner'] != "" ? $game['commander']." // ".$game['partner'] : $game['commander'] ?></a></td>
				</tr>
				<?php endwhile; ?>
			</table>
		</section>
	</div>
</main>