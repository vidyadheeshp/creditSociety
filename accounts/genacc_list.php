<?php

// genacc_list.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 09.11.2019
//	
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
		MsgBox("Access for Authorised Users only","accountsmenu.php",True);
		exit();
	}
//
require_once("../includes/pdofunctions_v1.php");
$db = connectPDO();
$PanelHeading = "General Ledger Accounts List ";
$Html = "";
$Sql  = "Select * from fm Order By FMID";
$result = getResultSet($db,$Sql);
$Html = "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed'>";
$Html .= "<thead>";
$Html .= "<tr>";
$Html .= "<th>SNo</th>";
$Html .= "<th>AccID</th>";
$Html .= "<th>Name</th>";
$Html .= "<th>AccType</th>";
$Html .= "<th>PlBsCode</th>";
$Html .= "<th>OpenBal</th>";
$Html .= "<th>Debit</th>";
$Html .= "<th>Credit</th>";
$Html .= "<th>Balance</th>";
$Html .= "<th>Action</th>";
$Html .= "</tr>";
$Html .= "</thead>";

$Html .= "<tbody>";
$SerialNo = 1;
$TotalOpenBal=0;
$TotalDebits=0;
$TotalCredits=0;
$TotalClosBal=0;
$TotalMthContr=0;
foreach($result as $row) {
	$FMID = $row['FMID'];
	$Html .= "<tr>";
	$Html .= "<td>$SerialNo</td>";
	$Html .= "<td>".$row['FMID']."</td>";
	$Html .= "<td>".$row['Name']."</td>";
	$Html .= "<td>".$row['AcType']."</td>";
	$Html .= "<td>".$row['PlBsCode']."</td>";
	$Html .= "<td align='right'>".ConvBalance($row['OpenBal'])."</td>";
	$Html .= "<td align='right'>".$row['Debits']."</td>";
	$Html .= "<td align='right'>".$row['Credits']."</td>";
	$Html .= "<td align='right'>".ConvBalance($row['ClosBal'])."</td>";
	$Html .= "<td><button onclick=js_genacc_edit('$FMID')>Edit</button>";
	$Html .= "<button onclick=js_genacc_del('$FMID')>Del</button>";
	$Html .= "<button onclick=js_genacc_ledger('$FMID')>Ledger</button></td>";
	$Html .= "</tr>";

	$SerialNo++;
	$TotalOpenBal  	+= $row['OpenBal'];
	$TotalDebits   	+= $row['Debits'];
	$TotalCredits 	+= $row['Credits'];
	$TotalClosBal  	+= $row['ClosBal'];
}
$Html .= "</tbody>";
$Html .= "<tfoot>";
$Html .= "<td></td>";
$Html .= "<td></td>";
$Html .= "<td></td>";
$Html .= "<td></td>";
$Html .= "<td></td>";
$Html .= "<td align='right'>".ConvBalance($TotalOpenBal)."</td>";
$Html .= "<td align='right'>".$TotalDebits."</td>";
$Html .= "<td align='right'>".$TotalCredits."</td>";
$Html .= "<td align='right'>".COnvBalance($TotalClosBal)."</td>";
$Html .= "<td</td>";
$Html .= "</tr>";
$Html .= "</tfoot>";
$Html .= "</table>";
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
					<?php echo $Report; ?>
				</div>
				<div class='panel-footer'>
					<button class='btn' onclick='js_genacc_add()'>New GeneralLedger</button>
					<button class='btn' onclick="window.location.href='../index.php'">Logout</button>
				</div>
			</div>
		</div>
	</div>
	<form id="hiddenform" method="post" action="genacc.php">
		<input type="hidden" id="hiddenFormFMID" name="hiddenFormFMID" readonly/>
		<input type="hidden" id="hiddenFormAction" name="hiddenFormAction" readonly/>
		<input type="submit" id="hiddenformSubmit">
	</form>
</body>
<?php include('../includes/modal.php'); ?>
<script>
	$("#hiddenformSubmit").prop("hidden",true);
	function js_genacc_add() {
       	$("#hiddenFormAction").val("Add");
       	$("#hiddenFormFMID").val(0);
       	$("#hiddenform").submit()
	}
	function js_genacc_edit(fmid) {
		alert("Edit AccID:" + fmid);
       	$("#hiddenFormAction").val("Edit");
       	$("#hiddenFormFMID").val(fmid);
       	$("#hiddenform").submit();
	}
	function js_genacc_del(fmid) {
		alert("Del AccID:" + fmid);
	}
	function js_genacc_ledger(fmid) {
		alert("Ledger AccID:" + fmid);
		$.ajax({
	        type: "POST",
	        url: "ajax_genaccledger.php",
	        data: "FMID="+fmid,
	        success : function(text){
	        	//alert(text);
		        var arrayReturned = JSON.parse(text);
	        	var header = arrayReturned['Header'];
	        	var html   = arrayReturned['Body'];
	        	$("#avdModalLabel").html(header);
	        	$("#modalbody_html").html(html);
	        	$("#avdModal").modal("show");
	        }
		});		
	}
</script>
