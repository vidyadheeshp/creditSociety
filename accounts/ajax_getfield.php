<?php
	// gitsociety
	// Author : Anand V Deshpande, Belagavi
	// Started : 27.10.2019
	// ajax_getfield.php
	session_start();
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();
	$FMID	= $_POST['fmid'];
	$FieldName = $_POST['fieldname'];
	$sql = "Select $FieldName from fm Where FMID='$FMID' LIMIT 1";
	$Result = getSingleField($db,$sql);
	echo $Result;
?>