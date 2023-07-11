<?php
	// gitsociety
	// Author : Anand V Deshpande, Belagavi
	// Started : 27.10.2019
	// ajax_checksharedataposted.php
	session_start();
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();
	$Month 		= $_POST['mm'];
	$Year 		= $_POST['yy'];
	$sql = "Select Count(*) from ft Where ForMonth='$Month' AND ForYear='$Year' AND Credit>0 and TrnType='MthContr' LIMIT 1";
	$noofrecs = getSingleField($db,$sql);
	if ($noofrecs <=0) {
		echo json_encode(array('Response' => "Not Posted"), JSON_FORCE_OBJECT);
		exit();
	} else {
		echo json_encode(array('Response' => "Already $noofrecs entries posted"), JSON_FORCE_OBJECT);
		exit();
	}
?>