<?php

// ftentries_list.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 13.11.2019
// Modified on 26.11.2019
//Other than BankEntries

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
$PanelHeading = "General Ledger Transactions List ";
$Html = "";
$Sql  = "Select A.FinYear,A.FTID,A.TrnDate,A.TrnCode,A.TrnType,A.TrnNo,A.FMID,B.Name,A.Particulars,A.Debit,A.Credit,A.DateStmp as DtStmp
		from ft A, fm B 
		Where A.FMID = B.FMID AND A.FixFMID=0 
		Order By A.TrnDate DESC ,A.TrnCode";
$result = getResultSet($db,$Sql);
$Html = "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed'>";
$Html .= "<thead>";
$Html .= "<tr>";
$Html .= "<th>SNo</th>";
$Html .= "<th>Year</th>";
$Html .= "<th>TrnID</th>";
$Html .= "<th>TrnDate</th>";
$Html .= "<th>TrnCode</th>";
$Html .= "<th>TrnType</th>";
$Html .= "<th>TrnNo</th>";
$Html .= "<th>Account</th>";
$Html .= "<th>Particulars</th>";
$Html .= "<th>Debit</th>";
$Html .= "<th>Credit</th>";
$Html .= "<th>DateStamp</th>";
$Html .= "<th>Action</th>";
$Html .= "</tr>";
$Html .= "</thead>";

$Html .= "<tbody>";
$SerialNo = 1;
$TotalDebits=0;
$TotalCredits=0;
foreach($result as $row) {
	$FTID = $row['FTID'];
	$Html .= "<tr>";
	$Html .= "<td>$SerialNo</td>";
	$Html .= "<td>".$row['FinYear']."</td>";
	$Html .= "<td>".$row['FTID']."</td>";
	$Html .= "<td>".date("d-m-Y",strtotime($row['TrnDate']))."</td>";
	$Html .= "<td>".$row['TrnCode']."</td>";
	$Html .= "<td>".$row['TrnType']."</td>";
	$Html .= "<td>".$row['TrnNo']."</td>";
	$Html .= "<td>".$row['Name']."</td>";
	$Html .= "<td>".$row['Particulars']."</td>";
	$Html .= "<td align='right'>".$row['Debit']."</td>";
	$Html .= "<td align='right'>".$row['Credit']."</td>";
	$Html .= "<td nowrap>".date("d-m-Y H:i",strtotime($row['DtStmp']))."</td>";
	$Html .= "<td><button onclick=js_ftentries_del('$FTID')>Del</button>";
	//$Html .= "<button onclick=js_ftentries_ledger('$FTID')>Ledger</button></td>";
	$Html .= "</tr>";

	$SerialNo++;
	$TotalDebits   	+= $row['Debit'];
	$TotalCredits 	+= $row['Credit'];
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
$Html .= "<td align='right'>".$TotalDebits."</td>";
$Html .= "<td align='right'>".$TotalCredits."</td>";
$Html .= "<td></td>";
$Html .= "<td></td>";
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
					<?php echo $Report; ?>
				</div>
				<div class='panel-footer'>
					<button class='btn' onclick='js_ftentries_add()'>New Transaction</button>
					<button class='btn' onclick="window.location.href='../index.php'">Logout</button>
				</div>
			</div>
		</div>
	</div>
	<form id="hiddenform" method="post" action="ftentries.php">
		<input type="hidden" id="hiddenFormFTID" name="hiddenFormFTID" readonly/>
		<input type="hidden" id="hiddenFormAction" name="hiddenFormAction" readonly/>
		<input type="submit" id="hiddenformSubmit">
	</form>
</body>
<?php include('../includes/modal.php'); ?>
<script>
	$("#hiddenformSubmit").prop("hidden",true);
  	$(document).ready(function() {
   		$('#gridtable').DataTable({"lengthMenu": [ 10, 25, 50, 75, 100,500,1000]});
   	} );
	
	function js_ftentries_add() {
       	$("#hiddenFormAction").val("Add");
       	$("#hiddenFormFTID").val(0);
       	$("#hiddenform").submit()
	}
	function js_ftentries_del(ftid) {
		alert("Del TrnID:" + ftid);
		$.ajax({
	        type: "POST",
	        url: "ftentries_del.php",
	        data: "ftid="+ftid,
	        success : function(text){
	        	alert(text);
	        	location.reload();
	        }
		});		

		
	}
	function js_ftentries_ledger(ftid) {
		var fmid = $("#fmid").val();
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
