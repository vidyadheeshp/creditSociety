<?php

session_start();
	require_once('../includes/functions.php');	
	// check usertype
	if(!isset($_SESSION['UserType'])) {
		MsgBox("Direct script access prohibited","../index.php",True);
		exit();
	}

	$UserType 	= $_SESSION['UserType'];
	if($UserType == 'Shares' or $UserType=='Admin'){
        $MemberName="Admin";
	} else{
		MsgBox("Access for Authorised Users only","accountsmenu.php",True);
		exit();
	}
	//	
	$PanelHeading = "Create Monthly Share Contributions";
	include('../includes/pdofunctions_v1.php');

	include('../includes/shares.php');
    
    include_once('navbar.php');
?>