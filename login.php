<?php
require_once "php/includes/start.php";

$email    = $_POST['email']    ?? null;
$password = $_POST['password'] ?? null;

$errors = array();

if (isset($_POST['submit'])) {	
	$query = "SELECT id, password FROM admin WHERE email = ?";
	$stmt  = $pdo->prepare($query);
	$stmt->execute([$email]);

	$user = $stmt->fetch();
	var_dump($user);
	if (!$user) {
		$errors['email'] = true;
	}
	else if (!password_verify($password, $user['password'])) {
		$errors['password'] = true;
	}
	else {
		$_SESSION['email'] = $email;
		$_SESSION['id'] = $user['id'];
		header("location:".$pages['index']['route']);
		die();
	}
}

require_once "php/includes/head.php";
require_once "php/includes/header.php";
?>
<main class="no-nav">
	<div class="left">
		<h1>Log In</h1>
	</div>
	<div class="middle">
		<form class="login" method="post">
			<div>
				<label for="username">Email:</label>
				<input type="text" name="email" id="email" value="<?= $email ?>"><span class="error <?= isset($errors['email']) ? "" : "hidden" ?>">Email not found.</span>
			</div>
			<div>
				<label for="password">Password:</label>
				<input type="password" name="password" id="password"><span class="error <?= isset($errors['password']) ? "" : "hidden" ?>">Incorrect password.</span>
			</div>
			<button type="submit" name="submit">Log In</button>
		</form>
	</div>
</main>
<?php require 'php/includes/footer.php'; ?>