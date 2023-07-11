<?php

// rep_banksummary.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 21.11.2020
// Bank Book Summary 
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

$db     = connectPDO();
$Report = "";
$FMList = "";
$FMList = genSelectFM($db,"bankfmid"," AcType='Bank' ", " required ");		

$FromDate     = date("Y-m-d");
$ToDate       = date("Y-m-d");
$RepFromDate  = date("d-m-Y");
$RepToDate    = date("d-m-Y");
$PanelHeading = "Bank Summary From ".$FromDate. " To ". $ToDate;

if(isset($_POST['fromdate'])){
	$FMID 	= $_POST['bankfmid'];
	$FromDate     = date("Y-m-d",strtotime($_POST['fromdate']));
	$ToDate       = date("Y-m-d",strtotime($_POST['todate']));
	$RepFromDate  = date("d-m-Y",strtotime($_POST['fromdate']));
	$RepToDate    = date("d-m-Y",strtotime($_POST['todate']));
	$Destination= $_POST['destination'];

	// Process
	// Outmost loop : ft group by date
	//    for each date ft without mthemi mthshare
	//    for each date for mthemi, mthshare
	/////////////////////////////////////////////////
	if($Destination=='Display'){

	
		$FMName = getSingleField($db,"Select Name   from fm Where FMID='$FMID'");
		$Header = "<center><strong>KLS GIT Employees Co-Op Credit Society Ltd.</strong></center><br>";
		$Header .= "Bank Daywise Summary : <strong>".$FMName. " AccID:".$FMID." </strong>From ".$RepFromDate. " To ".$RepToDate."<br>";
		$Html  = "";
		$Html .= "<table id='table1' class='table table-bordered table-condensed table-striped bluecolor'>";
		$Html .= "<tr style='background-color:yellow;'>
					<th>Date</th>
					<th>Particulars</th>
					<th>Receipts</th>
					<th>Payment</th>
					<th>Balance</th>
					</tr>";
		$Balance = getOpenBalGenAcc($db,$FMID,$FromDate);
		$ActBal  = $Balance;

		$Html .="<tr style='background-color:pink;'>";
		$Html .= "<td></td>";
		$Html .= "<td>Opening Balance</td>";
		// reverse balance after printing : Debit bal will be $Balance
		// Credit bal will be -ve balance
		if($Balance<=0) {
			$Html .= "<td align='right'>".abs($Balance)."</td>";
			$Html .= "<td></td>";
			$Balance = abs($Balance);
		}else{
			$Html .= "<td></td>";
			$Html .= "<td align='right'>".($Balance)."</td>";
			$Balance = $Balance * -1 ;
		}

		$Html .= "<td></td></tr>";
		// outermost loop
		$TotalReceipt = 0;
		$TotalPayment = 0;
		$Where = "( FMID='$FMID' or FixFMID='$FMID') AND (TrnDate Between '$FromDate' AND '$ToDate') ";
		$OuterResultSet = getResultSet($db,"Select TrnDate,FMID,FixFMID,SUM(Debit) as S_Debit,SUm(Credit) as S_Credit from ft Where $Where  Group By TrnDate,FMID,FixFMID Order By TrnDate,FMID,FixFMID");

		foreach($OuterResultSet as $OuterRow){
			$ProcDate   = date("Y-m-d",strtotime($OuterRow['TrnDate']));
			$DayReceipt = 0;
			$DayPayment = 0;
			$FtFMID 	= $OuterRow['FMID'];
			$FtFixMID 	= $OuterRow['FMID'];
			$Debit 		= $OuterRow['S_Debit'];
			$Credit 	= $OuterRow['S_Credit'];
			if($OuterRow['FixFMID']==$FMID) {
				$DayTotReceipt += $Credit;
				$TotalReceipt  += $Credit;
				$DayReceipt     = $Credit;
				$Balance       += $DayReceipt;
				
				$DayTotPayment += $Debit;
				$TotalPayment  += $Debit;
				$DayPayment     = $Debit;
				$Balance 	   -= $DayPayment;
			} else{
				$DayTotPayment += $Credit;
				$TotalPayment  += $Credit;
				$DayPayment     = $Credit;
				$Balance       -= $DayPayment;

				$DayTotReceipt += $Debit;
				$TotalReceipt  += $Debit;
				$DayReceipt    = $Debit;
				$Balance 	  += $DayPayment;
			}

			$Html .="<tr  style='background-color:lightblue;'>";
			$Html .= "<td>".date("d-m-Y",strtotime($OuterRow['TrnDate']))."</td>";
			$Html .= "<td align='left'>".getSingleField($db,"Select Name from fm Where FMID='$FtFMID'")."</td>";
			$Html .= "<td align='right'>".$DayReceipt."</td>";
			$Html .= "<td align='right'>".$DayPayment."</td>";
			$ActBal = $ActBal - $DayReceipt + $DayPayment;
			$Html .= "<td align='right'>".ConvBalance($ActBal)."</td>";
			$Html .= "</tr>";
		}
		$Html .="<tr  style='background-color:lightblue;'>";
		$Html .= "<td></td>";
		$Html .= "<td></td>";
		$Html .= "<td align='right'>".$TotalReceipt."</td>";
		$Html .= "<td align='right'>".$TotalPayment."</td>";
		$Html .= "<td></td>";
		$Html .= "</tr>";

		$Report = $Header . $Html;
		// need to change this
	} elseif($Destination=='Export'){
		$FMName = getSingleField($db,"Select Name   from fm Where FMID='$FMID'");
		$Header = "KLS GIT Employees Co-Op Credit Society Ltd.\n\n";
		$Header .= "Bank Book : ".$FMName. " AccID:".$FMID." From ".$RepFromDate. " To ".$RepToDate."\n";
		$Html  = "";
		$Html .= "Date\t
					TrnCode\t
					AccID\t
					Particulars\t
					Receipts\t
					Payment\t\n";
		$Balance = getOpenBalGenAcc($db,$FMID,$FromDate);
		$Html .= "\t\t\tBy Opening Balance\t";
		// reverse balance after printing : Debit bal will be $Balance
		// Credit bal will be -ve balance
		if($Balance<=0) {
			$Html .= abs($Balance)."\t";
			$Html .= "\t";
			$Balance = abs($Balance);
		}else{
			$Html .= "\t";
			$Html .= ($Balance)."\t";
			$Balance = $Balance * -1 ;
		}
		$Html .= "\n";
		// outermost loop
		$Where = "( FMID='$FMID' or FixFMID='$FMID') AND (TrnDate Between '$FromDate' AND '$ToDate') ";
		$OuterResultSet = getResultSet($db,"Select DISTINCT TrnDate from ft Where $Where  Order By TrnDate");
		$TotalReceipt = 0;
		$TotalPayment = 0;

		foreach($OuterResultSet as $OuterRow){
			$ProcDate   = date("Y-m-d",strtotime($OuterRow['TrnDate']));
			$DayTotReceipt = 0;
			$DayTotPayment = 0;

			$Where1 = "( FMID='$FMID' or FixFMID='$FMID') AND TrnDate = '$ProcDate' AND ";
			$Where1 .= " TrnType NOT IN('MthContr','MthEMI')  ";

			$Inner1ResultSet = getResultSet($db,"Select * from ft Where $Where1  Order By TrnDate");
			foreach($Inner1ResultSet as $Inner1Row){
				$DayReceipt = 0;
				$DayPayment = 0;
				$FtFixMID 	= $Inner1Row['FMID'];
				$FtFMID 	= $Inner1Row['FMID'];
				$FtFMName 	= getSingleField($db,"Select Name from fm Where FMID='$FtFMID'");

				$Debit 		= $Inner1Row['Debit'];
				$Credit 	= $Inner1Row['Credit'];
				if($Inner1Row['FixFMID']==$FMID) {
					if($Credit >0) {
						$DayTotReceipt += $Credit;
						$DayReceipt     = $Credit;
						$Balance       += $DayReceipt;
					} else{
						$DayTotPayment += $Debit;
						$DayPayment    = $Debit;
						$Balance 	  -= $DayPayment;
					}
				} else{
					if($Credit >0) {
						$DayTotPayment += $Credit;
						$DayPayment     = $Credit;
						$Balance       -= $DayPayment;
					} else{
						$DayTotReceipt += $Debit;
						$DayReceipt    = $Debit;
						$Balance 	  += $DayPayment;
					}
				}

				$FtFixMID 	= $Inner1Row['FMID'];
				$FtFMID 	= $Inner1Row['FMID'];
				$FtFMName 	= getSingleField($db,"Select Name from fm Where FMID='$FtFMID'");
				$SubAccID   = "";
				$SubAccName = "";
				if(strlen($Inner1Row['LoanID'])>0) {
					$SubAccID 	= $Inner1Row['LoanID'];
					$MemberID	= getSingleField($db,"Select MemberID from customers Where LoanID='$SubAccID'");
					$SubAccName = getSingleField($db,"Select Name from shareholders Where MemberID='$MemberID'");
				}
				if(strlen($Inner1Row['MemberID'])>0) {
					$SubAccID 	= $Inner1Row['MemberID'];
					$SubAccName = getSingleField($db,"Select Name from shareholders Where MemberID='$SubAccID'");
				}
				$Html .= $ProcDate."\t";
				$Html .= $Inner1Row['TrnCode']."\t";
				$Html .= $Inner1Row['FMID']."\t";
				$Html .= $FtFMName." ".$SubAccID." ".$SubAccName."\t";
				$Html .= $DayReceipt."\t";
				$Html .= $DayPayment."\t";
				$Html .= "\n";
			}

			$Where2 = "( FMID='$FMID' or FixFMID='$FMID') AND TrnDate = '$ProcDate' AND LENGTH(TrnType)>0 AND ";
			$Where2 .= " TrnType IN('MthContr','MthEMI')  ";
			$Inner2ResultSet = getResultSet($db,"Select FMID,TrnCode,SUM(Credit) as S_Credit,FixFMID from ft Where $Where2 Group By FMID Order By TrnDate");
			foreach($Inner2ResultSet as $Inner2Row){
				$DayReceipt = 0;
				$DayPayment = 0;
				$FtFixMID 	= $Inner2Row['FMID'];
				$FtFMID 	= $Inner2Row['FMID'];
				$FtFMName 	= getSingleField($db,"Select Name from fm Where FMID='$FtFMID'");

				$Debit 		= 0;
				$Credit 	= $Inner2Row['S_Credit'];
				if($Inner2Row['FixFMID']==$FMID) {
					if($Credit >0) {
						$DayTotReceipt += $Credit;
						$DayReceipt     = $Credit;
						$Balance       += $DayReceipt;
					} else{
						$DayTotPayment += $Debit;
						$DayPayment    = $Debit;
						$Balance 	  -= $DayPayment;
					}
				} else{
					if($Credit >0) {
						$DayTotPayment += $Credit;
						$DayPayment     = $Credit;
						$Balance       -= $DayPayment;
					} else{
						$DayTotReceipt += $Debit;
						$DayReceipt    = $Debit;
						$Balance 	  += $DayPayment;
					}
				}

				$FtFixMID 	= $Inner2Row['FMID'];
				$FtFMID 	= $Inner2Row['FMID'];
				$FtFMName 	= getSingleField($db,"Select Name from fm Where FMID='$FtFMID'");
				$SubAccID   = "";
				$SubAccName = "";
				$Html .= $ProcDate."\t";
				$Html .= $Inner2Row['TrnCode']."\t";
				$Html .= $Inner2Row['FMID']."\t";
				$Html .= $FtFMName." ".$SubAccID." ".$SubAccName."\t";
				$Html .= $DayReceipt."\t";
				$Html .= $DayPayment."\t";
				$Html .= "\n";
			}
			// print daily total and balance
				$Html .= "\t\t\t";
				$Html .= "Day Total\t";
				$Html .= $DayTotReceipt."\t";
				$Html .= $DayTotPayment."\t";
				$Html .= "\n";
			// print daily balance
				$Html .= "\t\t\t";
				$Html .= "Day Balance\t";
				if($Balance >0) {
					$Html .= $Balance."\t";
					$Html .= "\t";
				} else {
					$Html .= "\t";
					$Html .= $Balance."\t";
				}
				$Html .= "\n";

		}
		$Report = $Header . $Html;
		CreateLog("Bank Summary Excel created");
	    header("Content-Disposition: attachment; filename=bankbook.xls");
	    header("Pragma: no-cache");
	    header("Expires: 0");
	    print "$Report";		
	    exit();
	}
	//cho json_encode(array('Header'=>$Header,'Body'=> $Html), JSON_FORCE_OBJECT);	
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
					<div class='row form-inline hidden-print'>
						<form id='form1' name='form1' action='rep_banksummary.php' method='post'>
						<?php echo $FMList; ?>
						<label>Enter From Date</label>
						<input type='date' id='fromdate' name='fromdate' value='<?php echo $FromDate;?>' />
						<label>Enter To Date</label>
						<input type='date' id='todate' name='todate' value='<?php echo $ToDate;?>' />
						<select id='destination' name='destination' class='form-control' required>
							<option value='Display'>Display</option>
							<option value='Export'>Export2Excel</option>
						</select>
						<input type='submit' class='btn-primary'/>
						</form>
					</div>
					<div id='report' class='table-responsive col-md-6'>
						<?php echo $Report; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
