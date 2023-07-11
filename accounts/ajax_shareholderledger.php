<?php
	// Author: Anand V Deshpande
	// Date written : 09.11.2019
	//ajax_shareholderledger.php
	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();

	$MemberID = $_POST['MemberID'];
	$MemberName = getSingleField($db,"Select Name from shareholders Where MemberID='$MemberID'");
	$Header = "<center><strong>KLS GIT Employees Co-Op Credit Society Ltd.</strong><center>\n";
	$Header .= "ShareHolder Ledger: <strong>".$MemberName. " MemberID:".$MemberID." </strong>As On ".date("d-m-Y")."<br>";

	$OwnLoansSet = getResultSet($db,"Select LoanID,MemberID,LoanDate,ClosBal from customers Where MemberID='$MemberID'");
	$Html  = "";
	$Html .= "Own Loan Accounts<br>";
	$Html .= "<table id='table2' class='table table-bordered table-condensed bluecolor'>";
	$Html .= "<tr>
				<th>SNo</th>
				<th>LoanID</th>
				<th>LoanDate</th>
				<th>Balance</th>
			</tr>";
	$LoanSerialNo =1;	

	foreach ($OwnLoansSet as $OwnLoanRow) {
		if($OwnLoanRow['ClosBal'] <= 0) {
			$Bal = ($OwnLoanRow['ClosBal']*-1)." Dr";
		} else {
			$Bal = ($OwnLoanRow['ClosBal'])." Cr";
		}
		$Html .= "<tr>
				<td>".$LoanSerialNo."</td>
				<td>".$OwnLoanRow['LoanID']."</td>
				<td>".date("d-m-Y",strtotime($OwnLoanRow['LoanDate']))."</td>
				<td>".$Bal."</td>
				</tr>";
		$LoanSerialNo++;
	}
	
	$Html .= "</table>";
	CreateLog($Html);
	
	$GuarantorSet = getResultSet($db,"Select LoanID,MemberID,ClosBal from customers Where (G1MemberID='$MemberID' OR G2MemberID='$MemberID') AND ClosBal<0");
	foreach ($GuarantorSet as $LoanRow) {
		$LoanMemberID = $LoanRow['MemberID'];
		$LoanName = getSingleField($db,"Select Name from shareholders Where MemberID='$LoanMemberID'");
		$Header .= "Guarantor for MemberID: ".$LoanMemberID. ":".$LoanName. " Balance:".ConvBalance($LoanRow['ClosBal'])."<br>";
	}
	//$Html  = "";
	$Html .= "<table id='table1' class='table table-bordered table-condensed bluecolor'>";
	$Html .= "<tr>
				<th>SNo</th>
				<th>Date</th>
				<th>TrnCode</th>
				<th>Particulars</th>
				<th>Debit</th>
				<th>Credit</th>
				<th>Balance</th>
			</tr>";
	$Balance = getSingleField($db,"Select OpenBal from shareholders Where MemberID='$MemberID'");

	$SerialNo=1;
	$Html .="<tr>";
	$Html .= "<td>".$SerialNo."</td>";
	$Html .= "<td>01.04.2019</td>";
	$Html .= "<td></td>";
	$Html .= "<td>By Op Bal</td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	if($Balance<=0) {
		$Html .= "<td align='right'>".($Balance*-1)." Dr"."</td>";
	}else{
		$Html .= "<td align='right'>".($Balance)." Cr"."</td>";
	}
	$Html .= "</tr>";
	$SerialNo++;
	$TotDebit = 0;
	$TotCredit=0;
	$ResultSet = getResultSet($db,"Select * from ft Where MemberID='$MemberID' Order By TrnDate");
	foreach($ResultSet as $Row){
		$Balance = $Balance - $Row['Debit'] + $Row['Credit'];
		$Html .="<tr>";
		$Html .= "<td>".$SerialNo."</td>";
		$Html .= "<td nowrap>".date("d-m-Y",strtotime($Row['TrnDate']))."</td>";
		$Html .= "<td>".$Row['TrnCode']."</td>";
		$Html .= "<td>".$Row['Particulars']."</td>";
		$Html .= "<td align='right'>".$Row['Debit']."</td>";
		$Html .= "<td align='right'>".$Row['Credit']."</td>";
		if($Balance<=0) {
			$Html .= "<td align='right'>".($Balance*-1)." Dr"."</td>";
		}else{
			$Html .= "<td align='right'>".($Balance)." Cr"."</td>";
		}
		$Html .= "</tr>";
		$TotCredit += $Row['Credit'];
		$TotDebit  += $Row['Debit'];
		$SerialNo++;
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
 