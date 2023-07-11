<?php

// loans_edit_list.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 15.04.2020
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
$PanelHeading = "Loan Accounts List - Edit ";
//print_r($_POST);
if(isset($_POST['submit']))  {
	//print_r($_POST);
	CreateLog("Inside Submit procedure ");
	$ArrayLoanID = $_POST['loanid'];
	$ArrayMthEMI = $_POST['mthemi'];
	$ArrayPrevMthEmi = $_POST['prevmthemi'];

	CreateLog("Inside Submit procedure : Loan Accounts in post: ".count($ArrayLoanID));
	$db->BeginTransaction();
	CreateLog("ListEdit : BeginTransaction");
	$UpdatedCount=0;
	try {
		for($i=0;$i<count($ArrayLoanID);$i++){
			$LoanID 	= $ArrayLoanID[$i];
			$MthEMI 	= intval($ArrayMthEMI[$i]);
			$PrevMthEMI = intval($ArrayPrevMthEmi[$i]);
			if($PrevMthEMI != $MthEMI) {
				$Arr = array("MthEMI"=>$MthEMI);
				$where  = "LoanID = '$LoanID'";
				$Result = update($db,"customers",$Arr,$where);
				CreateLog("ListEdit : $LoanID MthEMI $MthEMI");
				$UpdatedCount++;
			}
		}
   	} catch (Exception $ex) {
   		$db->rollBack();
   		CreateLog("ListEdit ".$ex->getMessage());
      	return $ex->getMessage();
  	}
	$db->commit();	
	MsgBox("Successfully Updated $UpdatedCount Accounts ","accountsmenu.php",True);
}

$Html = "";
$Sql  = "Select B.*,A.Name,A.DeptID,A.DesignID from customers B, shareholders A 
		Where B.MemberID = A.MemberID Order By A.DeptID,A.Name";
$result = getResultSet($db,$Sql);
$Html = "";
$Html .= "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed'>";
$Html .= "<thead>";
$Html .= "<tr>";
$Html .= "<th>SNo</th>";
$Html .= "<th>Dept</th>";
$Html .= "<th>LoanID</th>";
$Html .= "<th>MemberID</th>";
$Html .= "<th>Name</th>";
$Html .= "<th>MthEMI</th>";
$Html .= "<th>LoanDate</th>";
$Html .= "<th>Balance</th>";
$Html .= "<th>IntUpto</th>";
$Html .= "<th>Status</th>";
$Html .= "</tr>";
$Html .= "</thead>";

$Html .= "<tbody>";
$SerialNo = 1;
$TotalClosBal = 0;
$TotalMthEMI  = 0;
$TotalIntColl = 0;
foreach($result as $row) {
	$LoanID 	= $row['LoanID'];
	$DeptID    	= $row['DeptID'];
	$MemberID  	= $row['MemberID'];
	$MthEMI 	= $row['MthEMI'];

	$Dept  		= getSingleField($db,"Select ShName        from departments Where DeptID='$DeptID'");
	$Html .= "<tr>";
	$Html .= "<td>$SerialNo</td>";
	$Html .= "<td>".$Dept."</td>";
	$Html .= "<td><input  tabindex='-1' type='text' id='loanid[]' name='loanid[]' value='$LoanID' readonly/></td>";
	$Html .= "<td>".$row['MemberID']."</td>";
	$Html .= "<td>".$row['Name']."</td>";
	$Html .= "<td><input type='hidden' id='prevmthemi[]' name='prevmthemi[]' value='$MthEMI'/> 
				<input type='text' id='mthemi[]' name='mthemi[]' value='$MthEMI'/></td>";
	$Html .= "<td align='right' nowrap>".date("d-m-Y",strtotime($row['LoanDate']))."</td>";
	$Html .= "<td align='right' nowrap>".ConvBalance($row['ClosBal'])."</td>";
	$Html .= "<td align='left' nowrap>".date("d-m-Y",strtotime($row['LastRecDate']))."</td>";
	$Html .= "<td>".$row['Status']."</td>";
	$Html .= "</tr>";
	$SerialNo++;
	$TotalClosBal  	+= $row['ClosBal'];
}
$Html .= "</tbody>";
$Html .= "</table>";
$Html .= "";
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
					<form id='loanedit' name='loanedit' method='post' action='loans_edit_list.php'>
					<div class='table-responsive'>
						<?php echo $Report; ?>
					</div>
						<input type='submit' id='submit' name='submit' value='Submit_Changes' class="btn btn-success"></input>
					</form>
				</div>
				<div class='panel-footer'>
					<button class='btn btn-success' onclick="window.location.href='loanaccounts.php'">New Loan Account</button>
					<button class='btn' onclick="window.location.href='../index.php'">Logout</button>
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
<script>
</script>
