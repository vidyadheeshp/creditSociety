<?php
	// Author : Anand V Deshpande,Belagavi
	// Date Written: 26.11.2019
	// getloantranlist.php
	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");

	$db = connectPDO();	
	if(isset($_POST['fromdate'])){
		$FromDate 	= $_POST['fromdate'];
		$ToDate 	= $_POST['todate'];
		$Destination= $_POST['destination'];

	} else{
		$FromDate 	= $_GET['fromdate'];
		$ToDate 	= $_GET['todate'];
		$Destination= $_GET['destination'];
	}

	$Sql = "Select B.FTID,B.TrnDate,B.TrnCode,B.TrnType,B.TrnNo,B.LoanID,C.Name,B.Debit,B.Credit,B.Principal,B.Interest,B.Days,B.ForMonth,B.ForYear from ft B, customers A, shareholders C 
		Where B.LoanID = A.LoanID and A.MemberID = C.MemberID And 
		TrnDate between '$FromDate' and '$ToDate' 
		Order By B.TrnDate,B.TrnCode ";
	//echo $Sql;
	// function build_table($db,$Sql,$SerialNoReqd,$EditReqd='No',$DelReqd='No',$PrimaryID) {
	if($Destination=='Display'){
		$Html = build_table($db,$Sql,"SNo","No","No","FTID");
		echo $Html;
	} else {
		$Html = export2excel($db,$Sql,"SNo");
	    header("Content-Disposition: attachment; filename=loanrecoverylist.xls");
	    header("Pragma: no-cache");
	    header("Expires: 0");
	    echo "$Html";	
	    exit();		
	}
?>