<?php
require_once "php/includes/start.php";

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

$org = $_POST['org'] ?? $settings['name'];
$start = $_POST['startDate'] ?? $settings['start_date'];
$end = $_POST['endDate'] ?? $settings['end_date'];
$min = $_POST['minimum'] ?? $settings['minimum'];
$max = $_POST['maximum'] ?? $settings['display max'];
$errors = array();

if (isset($_POST['updateSettings'])) {
	$query = "UPDATE `settings`
	SET `name` = ?,
	`start_date` = ?,
	`end_date` = ?,
	`minimum` = ?,
	`display max` = ?
	WHERE `id` = 1";
	$stmt = $pdo->prepare($query);

	$error = validateOrganisation($org);
	if (isset($error)) {
		$errors["$error org"] = true;
	}

	if ($end < $start) {
		$errors["swapped dates"] = true;
	}

	if ($min < 0) {
		$errors["invalid minimum"] = true;
	}

	if ($max < 1) {
		$errors["invalid display"] = true;
	}

	if (count($errors) === 0) {
		$stmt->execute([
			$org,
			$start,
			$end,
			$min,
			$max
		]);
	}
}

$playersQuery = "SELECT
	`id`,
	`username`,
	`name`,
	`priority`
FROM `players`
ORDER BY `priority` DESC,
`id` ASC";
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
			<label for="name">Organisation</label>
			<div>
				<input name="organisation" type="text" id="organisation" value="<?= $org ?>">
				<span class="error<?= isset($errors['invalid org']) ? "" : " hidden" ?>">Invalid name.</span>
			</div>
			<div>
				<div><label for="startDate">Starting date</label></div>
				<div><label for="endDate">Ending date</label></div>
			</div>
			<div>
				<div><input name="startDate" type="date" id="startDate" value="<?= $start ?>"></div>
				<div><input name="endDate" type="date" id="endDate" value="<?= $end ?>"></div>
				<div><span class="error<?= isset($errors['swapped dates']) ? "" : " hidden" ?>">Ending date must be after the starting date.</span></div>
			</div>
			<label for="minimum">Minimum Games</label>
			<div>
				<input name="minimum" type="number" min=0 max=25 id="minimum" value="<?= $min ?>">
				<span class="error<?= isset($errors['invalid minimum']) ? "" : " hidden" ?>">Must be a minimum of 0.</span>
			</div>
			<label for="maximum">Max Displayed</label>
			<div>
				<input name="maximum" type="number" min=1 max=25 id="maximum" value="<?= $max ?>">
				<span class="error<?= isset($errors['invalid display']) ? "" : " hidden" ?>">Must be a minimum of 1.</span>
			</div>
			<button type="submit" name="updateSettings">Update</button>
		</form>
	</div>
</main>
<?php require "php/includes/footer.php"; ?>