<?php
	// gitsociety
	// Author : Anand V Deshpande, Belagavi
	// Started : 27.10.2019
	// checkdata.php
	session_start();
	require_once("../pdoconnection_v1.php");
	$FieldName 	= $_POST['FieldName'];
	$FieldVal 	= removespecial2($_POST['FieldVal']);
	$FileName 	= $_POST['FileName'];
	$sql = "Select Count(*) from $FileName where $FieldName='$FieldVal' LIMIT 1";
	$noofrecs = getSingleField($con,$sql);
	if ($noofrecs <=0) {
		echo json_encode(array('FieldVal' => "Not Found"), JSON_FORCE_OBJECT);
		exit();
	} else {
		echo json_encode(array('FieldVal' => "Found"), JSON_FORCE_OBJECT);
		exit();
	}
?>