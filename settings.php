<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "php/includes/start.php";

$pdo = connectDB();

if (isset($_POST['updatePriorities'])) {
	$priorities = $_POST['priorities'];
	$query = "UPDATE `players` SET `priority` = ? WHERE `id` = ?";
	$stmt = $pdo->prepare($query);

	foreach ($priorities as $id => $priority) {
		$stmt->execute([
			$priority,
			$id
		]);
	}
}

if (isset($_POST['updateSettings'])) {
	$query = "UPDATE `settings`
	SET `start_date` = ?,
	`end_date` = ?,
	`minimum` = ?,
	`display max` = ?
	WHERE `id` = 1";
	$stmt = $pdo->prepare($query);
	$stmt->execute([
		$_POST['startDate'],
		$_POST['endDate'],
		$_POST['minimum'],
		$_POST['maximum']
	]);
}

$playersQuery = "SELECT `id`, `username`, `name`, `priority` FROM `players` ORDER BY `priority` DESC, `id` ASC";
$players = $pdo->prepare($playersQuery);
$players->execute();

require_once "php/includes/head.php";
?>
<?php
require_once "php/includes/header.php";
?>
<main>
	<div class="middle">
		<form method="post" class="user container">
			<?php while ($player = $players->fetch()): ?>
				<label><a href="<?= $pages['viewplayer']['route'].$player['id']."/" ?>"><?= $player['name'] ?></a></label>
				<input name="priorities[<?= $player['id'] ?>]" type="number" min="0" id="<?= $player['id'] ?>" value="<?= $player['priority'] ?>">
			<?php endwhile; ?>
			<button type="submit" name="updatePriorities">Update</button>
		</form>
		<form method="post" class="user container">
			<label for="startDate">Starting date</label>
			<input name="startDate" type="date" id="startDate" value="<?= $_POST['startDate'] ?? $settings['start_date'] ?>">
			<label for="endDate">Ending date</label>
			<input name="endDate" type="date" id="endDate" value="<?= $_POST['endDate'] ?? $settings['end_date'] ?>">
			<label for="minimum">Minimum Games</label>
			<input name="minimum" type="number" min=0 max=25 id="minimum" value="<?= $_POST['minimum'] ?? $settings['minimum'] ?>">
			<label for="maximum">Max Displayed</label>
			<input name="maximum" type="number" min=0 max=25 id="maximum" value="<?= $_POST['maximum'] ?? $settings['display max'] ?>">
			<button type="submit" name="updateSettings">Update</button>
		</form>
	</div>
</main>
<?php require "php/includes/footer.php"; ?>