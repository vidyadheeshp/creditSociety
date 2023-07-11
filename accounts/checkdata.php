<?php
	// gitsociety
	// Author : Anand V Deshpande, Belagavi
	// Started : 27.10.2019
	// checkdata.php
	session_start();
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();
	
	//require_once("../includes/pdoconnection_v1.php");
	$FieldName 	= $_POST['FieldName'];
	$FieldVal 	= filter_input(INPUT_POST,'FieldVal',FILTER_SANITIZE_STRING);
	$FileName 	= filter_input(INPUT_POST,'FileName',FILTER_SANITIZE_STRING);
	$sql = "Select Count(*) from $FileName where $FieldName='$FieldVal' LIMIT 1";
	$noofrecs = getSingleField($db,$sql);
	if ($noofrecs <=0) {
		echo json_encode(array('FieldVal' => "Not Found"), JSON_FORCE_OBJECT);
		exit();
	} else {
		echo json_encode(array('FieldVal' => "Found"), JSON_FORCE_OBJECT);
		exit();
	}
?>