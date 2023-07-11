<?php

// reploanmthemi.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 26.12.2019
//'
// modified on 05.06.2020 for General Accountwise List'	 of Loans

session_start();
require_once("../includes/functions.php");

// check usertype
	if(!isset($_SESSION['UserType'])) {
		MsgBox("Direct script access prohibited","../index.php",True);
		exit();
	}

	$UserType 	= $_SESSION['UserType'];
	if(strstr('Loans,Admin,Chairman',$UserType)){
	} else{
		MsgBox("Access for Authorised Users only","accountsmenu.php",True);
		exit();
	}
//
//require_once("../includes/functions.php");
require_once("../includes/pdofunctions_v1.php");
$db = connectPDO();
$PanelHeading = "Monthly EMI for Loan Accounts List ";
$Html = "";
$Sql  = "Select Name,FMID from fm Where AcType ='Cust' Order By FMID ";
$FMList = getResultSet($db,$Sql);
foreach($FMList as $fmrow) {
	$FMID 		= $fmrow['FMID'];
	$AccName 	= $fmrow['Name'];

	$Sql  = "Select B.*,A.Name,A.DeptID,A.DesignID from customers B, shareholders A 
			Where B.MemberID = A.MemberID  
			AND B.MthEMI > 0 AND B.Status='Active' and B.FMID = '$FMID'  
			Order By LoanID";
	$result = getResultSet($db,$Sql);
	$Html .=  "<strong>Account: ".$AccName. "  AccID: ".$FMID."</strong><br>";
	$Html .= "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed'>";
	$Html .= "<thead>";
	$Html .= "<tr>";
	$Html .= "<th>SNo</th>";
	$Html .= "<th>LoanID</th>";
	$Html .= "<th>MemberID</th>";
	$Html .= "<th>Name</th>";
	$Html .= "<th>Dept</th>";
	$Html .= "<th>LoanDate</th>";
	$Html .= "<th>LoanAmt</th>";
	$Html .= "<th>IntRt</th>";
	$Html .= "<th>Months</th>";
	$Html .= "<th>Balance</th>";
	$Html .= "<th>IntUpto</th>";
	$Html .= "<th>MthEMI</th>";
	$Html .= "</tr>";
	$Html .= "</thead>";

	$Html .= "<tbody>";
	$SerialNo = 1;
	$TotalClosBal = 0;
	$TotalMthEMI  = 0;
	foreach($result as $row) {
		$LoanID 	= $row['LoanID'];
		$DeptID    	= $row['DeptID'];
		$MemberID  	= $row['MemberID'];
		$Dept  		= getSingleField($db,"Select ShName        from departments Where DeptID='$DeptID'");
		$Html .= "<tr>";
		$Html .= "<td>$SerialNo</td>";
		$Html .= "<td>".$row['LoanID']."</td>";
		$Html .= "<td>".$row['MemberID']."</td>";
		$Html .= "<td>".$row['Name']."</td>";
		$Html .= "<td>".$Dept."</td>";
		$Html .= "<td align='right' nowrap>".date("d-m-Y",strtotime($row['LoanDate']))."</td>";
		$Html .= "<td align='right'>".$row['LoanAmt']."</td>";
		$Html .= "<td align='right'>".$row['IntRate']."</td>";
		$Html .= "<td align='right'>".$row['Months']."</td>";
		$Html .= "<td align='right' nowrap>".ConvBalance($row['ClosBal'])."</td>";
		$Html .= "<td align='right'>".date("d-m-Y",strtotime($row['LastRecDate']))."</td>";
		$Html .= "<td align='right'>".$row['MthEMI']."</td>";
		$Html .= "</tr>";

		$SerialNo++;
		$TotalClosBal  	+= $row['ClosBal'];
		$TotalMthEMI    += $row['MthEMI'];
	}
	$Html .= "</tbody>";
	$Html .= "<tfoot>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td align='right'>".ConvBalance($TotalClosBal)."</td>";
	$Html .= "<td></td>";
	$Html .= "<td align='right'>".$TotalMthEMI."</td>";
	$Html .= "</tr>";
	$Html .= "</tfoot>";
	$Html .= "</table>";

}


$Report = $Html;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style>
</style>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KLS GIT employees Co-Op Credit Society Ltd.</title>
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/git.png">
	<link  href="../bootstrap/dist/css/bootstrap.css" rel="stylesheet" />
	<script src="../bootstrap/dist/js/jquery-1.10.2.js"></script>
	<script src="../bootstrap/dist/js/bootstrap.min.js"></script>   
	<script src="../assets/js/dt/jquery.datatables.js"></script>
	<script src="../assets/js/dt/datatables.bootstrap.js"></script>
	<link  href="../assets/js/dt/datatables.bootstrap.css" rel="stylesheet" />
</head>
<style>
	body { 
	  background: url(assets/images/entrance.jpg) no-repeat center center fixed; 
	  -webkit-background-size: cover;
	  -moz-background-size: cover;
	  -o-background-size: cover;
	  background-size: cover;
	}
</style>
<body>
    <div class="container-fluid">
    	<div class="col-md-12">
    		<div class='row'>
				<?php include('accountsmenu.ini'); ?>
			</div>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
                    <center><h4><?php echo $PanelHeading; ?></h4></center>
				</div>
				<div class='panel-body'>
					<div class='table-responsive'>
					<?php echo $Report; ?>
					</div>
				</div>
				<div class='panel-footer'>
				</div>
			</div>
		</div>
	</div>
</body>
