<?php

// repforloancollection.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 21.02.2020
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
	$Report = "";
//
//require_once("../includes/functions.php");
require_once("../includes/pdofunctions_v1.php");
require_once("../includes/functions.php");

$db = connectPDO();
$PanelHeading = "Loan Statement For Current Month Collection";
$FMList = "";
//if(!isset($_POST['Submit'])) {
$FMList = genSelectFM($db,"loanfmid"," AcType='Cust' ", " required ");		
//}
if(isset($_POST['Submit'])) {
	$LoanFMID = $_POST['loanfmid'];
	$IntUptoDate = date("Y-m-d",strtotime($_POST['intuptodate']));

	$Html = "";
	$Sql  = "Select B.*,A.Name,A.DeptID,D.ShName,A.DesignID from customers B, shareholders A , departments D
			Where B.MemberID = A.MemberID  AND A.DeptID = D.DeptID 
			AND B.MthEMI > 0 AND B.Status='Active' AND B.ClosBal<0 AND B.FMID='$LoanFMID' 
			Order By A.DeptID,LoanID";
	$result = getResultSet($db,$Sql);
	$Html = "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed bluecolor'>";
	$Html .= "<thead>";
	$Html .= "<tr>";
	$Html .= "<th>SNo</th>";
	$Html .= "<th>LoanID</th>";
	$Html .= "<th>MemberID</th>";
	$Html .= "<th>Name</th>";
	$Html .= "<th>Dept</th>";
	$Html .= "<th>LoanAmt</th>";
	$Html .= "<th>MthEMI</th>";
	$Html .= "<th>Bal</th>";
	$Html .= "<th>Prin</th>";
	$Html .= "<th>Int</th>";
	$Html .= "<th>AfterColl</th>";
	$Html .= "</tr>";
	$Html .= "</thead>";

	$Html .= "<tbody>";
	$SerialNo 		= 1;
	$TotalClosBal 	= 0;
	$TotalMthEMI  	= 0;
	$TotalPrincipal = 0;
	$TotalInterest  = 0;
	$TotalNewBal    = 0;	
	$TotalLoan  	= 0;
	foreach($result as $row) {
		$LoanID 	= $row['LoanID'];
		$DeptID    	= $row['DeptID'];
		$MemberID  	= $row['MemberID'];
		//$Dept  		= getSingleField($db,"Select ShName from departments Where DeptID='$DeptID'");
		$ClosBal  = $row['ClosBal'];
		$LoanDate = date("Y-m-d",strtotime($row['LoanDate']));
		$IntRate  = $row['IntRate'];
		$LastRecDate = date("Y-m-d",strtotime($row['LastRecDate']));
		$MthEMI     = $row['MthEMI'];
		$Interest   = 0;
		$Principal  = 0;
		if(is_null($row['LastRecDate'])) {
			$Days = getDaysDiff($LoanDate,$IntUptoDate);
		} else{
			$Days = getDaysDiff($LastRecDate,$IntUptoDate);
		}
		$Interest = intval(  ($ClosBal*-1) * $IntRate * $Days / 36500);
		if($MthEMI >= $Interest){
			$Principal = $MthEMI - $Interest;
		}
		$NewBalance = intval($row['ClosBal']) + $Principal;
		if($MthEMI <> $Principal+$Interest) {
			$Html .= "<tr style='background:pink;'>";
		}else{
			$Html .= "<tr>";
		}
		$Html .= "<td>$SerialNo</td>";
		$Html .= "<td>".$row['LoanID']."</td>";
		$Html .= "<td>".$row['MemberID']."</td>";
		$Html .= "<td>".$row['Name'];
		if($MthEMI <> $Principal+$Interest) {
			$Html .= "Err:".$MthEMI.":".$Principal.":".$Interest;		
			$Errors++;
		}
		$Html .= "</td>";
		$Html .= "<td>".$row['ShName']."</td>";
		$Html .= "<td align='right'>".$row['LoanAmt']."</td>";
		$Html .= "<td align='right'>".$row['MthEMI']."</td>";

		$Html .= "<td align='right' nowrap>".ConvBalance($row['ClosBal'])."</td>";
		$Html .= "<td align='right' nowrap>".$Principal."</td>";
		$Html .= "<td align='right' nowrap>".$Interest."</td>";
		$Html .= "<td align='right' nowrap>".ConvBalance($NewBalance)."</td>";
		$Html .= "</tr>";

		$SerialNo++;
		$TotalLoan      += $row['LoanAmt'];
		$TotalClosBal  	+= $row['ClosBal'];
		$TotalMthEMI    += $row['MthEMI'];
		$TotalPrincipal += $Principal;
		$TotalInterest  += $Interest;
		$TotalNewBal    += $NewBalance;
	}
	$Html .= "</tbody>";
	$Html .= "<tfoot>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td>Errors:".$Errors."</td>";
	$Html .= "<td></td>";
	$Html .= "<td align='right'>".$TotalLoan."</td>";
	$Html .= "<td align='right'>".$TotalMthEMI."</td>";
	$Html .= "<td align='right'>".ConvBalance($TotalClosBal)."</td>";
	$Html .= "<td align='right'>".$TotalPrincipal."</td>";
	$Html .= "<td align='right'>".$TotalInterest."</td>";
	$Html .= "<td align='right'>".ConvBalance($TotalNewBal)."</td>";
	$Html .= "</tr>";
	$Html .= "</tfoot>";
	$Html .= "</table>";
	$Report = $Html;
}
if(isset($_POST['Export'])) {
	$Location   = $_POST['loca'];	
	$LoanFMID = $_POST['loanfmid'];
	$IntUptoDate = date("Y-m-d",strtotime($_POST['intuptodate']));

	$Html = "";
	$Sql  = "Select B.*,A.Name,A.DeptID,D.ShName,A.DesignID from customers B, shareholders A , departments D
			Where B.MemberID = A.MemberID  AND A.DeptID = D.DeptID 
			AND B.MthEMI > 0 AND B.Status='Active' AND B.ClosBal<0 AND B.FMID='$LoanFMID' 
			Order By A.DeptID,LoanID";
	$result = getResultSet($db,$Sql);
	$Html .= "Loan to be collected : Int upto ".$IntUptoDate."\n\n";
	$Html .= "SNo\t";
	$Html .= "LoanID\t";
	$Html .= "MemberID\t";
	$Html .= "Name\t";
	$Html .= "Dept\t";
	$Html .= "LoanAmt\t";
	$Html .= "MthEMI\t";
	$Html .= "Bal\t";
	$Html .= "Prin\t";
	$Html .= "Int\t";
	$Html .= "AfterColl\n";

	$SerialNo 		= 1;
	$TotalClosBal 	= 0;
	$TotalMthEMI  	= 0;
	$TotalPrincipal = 0;
	$TotalInterest  = 0;
	$TotalNewBal    = 0;	
	$TotalLoan  	= 0;
	foreach($result as $row) {
		$LoanID 	= $row['LoanID'];
		$DeptID    	= $row['DeptID'];
  		$Loc1       = "";
  		$Loc1  		= getSingleField($db,"Select Location from departments Where DeptID='$DeptID'");
  		$Loc1       = trim($Loc1);
  		//echo "Location in func $Location $Loc1 ";
  		if($Location == $Loc1){		
			$MemberID  	= $row['MemberID'];
			//$Dept  		= getSingleField($db,"Select ShName from departments Where DeptID='$DeptID'");
			$ClosBal  = $row['ClosBal'];
			$LoanDate = date("Y-m-d",strtotime($row['LoanDate']));
			$IntRate  = $row['IntRate'];
			$LastRecDate = date("Y-m-d",strtotime($row['LastRecDate']));
			$MthEMI     = $row['MthEMI'];
			$Interest   = 0;
			$Principal  = 0;
			if(is_null($row['LastRecDate'])) {
				$Days = getDaysDiff($LoanDate,$IntUptoDate);
			} else{
				$Days = getDaysDiff($LastRecDate,$IntUptoDate);
			}
			$Interest = intval(  ($ClosBal*-1) * $IntRate * $Days / 36500);
			if($MthEMI >= $Interest){
				$Principal = $MthEMI - $Interest;
			}
			$NewBalance = intval($row['ClosBal']) + $Principal;
			$Html .= $SerialNo."\t";
			$Html .= $row['LoanID']."\t";
			$Html .= $row['MemberID']."\t";
			$Html .= $row['Name'];
			if($MthEMI <> $Principal+$Interest) {
				$Html .= "Err:".$MthEMI.":".$Principal.":".$Interest;		
				$Errors++;
			}
			$Html .= "\t";
			$Html .= $row['ShName']."\t";
			$Html .= $row['LoanAmt']."\t";
			$Html .= $row['MthEMI']."\t";

			$Html .= ConvBalance($row['ClosBal'])."\t";
			$Html .= $Principal."\t";
			$Html .= $Interest."\t";
			$Html .= ConvBalance($NewBalance)."\t";
			$Html .= "\n";

			$SerialNo++;
			$TotalLoan      += $row['LoanAmt'];
			$TotalClosBal  	+= $row['ClosBal'];
			$TotalMthEMI    += $row['MthEMI'];
			$TotalPrincipal += $Principal;
			$TotalInterest  += $Interest;
			$TotalNewBal    += $NewBalance;
		}
	}
	$Html .= "\t\t\tErrors:".$Errors."\t";
	$Html .= "\t";
	$Html .= $TotalLoan."\t";
	$Html .= $TotalMthEMI."\t";
	$Html .= ConvBalance($TotalClosBal)."\t";
	$Html .= $TotalPrincipal."\t";
	$Html .= $TotalInterest."\t";
	$Html .= ConvBalance($TotalNewBal)."\t";
	$Html .= "\n";
	$Report = $Html;
    header("Content-Disposition: attachment; filename=emi_2_collect.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    print "$Report";	
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
					<div class='table-responsive'>
					<form id='form1' name='form1' class='form-inline' method='post' action='repforloancollection.php'>
						<?php echo $FMList; ?>	
						<label>Calculate Int.Upto</label>
						<input type='date' id='intuptodate' name='intuptodate' required />	
						<label>Location</label>
						<select id='loca' name='loca' class='form-control' required>
							<option value='GIT'>GIT</option>
							<option value='OTH'>Others</option>
						</select>
						<input type='submit' class='btn btn-success' id="Submit" name='Submit' value='Submit'></input>
						<input type='submit' class='btn btn-success' id="Export" name='Export' value='Export'/>						
					</form>
					<?php echo $Report; ?>
					</div>
				</div>
				<div class='panel-footer'>
				</div>
			</div>
		</div>
	</div>
</body>
<script>
	$(document).ready(function() {
    	$("#loca").val('<?php echo $Location;?>');
	});
</script>

