<?php
	// Author: Anand V Deshpande
	// Date written : 09.11.2019
	//ajax_loanaccountledger.php
	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	require_once("../includes/loans.php");
	$db = connectPDO();
	$LoanRecSet="";
	$LoanID="";
    $MemberRow = $_SESSION['UserRow'];
    $MemberName="";
    $MemberID="";
    foreach($MemberRow as $Row){
        $MemberID = $Row['MemberID'];
        $MemberName = $Row['Name'];
	if(isset($_POST['LoanID'])){
		$LoanID   	= $_POST['LoanID'];
        $LoanRecSet=getResultSet($db,"Select * from loanappl Where LoanID='$LoanID'");
        if(count($LoanRecSet)>0){
            foreach($LoanRecSet as $row){
                $set=array();
                if($row['G1MemberID']==$MemberID){
                    $set["G1Approved"]="No";
                    $where="LoanID='$LoanID'";
                   $result= update($db,"loanappl",$set,$where);
                }
                else{
                    $set["G2Approved"]="No";
                    $where="LoanID='$LoanID'";
                    $result=update($db,"loanappl",$set,$where);
                }
            }
            if($result){
                echo "Your rejection is registered successfully. Thank you!";
            }
        }
        else{
            echo "No records found.";
        }
    }
}