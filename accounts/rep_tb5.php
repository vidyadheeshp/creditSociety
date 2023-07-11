<?php

// rep_tb5 .php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 01.03.2020
//	Trial Balance Loan Accounts

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
		MsgBox("Access for Authorised Users only","accountsmenu",True);
		exit();
	}
//

require_once("../includes/pdofunctions_v1.php");
require_once("../includes/loans.php");

$db = connectPDO();
$Report       = "";
$FromDate     = date("Y-m-d");
$ToDate       = date("Y-m-d");
$AsOnDate     = date("Y-m-d");
$PanelHeading = "Trial Balance As On ".$AsOnDate;
$FMList = "";
$FMList = genSelectFM($db,"loanfmid"," AcType='Cust' ", " required ");		

if(isset($_POST['asondate'])){
	$LoanFMID     = intval($_POST['loanfmid']);
	$LoanAccountName = getSingleField($db,"Select Name from fm Where FMID='$LoanFMID'");
	$AsOnDate       = date("Y-m-d",strtotime($_POST['asondate']));
	$RepAsOnDate    = date("d-m-Y",strtotime($_POST['asondate']));
	$Destination= $_POST['destination'];
	$Sql = "Select A.LoanID,A.MemberID,S.Name,S.DeptID,A.OpenBal,
			(Select SUM(Debit)  from ft Where A.LoanID=ft.LoanID    AND (ft.TrnDate <='$AsOnDate')) as Debit1,
			(Select SUM(Credit) from ft Where A.LoanID = ft.LoanID    AND (ft.TrnDate <= '$AsOnDate')) as Credit1
		From customers A, shareholders S  
		Where A.FMID='$LoanFMID' AND A.MemberID=S.MemberID 
		Group By A.LoanID,A.MemberID,S.Name,S.DeptID,A.OpenBal 
		Order By S.DeptID,S.Name ";
	//echo $Sql;
	// function build_table($db,$Sql,$SerialNoReqd,$EditReqd='No',$DelReqd='No',$PrimaryID) {
	// function build_table($db,$Sql,$SerialNoReqd,$EditReqd='No',$DelReqd='No',$PrimaryID) {
	if($Destination=='Display'){
		$Html = "";
		// create report
		$Header = "<center><strong>KLS GIT Employees Co-Op Credit Society Ltd.</strong><center>\n";
		$Header .= "Trial Balance As On : <strong>".$RepAsOnDate." </strong>";
		$Html  = "";
		$Html .= "<table id='table1' class='table table-bordered table-condensed table-striped bluecolor'>";
		$Html .= "<tr>
				<th>S.No</th>
				<th>Dept</th>
				<th>LoanID</th>
				<th>MemberID</th>
				<th>Name of Account</th>
				<th>Debit</th>
				<th>Credit</th>
			</tr>";
		$ResultRow = getResultSet($db,$Sql);
		$SerialNo 	= 1;
		$TotDebit 	= 0;
		$TotCredit 	= 0;

		foreach($ResultRow as $Row){
			$DeptID 	= $Row['DeptID'];
			$Dept  		= getSingleField($db,"Select ShName from departments Where DeptID='$DeptID'");
			$Debit = 0;
			$Credit= 0;
			$OpenBal = 0;
			$ClosBal = 0;
			
			$OpenBal = $Row['OpenBal'];
			$Debit   = !is_null($Row['Debit1']) ? $Row['Debit1'] : 0; 
			$Credit  = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0; 

			$ClosBal = $OpenBal - $Debit + $Credit;
			if($ClosBal<>0) {
				$Html .="<tr>";
				$Html .= "<td>".$SerialNo."</td>";
				$Html .= "<td>".$Dept."</td>";
				$Html .= "<td>".$Row['LoanID']."</td>";
				$Html .= "<td>".$Row['MemberID']."</td>";
				$Html .= "<td>".$Row['Name']."</td>";
				if($ClosBal<=0) {
					$Html .= "<td align='right'>".number_format(abs($ClosBal),2,'.','')."</td>";
					$TotDebit += abs($ClosBal);
					$Html .= "<td></td>";
				}else{
					$Html .= "<td></td>";				
					$Html .= "<td align='right'>".number_format($ClosBal,2,'.','')."</td>";
					$TotCredit += $ClosBal;
				}
				$Html .= "</tr>";
				$SerialNo++;
			}
		}
		$Html .="<tr>";
		$Html .= "<td></td>";
		$Html .= "<td></td>";
		$Html .= "<td></td>";
		$Html .= "<td></td>";
		$Html .= "<td></td>";
		$Html .= "<td align='right'>".number_format($TotDebit,2,'.','')."</td>";
		$Html .= "<td align='right'>".number_format($TotCredit,2,'.','')."</td>";
		$Html .= "</tr>";
		$Report = $Html;
	} else {
		$Html = "";
		// create report
		$Html = "KLS GIT Employees Co-Op Credit Society Ltd.\n";
		$Html .= "Trial Balance As On :" .$RepAsOnDate."\n";
		$Html .= "S.No\t
				Dept\t
				LoanID\t
				MemberID\t
				Name of Account\t
				Debit\t
				Credit\n";
		$ResultRow = getResultSet($db,$Sql);
		$SerialNo 	= 1;
		$TotDebit 	= 0;
		$TotCredit 	= 0;

		foreach($ResultRow as $Row){
			$DeptID 	= $Row['DeptID'];
			$Dept  		= getSingleField($db,"Select ShName from departments Where DeptID='$DeptID'");
			$Debit = 0;
			$Credit= 0;
			$OpenBal = 0;
			$ClosBal = 0;
			
			$OpenBal = $Row['OpenBal'];
			$Debit   = !is_null($Row['Debit1']) ? $Row['Debit1'] : 0; 
			$Credit  = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0; 

			$ClosBal = $OpenBal - $Debit + $Credit;
			if($ClosBal<>0) {
				$Html .= $SerialNo."\t";
				$Html .= $Dept."\t";
				$Html .= $Row['LoanID']."\t";
				$Html .= $Row['MemberID']."\t";
				$Html .= $Row['Name']."\t";
				if($ClosBal<=0) {
					$Html .= number_format(abs($ClosBal),2,'.','')."\t";
					$TotDebit += abs($ClosBal);
					$Html .= "\t";
				}else{
					$Html .= "\t";				
					$Html .= number_format($ClosBal,2,'.','')."\t";
					$TotCredit += $ClosBal;
				}
				$Html .= "\n";
				$SerialNo++;
			}
		}
		$Html .= "\t";
		$Html .= "\t";
		$Html .= "\t";
		$Html .= "\t";
		$Html .= "\t";
		$Html .= number_format($TotDebit,2,'.','')."\t";
		$Html .= number_format($TotCredit,2,'.','')."\t";
		$Html .= "\n";
		$Report = $Html;

		//$Heading = "Loan Recovery Statment from $RepFromDate To $RepToDate";
		//$Html = export2excel($db,$Sql,"SNo",$Heading);
	    header("Content-Disposition: attachment; filename=loan_tb.xls");
	    header("Pragma: no-cache");
	    header("Expires: 0");
	    echo "$Html";	
	    exit();		
	}
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
    	<div class="col-md-10 col-md-offset-1">
    		<div class='row'>
				<?php include('accountsmenu.ini'); ?>
			</div>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
                    <center><h4><?php echo $PanelHeading; ?></h4></center>
				</div>
				<div class='panel-body'>
					<form id='form1' name='form1' action='rep_tb5.php' method='post'>
					<div class='row form-inline'>
						<?php echo $FMList; ?>
						<label>Enter As On Date</label>
						<input type='date' id='asondate' name='asondate' value='<?php echo $AsOnDate;?>' />
						<select id='destination' name='destination' class='form-control' required>
							<option value='Display'>Display</option>
							<option value='Export'>Export2Excel</option>
						</select>
						<input type='submit' class='btn-primary'/>
					</div>
					</form>
					<div id='report' class='table-responsive'>
						<?php echo $Report; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
