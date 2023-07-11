<?php

// rep_incexp1 .php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 08.03.2020
// Income Expenditure B/S 
session_start();
require_once("../includes/functions.php");
// check usertype
	//print_r($_SESSION);
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
$PanelHeading = "Income & Expenditure/Balance Sheet";
if(isset($_POST['fromdate'])){
	$FromDate     = date("Y-m-d",strtotime($_POST['fromdate']));
	$ToDate       = date("Y-m-d",strtotime($_POST['todate']));
	$RepFromDate  = date("d-m-Y",strtotime($_POST['fromdate']));
	$RepToDate    = date("d-m-Y",strtotime($_POST['todate']));
	$Destination  = $_POST['destination'];
	


	//echo $Sql;
	// function build_table($db,$Sql,$SerialNoReqd,$EditReqd='No',$DelReqd='No',$PrimaryID) {
	if($Destination=='Display'){
		$Html = genIncExpBS($db,$FromDate,$ToDate,$RepFromDate,$RepToDate);
		
		$Report = $Html;
	} else {
		$Html = genIncExpBS_Export2Excel($db,$FromDate,$ToDate,$RepFromDate,$RepToDate);
	    header("Content-Disposition: attachment; filename=incexp_bs.xls");
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
    	<div class="col-md-8 col-md-offset-2">
    		<div class='row'>
				<?php include('accountsmenu.ini'); ?>
			</div>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
                    <center><h4><?php echo $PanelHeading; ?></h4></center>
				</div>
				<div class='panel-body'>
					<form id='form1' name='form1' action='rep_incexp1.php' method='post'>
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
