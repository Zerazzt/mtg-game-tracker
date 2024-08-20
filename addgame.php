<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "php/includes/start.php";

$players[0] = $_POST["player1"] ?? null;
$players[1] = $_POST["player2"] ?? null;
$players[2] = $_POST["player3"] ?? null;
$players[3] = $_POST["player4"] ?? null;
$players[4] = $_POST["player5"] ?? null;
$players[5] = $_POST["player6"] ?? null;
$players[6] = $_POST["player7"] ?? null;
$players[7] = $_POST["player8"] ?? null;

$decks[0] = $_POST["deck1"] ?? null;
$decks[1] = $_POST["deck2"] ?? null;
$decks[2] = $_POST["deck3"] ?? null;
$decks[3] = $_POST["deck4"] ?? null;
$decks[4] = $_POST["deck5"] ?? null;
$decks[5] = $_POST["deck6"] ?? null;
$decks[6] = $_POST["deck7"] ?? null;
$decks[7] = $_POST["deck8"] ?? null;

$winner = $_POST["winner"]	?? null;

$errors = array();

$query = "SELECT `id`, `name` FROM `players` WHERE `priority` > 0 ORDER BY `priority` DESC, `id` ASC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$playerSet = $stmt->fetchAll();
$stmt->closeCursor();

if (isset($_POST["addGame"])) {
	$query = "INSERT INTO games (winning_player, winning_deck, date) VALUES (?, ?, NOW());";
	$stmt  = $pdo->prepare($query);
	$stmt->execute([
		$players[$winner - 1],
		$decks[$winner - 1]
	]);
	$game_id = getLastGameID();
	for ($i = 0; $i < MAX_PLAYER_COUNT; ++$i) {
		if (isset($players[$i]) && isset($decks[$i])) {
			$query = "INSERT INTO game_participation (game_id, player_id, deck_id, turn_order) VALUES (?, ?, ?, ?);";
			$stmt = $pdo->prepare($query);
			$stmt->execute([
				$game_id,
				$players[$i],
				$decks[$i],
				$i
			]);
		}
		else {
			$i = MAX_PLAYER_COUNT;
		}
	}
}

require_once "php/includes/head.php";
?>
<script defer src="/js/he.js"></script>
<script defer src="/js/addgame.js"></script>
<?php
require_once "php/includes/header.php";
?>
<main>
	<div class="middle">
		<h1>Add a new game</h1>
		<form method="post" class="container game">
			<?php
			for ($i = 1; $i <= MAX_PLAYER_COUNT; ++$i):
			?>
			<div>
				<label for="player<?= $i ?>">Player <?= $i ?>:</label>
				<select class="playerSelect" name="player<?= $i ?>" id="player<?= $i ?>">
					<option selected></option>
					<?php foreach ($playerSet as $player): ?>
					<option value=<?= $player['id'] ?>><?= $player['name'] ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div>
				<label for="owner<?= $i ?>">Owner <?= $i ?>:</label>
				<select class="ownerSelect" name="owner<?= $i ?>" id="owner<?= $i ?>">
					<option selected></option>
					<?php foreach ($playerSet as $player): ?>
					<option value=<?= $player['id'] ?>><?= $player['name'] ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div>
				<label for="deck<?= $i ?>">Deck <?= $i ?>:</label>
				<select class="deckSelect" name="deck<?= $i ?>" id="deck<?= $i ?>">
				
				</select>
			</div>
			<div>
				<input type="radio" name="winner" id="p<?= $i ?>w" value=<?= $i ?>>
				<label for="p<?= $i ?>w">Player <?= $i ?> wins</label>
			</div>
			<?php
			endfor;
			?>
			<button type="submit" name="addGame">Submit</button>
		</form>
	</div>
</main>
<?php require_once "php/includes/footer.php"; ?>