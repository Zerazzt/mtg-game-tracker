<?php
require "php/includes/start.php";
session_destroy();
header("location:".$pages['index']['route']);
die();
?>