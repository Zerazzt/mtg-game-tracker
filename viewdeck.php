<?php
require_once "php/includes/start.php";

$pdo = connectDB();

$id = $_GET['id'] ?? null;

$deckQuery = "SELECT `decks`.`id`, `decks`.`commander`, `decks`.`partner`, `deck_win_rates`.`wins`, `deck_win_rates`.`losses`, `deck_win_rates`.`win rate`, `players`.`id` AS `owner_id`, `players`.`name` FROM `decks` LEFT JOIN `deck_win_rates` ON `decks`.`id` = `deck_win_rates`.`id` LEFT JOIN `players` ON `decks`.`owner` = `players`.`id` WHERE `decks`.`id` = ?";
$deck = $pdo->prepare($deckQuery);
$deck->execute([$id]);
$deckData = $deck->fetch();

$gamesQuery = "SELECT `games`.`id` AS `id`, `games`.`date`, `players`.`id` AS `player id`, `players`.`name`, `games`.`winning_deck` FROM `game_participation` LEFT JOIN `games` ON `games`.`id` = `game_participation`.`game_id` LEFT JOIN `players` on `game_participation`.`player_id` = `players`.`id` WHERE `game_participation`.`deck_id` = ? ORDER BY `games`.`date` DESC";
$games = $pdo->prepare($gamesQuery);
$games->execute([$id]);

require_once "php/includes/head.php";
?>
<?php
require_once "php/includes/header.php";
?>
<main>
	<div class="left">
		<h1><?= $deckData['partner'] != "" ? $deckData['commander']." // ".$deckData['partner'] : $deckData['commander'] ?></h1>
		<div><span>By <a href="<?= $pages['viewplayer']['route'].$deckData['owner_id']."/"?>"><?= $deckData['name'] ?></a></span></div>
		<div>
			<span><?= $deckData['win rate']."% win rate ".$deckData['wins']."–".$deckData['losses'] ?></span>
		</div>
	</div>
	<div class="middle">
		<section>
			<h2>Played By</h2>
			<span>Under development.</span>
		</section>

		<section>
			<h2>Games Played</h2>
			<table>
				<tr>
					<th>Game #</th>
					<th>Date</th>
					<th>Player</th>
				</tr>
				<?php while ($game = $games->fetch()): ?>
				<tr class="<?= $game['winning_deck'] == $id ? "winner" : "loser" ?>">
					<td><a href="<?= $pages['viewgame']['route'].$game['id']."/" ?>"?><?= $game['id'] ?></a></td>
					<td><?= $game['date'] ?></td>
					<td><a href="<?= $pages['viewplayer']['route'].$game['player id']."/" ?>"><?= $game['name'] ?></a></td>
				</tr>
				<?php endwhile; ?>
			</table>
		</section>
	</div>
</main>