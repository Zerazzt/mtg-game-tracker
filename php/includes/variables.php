<?php
$pages = [
	"index" => [
		"route" => "/",
		"title" => "Home",
		"require_logged_in" => false,
		"logged_in_nav" => false,
		"logged_out_nav" => false,
	],
	"allplayers" => [
		"route" => "/players/",
		"title" => "Players",
		"require_logged_in" => false,
		"logged_in_nav" => true,
		"logged_out_nav" => true
	],
	"mtgtracker" => [
		"route" => "/tracker/",
		"title" => "Add Data",
		"require_logged_in" => true,
		"logged_in_nav" => false,
		"logged_out_nav" => false,
	],
	"adddeck" => [
		"route" => "/add/deck/",
		"title" => "Add Deck",
		"require_logged_in" => true,
		"logged_in_nav" => true,
		"logged_out_nav" => false
	],
	"addgame" => [
		"route" => "/add/game/",
		"title" => "Add Game",
		"require_logged_in" => true,
		"logged_in_nav" => true,
		"logged_out_nav" => false
	],
	"addplayer" => [
		"route" => "/add/player/",
		"title" => "Add Player",
		"require_logged_in" => true,
		"logged_in_nav" => true,
		"logged_out_nav" => false
	],
	"settings" => [
		"route" => "/settings/",
		"title" => "Settings",
		"require_logged_in" => true,
		"logged_in_nav" => true,
		"logged_out_nav" => false,
	],
	"comparedecks" => [
		"route" => "/compare/decks/",
		"title" => "Compare Decks",
		"require_logged_in" => true,
		"logged_in_nav" => false,
		"logged_out_nav" => false,
	],
	"compareplayers" => [
		"route" => "/compare/players/",
		"title" => "Compare Players",
		"require_logged_in" => false,
		"logged_in_nav" => true,
		"logged_out_nav" => true,
	],
	"comparedecks" => [
		"route" => "/compare/decks/",
		"title" => "Compare Decks",
		"require_logged_in" => false,
		"logged_in_nav" => true,
		"logged_out_nav" => true,
	],
	"login" => [
		"route" => "/login/",
		"title" => "Login",
		"require_logged_in" => false,
		"logged_in_nav" => false,
		"logged_out_nav" => true,
	],
	"logout" => [
		"route" => "/logout/",
		"title" => "Logout",
		"require_logged_in" => true,
		"logged_in_nav" => true,
		"logged_out_nav" => false,
	],
	"viewgame" => [
		"route" => "/view/game/",
		"title" => "View Game",
		"require_logged_in" => false,
		"logged_in_nav" => false,
		"logged_out_nav" => false,
	],
	"viewplayer" => [
		"route" => "/view/player/",
		"title" => "View Player",
		"require_logged_in" => false,
		"logged_in_nav" => false,
		"logged_out_nav" => false,
	],
	"viewdeck" => [
		"route" => "/view/deck/",
		"title" => "View Deck",
		"require_logged_in" => false,
		"logged_in_nav" => false,
		"logged_out_nav" => false,
	],
];

$files = [
	"privacy policy" => [
		"route" => "/privacy-policy/"
	]
];

DEFINE("MAX_PLAYER_COUNT", 8);
?>