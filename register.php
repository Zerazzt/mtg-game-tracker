<?php
error_reporting(E_ALL);
require_once "php/includes/start.php";

$email = $_POST["email"] ?? null;
$password = $_POST["password"] ?? null;
$errors = array();

if (isset($_POST["submit"])) {
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors["email"] = "invalid";
	}
	else {
		$query = "SELECT email FROM admin WHERE email = ?";
		$stmt = $pdo->prepare($query);
		$stmt->execute([$email]);
		if ($stmt->fetch()) {
			$errors["email"] = "taken";
		}
	}

	if (!isset($password)) {
		$errors["password"] = "empty";
	}
	else if (strlen($password) < 8) {
		$errors["password"] = "length";
	}

	if (count($errors) === 0) {
		$query = "INSERT INTO admin (email, password) VALUES (?, ?)";
		$stmt = $pdo->prepare($query);
		$stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);

		header("location:".$pages['login']['route']);
		die();
	}
}

require_once "php/includes/head.php";
?>
<?php
require_once "php/includes/header.php";
?>
<main>
	<form method="post">
		<div>
			<label for="email">Email:</label>
			<input type="email" name="email" id="email" value="<?= $email ?>">
		</div>
		<div>
			<label for="password">Password:</label>
			<input type="password" name="password" id="password">
		</div>
		<button type="submit" name="submit">Submit</button>
	</form>
</main>
<?php require "php/includes/footer.php"; ?>