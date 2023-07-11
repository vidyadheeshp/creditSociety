<?php
	// rep_loan_coll_summary.php
	session_start();
	include('../includes/pdofunctions_v1.php');
	$db = connectPDO();
	include('../includes/functions.php'); 
	include('../includes/loans.php'); 
	$Date = date("d-m-Y H:i");
	$PanelHeading = "Loan Summary As On ".$Date;
	$Report       = "";
	$FromDate     = date("Y-m-d");
	$ToDate       = date("Y-m-d");
	if(isset($_POST['fromdate'])){
		$FromDate 	= date("Y-m-d",strtotime($_POST['fromdate']));
		$ToDate 	= date("Y-m-d",strtotime($_POST['todate']));
		$RepFromDate= date("d-m-Y",strtotime($_POST['fromdate']));
		$RepToDate  = date("d-m-Y",strtotime($_POST['todate']));
		$Report     = genLoansCollSummary($db,$FromDate,$ToDate);
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
			<div class='col-md-8 col-md-offset-2'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
                    <center><h4><?php echo $PanelHeading; ?></h4></center>
				</div>
				<div class='panel-body'>
					<form id='form1' name='form1' action='rep_loan_coll_summary.php' method='post'>
					<div class='row form-inline'>
						<label>Enter From Date</label>
						<input type='date' id='fromdate' name='fromdate' value='<?php echo $FromDate;?>'/>
						<label>Enter To Date</label>
						<input type='date' id='todate' name='todate' value='<?php echo $ToDate;?>' />
						<input type='submit' class='btn-primary'/>
						<!--<button id='showtrnlist' class='btn' onclick='showLoanTranList()'>Show Transaction List</button>-->
					</div>
					</form>

					<?php echo $Report; ?>
				</div>
				<div class='panel-footer text-primary'>
					<strong><center>Software Developed By Anand V Deshpande,Belagavi</center></strong>
				</div>
			</div>			
			</div>
		</div>
	</div>
</body>
