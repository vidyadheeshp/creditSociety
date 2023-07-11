<?php
	// ex_dividend2.php

	// Author : Anand V Deshpande
	// Date Written : 23-11-2019

	session_start();
	include("../includes/functions.php");
	include("../includes/pdofunctions_v1.php");
	$FinYear = $_GET['FinYear'];
	$DivPer  = $_GET['DivPer'];
	CreateLog("Inside ajax php");
	$db = connectPDO();


	$Excel = "";
	$Sql  = "Select DISTINCT(DeptID) from dividends Where FinYear='$FinYear' Order By DeptID";
	$result = getResultSet($db,$Sql);
	foreach($result as $rowDept) {
		$DeptID = $rowDept['DeptID'];
		$Dept  	= getSingleField($db,"Select DeptName from departments Where DeptID='$DeptID'");

		$Excel .= getSingleField($db,"Select SocName from society Where SocID=1")."\n";
		$Excel .= "Dividend Calculation for FinYear $FinYear Percentage: ".$DivPer." For : ".$Dept."\n";
		$Excel .= "SNo\t";
		$Excel .= "MemberID\t";
		$Excel .= "Name\t";
		$Excel .= "OpenBal\t";
		$Excel .= "Debit\t";
		$Excel .= "Credit\t";
		$Excel .= "Balance\t";
		$Excel .= "Dividend\n";

		$SerialNo = 1;
		$TotalOpenBal=0;
		$TotalDebits=0;
		$TotalCredits=0;
		$TotalClosBal=0;
		$TotalDividend=0;

		$Sql2  = "Select * from dividends Where FinYear='$FinYear' and DeptID='$DeptID' Order By MemberID"; 
		$result2 = getResultSet($db,$Sql2);
		foreach($result2 as $row) {
			$MemberID   = $row['MemberID'];
			$MemberName = getSingleField($db,"Select Name from shareholders Where MemberID='$MemberID'");			
			$Excel .= $SerialNo."\t";
			$Excel .= $row['MemberID']."\t";
			$Excel .= $MemberName."\t";
			$Excel .= $row['OpenBal']."\t";
			$Excel .= $row['Debits']."\t";
			$Excel .= $row['Credits']."\t";
			$Excel .= $row['ClosBal']."\t";
			$Excel .= $row['DivAmt']."\n";

			$SerialNo++;
			$TotalOpenBal  	+= $row['OpenBal'];
			$TotalDebits   	+= $row['Debits'];
			$TotalCredits 	+= $row['Credits'];
			$TotalClosBal  	+= $row['ClosBal'];
			$TotalDividend 	+= $row['DivAmt'];
		}
		$Excel .= "\t";
		$Excel .= "\t";
		$Excel .= "\t";
		$Excel .= $TotalOpenBal."\t";
		$Excel .= $TotalDebits."\t";
		$Excel .= $TotalCredits."\t";
		$Excel .= $TotalClosBal."\t";
		$Excel .= $TotalDividend."\n\n";
	}
	CreateLog("Excel dividend3 created");
    header("Content-Disposition: attachment; filename=dividendlist2.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    print "$Excel";		
?>
