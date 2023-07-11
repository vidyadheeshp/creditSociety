<?php

// rep_tb4 .php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 19.03.2020
//Trial Balance ShareHolders Accouns Detailed
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
$Report       = "";
$FromDate     = date("Y-m-d");
$ToDate       = date("Y-m-d");
$PanelHeading = "ShareHolders Trial Balance From ".$FromDate. " To ". $ToDate;

if(isset($_POST['fromdate'])){
	$FromDate     = date("Y-m-d",strtotime($_POST['fromdate']));
	$ToDate       = date("Y-m-d",strtotime($_POST['todate']));
	$RepFromDate  = date("d-m-Y",strtotime($_POST['fromdate']));
	$RepToDate    = date("d-m-Y",strtotime($_POST['todate']));
	$PanelHeading = "ShareHolders Trial Balance From ".$RepFromDate. " To ". $RepToDate;
	$Destination= $_POST['destination'];
	$Sql = "Select A.DeptID,A.MemberID,A.Name,A.OpenBal,
			(Select SUM(Debit)  from ft Where A.MemberID=ft.MemberID    AND (ft.TrnDate Between '$FromDate' and '$ToDate')) as Debit1,
			(Select SUM(Credit) from ft Where A.MemberID = ft.MemberID    AND (ft.TrnDate Between '$FromDate' and '$ToDate')) as Credit1
		From shareholders A 
		Group By A.MemberID,A.Name,A.OpenBal 
		Order By A.DeptID,A.Name ";	
		//echo $Sql;
	// function build_table($db,$Sql,$SerialNoReqd,$EditReqd='No',$DelReqd='No',$PrimaryID) {
	if($Destination=='Display'){
		$Html = "";
		// create report
		$Header = "<center><strong>KLS GIT Employees Co-Op Credit Society Ltd.</strong><center>\n";
		$Header .= "ShareHolders Trial Balance From <strong>".$RepFromDate. " To ".$RepToDate  ." </strong>";
		$Html  = "";
		$Html .= "<table id='table1' class='table table-bordered table-condensed table-striped bluecolor'>";
		$Html .= "<tr>
				<th>S.No</th>
				<th>Dept</th>
				<th>MemberID</th>
				<th>Name of Account</th>
				<th>Open_Deb</th>
				<th>Open_Cre</th>
				<th>Debit</th>
				<th>Credit</th>
				<th>Clos-Deb</th>
				<th>Clos_Cre</th>
			</tr>";
		$ResultRow = getResultSet($db,$Sql);
		$SerialNo 	= 1;

		$TotOpenDeb = 0;
		$TotOpenCre = 0;
		$TotDebit   = 0;
		$TotCredit  = 0;
		$TotClosDeb = 0;
		$TotClosCre = 0;

		foreach($ResultRow as $Row){
			$DeptID 	= $Row['DeptID'];
			$Dept  		= getSingleField($db,"Select SHName from departments Where DeptID='$DeptID'");
			$Debit 	 = 0;
			$Credit  = 0;
			$OpenBal = 0;
			$ClosBal = 0;
			
			$OpenBal = getOpenBalShareAcc($db,$Row['MemberID'],$FromDate);

			$Debit  = !is_null($Row['Debit1']) ? $Row['Debit1'] : 0; 

			$Credit  = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0; 

			$ClosBal = $OpenBal - $Debit + $Credit;
			// number_format($number, 2, '.', '');
			if($ClosBal<>0) {
				$Html .="<tr>";
				$Html .= "<td>".$SerialNo."</td>";
				$Html .= "<td>".$Dept."</td>";
				$Html .= "<td>".$Row['MemberID']."</td>";
				$Html .= "<td>".$Row['Name']."</td>";
				if($ClosBal<>0 or $OpenBal <> 0 or $Debit<>0 or $Credit <>0) {
					if($OpenBal<0) {
						$Html .= "<td align='right'>".number_format(abs($OpenBal), 2, '.', '')."</td>";
						$Html .= "<td></td>";
						$TotOpenDeb  += abs($OpenBal);						
					}else{
						$Html .= "<td></td>";
						$Html .= "<td align='right'>".number_format(abs($OpenBal), 2, '.', '')."</td>";
						$TotOpenCre  += abs($OpenBal);						
					}
					$Html .= "<td align='right'>".number_format($Debit, 2, '.', '')."</td>";
					$Html .= "<td align='right'>".number_format($Credit,2,'.','')."</td>";
					if($ClosBal<0) {
						$Html .= "<td align='right'>".number_format(abs($ClosBal), 2, '.', '')."</td>";
						$Html .= "<td></td>";
						$TotClosDeb += abs($ClosBal);
					}else{
						$Html .= "<td></td>";
						$Html .= "<td align='right'>".number_format(abs($ClosBal), 2, '.', '')."</td>";
						$TotClosCre += $ClosBal;
					}
					$Html .= "<td></td>";
					$TotDebit  += $Debit;
					$TotCredit += $Credit;
				}
				$Html .= "</tr>";
			}
		}
		$Html .="<tr>";
		$Html .= "<td></td>";
		$Html .= "<td></td>";
		$Html .= "<td></td>";
		$Html .= "<td></td>";
		$Html .= "<td align='right'>".number_format($TotOpenDeb, 2, '.', '')."</td>";
		$Html .= "<td align='right'>".number_format($TotOpenCre, 2, '.', '')."</td>";
		$Html .= "<td align='right'>".number_format($TotDebit, 2, '.', '')."</td>";
		$Html .= "<td align='right'>".number_format($TotCredit, 2, '.', '')."</td>";
		$Html .= "<td align='right'>".number_format($TotClosDeb, 2, '.', '')."</td>";
		$Html .= "<td align='right'>".number_format($TotClosCre, 2, '.', '')."</td>";
		$Html .= "</tr>";
		$Report = $Html;
	} else {
		// export
		$Html = "";
		// create report
		$Header = "KLS GIT Employees Co-Op Credit Society Ltd.\n\n";
		$Header .= "ShareHolders Detailed Trial Balance From ".$RepFromDate. " To ".$RepToDate  ."\n\n";
		$Html  = $Header;
		$Html .= "S.No\tDept\tMemberID\tName of Account\tOpen_Deb\tOpen_Cre\tDebit\tCredit\tClos-Deb\tClos_Cre\t\n";
						
		$ResultRow = getResultSet($db,$Sql);
		$SerialNo 	= 1;

		$TotOpenDeb = 0;
		$TotOpenCre = 0;
		$TotDebit   = 0;
		$TotCredit  = 0;
		$TotClosDeb = 0;
		$TotClosCre = 0;

		foreach($ResultRow as $Row){
			$DeptID 	= $Row['DeptID'];
			$Dept  		= getSingleField($db,"Select SHName from departments Where DeptID='$DeptID'");
			$Debit 	 = 0;
			$Credit  = 0;
			$OpenBal = 0;
			$ClosBal = 0;
			
			$OpenBal = getOpenBalShareAcc($db,$Row['MemberID'],$FromDate);

			$Debit  = !is_null($Row['Debit1']) ? $Row['Debit1'] : 0; 

			$Credit  = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0; 

			$ClosBal = $OpenBal - $Debit + $Credit;
			// number_format($number, 2, '.', '');
			if($ClosBal<>0) {
				$Html .= $SerialNo."\t";
				$Html .= $Dept."\t";
				$Html .= $Row['MemberID']."\t";
				$Html .= $Row['Name']."\t";
				if($ClosBal<>0 or $OpenBal <> 0 or $Debit<>0 or $Credit <>0) {
					if($OpenBal<0) {
						$Html .= number_format(abs($OpenBal), 2, '.', '')."\t";
						$Html .= "\t";
						$TotOpenDeb  += abs($OpenBal);						
					}else{
						$Html .= "\t";
						$Html .= number_format(abs($OpenBal), 2, '.', '')."\t";
						$TotOpenCre  += abs($OpenBal);						
					}
					$Html .= number_format($Debit, 2, '.', '')."\t";
					$Html .= number_format($Credit,2,'.','')."\t";
					if($ClosBal<0) {
						$Html .= number_format(abs($ClosBal), 2, '.', '')."\t";
						$Html .= "\t";
						$TotClosDeb += abs($ClosBal);
					}else{
						$Html .= "\t";
						$Html .= number_format(abs($ClosBal), 2, '.', '')."\t";
						$TotClosCre += $ClosBal;
					}
					$Html .= "\t";
					$TotDebit  += $Debit;
					$TotCredit += $Credit;
				}
				$Html .= "\n";
			}
		}
		$Html .= "\t";
		$Html .= "\t";
		$Html .= "\t";
		$Html .= "\t";
		$Html .= number_format($TotOpenDeb, 2, '.', '')."\t";
		$Html .= number_format($TotOpenCre, 2, '.', '')."\t";
		$Html .= number_format($TotDebit, 2, '.', '')."\t";
		$Html .= number_format($TotCredit, 2, '.', '')."\t";
		$Html .= number_format($TotClosDeb, 2, '.', '')."\t";
		$Html .= number_format($TotClosCre, 2, '.', '')."\t";
		$Html .= "\n";
		$Report = $Html;

		//$Heading = "Loan Recovery Statment from $RepFromDate To $RepToDate";
		//$Html = export2excel($db,$Sql,"SNo",$Heading);
	    header("Content-Disposition: attachment; filename=sharesdetailed_tb.xls");
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
					<form id='form1' name='form1' action='rep_tb4.php' method='post'>
					<div class='row form-inline'>
			
						<label>Enter From Date</label>
						<input type='date' id='fromdate' name='fromdate' value='<?php echo $FromDate;?>' />
						<label>Enter To Date</label>
						<input type='date' id='todate' name='todate' value='<?php echo $ToDate;?>' />
						<select id='destination' name='destination' class='form-control' required>
							<option value='Display'>Display</option>
							<option value='Export'>Export2Excel</option>
						</select>
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
