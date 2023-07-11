<?php

// sharetrans_list.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 01.11.2019
//	
session_start();
require_once("../includes/functions.php");
// check usertype
	if(!isset($_SESSION['UserType'])) {
		MsgBox("Direct script access prohibited","../index.php",True);
		exit();
	}
	$UserType 	= $_SESSION['UserType'];
	if(strstr('Shares,Admin,Chairman',$UserType)){
	} else{
		MsgBox("Access for Authorised Users only","accountsmenu",True);
		exit();
	}

//

require_once("../includes/pdofunctions_v1.php");
require_once("../includes/shares.php");

$db = connectPDO();
$PanelHeading = "Share Holders Transaction List ";
$Report       = "";
$FromDate     = date("Y-m-d");
$ToDate       = date("Y-m-d");
if(isset($_POST['fromdate'])){
	$FromDate 	= $_POST['fromdate'];
	$ToDate 	= $_POST['todate'];
	$Destination= $_POST['destination'];
	$Sql = "Select B.FTID,B.TrnDate,B.TrnCode,B.TrnType,B.MemberID,A.Name,B.Debit,B.Credit,B.ForMonth,B.ForYear from ft B, shareholders A Where B.MemberID = A.MemberID and
		TrnDate between '$FromDate' and '$ToDate' 
		Order By B.TrnDate,B.TrnCode ";
	if($Destination=='Display'){
		$Html = build_table($db,$Sql,"SNo","No","No","FTID");
		$Report = $Html;
	} else {
		$Html = export2excel($db,$Sql,"SNo");
	    header("Content-Disposition: attachment; filename=sharecollectionlist.xls");
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
    	<div class="col-md-12">
    		<div class='row'>
				<?php include('accountsmenu.ini'); ?>
			</div>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
                    <center><h4><?php echo $PanelHeading; ?></h4></center>
				</div>
				<div class='panel-body'>
					<form id='form1' name='form1' action='sharetrans_list.php' method='post'>
					<div class='row form-inline'>
						<label>Enter From Date</label>
						<input type='date' id='fromdate' name='fromdate' value='<?php echo $FromDate;?>'/>
						<label>Enter To Date</label>
						<input type='date' id='todate' name='todate' value='<?php echo $ToDate;?>' />
						<select id='destination' name='destination' class='form-control' required>
							<option value='Display'>Display</option>
							<option value='Export'>Export2Excel</option>
						</select>
						<input type='submit' class='btn-primary'/>
						<!--<button id='showtrnlist' class='btn' onclick='showLoanTranList()'>Show Transaction List</button>-->
					</div>
					</form>
					<div id='sharetranlist' class='table-responsive'>
						<?php echo $Report; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<form id="hiddenform" method="post" action="sharetrans.php">
		<input type="hidden" id="hiddenTranID" name="hiddenTranID" readonly/>
		<input type="hidden" id="hidden_type"  name="hidden_type"  readonly/>
		<input type="submit" id="hiddenformSubmit">
	</form>
</body>
<?php include('../includes/modal.php'); ?>
<script>
	$("#hiddenformSubmit").prop("hidden",true);
  	$(document).ready(function() {
   		$('#gridtable').DataTable({"lengthMenu": [ 10, 25, 50, 75, 100,500,1000]});
   	});


	// modify following as reqd
	function js_addShareTran() {
		//alert("Edit MemberID:" + memberid);
       	$("#hiddenTranID").val(0);
       	$("#hidden_type").val("Add");
       	$("#hiddenform").submit();
		//window.location.href="shareholders.php?MemberID="+memberid;
	}
	function js_editsharetran(tranid) {
		alert("Edit TranID:" + tranid);
       	$("#hiddenTranID").val(tranid);
       	$("#hidden_type").val("Edit");
       	$("#hiddenform").submit();
		//window.location.href="shareholders.php?MemberID="+memberid;
	}
	function js_delsharetran(tranid) {
		if(confirm("Delete this Transaction ID="+tranid + " ?")){
			$.ajax({
		        type: "POST",
		        url: "sharetrans_del.php",
		        data: "TranID="+tranid,
		        success : function(text){
		        	alert(text);
		        	location.reload(true);
		        }
			});
		}
	}
	function js_sharetran_view(memberid) {
		alert("View MemberID:" + memberid);
	}
	function js_sharetran_ledger(memberid) {
		alert("Ledger MemberID:" + memberid);
	}
</script>
