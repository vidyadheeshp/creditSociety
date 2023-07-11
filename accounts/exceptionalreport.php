<?php
	// exceptionalreport.php
	session_start();
	require_once("../includes/functions.php");

	if(!isset($_SESSION['UserType'])) {
		MsgBox("Direct script access prohibited","../index.php",True);
		exit();
	}
	
	$UserType 	= $_SESSION['UserType'];
	if(strstr('Accounts,Admin',$UserType)){

	} else{
		MsgBox("Access for Authorised Users only","accountsmenu.php",True);
		exit();
	}
	//require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$PanelHeading = " Exceptional Report";

	$db 			   	= connectPDO();
	if(isset($_POST['Submit'])){
		$FromDate = date("Y-m-d",strtotime($_POST['fromdate']));
		$ToDate   = date("Y-m-d",strtotime($_POST['todate']));
		// find out no int collection, no principal collection,during the period
		$NoIntNoPrincipal = "<table class='table table-bordered table-responsive'>";
		$NoIntNoPrincipal .= "<tr>";
		$NoIntNoPrincipal .= "<th>LoanID</th><th>Name</th><th>LoanAmt</th><th>LoanDate</th><th>Balance</th>
		<th>LastIntRecd</th><th>Status</th><th>Principal</th><th>Interest</th></tr>";

		$Sql  = "Select B.*,A.Name,A.DeptID,A.DesignID from customers B, shareholders A 
				Where B.MemberID = A.MemberID and B.LoanDate <= '$FromDate' and B.ClosBal<0 
				Order By A.DeptID,B.LoanID ";
		$result = getResultSet($db,$Sql);
		foreach($result as $LoanRow){
			$LoanID = $LoanRow['LoanID'];
			$Sql2  = "Select sum(Credit) as S_Credit,SUM(Interest) as S_Int from ft Where LoanID='$LoanID' and TrnDate Between '$FromDate' and '$ToDate'";
			$result2 = getResultSet($db,$Sql2);
			foreach($result2 as $FTRow){
				if($FTRow['S_Credit']==0  or $FTRow['S_Int']=0){
					$NoIntNoPrincipal .= "<tr>";
					$NoIntNoPrincipal .= "<td>".$LoanID."</td>";
					$NoIntNoPrincipal .= "<td>".$LoanRow['Name']."</td>";
					$NoIntNoPrincipal .= "<td>".$LoanRow['LoanAmt']."</td>";
					$NoIntNoPrincipal .= "<td>".date("d-m-Y",strtotime($LoanRow['LoanDate']))."</td>";
					$NoIntNoPrincipal .= "<td style='text-align:right;'>".ConvBalance($LoanRow['ClosBal'])."</td>";
					$NoIntNoPrincipal .= "<td>".date("d-m-Y",strtotime($LoanRow['LastRecDate']))."</td>";
					$NoIntNoPrincipal .= "<td>".$LoanRow['Status']."</td>";
					$NoIntNoPrincipal .= "<td>".$FTRow['S_Credit']. "</td>";
					$NoIntNoPrincipal .= "<td>".$FTRow['S_Int']."</td>";
					$NoIntNoPrincipal .= "</tr>";
				}
			}
		}
		$NoIntNoPrincipal .= "</table>";

		$NoShares = "";
		$NoShares = "<table class='table table-bordered table-responsive'>";
		$NoShares .= "<tr>";
		$NoShares .= "<th>MemberID</th><th>Name</th><th>Balance</th></tr>";

		$Sql  = "Select A.Name,A.DeptID,A.DesignID from shareholders A 
				Order By DeptID,MemberID "; 
		$result = getResultSet($db,$Sql);
		foreach($result as $shareRow){
			$MemberID = $shareRow['MemberID'];
			$Sql2  = "Select sum(Credit) as S_Credit from ft Where MemberID='$MemberID' and TrnDate Between '$FromDate' and '$ToDate'";
			$result2 = getResultSet($db,$Sql2);
			foreach($result2 as $FTRow){
				if($FTRow['S_Credit']==0){
					$NoShares .= "MemberID:".$LoanID." ".$shareRow['Name']."</br>";
					$NoShares .= "<tr>";
					$NoShares .= "<td>".$MemberID."</th><th>". $shareRow['Name']."</th><th>".$shareRow['ClosBal']."</th></tr>";
				}
			}
		}
		$NoShares .= "</table>";
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
	<link  href="../includes/avd.css"  rel="stylesheet"></link>   

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
		</div>
		<div class='col-md-12'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
    	        	<center><h4>Exceptional Report</h4></center>
					<form id="addloan" name="addloan" role="form" class='form-inline' method="post" action="exceptionalreport.php" 	enctype="multipart/form-data">
					<label>FromDate</label>
					<input type='date' id='fromdate' name='fromdate' />
					<label>ToDate</label>
					<input type='date' id='todate' name='todate' />
					<input type='submit' name='Submit' value='Submit'/>
					</form>
				</div>
				<div class='panel-body'>
					<?php echo $NoIntNoPrincipal; ?>
					<?php echo $NoShares; ?>
				</div>
			</div>
			<div class='panel-footer'>
				<strong><center>Software Developed By Anand V Deshpande,Belagavi</center></strong>
			</div>
		</div>
	</div>
</body>
<?php include('../includes/modal.php'); ?>
<script type='text/javascript'>
	var errors = 0;
	function showdets() {
		$.ajax({
	        type: "POST",
	        url: "ajax_getfield2.php",
	        data: "fmid="+fixfmid,
	        success : function(text){
	        	var ret = JSON.parse(text);
	        	$("#actualfixfmidbal").val(ret['ClosBal']);
	        	$("#fixfmidbal").val(ret['ConvAmt']);
	        }
		});
	}
