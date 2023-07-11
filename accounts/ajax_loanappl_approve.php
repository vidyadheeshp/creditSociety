<?php
	// gitsociety
	// Author : Anand V Deshpande, Belagavi
	// Started : 27.10.2019
	// ajax_loanappl_approve.php
	session_start();
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();
	require_once("../includes/functions.php");
	$RowID	= $_POST['RowID'];
	$UserID = $_SESSION['UserID'];

	$Array  = array("Status"=>'Approved');
	$Where  = "RowID='$RowID'";
	$RetVal = update($db,"loanappl",$Array,$Where);
	if($RetVal){
		CreateLog("Loan Application ID=$RowID Accepted");
		$LogDesc = "Loan Appl ID=$RowID Approved by  User:".$UserID;
		$RetVal = insert($db,"logfile",array("LogType"=>'LoanAppl',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));			
		echo "Successfully Approved";
	} else{
		CreateLog("Loan Application ID=$RowID couldnot reject");
		$LogDesc = "Loan Appl ID=$RowID couldnot approve by  User:".$UserID;
		$RetVal = insert($db,"logfile",array("LogType"=>'LoanAppl',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));			
		echo "Sorry Couldnot Approve ";
	}
?>