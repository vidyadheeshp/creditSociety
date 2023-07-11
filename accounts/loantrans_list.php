<?php

// loantrans_list.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 26.11.2019
// modified on 09.04.2021 for trntype filter	
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

$db = connectPDO();
$FMList = genSelectFM($db,"loanfmid"," AcType='Cust' ", " required ");	
$PanelHeading = "Loan Transaction List ";
$Report       = "";
$FromDate     = date("Y-m-d");
$ToDate       = date("Y-m-d");
$Destination  = "";
if(isset($_POST['fromdate'])){
	$LoanFMID   = $_POST['loanfmid'];
	$FromDate 	= date("Y-m-d",strtotime($_POST['fromdate']));
	$ToDate 	= date("Y-m-d",strtotime($_POST['todate']));
	$RepFromDate= date("d-m-Y",strtotime($_POST['fromdate']));
	$RepToDate  = date("d-m-Y",strtotime($_POST['todate']));
	$Destination= $_POST['destination'];
	$TrnType 	= $_POST['trntype'];

	/*
	$Sql = "Select D.ShName as DeptName,C.Name,B.LoanID, B.FTID,B.TrnDate,B.TrnCode,B.TrnType,B.TrnNo,B.Debit,
		B.Credit,B.Principal+B.Interest as MthEMI,B.Principal,B.Interest,B.Days,B.ForMonth,B.ForYear from ft B, customers A, shareholders C , departments D 
		Where B.LoanID = A.LoanID and A.MemberID = C.MemberID and C.DeptID = D.DeptID 
		And A.FMID = '$LoanFMID' AND  
		TrnDate between '$FromDate' and '$ToDate' 
		Order By B.TrnDate,B.TrnCode,D.ShName,C.Name ";
		*/
	$Sql = "Select D.ShName as DeptName,C.Name,B.LoanID, B.FTID,B.TrnDate,B.TrnCode,B.TrnType,B.TrnNo,B.Debit,
		B.Credit,B.Principal+B.Interest as MthEMI,B.Principal,B.Interest,B.Days,B.ForMonth,B.ForYear from ft B, customers A, shareholders C , departments D 
		Where B.LoanID = A.LoanID and A.MemberID = C.MemberID and C.DeptID = D.DeptID 
		And A.FMID = '$LoanFMID' AND  
		TrnDate between '$FromDate' and '$ToDate' ";
	if($TrnType=='Others') {
		$Sql .= " AND TrnType='' ";
	} elseif($TrnType=='MthContr' or $TrnType=='MthEMI') {
		$Sql .= " AND TrnType='$TrnType' ";
	} 

	$Sql .= " Order By B.TrnDate,B.TrnCode,D.ShName,C.Name ";


	//echo $Sql;
	// function build_table($db,$Sql,$SerialNoReqd,$EditReqd='No',$DelReqd='No',$PrimaryID) {
	if($Destination=='Display'){
		//$HtmlSummary = GetLoanSummary($db,$FromDate,$ToDate);
		//$Html = build_table($db,$Sql,"SNo","No","No","FTID");
		//$Report = $Html;
		////
		// 09.04.2021
		
		$result = getResultSet($db,$Sql);
		//echo $Sql;
		//print_r($result);
		
		$Html = "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed'>";
		$Html .= "<thead>";
		$Html .= "<tr>";
		$Html .= "<th>SNo</th>";
		$Html .= "<th>Dept</th>";
		$Html .= "<th>LoanID</th>";
		$Html .= "<th>Name</th>";
		$Html .= "<th>TrnID</th>";
		$Html .= "<th>Date</th>";
		$Html .= "<th>Code</th>";
		$Html .= "<th>Type</th>";
		$Html .= "<th>TrNo</th>";
		$Html .= "<th>Debit</th>";
		$Html .= "<th>Credit</th>";
		$Html .= "<th>Principal</th>";
		$Html .= "<th>Interest</th>";
		$Html .= "<th>Days</th>";
		$Html .= "</tr>";
		$Html .= "</thead>";

		$Html .= "<tbody>";
		$SerialNo = 1;
		$TotDebit=0;
		$TotCredit=0;
		$TotPrin = 0;
		$TotInt  = 0;
		$Total   = 0;

		foreach($result as $row) {
			$LoanID 	= $row['LoanID'];
			$VchAmt     = ($row['Principal']) + ($row['Interest']);
			$Html .= "<tr>";
			$Html .= "<td>$SerialNo</td>";
			$Html .= "<td>".$row['DeptName']."</td>";
			$Html .= "<td>".$row['LoanID']."</td>";
			$Html .= "<td>".$row['Name']."</td>";
			$Html .= "<td>".$row['FTID']."</td>";
			$Html .= "<td align='right' nowrap>".date("d-m-Y",strtotime($row['TrnDate']))."</td>";

			$Html .= "<td align='right'>".$row['TrnCode']."</td>";
			$Html .= "<td align='right'>".$row['TrnType']."</td>";
			$Html .= "<td align='right'>".$row['TrnNo']."</td>";
			$Html .= "<td align='right'>".($row['Debit'])."</td>";
			$Html .= "<td align='right'>".($VchAmt)."</td>";
			$Html .= "<td align='right'>".($row['Principal'])."</td>";
			$Html .= "<td align='right'>".($row['Interest'])."</td>";
			$Html .= "<td align='right' nowrap>".($row['Days'])."</td>";
			$Html .= "</tr>";

			$SerialNo++;
			$TotPrin  		+= $row['Principal'];
			$TotInt		    += $row['Interest'];
			$Total 			+= $VchAmt;
			$TotCredit      += $VchAmt;
			$TotDebit       += $row['Debit'];
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
		$Html .= "<td></td>";
		$Html .= "<td align='right'>".$TotDebit."</td>";
		$Html .= "<td align='right'>".$TotCredit."</td>";
		$Html .= "<td align='right'>".$TotPrin."</td>";
		$Html .= "<td align='right'>".$TotInt."</td>";
		$Html .= "<td></td>";
		$Html .= "</tr>";
		$Html .= "</tfoot>";
		$Html .= "</table>";
		$Report = $Html;

	} else {
		$Heading = "Loan Recovery Statment from $RepFromDate To $RepToDate";
		$Html = export2excel($db,$Sql,"SNo",$Heading);
	    header("Content-Disposition: attachment; filename=loanrecoverylist.xls");
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
					<form id='form1' name='form1' action='loantrans_list.php' method='post'>
					<div class='row form-inline'>
						<label>Select Account</label>
						<?php echo $FMList; ?>
						<label>Enter From Date</label>
						<input type='date' id='fromdate' name='fromdate' value='<?php echo $FromDate;?>'/>
						<label>Enter To Date</label>
						<input type='date' id='todate' name='todate' value='<?php echo $ToDate;?>' />
						<select id='trntype' name='trntype' class='form-control' required>
							<option value='Others'>Others</option>
							<option value='MthEMI'>Loans</option>
							<option value='All'>All</option>
						</select>

						<select id='destination' name='destination' class='form-control' required>
							<option value='Display'>Display</option>
							<option value='Export'>Export2Excel</option>
						</select>
						<input type='submit' class='btn-primary'/>
						<!--<button id='showtrnlist' class='btn' onclick='showLoanTranList()'>Show Transaction List</button>-->
					</div>
					</form>
					<div id='loantranlist' class='table-responsive'>
						<?php echo $Report; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<form id="hiddenform" method="post" action="loantrans.php">
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
	function js_editloantran(tranid) {
		alert("Edit TranID:" + tranid);
       	$("#hiddenTranID").val(tranid);
       	$("#hidden_type").val("Edit");
       	$("#hiddenform").submit();
		//window.location.href="shareholders.php?MemberID="+memberid;
	}
	function js_delloantran(tranid) {
		if(confirm("Delete this Transaction ID="+tranid + " ?")){
			$.ajax({
		        type: "POST",
		        url: "loantrans_del.php",
		        data: "TranID="+tranid,
		        success : function(text){
		        	alert(text);
		        	location.reload(true);
		        }
			});
		}
	}
</script>
