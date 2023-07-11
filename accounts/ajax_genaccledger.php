<?php
	// Author: Anand V Deshpande
	// Date written : 09.11.2019
	//ajax_genaccledger.php
	session_start();
	if(!isset($_SESSION['UserType'])) {
		MsgBox("Direct script access prohibited","../index.php",True);
		exit();
	}
	$UserType 	= $_SESSION['UserType'];
	if(strstr('Accounts,Admin,Chairman',$UserType)){

	} else{
		MsgBox("Access for Authorised Users only","",True);
		exit();
	}	
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();

	$FMID 	= $_POST['FMID'];
	$FMName = getSingleField($db,"Select Name   from fm Where FMID='$FMID'");
	$AcType = getSingleField($db,"Select AcType from fm Where FMID='$FMID'");
	$Header = "<center><strong>KLS GIT Employees Co-Op Credit Society Ltd.</strong><center>\n";
	$Header .= "GL : <strong>".$FMName. " AccID:".$FMID." </strong>As On ".date("d-m-Y")."\n";
	$Html  = "";
	$Html .= "<table id='table1' class='table table-bordered table-condensed table-striped bluecolor'>";
	$Html .= "<tr>
				<th>SNo</th>
				<th>Date</th>
				<th>TrnCode</th>
				<th>Particulars</th>
				<th>Debit</th>
				<th>Credit</th>
				<th>Balance</th>
			</tr>";
	$Balance = getSingleField($db,"Select OpenBal from fm Where FMID='$FMID'");

	$SerialNo=1;
	$Html .="<tr>";
	$Html .= "<td>".$SerialNo."</td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td>By Opening Balance</td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	if($Balance<=0) {
		$Html .= "<td align='right'>".abs($Balance)." Dr"."</td>";
	}else{
		$Html .= "<td align='right'>".($Balance)." Cr"."</td>";
	}
	$Html .= "</tr>";
	$SerialNo++;
	$TotDebit = 0;
	$TotCredit=0;
	if($AcType=='Bank'){
		$ResultSet = getResultSet($db,"Select * from ft WHere FMID='$FMID' or FixFMID='$FMID' Order By TrnDate");
	}else{
		$ResultSet = getResultSet($db,"Select * from ft WHere FMID='$FMID' Order By TrnDate");
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

		$Html .="<tr>";
		$Html .= "<td>".$SerialNo."</td>";
		$Html .= "<td nowrap>".date("d-m-Y",strtotime($Row['TrnDate']))."</td>";
		$Html .= "<td>".$Row['TrnCode']."</td>";
		$Html .= "<td>".$Particulars."</td>";
		$Html .= "<td align='right'>".$Debit."</td>";
		$Html .= "<td align='right'>".$Credit."</td>";
		if($Balance<=0) {
			$Html .= "<td align='right'>".abs($Balance)." Dr"."</td>";
		}else{
			$Html .= "<td align='right'>".($Balance)." Cr"."</td>";
		}
		$Html .= "</tr>";
		$TotCredit += $Credit;
		$TotDebit  += $Debit;
	}
	$Html .="<tr>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td align='right'>".$TotDebit."</td>";
	$Html .= "<td align='right'>".$TotCredit."</td>";
	$Html .= "<td></td>";
	$Html .= "</tr>";
	echo json_encode(array('Header'=>$Header,'Body'=> $Html), JSON_FORCE_OBJECT);	
?>
