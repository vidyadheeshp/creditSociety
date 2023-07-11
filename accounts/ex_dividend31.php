<?php
	// ex_dividend31.php

	// Author : Anand V Deshpande
	// Date Written : 23-11-2019
	// Modified for divd calc rd  on 03.11.2020

	session_start();
	include("../includes/functions.php");
	include("../includes/pdofunctions_v1.php");
	$FinYear = $_GET['FinYear'];
	$Div1Per  = $_GET['Div1Per'];
	$Div2Per  = $_GET['Div2Per'];
	CreateLog("Inside ajax php");
	$db = connectPDO();
	$Excel = "";
	$Excel  = getSingleField($db,"Select SocName from society Where SocID=1")."\n";
	$Excel .= "Dividend Calculation for FinYear $FinYear Percentage: ".$Div1Per." and ".$Div2Per."\n";


	$Sql  = "Select DeptID,Count(*) as mCount,SUM(OpenBal) as S_OpenBal,SUM(Debits) as S_Debits,SUM(Credits) as S_Credits,SUM(ClosBal) as S_ClosBal,
		SUM(DivAmt) as S_Dividend,
		SUM(Dividend1) as S_Dividend1,
		SUM(Dividend2) as S_Dividend2 
	 from dividends Where FinYear='$FinYear' Group By DeptID";
	$result = getResultSet($db,$Sql);
	$Excel .= "SNo\t";
	$Excel .= "DeptID\t";
	$Excel .= "Department\t";
	$Excel .= "Count\t";
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
	$TotalCount  =0;
	$TotalDividend=0;
	foreach($result as $row) {
		$DeptID = $row['DeptID'];
		$Dept  	= getSingleField($db,"Select DeptName from departments Where DeptID='$DeptID'");
		$Excel .= $SerialNo."\t";
		$Excel .= $row['DeptID']."\t";
		$Excel .= $Dept."\t";
		$Excel .= $row['mCount']."\t";
		$Excel .= $row['S_OpenBal']."\t";
		$Excel .= $row['S_Debits']."\t";
		$Excel .= $row['S_Credits']."\t";
		$Excel .= $row['S_ClosBal']."\t";
		$Excel .= $row['S_Dividend1']."\t";
		$Excel .= $row['S_Dividend2']."\t";
		$Excel .= $row['S_Dividend']."\n";

		$SerialNo++;
		$TotalDiv1  	+= $row['S_Dividend1'];
		$TotalDiv2   	+= $row['S_Dividend2'];
		$TotalOpenBal  	+= $row['S_OpenBal'];
		$TotalDebits   	+= $row['S_Debits'];
		$TotalCredits 	+= $row['S_Credits'];
		$TotalClosBal  	+= $row['S_ClosBal'];
		$TotalDividend 	+= $row['S_Dividend'];
		$TotalCount     += $row['mCount'];
	}
	$Excel .= "\t";
	$Excel .= "\t";
	$Excel .= "\t";
	$Excel .= $TotalCount."\t";
	$Excel .= $TotalOpenBal."\t";
	$Excel .= $TotalDebits."\t";
	$Excel .= $TotalCredits."\t";
	$Excel .= $TotalClosBal."\t";
	$Excel .= $TotalDiv1."\t";
	$Excel .= $TotalDiv2."\t";
	$Excel .= $TotalDividend."\t";
	CreateLog("Excel dividend3 created");
    header("Content-Disposition: attachment; filename=dividendlist31_$FinYear.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    print "$Excel";		
?>
