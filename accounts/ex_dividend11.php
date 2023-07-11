<?php
	// ex_dividend11.php

	// Author : Anand V Deshpande
	// Date Written : 23-11-2019
	// Modified for divd calc rd on 03.11.2020

	session_start();
	include("../includes/functions.php");
	include("../includes/pdofunctions_v1.php");
	$FinYear = $_GET['FinYear'];
	$DivPer  = $_GET['DivPer'];
	$Div1Per  = $_GET['Div1Per'];
	$Div2Per  = $_GET['Div2Per'];
	CreateLog("Inside ajax php");
	$db = connectPDO();
	$Excel  = getSingleField($db,"Select SocName from society Where SocID=1");
	$Excel .= "Dividend Calculation for FinYear $FinYear Percentage: ".$Div1Per. " and ".$Div2Per;

	$Excel .= "S.No\t";
	$Excel .= "MemberID\t";
	$Excel .= "Name\t";
	$Excel .= "Dept\t";
	$Excel .= "OpenBal\t";
	$Excel .= "Debits\t";
	$Excel .= "Credits\t";
	$Excel .= "ClosBal\t";
	$Excel .= "Div1\t";
	$Excel .= "Div2\t";
	$Excel .= "Dividend\n";
	$SerialNo=1;
	$Count=0;
	$IndDeb = 0;
	$IndCre = 0;
	$TotalOpenBal=0;
	$TotalClosBal=0;
	$TotDeb = 0;
	$TotCre = 0;
	$TotalDiv1   =0;
	$TotalDiv2   =0;	
	$TotalDividend=0;
	$ResultSet = getResultSet($db,"Select * from dividends Where FinYear='$FinYear' Order By MemberID");
	foreach($ResultSet as $row) {
		$MemberID   = $row['MemberID'];
		$DeptID  	= $row['DeptID'];
		$MemberName = getSingleField($db,"Select Name from shareholders Where MemberID='$MemberID'");
		$Dept     	= getSingleField($db,"Select ShName from departments where DeptID='$DeptID'");
		$Excel .= $SerialNo."\t";
		$Excel .= $row['MemberID']."\t";
		$Excel .= $MemberName."\t";
		$Excel .= $Dept."\t";
		$Excel .= $row['OpenBal']."\t";
		$Excel .= $row['Debits']."\t";
		$Excel .= $row['Credits']."\t";
		$Excel .= $row['ClosBal']."\t";
		$Excel .= $row['Dividend1']."\t";
		$Excel .= $row['Dividend2']."\t";
		$Excel .= $row['DivAmt']."\n";

		$TotalOpenBal	+= $row['OpenBal'];
		$TotalClosBal	+= $row['ClosBal'];
		$TotDeb 		+= $row['Debits'];
		$TotCre 		+= $row['Credits'];		
		$TotalDividend  += $row['DivAmt'];
		$TotalDiv1  	+= $row['Dividend1'];
		$TotalDiv2   	+= $row['Dividend2'];		
		$Count++;
		$SerialNo++;
	}
	$Excel .= "\t";
	$Excel .= "\t";
	$Excel .= "Total\t";
	$Excel .= "\t";
	$Excel .= $TotalOpenBal."\t";
	$Excel .= $TotDeb."\t";
	$Excel .= $TotCre."\t";
	$Excel .= $TotalClosBal."\t";
	$Excel .= $TotalDiv1."\t";
	$Excel .= $TotalDiv2."\t";
	$Excel .= $TotalDividend."\n";
	CreateLog("Excel dividend1 created");
    header("Content-Disposition: attachment; filename=dividendlist11_$FinYear.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    print "$Excel";	
?>
