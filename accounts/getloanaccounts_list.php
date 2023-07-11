<?php

// getcustomers_list.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 27.11.2019
//Date modified  01.10.2020 hidden : Interest rate
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
//
//require_once("../includes/functions.php");
require_once("../includes/pdofunctions_v1.php");
$db = connectPDO();

$FMList = "";
$Qry = "Select FMID,Name from fm Where AcType='Cust' Order By FMID ";
$QryResult = getResultSet($db,$Qry);
$FMList  = "<select id='loanfmid' name='loanfmid' class='form-control' required >";
$FMList .= "<option value=''>Select Account</option>";
$FMList .= "<option value='All'>All</option>";
foreach($QryResult as $row){
	$FMList .= "<option value=".$row['FMID'].">".$row['Name']."</option>";
}
$FMList .= "</select>";

$StatusList = "";
$StatusList = "<select id='statuslist' name='statuslist' class='form-control' required >
	<option value=''>Select Status</option>
	<option value='All'>All</option>
	<option value='Active'>Active</option>
	<option value='Closed'>Closed</option>
	<option value='New'>New</option>
	<option value='ToBeSettled'>ToBeSettled</option>
	</select>";

$SortOrder = "<Select class='form-control' id='sortby' name='sortby' required>
				<option value=''>Sort</option>
				<option value='LoanID'>LoanID</option>
				<option value='LoanDate ASC'>Loan Date  (Ascending)</option>
				<option value='LoanDate DESC'>Loan Date (Descending)</option>
				<option value='A.DeptID'>Dept</option>
			  </select>";
$FinYears = "<Select class='form-control' id='forfinyear' name='forfinyear' required>
				<option value=''>FinYear</option>
				<option value='All'>All</option>
				<option value='2019-2020'>19-20</option>
				<option value='2020-2021'>20-21</option>
				<option value='2021-2022'>21-22</option>
			  </select>";

$PanelHeading = "Loan Accounts List ";

$Html 	= "";
$Report = "";

if(isset($_POST['submit'])) {
	//echo $_POST['submit'];
	$StatusReqd = $_POST['statuslist'];
	$SortBy 	= $_POST['sortby'];
	$LoanFMID   = $_POST['loanfmid'];
	if($LoanFMID=='All'){

	} else{
		$LoanFMID   = intval($_POST['loanfmid']);		
	}

	$ForFinYear = $_POST['forfinyear'];
	if($ForFinYear=='All'){
		$YearStr = "";
	} else{
		$Str11      = "04/01/" .sprintf("%04d",substr($ForFinYear,0,4)); 
		$Str12      = "03/31/" .sprintf("%04d",substr($ForFinYear,5,4)); 

		$FromDate = date("Y-m-d",strtotime($Str11));
		$ToDate   = date("Y-m-d",strtotime($Str12));

		echo $FromDate. ":".$ToDate;
		$YearStr = " AND B.LoanDate >='".$FromDate . "' AND B.LoanDate <='".$ToDate."'";
	}

	$Sql  = "Select B.*,A.Name,A.DeptID,A.DesignID from customers B, shareholders A 
			Where B.MemberID = A.MemberID ";
	if($StatusReqd !='All') {
		$Sql .= " And B.Status='$StatusReqd' ";
	}
	if($LoanFMID !='All') {
		$Sql .= " And B.FMID='$LoanFMID' ";
	}
	$Sql .= $YearStr; 
	$Sql .= " Order By $SortBy ";
	// echo $Sql;


	$result = getResultSet($db,$Sql);
	$Html = "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed'>";
	$Html .= "<thead>";
	$Html .= "<tr  class='bg-primary'>";
	$Html .= "<th>SNo</th>";
	$Html .= "<th>LoanID</th>";
	$Html .= "<th>MemberID</th>";
	$Html .= "<th>Name</th>";
	$Html .= "<th>Dept</th>";
	//$Html .= "<th>Designation</th>";
	$Html .= "<th>LoanDate</th>";
	//$Html .= "<th>IntRt</th>";
	$Html .= "<th>Months</th>";
	$Html .= "<th>OpenBal</th>";
	$Html .= "<th>Debit</th>";
	$Html .= "<th>Credit</th>";
	$Html .= "<th>Balance</th>";
	$Html .= "<th>IntColl</th>";
	$Html .= "<th>MthEMI</th>";
	$Html .= "<th>IntUpto</th>";
	$Html .= "<th>Status</th>";
	$Html .= "<th>Action</th>";
	$Html .= "</tr>";
	$Html .= "</thead>";

	$Html .= "<tbody>";
	$SerialNo = 1;
	$TotalOpenBal = 0;
	$TotalDebits  = 0;
	$TotalCredits = 0;
	$TotalClosBal = 0;
	$TotalMthEMI  = 0;
	$TotalIntColl = 0;
	foreach($result as $row) {
		$LoanID 	= $row['LoanID'];
		$DeptID    	= $row['DeptID'];
		$DesignID	= $row['DesignID'];
		$MemberID  	= $row['MemberID'];
		$Dept  		= getSingleField($db,"Select ShName        from departments Where DeptID='$DeptID'");
		$Designation= getSingleField($db,"Select Designation   from designation Where DesignID='$DesignID'");
		$IntColl    = getSingleField($db,"Select SUM(Interest) from ft Where LoanID='$LoanID'");
		$RowColor   = "";
		if($row['Status']=='Closed'){
			$RowColor = " style='background-color:pink' ";
		} elseif($row['Status']=='New'){
			$RowColor = " style='background-color:violet' ";
		} elseif($row['ClosBal']>=0) {
			$RowColor = " style='background-color:yellow' ";
		} else{
			$RowColor = " style='background-color:lightgreen' ";
		}
		$Html .= "<tr $RowColor>";
		$Html .= "<td>$SerialNo</td>";
		$Html .= "<td>".$row['LoanID']."</td>";
		$Html .= "<td>".$row['MemberID']."</td>";
		$Html .= "<td>".$row['Name']."</td>";
		$Html .= "<td>".$Dept."</td>";
		//$Html .= "<td>".$Designation."</td>";
		$Html .= "<td align='right' nowrap>".date("d-m-Y",strtotime($row['LoanDate']))."</td>";
		//$Html .= "<td align='right'>".$row['IntRate']."</td>";
		$Html .= "<td align='right'>".$row['Months']."</td>";
		$Html .= "<td align='right' nowrap>".ConvBalance($row['OpenBal'])."</td>";
		$Html .= "<td align='right'>".$row['Debits']."</td>";
		$Html .= "<td align='right'>".$row['Credits']."</td>";
		$Html .= "<td align='right' nowrap>".ConvBalance($row['ClosBal'])."</td>";
		$Html .= "<td align='right'>".$IntColl."</td>";
		$Html .= "<td align='right'>".$row['MthEMI']."</td>";
		$Html .= "<td align='left' nowrap>".date("d-m-Y",strtotime($row['LastRecDate']))."</td>";
		$Html .= "<td>".$row['Status']."</td>";
		$Html .= "<td nowrap><button class='btn-sm' onclick=js_loanaccount_edit('$LoanID')>Edit</button>";
		$Html .= "<button  class='btn-sm' onclick=js_loanaccount_del('$LoanID')>Del</button>";
		$Html .= "<button  class='btn-sm' onclick=js_loanaccount_ledger('$LoanID')>Ledger</button></td>";
		$Html .= "</tr>";

		$SerialNo++;
		$TotalOpenBal  	+= $row['OpenBal'];
		$TotalDebits   	+= $row['Debits'];
		$TotalCredits 	+= $row['Credits'];
		$TotalClosBal  	+= $row['ClosBal'];
		$TotalMthEMI    += intval($row['MthEMI']);
		$TotalIntColl   += $IntColl;

	}
	$Html .= "</tbody>";
	$Html .= "<tfoot>";
	$Html .= "<tr class='bg-primary'>";
	$Html .= "<td></td>";
	//$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td align='right'>".ConvBalance($TotalOpenBal)."</td>";
	$Html .= "<td align='right'>".$TotalDebits."</td>";
	$Html .= "<td align='right'>".$TotalCredits."</td>";
	$Html .= "<td align='right'>".ConvBalance($TotalClosBal)."</td>";
	$Html .= "<td align='right'>".$TotalIntColl."</td>";
	$Html .= "<td align='right'>".$TotalMthEMI."</td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "</tr>";
	$Html .= "</tfoot>";
	$Html .= "</table>";
	$Report = $Html;
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
	.button_lessheight{
		Height: 12px;
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
					<div class='table-responsive'>
						<form class='form-inline' id='loanlist' method='post' action='getloanaccounts_list.php'>
							<?php echo $FMList; ?>
							<?php echo $StatusList; ?>
							<?php echo $SortOrder; ?>
							<?php echo $FinYears; ?>
							<input type='submit' id='submit' name='submit' class='btn btn-success' value='Submit'/>
						</form>
						<?php echo $Report; ?>
					</div>
				</div>
				<div class='panel-footer'>
					<button class='buttn' onclick="window.location.href='loanaccounts.php'">New Loan Account</button>
					<button class='buttn' onclick="window.location.href='../index.php'">Logout</button>
				</div>
			</div>
		</div>
	</div>
	<form id="hiddenform" method="post" action="loanaccounts.php">
		<input type="hidden" id="hiddenFormLoanID" name="hiddenFormLoanID" readonly/>
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

	function js_loanaccount_edit(loanid) {
		alert("Edit LoanID:" + loanid);
       	$("#hiddenFormAction").val("Edit");
       	$("#hiddenFormLoanID").val(loanid);
       	$("#hiddenform").submit()
		//window.location.href="shareholders.php?MemberID="+memberid;
	}
	function js_loanaccount_del(loanid) {
		alert("Del LoanID:" + loanid);
		$.ajax({
	        type: "POST",
	        url: "ajax_loanaccount_del.php",
	        data: "LoanID="+loanid,
	        success : function(text){
	        	//alert(text);
		        var arrayReturned = JSON.parse(text);
	        	var header = arrayReturned['Header'];
	        	var html   = arrayReturned['Body'];
	        	$("#avdModalLabel").html(header);
	        	$("#modalbody_html").html(html);
	        	$("#avdModal").modal("show");
	        	location.reload();
	        }
		});		

	}
	function js_loanaccount_ledger(loanid) {
		//alert("Ledger LoanID:" + loanid);
		$.ajax({
	        type: "POST",
	        url: "ajax_loanaccountledger.php",
	        data: "LoanID="+loanid,
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
