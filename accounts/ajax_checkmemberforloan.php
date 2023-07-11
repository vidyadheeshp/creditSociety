<?php
	// gitsociety
	// Author : Anand V Deshpande, Belagavi
	// Started : 27.10.2019
	// ajax_checkmemberforloan.php
	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();
	$MemberID 	= $_POST['MemberID'];
	$sql = "Select ClosBal from shareholders where MemberID='$MemberID' LIMIT 1";
	$ShareClosBal = getSingleField($db,$sql);
	$sql = "Select DOR from shareholders where MemberID='$MemberID' LIMIT 1";
	$DOR = getSingleField($db,$sql);
	$DOR = date("d-m-Y",strtotime($DOR));
	$ToDay = date("Y-m-d");
	$DaysDiff = getDaysDiff($ToDay,date("Y-m-d",strtotime($DOR)));
	$MonthsLeft = "";
	if($DaysDiff<0){
		$MonthsLeft = " Already retired ";
	} else {
		$MonthsLeft = intval($DaysDiff*12/365);
	}
	$sql = "Select * from customers where MemberID='$MemberID'";
	$LoansSet = getResultSet($db,$sql);
	$TotLoans = 0;
	$LoanAcIDs = "";
	foreach($LoansSet as $row){
		$TotLoans += ($row['ClosBal'])*-1;
		$LoanAcIDs = $LoanAcIDs . ":".$row['LoanID'];
	}
	echo json_encode(array('ClosBal' => $ShareClosBal,"TotLoans"=>$TotLoans,"LoanAcIDs"=>$LoanAcIDs,"DOR"=>$DOR,"MonthsLeft"=>$MonthsLeft), JSON_FORCE_OBJECT);
	exit();
?>