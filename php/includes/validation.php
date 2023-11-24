<?php
function validateUsername($username) {
	$pdo = connectDB();
	$error = null;

	if (strlen($username) === 0 || strlen($username) > 64) {
		$error = "invalid";
	}
	else {
		$query = "SELECT username FROM players WHERE username = ?";
		$stmt  = $pdo->prepare($query);
		$stmt->execute([$username]);
		if ($stmt->fetch()) {
			$error = "taken";
		}
	}

	return $error;
}

function validateName($name) {
	$error = null;
	if (strlen($name) === 0 || strlen($name) > 64) {
		$error = "invalid";
	}
	return $error;
}

function validateOwner($owner) {
	$error = null;
	$pdo   = connectDB();
	$query = "SELECT id FROM players WHERE id = ?";
	$stmt  = $pdo->prepare($query);
	$stmt->execute([$owner]);
	if (!$stmt->fetch()) {
		$error = "unknown";
	}
	return $error;
}

function validateCommander($commander) {
	$error = null;
	if (strlen($commander) === 0 || strlen($commander) > 64) {
		$error = "invalid";
	}
	return $error;
}

function validatePartner($partner) {
	$error = null;
	if (strlen($partner) > 64) {
		$error = "invalid";
	}
	return $error;
}

function validateCompanion($companion) {
	$error = null;
	if (strlen($companion) > 64) {
		$error = "invalid";
	}
	return $error;
}

function validateEmail($email, $email_confirm) {
	$pdo = connectDB();
	$error = null;

	if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 64) {
		$error = "invalid";
	}
	else if ($email !== $email_confirm) {
		$error = "matching";
	}

	return $error;
}

function validatePassword($password, $password_confirm) {
	$error = null;

	if (strlen($password) < 8) {
		$error = "invalid";
	}
	else if ($password !== $password_confirm) {
		$error = "matching";
	}

	return $error;
}

function validateAddress($address) {
	$error = null;
	return $error;
}

function validateCity($city) {
	$error = null;
	return $error;
}

function validateProvince($province) {
	$error = null;

	$provinces = [
		"Alberta",
		"British Columbia",
		"Manitoba",
		"Newfoundland",
		"New Brunswik",
		"Northwest Territories",
		"Nova Scotia",
		"Nunavut",
		"Ontario",
		"Prince Edward Island",
		"Quebec",
		"Saskatchewan",
		"Yukon"
	];
	if (!in_array($province, $provinces)) {
		$error = "invalid";
	}

	return $error;
}

function validatePostalCode($postal_code) {
	$error = null;
	return $error;
}

function validatePhoneNumber($phone) {
	$error = null;
	return $error;
}

function validateTitle($title) {
	$error = null;
	return $error;
}

function validateDescription($description) {
	$error = null;
	return $error;
}

function validateCost($cost) {
	$error = null;
	return $error;
}

function validateQuantity($quantity) {
	$error = null;
	return $error;
}
?>