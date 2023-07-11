<?php
session_start();
$_SESSION['rdrurl'] = $_SERVER['REQUEST_URI'];
include_once('../header.html');
include_once("navbar.php");
include_once('../emicalculator.php');
?>