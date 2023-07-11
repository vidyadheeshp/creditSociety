<?php
	// Author : Anand V Deshpande,Belagavi
	// Date Written: 14.01.2020
	// List of Monthly Loan EMI Transactions
	// reploanrecoverylist.php

	session_start();
	require_once("../includes/functions.php");

	// check usertype
	if(!isset($_SESSION['UserType'])) {
		MsgBox("Direct script access prohibited","../index.php",True);
		exit();
	}

	$UserType 	= $_SESSION['UserType'];
	if($UserType == 'Loans' or $UserType=='Admin' or $UserType=='Chairman'){

	} else{
		MsgBox("Access for Authorised Users only","accountsmenu.php",True);
		exit();
	}
	//	
	//require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");

	$db = connectPDO();
	$PanelHeading = "Monthly Loan Recovery Statement ";

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
			<div class='panel panel-primary'>
				<div class='panel-heading'>
                    <center><h4><?php echo $PanelHeading; ?></h4></center>
				</div>
				<div class='panel-body'>
					<div class='row form-inline'>
						<label>Enter From Date</label>
						<input type='date' id='fromdate' name='fromdate' value='<?php echo $FromDate;?>'/>
						<label>Enter To Date</label>
						<input type='date' id='todate' name='todate' value='<?php echo $ToDate;?>' />
						<button id='showtrnlist' class='btn' onclick='showLoanTranList()'>Show Transaction List</button>
					</div>
					<div id='loantranlist' class='table-responsive'>
					</div>
				</div>
				<div class='panel-footer'>
				</div>
			</div>
		</div>
	</div>
</body>
<script>
  	$(document).ready(function() {
   		$('#gridtable').DataTable({"lengthMenu": [ 10, 25, 50, 75, 100,500,1000]});
   	});

	function showLoanTranList(){
		var fromdate = $("#fromdate").val();
		var todate   = $("#todate").val();
		alert(fromdate);
		$.ajax({
	        type: "POST",
	        url: "getloanrecoverylist.php",
	        data: "fromdate="+fromdate+"&todate="+todate,
	        success : function(text){
	        	alert(text);
	        	$("#loantranlist").html(text);
	        }
		});
	};	
</script> 

