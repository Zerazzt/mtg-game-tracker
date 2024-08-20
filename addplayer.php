<?php
require_once "php/includes/start.php";

$username = $_POST["username"] ?? null;
$name     = $_POST["name"]     ?? null;

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
		$query = "INSERT INTO `players` (`username`, `name`, `priority`) VALUES (?, ?, 1);";
		$stmt  = $pdo->prepare($query);
		$stmt->execute([
			htmlentities($username),
			htmlentities($name)
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
		<h1>Add a new user</h1>
		<form method="post" class="container user">
			<div>
				<label for="username">Username:</label>
				<input type="text" name="username" id="username">
			</div>
			<div>
				<label for="name">Name:</label>
				<input type="text" name="name" id="name">
			</div>
			<button type="submit" name="addUser">Submit</button>
		</form>
	</div>
</main>
<?php require_once "php/includes/footer.php"; ?>