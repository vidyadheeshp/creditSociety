<?php
	// Author: Anand V Deshpande
	// Date written : 09.11.2019
	//ajax_loanaccountledger.php
	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	require_once("../includes/loans.php");
	$db = connectPDO();
	$LoanRecSet="";
	$LoanID="";
	if(isset($_POST['LoanID']))
		$LoanID   	= $_POST['LoanID'];
	$LoanRecSet = getResultSet($db,"Select * from customers Where LoanID='$LoanID'");
	foreach ($LoanRecSet as $LoanRow) {
		$MemberID 	= $LoanRow['MemberID'];
		$MemberName = getSingleField($db,"Select Name from shareholders Where MemberID='$MemberID'");
		$LoanAmt    = $LoanRow['LoanAmt'];
		$IntRate    = $LoanRow['IntRate'];
		$LoanDate   = date("d-m-Y",strtotime($LoanRow['LoanDate']));
		$Balance    = $LoanRow['OpenBal'];
		$G1MemberID = $LoanRow['G1MemberID'];
		$G1Name     = getSingleField($db,"Select Name from shareholders Where MemberID='$G1MemberID'");
		$G2MemberID = $LoanRow['G2MemberID'];
		$G2Name     = getSingleField($db,"Select Name from shareholders Where MemberID='$G2MemberID'");
		$MthEMI 	= $LoanRow['MthEMI'];
	}
	//$Header = "<center><strong>KLS GIT Employees Co-Op Credit Society Ltd.</strong><center><br>";
	$Header .= "\nLoan Ledger: <strong>".$LoanID . "  ".$MemberName. " MemberID:".$MemberID." </strong>As On ".date("d-m-Y")."<br>";
	$Header .= "\nLoanAmt: ".$LoanAmt." LoanDate: ".$LoanDate. " IntRate: ".$IntRate."%" . " EMI: ".$MthEMI. "<br>";
	$Header .= "\nDate Of Joining:" . getSingleField($db,"Select DOB from shareholders Where MemberID='$MemberID'"). " Date of Retirement : ". getSingleField($db,"Select DOR from shareholders Where MemberID='$MemberID'")."<br>";
	$Header .= "\nGuarantor1: ".$G1MemberID. ":".$G1Name."<br>";
	$Header .= "\nGuarantor2: ".$G2MemberID. ":".$G2Name."<br>";
 
	$Html  = "";
	$Html .= "\n<table id='table1' class='table table-bordered table-condensed bluecolor'>";
	$Html .= "\n<tr>
				\n\t<th>SNo</th>
				\n\t<th>Date</th>
				\n\t<th>TrnCode</th>
				\n\t<th>Particulars</th>
				\n\t<th>Debit</th>
				\n\t<th>Credit</th>
				\n\t<th>Balance</th>
				\n\t<th>Interest</th>
				\n\t<th>Days</th>
			\n</tr>";
	$SerialNo=1;
	$Html .="\n<tr>";
	$Html .= "\n\t<td>".$SerialNo."</td>";
	$Html .= "\n\t<td>01.04.2019</td>";
	$Html .= "\n\t<td></td>";
	$Html .= "\n\t<td>By Op Bal</td>";
	$Html .= "\n\t<td></td>";
	$Html .= "\n\t<td></td>";
	if($Balance<=0) {
		$Html .= "\n\t<td align='right' nowrap>".($Balance*-1)." Dr"."</td>";
	}else{
		$Html .= "\n\t<td align='right' nowrap>".($Balance)." Cr"."</td>";
	}
	$Html .= "\n\t<td></td>";
	$Html .= "\n\t<td></td>";
	$Html .= "\n</tr>";
	$SerialNo++;
	$TotDebit = 0;
	$TotCredit=0;
	$TotInterest=0;
	$ResultSet = getResultSet($db,"Select * from ft Where LoanID='$LoanID' Order By TrnDate");
	foreach($ResultSet as $Row){
		$Balance = $Balance - $Row['Debit'] + $Row['Credit'];
		$Html .="\n<tr>";
		$Html .= "\n\t<td>".$SerialNo."</td>";
		$Html .= "\n\t<td nowrap>".date("d-m-Y",strtotime($Row['TrnDate']))."</td>";
		$Html .= "\n\t<td>".$Row['TrnCode']."</td>";
		$Html .= "\n\t<td>".$Row['Particulars']."</td>";
		$Html .= "\n\t<td align='right'>".$Row['Debit']."</td>";
		$Html .= "\n\t<td align='right'>".$Row['Credit']."</td>";
		if($Balance<=0) {
			$Html .= "\n\t<td align='right' nowrap>".($Balance*-1)." Dr"."</td>";
		}else{
			$Html .= "\n\t<td align='right' nowrap>".($Balance)." Cr"."</td>";
		}
		$Html .= "\n\t<td align='right'>".$Row['Interest']."</td>";
		$Html .= "\n\t<td align='right'>".$Row['Days']."</td>";
		$Html .= "\n</tr>";
		$TotCredit 	+= $Row['Credit'];
		$TotDebit  	+= $Row['Debit'];
		$TotInterest+= $Row['Interest'];
		$SerialNo++;
	}
	$Html .="\n<tr>";
	$Html .= "\n\t<td></td>";
	$Html .= "\n\t<td></td>";
	$Html .= "\n\t<td></td>";
	$Html .= "\n\t<td></td>";
	$Html .= "\n\t<td align='right'>".$TotDebit."</td>";
	$Html .= "\n\t<td align='right'>".$TotCredit."</td>";
	$Html .= "\n\t<td></td>";
	$Html .= "\n\t<td align='right'>".$TotInterest."</td>";
	$Html .= "\n\t<td></td>";
	$Html .= "\n</tr>";
	list($ConvAmt,$ClosBal,$Interest,$Days,$LastRecDate,$MthEMI) = getLoanDets($db,$LoanID);	
	if($Interest>0){
		$Desc1 = "LastRecDate:".$LastRecDate." Days:".$Days. " Interest:".$Interest;
		$Html .="\n<tr>";
		$Html .= "\n\t<td></td>";
		$Html .= "\n\t<td></td>";
		$Html .= "\n\t<td></td>";
		$Html .= "\n\t<td>".$Desc1."</td>";
		$Html .= "\n\t<td></td>";
		$Html .= "\n\t<td></td>";
		$Html .= "\n\t<td></td>";
		$Html .= "\n\t<td></td>";
		$Html .= "\n\t<td></td>";
		$Html .= "\n</tr>";
	}
	echo json_encode(array('Header'=>$Header,'Body'=> $Html), JSON_FORCE_OBJECT);	
?>
