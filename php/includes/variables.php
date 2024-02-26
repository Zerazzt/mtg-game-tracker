<?php
$pages = [
	"index" => [
		"route" => "/",
		"title" => "Trent Magic the Gathering Community",
		"require_logged_in" => false,
		"logged_in_nav" => false,
		"logged_out_nav" => false,
	],
	"mtgtracker" => [
		"route" => "/tracker/",
		"title" => "Add Results",
		"require_logged_in" => true,
		"logged_in_nav" => true,
		"logged_out_nav" => false,
	],
	"manageplayers" => [
		"route" => "/manage/players/",
		"title" => "Manage Players",
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

DEFINE("MAX_PLAYER_COUNT", 8);
?>