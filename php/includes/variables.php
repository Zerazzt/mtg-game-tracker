<?php
$logged_in_nav = [ // Pages which show up in the nav when logged in.
	"mtgtracker",
	"compareplayers",
	"logout"
];

$logged_out_nav = [ // Pages which show up in the nav when logged out.
	"compareplayers",
	"login"
];

$require_logged_in = [ // Pages which require the user to be logged in to access.
	"logout",
	"mtgtracker"
];

$page_titles = [ // Mapping of file names to page titles.
	"index"          => "Home",
	"mtgtracker"     => "Add Results",
	"compareplayers" => "Compare Players",
	"logout"         => "Log Out",
	"login"          => "Log In",
	"viewgame"       => "View Game"
];

$page_routes = [
	"index"          => "/",
	"mtgtracker"     => "/tracker",
	"login"          => "/login",
	"logout"         => "/logout",
	"compareplayers" => "/compare/players"
];

DEFINE("MAX_PLAYER_COUNT", 8);
?>