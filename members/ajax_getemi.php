<?php
	// gitsociety
	// Author : Anand V Deshpande, Belagavi
	// Started : 22.12.2019
	// ajax_getemi.php
	session_start();
	require_once("../includes/loans.php");
	$Principal = intval($_POST['LoanAmt']);
	$Years     = intval($_POST['Years']);
	$IntRate   = floatval($_POST['IntRate']);
	//echo $Principal.'-'.$Years.'-'.$IntRate.'<br/>';
	$EMI = intval(emi_calculator($Principal, $IntRate, $Years));
	echo $EMI;
?>