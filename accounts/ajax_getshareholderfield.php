<?php
	// gitsociety
	// Author : Anand V Deshpande, Belagavi
	// Started : 27.10.2019
	// ajax_getshareholderfield.php
	session_start();
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();
	$MemberID	= $_POST['MemberID'];
	$FieldName	= $_POST['FieldName'];
	$sql = "Select $FieldName from shareholders Where MemberID='$MemberID' LIMIT 1";
	$Result = getSingleField($db,$sql);
	echo $Result;
?>