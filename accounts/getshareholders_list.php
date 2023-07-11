<?php

// rep_shareholders_list.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 26.10.2019
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
		MsgBox("Access for Authorised Users only","accountsmenu.php",True);
		exit();
	}
//
//require_once("../includes/functions.php");
require_once("../includes/pdofunctions_v1.php");
$db = connectPDO();
$PanelHeading = "Share Holders List ";
$Html = "";
$Sql  = "Select * from shareholders Order By SHID";
$result = getResultSet($db,$Sql);
$Html = "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed'>";
$Html .= "<thead>";
$Html .= "<tr class='bg-primary'>";
$Html .= "<th>SNo</th>";
$Html .= "<th>MemberID</th>";
$Html .= "<th>Name</th>";
$Html .= "<th>Dept</th>";
$Html .= "<th>Mobile</th>";
$Html .= "<th>OpenBal</th>";
$Html .= "<th>Debit</th>";
$Html .= "<th>Credit</th>";
$Html .= "<th>Balance</th>";
$Html .= "<th>MthContr</th>";
$Html .= "<th>DtOfJoin</th>";
$Html .= "<th>DtOfRetr</th>";
$Html .= "<th>Status</th>";
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
	$DeptID    	= $row['DeptID'];
	$DesignID	= $row['DesignID'];
	$MemberID  	= $row['MemberID'];
	$Dept  		= getSingleField($db,"Select ShName from departments Where DeptID='$DeptID'");
	$Designation= getSingleField($db,"Select Designation from designation Where DesignID='$DesignID'");
	$RowColor   = "";
	if($row['Status']=='Closed'){
		$RowColor = " style='background-color:pink' ";
	} elseif($row['Status']=='New'){
		$RowColor = " style='background-color:violet' ";
	} elseif($row['ClosBal']<=0) {
		$RowColor = " style='background-color:yellow' ";
	} else{
		$RowColor = " style='background-color:lightgreen' ";
	}
	$Html .= "<tr $RowColor>";	
	$Html .= "<td>$SerialNo</td>";
	$Html .= "<td>".$row['MemberID']."</td>";
	$Html .= "<td>".$row['Name']."</td>";
	$Html .= "<td>".$Dept."</td>";
	$Html .= "<td>".$row['Mobile']."</td>";
	$Html .= "<td align='right'>".$row['OpenBal']."</td>";
	$Html .= "<td align='right'>".$row['Debits']."</td>";
	$Html .= "<td align='right'>".$row['Credits']."</td>";
	$Html .= "<td align='right'>".$row['ClosBal']."</td>";
	$Html .= "<td align='right'>".$row['MthContr']."</td>";
	$Html .= "<td nowrap>".date("d-m-Y",strtotime($row['OpenDate']))."</td>";
	$Html .= "<td nowrap>".date("d-m-Y",strtotime($row['DOR']))."</td>";
	$Html .= "<td>".$row['Status']."</td>";
	$Html .= "<td nowrap><button class='btn-sm' onclick=js_shareholder_edit('$MemberID')>Edit</button>";
	$Html .= "<button  class='btn-sm' onclick=js_shareholder_del('$MemberID')>Del</button>";
	$Html .= "<button  class='btn-sm' onclick=js_shareholder_ledger('$MemberID')>Ledger</button>";
	$Html .= "<button  class='btn-sm' onclick=js_shareholder_liab('$MemberID')>Liab</button></td>";
	$Html .= "</tr>";
	$SerialNo++;
	$TotalOpenBal  	+= $row['OpenBal'];
	$TotalDebits   	+= $row['Debits'];
	$TotalCredits 	+= $row['Credits'];
	$TotalClosBal  	+= $row['ClosBal'];
	$TotalMthContr  += $row['MthContr'];
}
$Html .= "</tbody>";
$Html .= "<tfoot>";
$Html .= "<tr class='bg-primary'>";
$Html .= "<td></td>";
$Html .= "<td></td>";
$Html .= "<td></td>";
$Html .= "<td></td>";
$Html .= "<td></td>";
$Html .= "<td align='right'>".$TotalOpenBal."</td>";
$Html .= "<td align='right'>".$TotalDebits."</td>";
$Html .= "<td align='right'>".$TotalCredits."</td>";
$Html .= "<td align='right'>".$TotalClosBal."</td>";
$Html .= "<td align='right'>".$TotalMthContr."</td>";
$Html .= "<td></td>";
$Html .= "<td></td>";
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
    <div class="container-fluid hidden-print">
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
					<button class='btn btn-success' onclick="window.location.href='shareholders.php'">New Share Holder</button>
					<!--<button class='btn' onclick="window.location.href='../index.php'">Logout</button>-->
				</div>
			</div>
		</div>
	</div>
	<form id="hiddenform" method="post" action="shareholders.php">
		<input type="hidden" id="hiddenFormMemberID" name="hiddenFormMemberID" readonly/>
		<input type="hidden" id="hiddenFormAction" name="hiddenFormAction" readonly/>
		<input type="submit" id="hiddenformSubmit">
	</form>
</body>
<?php include('../includes/modal.php'); ?>

	<script src="../assets/js/dt/datatables.bootstrap.js"></script>
	<link  href="../assets/js/dt/datatables.bootstrap.css" rel="stylesheet" />
	<script src="../assets/js/dt/jquery.datatables.js"></script>	
<script>
	$("#hiddenformSubmit").prop("hidden",true);
  	$(document).ready(function() {
   		$('#gridtable').DataTable({"lengthMenu": [ 10, 25, 50, 75, 100,500,1000]});
   		//$('#gridtable').DataTable();
   	} );

	function js_shareholder_edit(memberid) {
		if(confirm("Edit MemberID:" + memberid)) {
	       	$("#hiddenFormAction").val("Edit");
	       	$("#hiddenFormMemberID").val(memberid);
	       	$("#hiddenform").submit()
			//window.location.href="shareholders.php?MemberID="+memberid;
		}
	}
	function js_shareholder_del(memberid) {
		alert("Del MemberID:" + memberid);
	}
	function js_shareholder_ledger(memberid) {
		//alert("Ledger MemberID:" + memberid);
		$.ajax({
	        type: "POST",
	        url: "ajax_shareholderledger.php",
	        data: "MemberID="+memberid,
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
	function js_shareholder_liab(memberid) {
		//alert("Ledger MemberID:" + memberid);
		$.ajax({
	        type: "POST",
	        url: "ajax_shareholder_liab.php",
	        data: "MemberID="+memberid,
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
