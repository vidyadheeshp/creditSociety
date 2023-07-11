<?php
    session_start();
    require_once('../includes/functions.php');
    if(!isset($_SESSION['UserType'])) {
       MsgBox("Direct script access prohibited","../index.php",True);
       exit();
    }	
    $UserType 	= $_SESSION['UserType'];
	
    if($UserType == 'Member'){
    } else{
       MsgBox("Access for Members only","../login.php",True);
       exit();
    }	
    include('../includes/pdofunctions_v1.php');
    $db = connectPDO();
    
    include('../includes/shares.php'); 
    $Date = date("d-m-Y H:i");
   //var_dump($_SESSION['UserRow']);
    $MemberRow = $_SESSION['UserRow'];
    $MemberName="";
    $MemberID="";
    include_once("navbar.php");