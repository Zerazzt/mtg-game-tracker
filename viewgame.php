<?php
require_once "php/includes/start.php";

$id = $_GET['id'] ?? null;

$gamesQuery = "SELECT * FROM `games` WHERE `id` = ?";
$games = $pdo->prepare($gamesQuery);
$games->execute([$id]);
$game = $games->fetch();

$participantsQuery = "SELECT `players`.`id` AS `player id`, `players`.`name`, `decks`.`id` AS `deck id`, `decks`.`commander`, `decks`.`partner`, `game_participation`.`turn_order` FROM `game_participation` LEFT JOIN `players` ON `game_participation`.`player_id` = `players`.`id` LEFT JOIN `decks` ON `game_participation`.`deck_id` = `decks`.`id` WHERE `game_id` = ? ORDER BY `turn_order` ASC";
$participants = $pdo->prepare($participantsQuery);
$participants->execute([$id]);

require_once "php/includes/head.php";
?>
<?php
require_once "php/includes/header.php";
?>
<main>
	<div class="single">
		<table>
			<tr>
				<th>Turn Order</th>
				<th>Player</th>
				<th>Deck</th>
			</tr>
			<?php while ($participant = $participants->fetch()): ?>
			<tr class="<?= $game['winning_player'] == $participant['player id'] ? "winner" : "loser" ?>">
				<td><?= $participant['turn_order'] + 1 ?></td>
				<td><a href="<?= $pages['viewplayer']['route'].$participant['player id']."/" ?>"><?= $participant['name'] ?></a></td>
					<td><a href="<?= $pages['viewdeck']['route'].$participant['deck id']."/" ?>"><?=$participant['partner'] != "" ? $participant['commander']." // ".$participant['partner'] : $participant['commander'] ?></a></td>
			</tr>
			<?php endwhile; ?>
		</table>
		<span>Played on: <?= $game['date'] ?></span>
		<a href="<?= $pages['viewgame']['route'].($id - 1)."/" ?>">Prev</a>
		<a href="<?= $pages['viewgame']['route'].($id + 1)."/" ?>">Next</a>
	</div>
</main>
<?php require_once "php/includes/footer.php"; ?>