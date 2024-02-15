<?php
require_once "php/includes/start.php";

$pdo = connectDB();

$id = $_GET['id'] ?? null;

$gamesQuery = "SELECT * FROM `games` WHERE `id` = ?";
$games = $pdo->prepare($gamesQuery);
$games->execute([$id]);
$game = $games->fetch();

$participantsQuery = "SELECT `game_participation`.`player_id`, `players`.`name`, `decks`.`commander`, `game_participation`.`turn_order` FROM `game_participation` LEFT JOIN `players` ON `game_participation`.`player_id` = `players`.`id` LEFT JOIN `decks` ON `game_participation`.`deck_id` = `decks`.`id` WHERE `game_id` = ? ORDER BY `turn_order` ASC";
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
			<tr <?= $game['winning_player'] == $participant['player_id'] ? "class=\"winner\"" : "" ?>>
				<td><?= $participant['turn_order'] + 1 ?></td>
				<td><?= $participant['name'] ?></td>
				<td><?= $participant['commander'] ?></td>
			</tr>
			<?php endwhile; ?>
		</table>
		<span>Played on: <?= $game['date'] ?></span>
	</div>
</main>