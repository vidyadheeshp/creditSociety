<?php

// rep_recpay1 .php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 07.03.2020
// Receipt and Payment Statement
session_start();
require_once("../includes/functions.php");
// check usertype
	if(!isset($_SESSION['UserType'])) {
		MsgBox("Direct script access prohibited","../index.php",True);
		exit();
	}
	$UserType 	= $_SESSION['UserType'];
	if(strstr('Accounts,Admin,Chairman',$UserType)){
	} else{
		MsgBox("Access for Authorised Users only","accountsmenu",True);
		exit();
	}
//

require_once("../includes/pdofunctions_v1.php");

$db = connectPDO();
$Report       = "";
$FromDate     = date("Y-m-d");
$ToDate       = date("Y-m-d");
$PanelHeading = "Receipt & Payment From ".date("d-m-Y",strtotime($FromDate)). " To ". date("d-m-Y",strtotime($ToDate));
if(isset($_POST['fromdate'])){
	$FromDate     = date("Y-m-d",strtotime($_POST['fromdate']));
	$ToDate       = date("Y-m-d",strtotime($_POST['todate']));
	$RepFromDate  = date("d-m-Y",strtotime($_POST['fromdate']));
	$RepToDate    = date("d-m-Y",strtotime($_POST['todate']));
	$Destination  = $_POST['destination'];
	$Sql = "Select A.PlBsCode,A.FMID,A.Name,
			(Select SUM(Debit)  from ft Where A.FMID=ft.FMID    AND (ft.TrnDate Between '$FromDate' and '$ToDate')) as Debit1,
			(Select SUM(Credit) from ft Where A.FMID=ft.FMID    AND (ft.TrnDate Between '$FromDate' and '$ToDate')) as Credit1,
			(Select SUM(Debit)  from ft Where A.FMID=ft.FixFMID AND (ft.TrnDate Between '$FromDate' and '$ToDate')) as Credit2,
			(Select SUM(Credit) from ft Where A.FMID=ft.FixFMID AND (ft.TrnDate Between '$FromDate' and '$ToDate')) as Debit2
		From fm A 
		Group By A.PlBsCode,A.FMID,A.Name ";
	//echo $Sql;
	// function build_table($db,$Sql,$SerialNoReqd,$EditReqd='No',$DelReqd='No',$PrimaryID) {
	if($Destination=='Display'){
		$Html = "";
		// create report
		$Header = "<center><strong>KLS GIT Employees Co-Op Credit Society Ltd.</strong><center>\n";
		$Header .= "Receipt And Payment From <strong>".$RepFromDate. " To ".$RepToDate  ." </strong>";
		$Html  = "";
		$Html .= "<table id='table1' class='table table-bordered table-condensed table-striped bluecolor'>";
		$Html .= "<tr style='background-color:yellow;'>
				<th>S.No</th>
				<th>Name of Account</th>
				<th align='right'>Receipt</th>
				<th align='right'>Payment</th>
			</tr>";
		$ResultRow = getResultSet($db,$Sql);
		$SerialNo 	= 1;

		$TotDebit   = 0;
		$TotCredit  = 0;
		// First Process Receipts
		foreach($ResultRow as $Row){
			$Credit  = 0;
			$Credit1  = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0; 
			$Credit2  = !is_null($Row['Credit2']) ? $Row['Credit2'] : 0; 
			$Credit   = $Credit1 + $Credit2;

			// number_format($number, 2, '.', '');
			if($Credit >0) {
				$Html .="<tr>";
				$Html .= "<td>".$SerialNo."</td>";
				$Html .= "<td>".$Row['Name']."</td>";
				$Html .= "<td align='right'>".number_format($Credit,2, '.', '')."</td>";
				$Html .= "<td align='right'></td>";
				$TotCredit += $Credit;
				$Html .= "</tr>";
				$SerialNo++;
			}
		}
		foreach($ResultRow as $Row){
			$Debit 	 = 0;
			$Credit  = 0;

			$Debit1  = !is_null($Row['Debit1']) ? $Row['Debit1'] : 0; 
			$Debit2  = !is_null($Row['Debit2']) ? $Row['Debit2'] : 0; 
			$Debit   = $Debit1 + $Debit2;
			// number_format($number, 2, '.', '');
			if($Debit >0) {
				$Html .="<tr>";
				$Html .= "<td>".$SerialNo."</td>";
				$Html .= "<td>".$Row['Name']."</td>";
				$Html .= "<td align='right'></td>";
				$Html .= "<td align='right'>".number_format($Debit,2,'.','')."</td>";
				$TotDebit  += $Debit;
				$Html .= "</tr>";
				$SerialNo++;
			}
		}
		$Html .="<tr style='background-color:yellow'>";
		$Html .= "<td></td>";
		$Html .= "<td></td>";
		$Html .= "<td align='right'>".number_format($TotCredit, 2, '.', '')."</td>";
		$Html .= "<td align='right'>".number_format($TotDebit, 2, '.', '')."</td>";
		$Html .= "</tr>";
		$Report = $Html;
	} else {
		//$Heading = "Loan Recovery Statment from $RepFromDate To $RepToDate";
		//$Html = export2excel($db,$Sql,"SNo",$Heading);
	    //header("Content-Disposition: attachment; filename=loanrecoverylist.xls");
	    //header("Pragma: no-cache");
	    //header("Expires: 0");
	    //echo "$Html";	
	    //exit();		
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
    	<div class="col-md-8 col-md-offset-2">
    		<div class='row'>
				<?php include('accountsmenu.ini'); ?>
			</div>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
                    <center><h4><?php echo $PanelHeading; ?></h4></center>
				</div>
				<div class='panel-body'>
					<form id='form1' name='form1' action='rep_recpay1.php' method='post'>
					<div class='row form-inline'>
						<label>Enter From Date</label>
						<input type='date' id='fromdate' name='fromdate' value='<?php echo $FromDate;?>' />
						<label>Enter To Date</label>
						<input type='date' id='todate' name='todate' value='<?php echo $ToDate;?>' />
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
