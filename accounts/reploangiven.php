<?php

// reploangivem.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 25.02.2020
//prints loan given statement
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
	$Report = "";
//
//require_once("../includes/functions.php");
require_once("../includes/pdofunctions_v1.php");
require_once("../includes/functions.php");

$db = connectPDO();
$PanelHeading = "Loan Given Statement";
//}

$Html = "";
if(isset($_POST['Submit'])) {
	$Sql  	= "Select Name,FMID from fm Where AcType ='Cust' Order By FMID ";
	$FMList = getResultSet($db,$Sql);
	foreach($FMList as $fmrow) {
		$FMID 		= $fmrow['FMID'];
		$AccName 	= $fmrow['Name'];

		$FromDate 	= date("Y-m-d",strtotime($_POST['fromdate']));
		$ToDate 	= date("Y-m-d",strtotime($_POST['todate']));
		$PanelHeading = "Loan Given Statement From ".date("d-m-Y",strtotime($FromDate)). " To ".date("d-m-Y",strtotime($ToDate));
		$Sql  = "Select B.*,A.Name,A.DeptID,D.ShName,A.DesignID from customers B, shareholders A , departments D
				Where B.MemberID = A.MemberID  AND A.DeptID = D.DeptID 
				AND B.MthEMI > 0 AND B.Status='Active' AND B.ClosBal<0 AND B.LoanDate >= '$FromDate' AND B.LoanDate <= '$ToDate' AND B.FMID = '$FMID'
				Order By D.ShName,LoanID";
		$result = getResultSet($db,$Sql);
		$Html .=  "<h3>Account: ".$AccName. "  AccID: ".$FMID."</h3>";

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
		$Html .= "<th>Intrate</th>";
		$Html .= "<th>MthEMI</th>";
		$Html .= "</tr>";
		$Html .= "</thead>";

		$Html .= "<tbody>";
		$SerialNo 		= 1;
		$TotalMthEMI  	= 0;
		$TotalLoan  	= 0;
		foreach($result as $row) {
			$LoanID 	= $row['LoanID'];
			$DeptID    	= $row['DeptID'];
			$MemberID  	= $row['MemberID'];
			//$Dept  		= getSingleField($db,"Select ShName from departments Where DeptID='$DeptID'");
			$ClosBal  = $row['ClosBal'];
			$LoanDate = date("Y-m-d",strtotime($row['LoanDate']));
			$IntRate  = $row['IntRate'];
			$MthEMI     = $row['MthEMI'];
			$Html .= "<tr>";
			$Html .= "<td>$SerialNo</td>";
			$Html .= "<td>".$row['LoanID']."</td>";
			$Html .= "<td>".$row['MemberID']."</td>";
			$Html .= "<td>".$row['Name']."</td>";
			$Html .= "<td>".$row['ShName']."</td>";
			$Html .= "<td align='right'>".date("d-m-Y",strtotime($row['LoanDate']))."</td>";
			$Html .= "<td align='right'>".$row['LoanAmt']."</td>";
			$Html .= "<td align='right'>".$row['IntRate']."</td>";
			$Html .= "<td align='right'>".$row['MthEMI']."</td>";
			$Html .= "</tr>";

			$SerialNo++;
			$TotalLoan      += $row['LoanAmt'];
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
		$Html .= "<td align='right'>".$TotalLoan."</td>";
		$Html .= "<td></td>";
		$Html .= "<td align='right'>".$TotalMthEMI."</td>";
		$Html .= "</tr>";
		$Html .= "</tfoot>";
		$Html .= "</table>";
	}
	$Report = $Html;
}

if(isset($_POST['Submit2'])) {

	$FromDate = date("Y-m-d",strtotime($_POST['fromdate']));
	$ToDate = date("Y-m-d",strtotime($_POST['todate']));
	$PanelHeading = "Loan Given Statement From ".date("d-m-Y",strtotime($FromDate)). " To ".date("d-m-Y",strtotime($ToDate));
	$Html = "";
	$Sql  = "Select B.*,A.Name,A.DeptID,D.ShName,A.DesignID from customers B, shareholders A , departments D
			Where B.MemberID = A.MemberID  AND A.DeptID = D.DeptID 
			AND B.MthEMI > 0 AND B.Status='Active' AND B.ClosBal<0 AND B.LoanDate >= '$FromDate' AND B.LoanDate <= '$ToDate'  
			Order By D.ShName,LoanID";
	$result = getResultSet($db,$Sql);

	$Html  = "KLS Gogte Institute of Technology\n";
	$Html .= "Employees Co-Op Credit Society Ltd.,\n";
	$Html .= "New Loans during ".date("d-m-Y",strtotime($FromDate)). " and ".date("d-m-Y",strtotime($ToDate)). "\n";
	$Html .= "\n";
	$Html .= "SNo\t";
	$Html .= "Name\t";
	$Html .= "Dept\t";
	$Html .= "MthEMI\t\n";
	$SerialNo 		= 1;
	$TotalMthEMI  	= 0;
	$TotalLoan  	= 0;
	foreach($result as $row) {
		$LoanID 	= $row['LoanID'];
		$DeptID    	= $row['DeptID'];
		$MemberID  	= $row['MemberID'];
		//$Dept  		= getSingleField($db,"Select ShName from departments Where DeptID='$DeptID'");
		$ClosBal  = $row['ClosBal'];
		$LoanDate = date("Y-m-d",strtotime($row['LoanDate']));
		$IntRate  = $row['IntRate'];
		$MthEMI     = $row['MthEMI'];
		$Html .= $SerialNo."\t";
		$Html .= $row['Name']."\t";
		$Html .= $row['ShName']."\t";
		$Html .= $row['MthEMI']."\t\n";
		$SerialNo++;
		$TotalMthEMI    += $row['MthEMI'];
	}
	$Html .= "\t\t\t";
	$Html .= $TotalMthEMI."\n";

    header("Content-Disposition: attachment; filename=newloans.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    print "$Html";
    exit();
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style>
</style>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KLS GIT employees Co-Op Credit Society Ltd.</title>
	<link   rel= "shortcut icon" type="image/x-icon" href="assets/img/git.png">
	<link  href= "../bootstrap/dist/css/bootstrap.css" rel="stylesheet" />
	<script src= "../bootstrap/dist/js/jquery-1.10.2.js"></script>
	<script src= "../bootstrap/dist/js/bootstrap.min.js"></script>   
	<script src= "../assets/js/dt/jquery.datatables.js"></script>
	<script src= "../assets/js/dt/datatables.bootstrap.js"></script>
	<link  href= "../assets/js/dt/datatables.bootstrap.css" rel="stylesheet" />
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
					<form id='form1' name='form1' class='form-inline hidden-print' method='post' action='reploangiven.php'>
						<label>Loan given From </label>
						<input type='date' id='fromdate' name='fromdate' required />				
						<label>To</label>
						<input type='date' id='todate' name='todate' required />				
						<input type='submit' class='btn btn-success' id="Submit" name='Submit' value='Display'></input>
						<input type='submit' class='btn btn-success' id="Submit2" name='Submit2' value='ExportForAccounts'></input>
					</form>
					<?php echo $Report; ?>
					</div>
				</div>
				<div class='panel-footer'>
				</div>
			</div>
		</div>
	</div>
</body>