<?php
	// gitsociety
	// Author : Anand V Deshpande, Belagavi
	// Started : 27.10.2019
	// ajax_checkguarantor.php
	session_start();
	require_once("../includes/pdofunctions_v1.php");
	require_once("../includes/functions.php");
	$db = connectPDO();
	$MemberID 	= $_POST['MemberID'];
	CreateLog("Inside ajax checkguarantor MemberID: $MemberID");
	$sql = "Select * from customers where (G1MemberID='$MemberID' OR G2MemberID='$MemberID' ) AND ClosBal<0 LIMIT 1";
	CreateLog($sql);
	$GuarantorsInfo = "";
	$Rs = getResultSet($db,$sql);
	foreach($Rs as $row){
		$LoanMemberID = $row['MemberID'];
		$GuarantorsInfo .= $row['LoanID'];
		$GuarantorsInfo .= getSingleField($db,"Select Name from shareholders Where MemberID='$LoanMemberID'");
		$GuarantorsInfo .= " Bal: ". ($row['ClosBal'])*-1 . "<br>";
	}
	//$GuarantorForNos = getSingleField($db,$sql);
	$ShareClosBal = getSingleField($db,"Select ClosBal from shareholders Where MemberID='$MemberID'");
	
	$sql 	= "Select DOR from shareholders where MemberID='$MemberID' LIMIT 1";
	$DOR 	= getSingleField($db,$sql);
	$DOR 	= date("d-m-Y",strtotime($DOR));
	$ToDay 	= date("Y-m-d");
	$DaysDiff = getDaysDiff($ToDay,date("Y-m-d",strtotime($DOR)));
	CreateLog("Today " . $ToDay. " DOR ".date("Y-m-d",strtotime($DOR)));
	CreateLog("DaysLeft".$DaysDiff);
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
	$LoanDescription = "";
	foreach($LoansSet as $row){
		$TotLoans += ($row['ClosBal'])*-1;
		$LoanAcIDs .= $LoanAcIDs . ":".$row['LoanID'];
		$LoanDescription .= $row['LoanID']." Bal: ". ($row['ClosBal'])*-1 . "<br>";
	}
	echo json_encode(array('ClosBal' => $ShareClosBal,"TotLoans"=>$TotLoans,"LoanAcIDs"=>$LoanAcIDs,'DOR'=> $DOR,"MonthsLeft"=>$MonthsLeft,"DaysLeft"=>$DaysDiff,"Description"=>$LoanDescription,"GuarantorsInfo"=>$GuarantorsInfo), JSON_FORCE_OBJECT);
	exit();
?>