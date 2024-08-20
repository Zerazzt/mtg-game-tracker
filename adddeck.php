<?php
require_once "php/includes/start.php";

$owner      = $_POST["owner"]      ?? null;
$commander  = $_POST["commander"]  ?? null;
$partner    = $_POST["partner"]    ?? null;
$companion  = $_POST["companion"]  ?? null;
$background = $_POST["background"] ?? null;

$errors = array();

$query = "SELECT `id`, `name` FROM `players` WHERE `priority` > 0";
$stmt = $pdo->prepare($query);
$stmt->execute();
$players = $stmt->fetchAll();
$stmt->closeCursor();

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
	$error = validateBackground($background);
	if (isset($error)) {
		$errors["$error background"] = true;
	}
	if (count($errors) === 0) {
		$query = "INSERT INTO decks (owner, commander, partner, background) VALUES (?, ?, ?, ?);";
		$stmt  = $pdo->prepare($query);
		$stmt->execute([
			$owner,
			htmlentities($commander),
			htmlentities($partner),
			htmlentities($background)
		]);
	}
}

require_once "php/includes/head.php";
?>
<?php
require_once "php/includes/header.php";
?>
<main>
	<div class="middle">
		<h1>Add a new deck</h1>
		<form method="post" class="container deck">
			<div>
				<label for="owner">Owner:</label>
				<select class="playerSelect" name="owner" id="owner">
					<option selected></option>
					<?php foreach ($players as $player): ?>
					<option value=<?= $player['id'] ?>><?= $player['name'] ?></option>
					<?php endforeach; ?>
				</select>
				<span class="error <?= $errors['unknown owner'] ? "" : " hidden" ?>">Invalid deck owner</span>
			</div>
			<div>
				<label for="commander">Commander:</label>
				<input type="text" name="commander" id="commander">
			</div>
			<div>
				<label for="partner">Partner:</label>
				<input type="text" name="partner" id="partner">
			</div>
			<div>
				<label for="background">Background:</label>
				<input type="text" name="background" id="background">
			</div>
			<!-- <div>
				<label for="companion">Companion:</label>
				<input type="text" name="companion" id="companion">
			</div> -->
			<button type="submit" name="addDeck">Submit</button>
		</form>
	</div>
</main>
<?php require_once "php/includes/footer.php"; ?>