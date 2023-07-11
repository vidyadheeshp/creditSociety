<?php
	// gitsociety
	// Author : Anand V Deshpande, Belagavi
	// Started : 22.12.2019
	// ajax_getloansettings.php
	session_start();
	require_once("../includes/pdofunctions_v1.php");
	require_once("../includes/functions.php");
	$db = connectPDO();
	$LoanFMID 	= $_POST['LoanFMID'];
	$IntRate    = getSingleField($db,"Select LoanIntRate from loansettings Where LoanFMID='$LoanFMID'");
	echo $IntRate;
?>