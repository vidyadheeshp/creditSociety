<?php

// rep_genledgers .php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 29.07.2021

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
		MsgBox("Access for Authorised Users only","accountsmenu",True);
		exit();
	}

//

require_once("../includes/pdofunctions_v1.php");

$db = connectPDO();
$Report       = "";
$FromDate     = date("Y-m-d");
$ToDate       = date("Y-m-d");
$PanelHeading = "General Ledgers From ".$FromDate. " To ". $ToDate;

if(isset($_POST['fromdate'])){
	$FromDate     = date("Y-m-d",strtotime($_POST['fromdate']));
	$ToDate       = date("Y-m-d",strtotime($_POST['todate']));
	$RepFromDate  = date("d-m-Y",strtotime($_POST['fromdate']));
	$RepToDate    = date("d-m-Y",strtotime($_POST['todate']));

	$Html = "";
	$Html .= "KLS GIT Employees Co-Op Credit Society Ltd.\n";
	$Html .= "General Ledgers $RepFromDate To $RepToDate\n";
	$sql = "Select * from fm Order By FMID";
	$resultFM = getResultSet($db,$sql);
	foreach($resultFM as $row){
		$FMID 	= $row['FMID'];
		$FMName = $row['Name'];
		$AcType = #row['AcType'];
		$Html .= "GL : ".$FMName. " AccID:".$FMID." As On ".date("d-m-Y")."\n";
		$Html .= "SNo\tDate\tTrnCode\tParticulars\tDebit\tCredit\tBalance\n";
		//$Balance = getSingleField($db,"Select OpenBal from fm Where FMID='$FMID'");
		// modified on 11.10.2021
		$Balance = getOpenBalGenAcc($db,$FMID,$FromDate);

		$SerialNo=1;
		$Html .= $SerialNo."\t";
		$Html .= "\t";
		$Html .= "\t";
		$Html .= "By Opening Balance\t";
		$Html .= "\t";
		$Html .= "\t";
		if($Balance<=0) {
			$Html .= abs($Balance)." Dr"."\t";
		}else{
			$Html .= ($Balance)." Cr"."\t";
		}
		$Html .= "\n";
		$SerialNo++;
		$TotDebit = 0;
		$TotCredit=0;
		if($AcType=='Bank'){
			$ResultSet = getResultSet($db,"Select * from ft WHere (FMID='$FMID' or FixFMID='$FMID') AND TrnDate >= '$FromDate' AND TrnDate<='$ToDate'  Order By TrnDate");
		}else{
			$ResultSet = getResultSet($db,"Select * from ft WHere (FMID='$FMID') AND TrnDate >= '$FromDate' AND TrnDate<='$ToDate' Order By TrnDate");
		}
		foreach($ResultSet as $Row){
			$Particulars = $Row['Particulars'];
			$Particulars = trim($Particulars).$Row['MemberID']. " ".$Row['LoanID'];
			$Debit 	= $Row['Debit'];
			$Credit = $Row['Credit'];
			if($AcType=='Bank' and $Row['FixFMID']==$FMID){
				// Interchange Debit/Credit
				$Temp = $Debit;
				$Debit = $Credit;
				$Credit = $Temp;
			}
			$Balance = $Balance - $Debit + $Credit;

			$Html .= $SerialNo."\t";
			$Html .= date("d-m-Y",strtotime($Row['TrnDate']))."\t";
			$Html .= $Row['TrnCode']."\t";
			$Html .= $Particulars."\t";
			$Html .= $Debit."\t";
			$Html .= $Credit."\t";
			if($Balance<=0) {
				$Html .= abs($Balance)." Dr"."\t";
			}else{
				$Html .= ($Balance)." Cr"."\t";
			}
			$Html .= "\n";
			$TotCredit += $Credit;
			$TotDebit  += $Debit;
		}
		$Html .= "\t";
		$Html .= "\t";
		$Html .= "\t";
		$Html .= "\t";
		$Html .= $TotDebit."\t";
		$Html .= $TotCredit."\t";
		$Html .= "\t";
		$Html .= "\n\n\n";
	}
	$Report = $Html;
	//$Heading = "Loan Recovery Statment from $RepFromDate To $RepToDate";
	//$Html = export2excel($db,$Sql,"SNo",$Heading);
    header("Content-Disposition: attachment; filename=genled.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo "$Html";	
    exit();	
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
					<form id='form1' name='form1' action='rep_genledgers1.php' method='post'>
					<div class='row form-inline'>
						<label>Enter From Date</label>
						<input type='date' id='fromdate' name='fromdate' value='<?php echo $FromDate;?>' />
						<label>Enter To Date</label>
						<input type='date' id='todate' name='todate' value='<?php echo $ToDate;?>' />
						<input type='submit' class='btn-primary'/>
					</div>
					</form>
					<div id='report' class='table-responsive'>
						<?php echo $Report; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
