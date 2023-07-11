<?php
	// gitsociety
	// Author : Anand V Deshpande, Belagavi
	// Started : 27.10.2019
	// ajax_getfield2.php
	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();
	$FMID	= $_POST['fmid'];
	$sql = "Select ClosBal from fm Where FMID='$FMID' LIMIT 1";
	$ClosBal = getSingleField($db,$sql);
	$ConvAmt = ConvBalance($ClosBal);
	echo json_encode(array('ConvAmt'=> $ConvAmt,'ClosBal'=> $ClosBal), JSON_FORCE_OBJECT);	
?>