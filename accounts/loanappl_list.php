<?php

// loanappl_list.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 22.12.2019
//	
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
$PanelHeading = " Loan Applications List ";
$Html = "";
// modify sql
$Sql  = "Select B.*,A.Name,A.DeptID,A.DesignID,A.DOR from loanappl B, shareholders A 
		Where B.MemberID = A.MemberID Order By DateStmp Desc";
$result = getResultSet($db,$Sql);
$Html = "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed'>";
$Html .= "<thead>";
$Html .= "<tr>";
$Html .= "<th>SNo</th>";
$Html .= "<th>ID</th>";
$Html .= "<th>MemberID</th>";
$Html .= "<th>Name</th>";
$Html .= "<th>Dept</th>";
$Html .= "<th>DtOfRetr</th>";
$Html .= "<th>LoanAccount</th>";
$Html .= "<th>ApplOn</th>";
$Html .= "<th>LoanAmt</th>";
$Html .= "<th>IntRt</th>";
$Html .= "<th>Months</th>";
$Html .= "<th>MthEMI</th>";
$Html .= "<th>Guarn_1</th>";
$Html .= "<th>Guarn_2</th>";
$Html .= "<th>Status</th>";
$Html .= "<th>Action</th>";
$Html .= "</tr>";
$Html .= "</thead>";

$Html .= "<tbody>";
$SerialNo = 1;
foreach($result as $row) {
	$RowID 		= $row['RowID'];
	$LoanID 	= $row['LoanID'];
	$DeptID    	= $row['DeptID'];
	$DesignID	= $row['DesignID'];
	$MemberID  	= $row['MemberID'];
	$FMID  		= $row['FMID'];
	$FMName 	= getSingleField($db,"Select Name          from fm Where FMID='$FMID'");
	$Dept  		= getSingleField($db,"Select ShName        from departments Where DeptID='$DeptID'");
	$Designation= getSingleField($db,"Select Designation   from designation Where DesignID='$DesignID'");
	$IntColl    = getSingleField($db,"Select SUM(Interest) from ft Where LoanID='$LoanID'");
	if($row['Status']=='Rejected'){
		$RowClass = " class='danger' "; 
	} else{
		$RowClass = "";
	}
	$Html .= "<tr $RowClass>";
	$Html .= "<td>$SerialNo</td>";
	$Html .= "<td>".$RowID."</td>";
	$Html .= "<td>".$row['MemberID']."</td>";
	$Html .= "<td>".$row['Name']."</td>";
	$Html .= "<td>".$Dept."</td>";
	$Html .= "<td>".date("d-m-Y",strtotime($row['DOR']))."</td>";
	$Html .= "<td>".$FMName."</td>";
	$Html .= "<td align='right' nowrap>".date("d-m-Y",strtotime($row['ApplOn']))."</td>";
	$Html .= "<td align='right' nowrap>".$row['LoanAmt']."</td>";
	$Html .= "<td align='right'>".$row['IntRate']."</td>";
	$Html .= "<td align='right'>".$row['Months']."</td>";
	$Html .= "<td align='right'>".$row['MthEMI']."</td>";
	$Html .= "<td align='right'>".$row['G1MemberID']."</td>";
	$Html .= "<td align='right'>".$row['G2MemberID']."</td>";

	$Html .= "<td>".$row['Status']."</td>";
	$Html .= "<td nowrap><button class='btn-sm' onclick=js_loanappl_approve('$RowID')>Approve</button>";
	$Html .= "<button  class='btn-sm' onclick=js_loanappl_reject('$RowID')>Reject</button>";
	$Html .= "</tr>";

	$SerialNo++;
}
$Html .= "</tbody>";
$Html .= "</table>";
$Report = $Html;
?>

    <?php include_once("navbar.php");?>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
                    <center><h4><?php echo $PanelHeading; ?></h4></center>
				</div>
				<div class='panel-body'>
					<div class='table-responsive'>
					<?php echo $Report; ?>
					</div>
				</div>
				
			</div>
		</div>
	</div>
	
</body>


<script>
	
	/*
  	$(document).ready(function() {
   		$('#gridtable').DataTable({"lengthMenu": [ 10, 25, 50, 75, 100,500,1000]});
   	});
   	*/
	function js_loanappl_approve(rowid) {
		if(confirm("Approve Loan for RowID:" + rowid)){
			$.ajax({
		        type: "POST",
		        url: "ajax_loanappl_approve.php",
		        data: "RowID="+rowid,
		        success : function(text){
		        	alert(text);
					location.reload(true);	        	
		        }
			});		
		};
	}
	function js_loanappl_reject(rowid) {
		if(confirm("Reject Application RowID : " + rowid)){
			$.ajax({
		        type: "POST",
		        url: "ajax_loanappl_reject.php",
		        data: "RowID="+rowid,
		        success : function(text){
		        	alert(text);
					location.reload(true);	        	
		        }
			});		
		}
	}
</script>
