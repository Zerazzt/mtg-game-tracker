<?php
require_once "php/includes/start.php";

$username = $_POST["username"] ?? null;
$name     = $_POST["name"]     ?? null;

$owner     = $_POST["owner"]     ?? null;
$commander = $_POST["commander"] ?? null;
$partner   = $_POST["partner"]   ?? null;
$companion = $_POST["companion"] ?? null;

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

if (isset($_POST["addUser"])) {
	$error = validateUsername($username);
	if (isset($error)) {
		$errors["$error username"] = true;
	}
	$error = validateName($name);
	if (isset($error)) {
		$errors["$error name"] = true;
	}
	if (count($errors) === 0) {
		$pdo   = connectDB();
		$query = "INSERT INTO players (username, name) VALUES (?, ?);";
		$stmt  = $pdo->prepare($query);
		$stmt->execute([
			$username,
			$name
		]);
	}
}

if (isset($_POST["addDeck"])) {
	$error = validateOwner($owner);
	if (isset($error)) {
		$errors["$error owner"] = true;
	}
	$error = validateCommander($commander);
	if (isset($error)) {
		$errors["$error commander"] = true;
	}
	$error = validatePartner($partner);
	if (isset($error)) {
		$errors["$error partner"] = true;
	}
	$error = validateCompanion($companion);
	if (isset($error)) {
		$errors["$error companion"] = true;
	}
	if (count($errors) === 0) {
		$pdo   = connectDB();
		$query = "INSERT INTO decks (owner, commander, partner) VALUES (?, ?, ?);";
		$stmt  = $pdo->prepare($query);
		$stmt->execute([
			$owner,
			$commander,
			$partner
		]);
	}
}

if (isset($_POST["addGame"])) {
	$pdo   = connectDB();
	$query = "INSERT INTO games (winning_player, winning_deck, date) VALUES (?, ?, NOW());";
	$stmt  = $pdo->prepare($query);
	$stmt->execute([
		$players[$winner - 1],
		$decks[$winner -1]
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
<script defer src="js/mtgtracker.js"></script>
<?php
require_once "php/includes/header.php";
?>
<main>
	<!-- <div class="left">
		<h1>Magic the Gathering&trade;: Results Tracker</h1>
	</div> -->
	<div class="middle">
		<nav>
			<ul>
				<li><button class="opener" name="user">Add User</button></li>
				<li><button class="opener" name="deck">Add Deck</button></li>
				<li><button class="opener" name="game">Add Game</button></li>
			</ul>
		</nav>
		<!-- <button class="opener" name="user">Add User</button>
		<button class="opener" name="deck">Add Deck</button>
		<button class="opener" name="game">Add Game</button> -->
		<dialog id="add">
			
		</dialog>
		<!-- <div id="add" class="modal">
			<section class="modal-content"></section>
		</div> -->
	</div>
</main>
<?php require "php/includes/footer.php"; ?>