<?php
	// Author : Anand V Deshpande,Belagavi
	// Date Written: 26.10.2019
	// getsharetranlist.php
	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");

	$db = connectPDO();	
	$FromDate 	= $_POST['fromdate'];
	$ToDate 	= $_POST['todate'];

	$Sql = "Select B.FTID,B.TrnDate,B.TrnCode,B.TrnType,B.MemberID,A.Name,B.Debit,B.Credit,B.ForMonth,B.ForYear from ft B, shareholders A Where B.MemberID = A.MemberID and
		TrnDate between '$FromDate' and '$ToDate' 
		Order By B.TrnDate,B.TrnCode ";
	//echo $Sql;
	// function build_table($db,$Sql,$SerialNoReqd,$EditReqd='No',$DelReqd='No',$PrimaryID) {
	$Html = build_table($db,$Sql,"SNo","No","No","FTID");
	echo $Html;
?>