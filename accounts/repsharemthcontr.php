<?php

// 
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 25.12.2019
// repsharemthcontr.php

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
require_once("../includes/pdofunctions_v1.php");
$db = connectPDO();
$PanelHeading = "Share Holders Monthly Contribution List ";
$Html = "";
$Sql  = "Select * from shareholders Where ClosBal<>0 Order By DeptID";
$result = getResultSet($db,$Sql);
$Html = "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed w-auto'>";
$Html .= "<thead>";
$Html .= "<tr>";
$Html .= "<th>SNo</th>";
$Html .= "<th>MemberID</th>";
$Html .= "<th>Name</th>";
$Html .= "<th class='text-center'>Dept</th>";
$Html .= "<th class='text-left'>Mobile</th>";
$Html .= "<th class='text-left'>DtOfJoin</th>";
$Html .= "<th class='text-left'>DtOfRetr</th>";
$Html .= "<th class='text-right'>Balance</th>";
$Html .= "<th class='text-right'>MthContr</th>";
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
	$Html .= "<tr>";
	$Html .= "<td>$SerialNo</td>";
	$Html .= "<td>".$row['MemberID']."</td>";
	$Html .= "<td>".$row['Name']."</td>";
	$Html .= "<td>".$Dept."</td>";
	$Html .= "<td>".$row['Mobile']."</td>";
	$Html .= "<td nowrap>".date("d-m-Y",strtotime($row['OpenDate']))."</td>";
	$Html .= "<td nowrap>".date("d-m-Y",strtotime($row['DOR']))."</td>";
	$Html .= "<td align='right'>".$row['ClosBal']."</td>";
	$Html .= "<td align='right'>".$row['MthContr']."</td>";
	$Html .= "</tr>";

	$SerialNo++;
	$TotalClosBal  	+= $row['ClosBal'];
	$TotalMthContr  += $row['MthContr'];
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
$Html .= "<td align='right'>".$TotalClosBal."</td>";
$Html .= "<td align='right'>".$TotalMthContr."</td>";
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
				</div>
			</div>
		</div>
	</div>
</body>
<script>
  	$(document).ready(function() {
   		$('#gridtable').DataTable({"lengthMenu": [ 10, 25, 50, 75, 100,500,1000]});
   	});
</script>

