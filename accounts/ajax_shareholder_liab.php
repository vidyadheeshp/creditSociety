<?php
	// Author: Anand V Deshpande
	// Date written : 09.11.2019
	//ajax_shareholder_liab.php
	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();

	$MemberID = $_POST['MemberID'];
	$MemberName = getSingleField($db,"Select Name from shareholders Where MemberID='$MemberID'");
	$Header = "<center><strong>KLS GIT Employees Co-Op Credit Society Ltd.</strong><center>\n";
	$Header .= "ShareHolder Liablitities: <strong>".$MemberName. " MemberID:".$MemberID." </strong>As On ".date("d-m-Y")."<br>";

	$OwnLoansSet = getResultSet($db,"Select LoanID,MemberID,LoanDate,LoanAmt,ClosBal from customers Where MemberID='$MemberID'");
	$Html  = "";
	$Html .= "Own Loan Accounts<br>";
	$Html .= "<table id='table2' class='table table-bordered table-condensed bluecolor'>";
	$Html .= "<tr>
				<th>SNo</th>
				<th>LoanID</th>
				<th>LoanDate</th>
				<th>LoanAmt</th>
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
				<td>".$OwnLoanRow['LoanAmt']."</td>
				<td>".date("d-m-Y",strtotime($OwnLoanRow['LoanDate']))."</td>
				<td>".$Bal."</td>
				</tr>";
		$LoanSerialNo++;
	}
	$Html .= "</table>";

	CreateLog($Html);
	
	$Html .= "Stood Guarantor for following Loan Accounts<br>";
	$Html .= "<table id='table3' class='table table-bordered table-condensed bluecolor'>";
	$Html .= "<tr>
				<th>SNo</th>
				<th>LoanID</th>
				<th>Name</th>
				<th>LoanDate</th>
				<th>LoanAmt</th>
				<th>Balance</th>
				<th>Status</th>
			</tr>";
	$LoanSerialNo =1;	

	$GuarantorSet = getResultSet($db,"Select LoanID,MemberID,ClosBal,LoanAmt,LoanDate,Status from customers Where (G1MemberID='$MemberID' OR G2MemberID='$MemberID') AND ClosBal<0");
	foreach ($GuarantorSet as $LoanRow) {
		$LoanMemberID = $LoanRow['MemberID'];
		$LoanName = getSingleField($db,"Select Name from shareholders Where MemberID='$LoanMemberID'");
		if($LoanRow['ClosBal'] <= 0) {
			$Bal = ($LoanRow['ClosBal']*-1)." Dr";
		} else {
			$Bal = ($LoanRow['ClosBal'])." Cr";
		}
		$Html .= "<tr>
				<td>".$LoanSerialNo."</td>
				<td>".$LoanRow['LoanID']."</td>
				<td>".$LoanName."</td>
				<td>".$LoanRow['LoanAmt']."</td>
				<td>".date("d-m-Y",strtotime($LoanRow['LoanDate']))."</td>
				<td>".$Bal."</td>
				<td>".$LoanRow['Status']."</td>
				</tr>";
		$LoanSerialNo++;
	}
	$Html .= "</table>";
	echo json_encode(array('Header'=>$Header,'Body'=> $Html), JSON_FORCE_OBJECT);	
?>
