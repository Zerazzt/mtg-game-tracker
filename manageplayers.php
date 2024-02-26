<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "php/includes/start.php";

$pdo = connectDB();

if (isset($_POST['update'])) {
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
			<button type="submit" name="update">Update</button>
		</form>
	</div>
</main>
<?php require "php/includes/footer.php"; ?>