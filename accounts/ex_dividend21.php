<?php
	// ex_dividend21.php

	// Author : Anand V Deshpande
	// Date Written : 23-11-2019
	// Modified 03.11.2020 for new div calc rd

	session_start();
	include("../includes/functions.php");
	include("../includes/pdofunctions_v1.php");
	$FinYear = $_GET['FinYear'];
	$Div1Per  = $_GET['Div1Per'];
	$Div2Per  = $_GET['Div2Per'];
	CreateLog("Inside ajax php");
	$db = connectPDO();


	$Excel = "";
	$Sql  = "Select DISTINCT(DeptID) from dividends Where FinYear='$FinYear' Order By DeptID";
	$result = getResultSet($db,$Sql);
	foreach($result as $rowDept) {
		$DeptID = $rowDept['DeptID'];
		$Dept  	= getSingleField($db,"Select DeptName from departments Where DeptID='$DeptID'");

		$Excel .= getSingleField($db,"Select SocName from society Where SocID=1")."\n";
		$Excel .= "Dividend Calculation for FinYear $FinYear Percentage: ".$Div1Per." and ".$Div2Per . " For : ".$Dept."\n";
		$Excel .= "SNo\t";
		$Excel .= "MemberID\t";
		$Excel .= "Name\t";
		$Excel .= "OpenBal\t";
		$Excel .= "Debit\t";
		$Excel .= "Credit\t";
		$Excel .= "Balance\t";
		$Excel .= "Div1\t";
		$Excel .= "Div2\t";
		$Excel .= "Dividend\n";
		$SerialNo = 1;
		$TotalDiv1   =0;
		$TotalDiv2   =0;
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
			$Excel .= $row['Dividend1']."\t";
			$Excel .= $row['Dividend2']."\t";
			$Excel .= $row['DivAmt']."\n";

			$SerialNo++;
			$TotalOpenBal  	+= $row['OpenBal'];
			$TotalDebits   	+= $row['Debits'];
			$TotalCredits 	+= $row['Credits'];
			$TotalClosBal  	+= $row['ClosBal'];
			$TotalDiv1  	+= $row['Dividend1'];
			$TotalDiv2   	+= $row['Dividend2'];
			$TotalDividend 	+= $row['DivAmt'];
		}
		$Excel .= "\t";
		$Excel .= "\t";
		$Excel .= "\t";
		$Excel .= $TotalOpenBal."\t";
		$Excel .= $TotalDebits."\t";
		$Excel .= $TotalCredits."\t";
		$Excel .= $TotalClosBal."\t";
		$Excel .= $TotalDiv1."\t";
		$Excel .= $TotalDiv2."\t";
		$Excel .= $TotalDividend."\n\n";
	}
	CreateLog("Excel dividend3 created");
    header("Content-Disposition: attachment; filename=dividendlist21_$FinYear.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    print "$Excel";		
?>
