<?php
// Author : Anand V Deshpande
// common functions 
// functions.php
// modified on 16.12.2019
function ExeQuery($db, $SqlQuery)
{
	$stmt = $db->prepare($SqlQuery);
	$stmt->execute();
}
function MsgBox($Msg, $DisplayPage = "", $CreateLog = true)
{
	//echo getcwd(); 
	if (strlen($DisplayPage) == 0) {
		echo "<script type=''javascript'>alert('$Msg');</script>";
	} else {
		echo "<script type=''javascript'>alert('$Msg');
			window.location.href='$DisplayPage';
			</script>";
	}
	if ($CreateLog == True) {
		CreateLog($Msg);
	}
}
function CreateLog($Msg)
{
	//echo "UserType:".$_SESSION['UserType'];
	$file = fopen($_SESSION['mainpath'] . "/logs/logfile.txt", "a");
	$Date1 = date("d-m-Y H:i:s");

	fwrite($file, $Date1 . ":" . $Msg . "\n");
	fclose($file);
}
// added 1 to the days for interest calc
// on 16.12.2019
function getDaysDiff($p_fromdate, $p_todate)
{
	// date2 is more than date1
	$date1 	= strtotime($p_fromdate);
	$date2 	= strtotime($p_todate);
	$diff 	= $date2 - $date1;
	$diff 	= round($diff / 86400);
	return $diff;
}

function ConvBalance($Balance)
{
	$Balance = floatval($Balance);
	if ($Balance < 0) {
		$Conv = ($Balance * -1) . " Dr";
	} else {
		$Conv = $Balance . " Cr";
	}
	return $Conv;
}
function ConvBalanceFormat($Balance)
{
	$Bal = floatval($Balance);
	if ($Bal < 0) {
		$Conv = ($Bal * -1);
	} else {
		$Conv = $Bal;
	}
	$Conv = moneyFormatIndia($Conv);
	if ($Balance < 0) {
		$Conv .= " Dr";
	} else {
		$Conv .= " Cr";
	}
	return $Conv;
}

function genFinYear($date1)
{
	$date1 = strtotime($date1);
	$Month = date("m", $date1);
	$Year  = date("y", $date1);
	if ($Month >= 4 and $Month <= 12) {
		$FinYear = sprintf("%02d", $Year);
		$FinYear = $FinYear . sprintf("%02d", $Year + 1);
		return $FinYear;
	} else {
		$FinYear = sprintf("%02d", $Year - 1);
		$FinYear = $FinYear . sprintf("%02d", $Year);
		return $FinYear;
	}
}
function genMaxTrnNo($db, $TrnCode, $FinYear, $Table)
{
	$MaxTrnNo = getSingleField($db, "Select Max(TrnNo) from $Table Where FinYear='$FinYear' AND TrnCode='$TrnCode'");
	$MaxTrnNo = $MaxTrnNo + 1;
	return $MaxTrnNo;
}
function anchor($Path, $Option)
{
	//$anchor = "<a href='$Path' data-toggle='tooltip' data-placement='top' class='btn btn-default'>$Option</a>"; 
	$anchor = "<a href='$Path' data-toggle='tooltip' data-placement='top'>$Option</a>";
	return $anchor;
}
// FieldName can contain an array as []
function input_readonly($FieldName, $FieldVal, $OtherAttr)
{

	$InputElement = "<input id='$FieldName' name='$FieldName' value='$FieldVal' class='form-control' $OtherAttr /> ";
	return $InputElement;
}
function genShareHoldersSelect($db, $Where = "")
{
	$ShareHoldersList = "";
	$sql = "Select MemberID,Name,ClosBal from shareholders $Where Order By Name";
	$result = getResultSet($db, $sql);
	$ShareHoldersList .= "<select id='memberid' name='memberid' class='form-control' required>";
	$ShareHoldersList .= "<option value=''>Select ShareHolder</option>";
	foreach ($result as $row) {
		$ShareHoldersList .= "<option value='" . $row['MemberID'] . "'>" . $row['Name'] . " (" . $row['MemberID'] . ")" . "(" . $row['ClosBal'] . ")" . "</option>";
	}
	$ShareHoldersList .= "</select>";
	return $ShareHoldersList;
}
function genShareHoldersSelect2($db, $Where = "", $SelectID)
{
	$ShareHoldersList = "";
	$sql = "Select MemberID,Name,ClosBal from shareholders $Where Order By Name";
	$result = getResultSet($db, $sql);
	$ShareHoldersList .= "<select id='$SelectID' name='$SelectID' class='form-control' required>";
	$ShareHoldersList .= "<option value=''>Select ShareHolder</option>";
	foreach ($result as $row) {
		$ShareHoldersList .= "<option value='" . $row['MemberID'] . "'>" . $row['Name'] . " (" . $row['MemberID'] . ")" . "(" . $row['ClosBal'] . ")" . "</option>";
	}
	$ShareHoldersList .= "</select>";
	return $ShareHoldersList;
}
function genShareHoldersSelect3($db, $Where = "", $SelectID)
{
	$ShareHoldersList = "";
	$sql = "Select MemberID,Name,ClosBal from shareholders $Where Order By Name";
	$result = getResultSet($db, $sql);
	$ShareHoldersList .= "<select id='$SelectID' name='$SelectID' class='form-control' required>";
	$ShareHoldersList .= "<option value=''>Select ShareHolder</option>";
	foreach ($result as $row) {
		$ShareHoldersList .= "<option value='" . $row['MemberID'] . "'>" . $row['Name'] . " (" . $row['MemberID'] . ")" . "</option>";
	}
	$ShareHoldersList .= "</select>";
	return $ShareHoldersList;
}
function setShareHoldersSelect3($db, $Where = "", $SelectID, $selectedID)
{
	$ShareHoldersList = "";
	$sql = "Select MemberID,Name,ClosBal from shareholders $Where Order By Name";

	$result = getResultSet($db, $sql);
	$ShareHoldersList .= "<select id='$SelectID' name='$SelectID' class='form-control' required>";
	$ShareHoldersList .= "<option value=''>Select ShareHolder</option>";

	foreach ($result as $row) {
		if ($row['MemberID'] == $selectedID) {

			$ShareHoldersList .= "<option selected value='" . $row['MemberID'] . "'>" . $row['Name'] . " (" . $row['MemberID'] . ")" . "</option>";
		} else {
			$ShareHoldersList .= "<option value='" . $row['MemberID'] . "'>" . $row['Name'] . " (" . $row['MemberID'] . ")" . "</option>";
		}
	}
	$ShareHoldersList .= "</select>";
	return $ShareHoldersList;
}
function genShareHoldersDataList($db, $Where = "")
{
	$ShareHoldersList = "";
	$sql = "Select MemberID,Name,ClosBal from shareholders $Where Order By Name";
	$result = getResultSet($db, $sql);
	$ShareHoldersList .= "<input type='text' id='memberid' name='memberid' class='form-control' required list='datalist_shareholders'>";
	$ShareHoldersList .= "<datalist id='datalist_shareholders'>";
	$ShareHoldersList .= "<option value=''>Select ShareHolder</option>";
	foreach ($result as $row) {
		$ShareHoldersList .= "<option value='" . $row['MemberID'] . "'>" . $row['Name'] . " (" . $row['MemberID'] . ")" . "(" . $row['ClosBal'] . ")" . "</option>";
	}
	$ShareHoldersList .= "</datalist>";
	return $ShareHoldersList;
}
function genCustSelect($db, $FMID)
{
	$CustList = "";
	$sql = "Select B.LoanID,B.MemberID,A.Name,B.ClosBal from customers B,shareholders A 
					Where B.FMID='$FMID' AND B.MemberID=A.MemberID Order By A.Name";
	//CreateLog("Inside genCustSelect function : $sql");
	$result = getResultSet($db, $sql);
	$CustList .= "<select id='loanid' name='loanid' class='form-control' required>";
	$CustList .= "<option value=''>Select Loan Account</option>";
	foreach ($result as $row) {
		$Balance = ConvBalance($row['ClosBal']);

		$CustList .= "<option value='" . $row['LoanID'] . "'>" . $row['Name'] . " (" . $row['LoanID'] . ")( " . $Balance . ")</option>";
	}
	$CustList .= "</select>";
	return $CustList;
}
function genCustDataList($db, $FMID)
{
	$CustList = "";
	$sql = "Select B.LoanID,B.MemberID,A.Name from customers B,shareholders A 
					Where B.FMID='$FMID' AND B.MemberID=A.MemberID Order By A.Name";
	CreateLog("Inside genCustSelect function : $sql");
	$result = getResultSet($db, $sql);
	$CustList .= "<input id='loanid' name='loanid' class='form-control' list='datalist_customers' required>";
	$CustList .= "<datalist id='datalist_customers'>";
	$CustList .= "<option value=''>Select Loan Account</option>";
	foreach ($result as $row) {
		$CustList .= "<option value='" . $row['LoanID'] . "'>" . $row['Name'] . " (" . $row['LoanID'] . ")" . "</option>";
	}
	$CustList .= "</datalist>";
	return $CustList;
}

function genDesignationSelect($db)
{
	$DesignationList = "";
	$sql = "Select * from designation Order By Designation";
	$result = getResultSet($db, $sql);
	$DesignationList .= "<option value=''>Select Designation</option>";
	foreach ($result as $row) {
		$DesignationList .= "<option value=" . $row['DesignID'] . ">" . $row['Designation'] . "</option>";
	}
	return $DesignationList;
}
function genSelectPlBs($db)
{
	$List = "";
	$sql = "Select PlBsCode,Name from plbs Where MainGroup='No' Order By PlBsCode";
	$result = getResultSet($db, $sql);
	$List  = "<select id='plbscode' name='plbscode' class='form-control' required>";
	$List .= "<option value=''>Select</option>";
	foreach ($result as $row) {
		$List .= "<option value='" . $row['PlBsCode'] . "'>" . $row['Name'] . "</option>";
	}
	$List .= "</select>";
	return $List;
}
function genSelectFM($db, $SelectID, $Where = 1, $Additional)
{
	$FMList = "";
	$sql = "Select FMID,Name from fm Where $Where Order By Name";
	$result = getResultSet($db, $sql);
	$FMList  = "<select id='$SelectID' name='$SelectID' class='form-control drop' $Additional >";
	$FMList .= "<option value=''>Select Account</option>";
	foreach ($result as $row) {
		$FMList .= "<option value=" . $row['FMID'] . ">" . $row['Name'] . "</option>";
	}
	$FMList .= "</select>";
	return $FMList;
}
function setSelectFM($db, $SelectID, $Where = 1, $Additional, $selectedID)
{
	$FMList = "";
	$sql = "Select FMID,Name from fm Where $Where Order By Name";
	$result = getResultSet($db, $sql);
	$FMList  = "<select id='$SelectID' name='$SelectID' class='drop' $Additional >";
	$FMList .= "<option value=''>Select Account</option>";

	foreach ($result as $row) {
		if ($row['FMID'] == $selectedID) {
			$FMList .= "<option selected value=" . $row['FMID'] . ">" . $row['Name'] . "</option>";
		} else {
			$FMList .= "<option value=" . $row['FMID'] . ">" . $row['Name'] . "</option>";
		}
	}
	$FMList .= "</select>";
	return $FMList;
}
function updateFM($db, $Where, $Debit, $Credit)
{
	//echo "Where ".$Where;
	$sql  = "UPDATE fm Set Debits=Debits+$Debit,Credits=Credits+$Credit,ClosBal=OpenBal-Debits+Credits Where $Where";
	CreateLog($sql);
	$stmt = $db->prepare($sql);
	$stmt->execute();
}
function updateShareHolders($db, $Where, $Debit, $Credit)
{
	$sql  = "UPDATE shareholders Set Debits=Debits+$Debit,Credits=Credits+$Credit,ClosBal=OpenBal-Debits+Credits Where $Where";
	CreateLog($sql);
	$stmt = $db->prepare($sql);
	$stmt->execute();
}
function updateCustomers($db, $Where, $Debit, $Credit)
{
	$sql  = "UPDATE customers Set Debits=Debits+$Debit,Credits=Credits+$Credit,ClosBal=OpenBal-Debits+Credits Where $Where";
	CreateLog($sql);
	$stmt = $db->prepare($sql);
	$stmt->execute();
}
function moneyFormatIndia($num)
{

	return number_format($num, 2, '.', '');
}

function moneyFormatIndia_old($num)
{
	$explrestunits = "";
	if (strlen($num) > 3) {
		$lastthree = substr($num, strlen($num) - 3, strlen($num));
		$restunits = substr($num, 0, strlen($num) - 3); // extracts the last three digits
		$restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
		$expunit = str_split($restunits, 2);
		for ($i = 0; $i < sizeof($expunit); $i++) {
			// creates each of the 2's group and adds a comma to the end
			if ($i == 0) {
				$explrestunits .= (int)$expunit[$i] . ","; // if is first value , convert into integer
			} else {
				$explrestunits .= $expunit[$i] . ",";
			}
		}
		$thecash = $explrestunits . $lastthree;
	} else {
		$thecash = $num;
	}
	if (substr($thecash, 0, 2) == "0,") {
		$thecash = substr($thecash, 2, 20);
	}
	return $thecash; // writes the final format where $currency is the currency symbol.
}
function genDashboardMember($db)
{
	$LoanID = "";
	$Html = "";
	$Html .= "\n<table class='dashboardtable'>";
	$Html .= "\n<thead>";
	$Html .= "\n<tr>";
	$Html .= "\n<th class='text-left'>Particulars</th>";
	$Html .= "\n<th class='text-right'>Mth</th>";
	$Html .= "\n<th class='text-left'>ID</th>";
	$Html .= "\n<th class='text-right'>Amount</th>";
	$Html .= "\n</tr>";
	$Html .= "\n</thead>";

	$Html .= "\n<tbody>";
	$MemberID = "UnKnown";
	foreach ($_SESSION['UserRow'] as $row) {
		$ClosBal  = $row['ClosBal'];
		$MemberID = trim($row['MemberID']);
		$MthContr = $row['MthContr'];
	}
	$AHref = "<a href='#' onclick=\"javascript:showshareledger('$MemberID')\">";
	$Html .= "\n<tr><td>Share Details</td>";
	$Html .= "\n<td align='right'>" . $MthContr . "</td>";
	$Html .= "\n<td align='left'>" . $AHref . $MemberID . "</a></td>";
	$Html .= "\n<td align='right'>" . moneyFormatIndia($ClosBal) . "</td></tr>";
	$ResultSet = getResultSet($db, "Select FMID,LoanID,ClosBal,LoanAmt,LoanDate,IntRate,MthEMI from customers Where MemberID='$MemberID' And Status='Active'");
	foreach ($ResultSet as $row) {
		$FMID 	= $row['FMID'];
		$LoanID = $row['LoanID'];
		$MthEMI = $row['MthEMI'];
		$FMName = getSingleField($db, "Select Name from fm Where FMID='$FMID'");
		$AHref 	= "<a href='#' onclick=javascript:showloanledger('$LoanID')>";
		$Html 	.= "\n<tr>";
		$Html 	.= "\n<td>" . $FMName . "</td>";
		$Html	 .= "\n<td align='center'>" . $MthEMI . "</td>";
		$Html 	.= "\n<td align='left'>" . $AHref . $LoanID . "</a></td>";
		$Html 	.= "\n<td align='right'>" . moneyFormatIndia($row['ClosBal'] * -1) . "</td>";
		$Html 	.= "\n</tr>";
	}
	$Html .= "\n<tr>";
	$Html .= "\n</tr>";

	$Html .= "\n<tr class='info'>";
	$Html .= "\n<td>Share Variations</td>";
	$Html .= "\n<td></td>";
	$Html .= "\n<td></td>";
	$Html .= "\n<td></td>";
	$Html .= "\n</tr>";
	$ResultSet = getResultSet($db, "Select MemberID,PrevContr,NewContr,Status,Requested from sharevariations Where MemberID='$MemberID'");
	foreach ($ResultSet as $row) {
		$Class  = "";
		if ($row['Status'] == 'Applied') {
			$Class = "class='danger'";
		}
		$Html .= "\n<tr $Class>";
		$Html .= "\n<td>" . "PrevContr:" . $row['PrevContr'] . " NewContr: " . $row['NewContr'] . "</td>";
		$Html .= "\n<td align='center'>" . date("d-m-Y H:i", strtotime($row['Requested'])) . "</td>";
		$Html .= "\n<td align='center'>" . $row['Status'] . "</a></td>";
		$Html .= "\n<td align='right'></td>";
		$Html .= "\n</tr>";
	}


	$Html .= "\n<tr class='info'>";
	$Html .= "\n<td>Loan Applications</td>";
	$Html .= "\n<td></td>";
	$Html .= "\n<td></td>";
	$Html .= "\n<td></td>";
	$Html .= "\n</tr>";
	$ResultSet = getResultSet($db, "Select FMID,LoanID,LoanAmt,IntRate,MthEMI,Status,ApplOn from loanappl Where MemberID='$MemberID'");
	foreach ($ResultSet as $row) {
		$FMID 	= $row['FMID'];
		$MthEMI = $row['MthEMI'];
		$LoanID = $row['LoanID'];
		$FMName = getSingleField($db, "Select Name from fm Where FMID='$FMID'");
		$Class  = "";
		if ($row['Status'] == 'New') {
			$Class = "class='danger'";
		}

		$AHref1 = "<a href='#' onclick=\"javascript:showLoanAppln('$LoanID')\"> ";
		$Html .= "\n<tr $Class>";
		$Html .= "\n<td>" . $FMName . "<br>" . "IntRt:" . $row['IntRate'] . " ApplOn " . date("d-m-Y H:i:s", strtotime($row['ApplOn'])) . "</td>";
		$Html .= "\n<td align='center'>" . $MthEMI . "</td>";
		$Html .= "\n<td align='center'>" . $AHref1 . $row['LoanID'] . "</a></td>";
		$Html .= "\n<td align='right'>" . moneyFormatIndia($row['LoanAmt']) . "</td>";
		$Html .= "\n</tr>";
	}
	// standing guarantors for list
	$ResultSet = getResultSet($db, "Select LoanID,MemberID,ClosBal,Status,MthEMI from customers  Where (G1MemberID='$MemberID' or G2MemberID='$MemberID') AND ClosBal<0 ");
	if (count($ResultSet) > 0) {
		$Html .= "\n<tr class='info'>";
		$Html .= "\n<td colspan=4>Standing Guarantor for</td>";
		//$Html .= "\n<td></td>";
		//$Html .= "\n<td></td>";
		//$Html .= "\n<td></td>";
		$Html .= "\n</tr>";

		foreach ($ResultSet as $row) {
			$Str1 = "Select Name from shareholders Where MemberID='" . $row['MemberID'] . "'";
			$LoanAccName = getSingleField($db, $Str1);
			$Html .= "\n<tr class='danger'>";
			$Html .= "\n<td>" . $LoanAccName . "</td>";
			$Html .= "\n<td align='right'>" . $row['MthEMI'] . "</td>";
			$Html .= "\n<td align='left'>" . $row['LoanID'] . "</a></td>";
			$Html .= "\n<td align='right'>" . moneyFormatIndia($row['ClosBal'] * -1) . "</td>";
			$Html .= "\n</tr>";
		}
	}

	$ResultSet = getResultSet($db, "select LoanID,MemberID,LoanAmt,MthEMI,Months,ApplOn from loanappl where
		 (G1MemberID='$MemberID' and G1Approved='') or (G2MemberID='$MemberID' and G2Approved='') and Status='New'");
	if (count($ResultSet) > 0) {


		$Html .= "\n<tr class='info'>";
		$Html .= "\n<td colspan=4>Received request for loan Guarantee</td>";
		$Html .= "\n</tr>";
		$Html .= "\n<tr>
				<td><b>Name</b></td>
				<td><b>Installments<br>& yrs</b></td>
				<td><b>Date</b></td>
				<td><b>Amt</b></td>
			</tr>";
		foreach ($ResultSet as $row) {
			$GLoanID = $row['LoanID'];
			$AHref2 = "<a href='#' onclick=\"javascript:aproveGarn('$GLoanID')\"> ";

			$Str1 = "Select Name from shareholders Where MemberID='" . $row['MemberID'] . "'";
			$LoanAccName = getSingleField($db, $Str1);
			$Html .= "\n<tr class='danger'>";
			$Html .= "\n<td>" . $AHref2 . $LoanAccName . "</a></td>";
			$Html .= "\n<td align='right'>" . number_format($row['MthEMI']) . "/- " . ($row['Months'] / 12) . "yrs</td>";
			$Html .= "\n<td align='left'>" . date("d-m-y", strtotime($row['ApplOn'])) . "</a></td>";
			$Html .= "\n<td align='right'>" . number_format($row['LoanAmt']) . ".00/-</td>";
			$Html .= "\n</tr>";
		}
	}
	$Html .= "\n</tbody></table>";
	return $Html;
}
function getOpenBalGenAcc($db, $FMID, $BalUptoDate)
{
	$Sql = "Select OpenBal,
			(Select SUM(Debit)  from ft Where ft.FMID =  '$FMID' AND ft.TrnDate < '$BalUptoDate' ) as Debit1,
			(Select SUM(Credit) from ft Where ft.FMID =  '$FMID' AND ft.TrnDate < '$BalUptoDate' ) as Credit1,
			(Select SUM(Debit)  from ft Where ft.FixFMID='$FMID' AND ft.TrnDate < '$BalUptoDate' ) as Credit2,
			(Select SUM(Credit) from ft Where ft.FixFMID='$FMID' AND ft.TrnDate < '$BalUptoDate' ) as Debit2
		From fm 
		Where fm.FMID = '$FMID'";
	$ResultRow = getResultSet($db, $Sql);
	foreach ($ResultRow as $Row) {
		$OpenBal = $Row['OpenBal'];
		$Debit1  = !is_null($Row['Debit1']) ? $Row['Debit1'] : 0;
		$Debit2  = !is_null($Row['Debit2']) ? $Row['Debit2'] : 0;
		$Debit   = $Debit1 + $Debit2;
		$Credit1  = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0;
		$Credit2  = !is_null($Row['Credit2']) ? $Row['Credit2'] : 0;
		$Credit   = $Credit1 + $Credit2;
		$ClosBal = $OpenBal - $Debit + $Credit;
		return $ClosBal;
	}
}
function getClosBalGenAcc($db, $FMID, $BalUptoDate)
{
	$Sql = "Select OpenBal,
			(Select SUM(Debit)  from ft Where ft.FMID =  '$FMID' AND ft.TrnDate <= '$BalUptoDate' ) as Debit1,
			(Select SUM(Credit) from ft Where ft.FMID =  '$FMID' AND ft.TrnDate <= '$BalUptoDate' ) as Credit1,
			(Select SUM(Debit)  from ft Where ft.FixFMID='$FMID' AND ft.TrnDate <= '$BalUptoDate' ) as Credit2,
			(Select SUM(Credit) from ft Where ft.FixFMID='$FMID' AND ft.TrnDate <= '$BalUptoDate' ) as Debit2
		From fm 
		Where fm.FMID = '$FMID'";
	$ResultRow = getResultSet($db, $Sql);
	foreach ($ResultRow as $Row) {
		$OpenBal = $Row['OpenBal'];
		$Debit1  = !is_null($Row['Debit1']) ? $Row['Debit1'] : 0;
		$Debit2  = !is_null($Row['Debit2']) ? $Row['Debit2'] : 0;
		$Debit   = $Debit1 + $Debit2;

		$Credit1  = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0;
		$Credit2  = !is_null($Row['Credit2']) ? $Row['Credit2'] : 0;
		$Credit   = $Credit1 + $Credit2;
		$ClosBal = $OpenBal - $Debit + $Credit;
		return $ClosBal;
	}
}

function genIncExpBS($db, $FromDate, $ToDate, $RepFromDate, $RepToDate)
{

	$TotDebit   = 0;
	$TotCredit  = 0;
	$Profit = 0;
	$Loss 	= 0;

	//$Array = array();
	// Income
	$Sql = "Select A.PlBsCode,A.FMID,A.Name,
			(Select SUM(Debit)  from ft Where A.FMID=ft.FMID    AND (ft.TrnDate Between '$FromDate' and '$ToDate')) as Debit1,
			(Select SUM(Credit) from ft Where A.FMID=ft.FMID    AND (ft.TrnDate Between '$FromDate' and '$ToDate')) as Credit1
		From fm A 
		Where A.PlBsCode like 'I%' 
		Group By A.PlBsCode,A.FMID,A.Name 
		Order By A.PlBsCode,A.Name ";
	$IncomeRow = getResultSet($db, $Sql);
	// Expenditure
	$Sql = "Select A.PlBsCode,A.FMID,A.Name,
			(Select SUM(Debit)  from ft Where A.FMID=ft.FMID    AND (ft.TrnDate Between '$FromDate' and '$ToDate')) as Debit1,
			(Select SUM(Credit) from ft Where A.FMID=ft.FMID    AND (ft.TrnDate Between '$FromDate' and '$ToDate')) as Credit1
		From fm A 
		Where A.PlBsCode like 'E%' 
		Group By A.PlBsCode,A.FMID,A.Name 
		Order By A.PlBsCode,A.Name ";
	$ExpensesRow = getResultSet($db, $Sql);



	// Assets
	$Sql = "Select A.PlBsCode,A.FMID,A.Name 
		From fm A 
		Where A.PlBsCode like 'A%' 
		Group By A.PlBsCode,A.FMID,A.Name 
		Order By A.PlBsCode,A.Name ";
	$AssetsRow = getResultSet($db, $Sql);
	// Liabilities
	$Sql = "Select A.PlBsCode,A.FMID,A.Name
		From fm A 
		Where A.PlBsCode like 'L%' 
		Group By A.PlBsCode,A.FMID,A.Name 
		Order By A.PlBsCode,A.Name ";
	$LiabilitesRow = getResultSet($db, $Sql);

	// First Find Profit / Loss 
	$TotInc 	= 0;
	$TotExp  	= 0;
	$TotAssets 	= 0;
	$TotLiab 	= 0;


	foreach ($IncomeRow as $Row) {
		$Credit = 0;
		$Debit 	= 0;
		$Credit  = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0;
		$Debit = !is_null($Row['Debit1']) ? $Row['Debit1'] : 0;

		$TotInc  = $TotInc + $Credit - $Debit;
	}

	foreach ($ExpensesRow as $Row) {
		$Credit  = 0;
		$Debit   = 0;
		$Credit  = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0;
		$Debit   = !is_null($Row['Debit1']) ? $Row['Debit1'] : 0;

		$TotExp  = $TotExp + $Debit - $Credit;
	}
	if ($TotInc > $TotExp) {
		$Profit = $TotInc - $TotExp;
	} else {
		$Loss 	= $TotExp - $TotInc;
	}

	// First Print Income
	$Html = "";
	// create report
	$Header = "<center><strong>KLS GIT Employees Co-Op Credit Society Ltd.</strong><center>\n";
	$Header .= "Income & Expenditure From <strong>" . $RepFromDate . " To " . $RepToDate  . " </strong>";
	$Html  = "";
	$Html .= "<table id='table1' class='table table-bordered table-condensed table-striped bluecolor'>";
	$Html .= "<tr style='background-color:yellow;'>
				<th align='center'>Particulars</th>
				<th align='right'>Amount</th>
			</tr>";
	$Html .= "<tr>";
	$Html .= "<td><strong>Income</strong></td>";
	$Html .= "<td></td>";

	$SerialNo 	= 1;
	$TotCredit 	= 0;
	foreach ($IncomeRow as $Row) {
		$Credit  = 0;
		$Credit   = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0;

		$Debit  = !is_null($Row['Debit1']) ? $Row['Debit1'] : 0;
		// number_format($number, 2, '.', '');
		if ($Credit - $Debit > 0) {
			$Html .= "<tr>";
			$Html .= "<td>" . $Row['Name'] . "</td>";
			$Html .= "<td align='right'>" . number_format($Credit - $Debit, 2, '.', '') . "</td>";
			$TotCredit += $Credit - $Debit;
			$Html .= "</tr>";
			$SerialNo++;
		}
	}
	if ($Loss > 0) {
		$Html .= "<tr>";
		$Html .= "<td>Net Loss</td>";
		$Html .= "<td align='right'>" . number_format($Loss, 2, '.', '') . "</td>";
		$Html .= "</tr>";
	}
	$Html .= "<tr>";
	$Html .= "<td>Total</td>";
	$Html .= "<td align='right'><b>" . number_format($TotCredit, 2, '.', '') . "</b></td>";
	$Html .= "</tr>";

	$Html .= "<tr>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "</tr>";

	$Html .= "<tr>";
	$Html .= "<td><strong>Expenditure</strong></td>";
	$Html .= "<td></td>";
	$TotDebit = 0;
	foreach ($ExpensesRow as $Row) {
		$Debit 	 = 0;
		$Credit  = 0;
		$Credit  = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0;
		$Debit  = !is_null($Row['Debit1']) ? $Row['Debit1'] : 0;
		// number_format($number, 2, '.', '');
		if ($Debit - $Credit > 0) {
			$Html .= "<tr>";
			$Html .= "<td>" . $Row['Name'] . "</td>";
			$Html .= "<td align='right'>" . number_format($Debit - $Credit, 2, '.', '') . "</td>";
			$TotDebit += $Debit - $Credit;
			$Html .= "</tr>";
			$SerialNo++;
		}
	}
	if ($Profit > 0) {
		$Html .= "<tr>";
		$Html .= "<td>Net Profit</td>";
		$Html .= "<td align='right'>" . number_format($Profit, 2, '.', '') . "</td>";
		$Html .= "</tr>";
	}
	$Html .= "<tr>";
	$Html .= "<td>Total</td>";
	$Html .= "<td align='right'><b>" . number_format($TotDebit + $Profit, 2, '.', '') . "</b></td>";
	$Html .= "</tr>";
	$Html .= "</table>";

	//////////////////////////////////////////////////////////////////////////
	// Now print Balance Sheet
	$TotDebit 	= 0;
	$TotCredit 	= 0;
	$Header = "<center><strong>KLS GIT Employees Co-Op Credit Society Ltd.</strong><center>\n";
	$Header .= "Balance Sheet As at <strong>" . $RepToDate  . " </strong>";
	$Html .= "";
	$Html .= "<table id='table2' class='table table-bordered table-condensed table-striped bluecolor'>";
	$Html .= "<tr style='background-color:yellow;'>
				<th align='center'>Particulars</th>
				<th align='right'>Amount</th>
			</tr>";
	$Html .= "<tr>";
	$Html .= "<td><strong>Liabilities</strong></td>";
	$Html .= "<td></td>";

	// process Liabilities
	$TotCredit = 0;
	foreach ($LiabilitesRow as $Row) {
		$Balance = getClosBalGenAcc($db, $Row['FMID'], $ToDate);
		// number_format($number, 2, '.', '');
		if ($Balance > 0) {
			$Html .= "<tr>";
			$Html .= "<td>" . $Row['Name'] . "</td>";
			$Html .= "<td align='right'>" . number_format($Balance, 2, '.', '') . "</td>";
			$TotCredit += $Balance;
			$Html .= "</tr>";
		}
	}
	if ($Profit > 0) {
		$Html .= "<tr>";
		$Html .= "<td>Net Profit</td>";
		$Html .= "<td align='right'>" . number_format($Profit, 2, '.', '') . "</td>";
		$Html .= "</tr>";
	}

	$Html .= "<tr>";
	$Html .= "<td>Total</td>";
	$Html .= "<td align='right'><b>" . number_format($TotCredit + $Profit, 2, '.', '') . "</b></td>";
	$Html .= "</tr>";



	$Html .= "<tr>";
	$Html .= "<td><strong>Assets</strong></td>";
	$Html .= "<td></td>";

	// process Assets
	$TotDebit = 0;
	foreach ($AssetsRow as $Row) {
		$Balance = getClosBalGenAcc($db, $Row['FMID'], $ToDate);
		// number_format($number, 2, '.', '');
		if ($Balance != 0) {
			$Balance = $Balance * -1;

			$Html .= "<tr>";
			$Html .= "<td>" . $Row['Name'] . "</td>";
			$Html .= "<td align='right'>" . number_format($Balance, 2, '.', '') . "</td>";
			$TotDebit += $Balance;
			$Html .= "</tr>";
		}
	}
	if ($Loss > 0) {
		$Html .= "<tr>";
		$Html .= "<td>Net Profit</td>";
		$Html .= "<td align='right'>" . number_format($Loss, 2, '.', '') . "</td>";
		$Html .= "</tr>";
	}
	$Html .= "<tr>";
	$Html .= "<td>Total</td>";
	$Html .= "<td align='right'><b>" . number_format($TotDebit + $Loss, 2, '.', '') . "</b></td>";
	$Html .= "</tr>";
	$Html .= "</table>";
	return $Html;
}
function genIncExpBS_Export2Excel($db, $FromDate, $ToDate, $RepFromDate, $RepToDate)
{

	$TotDebit   = 0;
	$TotCredit  = 0;
	$Profit = 0;
	$Loss 	= 0;

	//$Array = array();
	// Income
	$Sql = "Select A.PlBsCode,A.FMID,A.Name,
			(Select SUM(Debit)  from ft Where A.FMID=ft.FMID    AND (ft.TrnDate Between '$FromDate' and '$ToDate')) as Debit1,
			(Select SUM(Credit) from ft Where A.FMID=ft.FMID    AND (ft.TrnDate Between '$FromDate' and '$ToDate')) as Credit1
		From fm A 
		Where A.PlBsCode like 'I%' 
		Group By A.PlBsCode,A.FMID,A.Name 
		Order By A.PlBsCode,A.Name ";
	$IncomeRow = getResultSet($db, $Sql);
	// Expenditure
	$Sql = "Select A.PlBsCode,A.FMID,A.Name,
			(Select SUM(Debit)  from ft Where A.FMID=ft.FMID    AND (ft.TrnDate Between '$FromDate' and '$ToDate')) as Debit1,
			(Select SUM(Credit) from ft Where A.FMID=ft.FMID    AND (ft.TrnDate Between '$FromDate' and '$ToDate')) as Credit1
		From fm A 
		Where A.PlBsCode like 'E%' 
		Group By A.PlBsCode,A.FMID,A.Name 
		Order By A.PlBsCode,A.Name ";
	$ExpensesRow = getResultSet($db, $Sql);



	// Assets
	$Sql = "Select A.PlBsCode,A.FMID,A.Name 
		From fm A 
		Where A.PlBsCode like 'A%' 
		Group By A.PlBsCode,A.FMID,A.Name 
		Order By A.PlBsCode,A.Name ";
	$AssetsRow = getResultSet($db, $Sql);
	// Liabilities
	$Sql = "Select A.PlBsCode,A.FMID,A.Name
		From fm A 
		Where A.PlBsCode like 'L%' 
		Group By A.PlBsCode,A.FMID,A.Name 
		Order By A.PlBsCode,A.Name ";
	$LiabilitesRow = getResultSet($db, $Sql);

	// First Find Profit / Loss 
	$TotInc 	= 0;
	$TotExp  	= 0;
	$TotAssets 	= 0;
	$TotLiab 	= 0;


	foreach ($IncomeRow as $Row) {
		$Credit = 0;
		$Debit 	= 0;
		$Credit  = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0;
		$Debit = !is_null($Row['Debit1']) ? $Row['Debit1'] : 0;

		$TotInc  = $TotInc + $Credit - $Debit;
	}

	foreach ($ExpensesRow as $Row) {
		$Credit  = 0;
		$Debit   = 0;
		$Credit  = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0;
		$Debit   = !is_null($Row['Debit1']) ? $Row['Debit1'] : 0;

		$TotExp  = $TotExp + $Debit - $Credit;
	}
	if ($TotInc > $TotExp) {
		$Profit = $TotInc - $TotExp;
	} else {
		$Loss 	= $TotExp - $TotInc;
	}

	// First Print Income
	$Html = "";
	// create report
	$Html =  "KLS GIT Employees Co-Op Credit Society Ltd.\n";
	$Html .= "Income & Expenditure From " . $RepFromDate . " To " . $RepToDate  . "\n";
	$Html .= "Particulars\t";
	$Html .= "Amount\t\n";
	$Html .= "Income\t\n";

	$SerialNo 	= 1;
	$TotCredit 	= 0;
	foreach ($IncomeRow as $Row) {
		$Credit  = 0;
		$Credit   = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0;

		$Debit  = !is_null($Row['Debit1']) ? $Row['Debit1'] : 0;
		// number_format($number, 2, '.', '');
		if ($Credit - $Debit > 0) {
			$Html .= $Row['Name'] . "\t";
			$Html .= number_format($Credit - $Debit, 2, '.', '') . "\t\n";
			$TotCredit += $Credit - $Debit;
			$SerialNo++;
		}
	}
	if ($Loss > 0) {
		$Html .= "Net Loss\t";
		$Html .= number_format($Loss, 2, '.', '') . "\t\n";
	}
	$Html .= "Total\t";
	$Html .= number_format($TotCredit, 2, '.', '') . "\t\n\n";


	$Html .= "Expenditure\t\t\n";
	$TotDebit = 0;
	foreach ($ExpensesRow as $Row) {
		$Debit 	 = 0;
		$Credit  = 0;
		$Credit  = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0;
		$Debit  = !is_null($Row['Debit1']) ? $Row['Debit1'] : 0;
		// number_format($number, 2, '.', '');
		if ($Debit - $Credit > 0) {
			$Html .= $Row['Name'] . "\t";
			$Html .= number_format($Debit - $Credit, 2, '.', '') . "\t\n";
			$TotDebit += $Debit - $Credit;
			$SerialNo++;
		}
	}
	if ($Profit > 0) {
		$Html .= "Net Profit\t";
		$Html .= number_format($Profit, 2, '.', '') . "\t\n";
	}
	$Html .= "Total\t";
	$Html .= number_format($TotDebit + $Profit, 2, '.', '') . "\t\n";

	//////////////////////////////////////////////////////////////////////////
	// Now print Balance Sheet
	$TotDebit 	= 0;
	$TotCredit 	= 0;
	$Html .= "KLS GIT Employees Co-Op Credit Society Ltd.\n";
	$Html .= "Balance Sheet As at " . $RepToDate  . "\n\n";
	$Html .= "Particulars\t";
	$Html .= "Amount\t\n";
	$Html .= "Liabilities\t\t\n";

	// process Liabilities
	$TotCredit = 0;
	foreach ($LiabilitesRow as $Row) {
		$Balance = getClosBalGenAcc($db, $Row['FMID'], $ToDate);
		// number_format($number, 2, '.', '');
		if ($Balance > 0) {
			$Html .= $Row['Name'] . "\t";
			$Html .= number_format($Balance, 2, '.', '') . "\t\n";
			$TotCredit += $Balance;
		}
	}
	if ($Profit > 0) {
		$Html .= "Net Profit\t";
		$Html .= number_format($Profit, 2, '.', '') . "\t\n";
	}

	$Html .= "Total\t";
	$Html .= number_format($TotCredit + $Profit, 2, '.', '') . "\t\n";



	$Html .= "Assets\t\n";

	// process Assets
	$TotDebit = 0;
	foreach ($AssetsRow as $Row) {
		$Balance = getClosBalGenAcc($db, $Row['FMID'], $ToDate);
		// number_format($number, 2, '.', '');
		if ($Balance != 0) {
			$Balance = $Balance * -1;

			$Html .= $Row['Name'] . "\t";
			$Html .= number_format($Balance, 2, '.', '') . "\t\n";
			$TotDebit += $Balance;
		}
	}
	if ($Loss > 0) {
		$Html .= "Net Profit\t";
		$Html .= number_format($Loss, 2, '.', '') . "\t\n";
	}
	$Html .= "Total\t";
	$Html .= number_format($TotDebit + $Loss, 2, '.', '') . "\t\n";
	return $Html;
}

function getLoanShareAccName($db, $LoanID, $MemberID)
{
	$Nm = " ";
	if (strlen($LoanID) == 6) {
		$LoanMemberID = getSingleField($db, "Select MemberID from customers Where LoanID='$LoanID'");
		$Nm 	      = getSingleField($db, "Select Name from shareholders WHere MemberID='$LoanMemberID'");
	} elseif (strlen($MemberID) == 6) {
		$Nm 	      = getSingleField($db, "Select Name from shareholders WHere MemberID='$MemberID'");
	}
	return $Nm;
}

// added on 20.12.2021
function getOpenBalShares($db, $FMID, $MemberID, $BalUptoDate)
{
	$OpenBal = getSingleField($db, "Select OpenBal from shareholders Where MemberID='$MemberID'");
	$Sql = "Select shareholders.OpenBal,
			(Select SUM(Debit)  from ft Where ft.FMID = '$FMID' and ft.MemberID =  '$MemberID' AND ft.TrnDate < '$BalUptoDate' ) as Debit1,
			(Select SUM(Credit) from ft Where ft.FMID = '$FMID' and ft.MemberID =  '$MemberID' AND ft.TrnDate < '$BalUptoDate' ) as Credit1 
		From shareholders Where MemberID = '$MemberID'";
	$ResultRow = getResultSet($db, $Sql);
	foreach ($ResultRow as $Row) {

		$Debit  = !is_null($Row['Debit1']) ? $Row['Debit1'] : 0;

		$Credit  = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0;
		$ClosBal = $OpenBal - $Debit + $Credit;
		return $ClosBal;
	}
}


function getIndianCurrency(float $number)
{
	$decimal = round($number - ($no = floor($number)), 2) * 100;
	$hundred = null;
	$digits_length = strlen($no);
	$i = 0;
	$str = array();
	$words = array(
		0 => '', 1 => 'one', 2 => 'two',
		3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
		7 => 'seven', 8 => 'eight', 9 => 'nine',
		10 => 'ten', 11 => 'eleven', 12 => 'twelve',
		13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
		16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
		19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
		40 => 'forty', 50 => 'fifty', 60 => 'sixty',
		70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
	);
	$digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
	while ($i < $digits_length) {
		$divider = ($i == 2) ? 10 : 100;
		$number = floor($no % $divider);
		$no = floor($no / $divider);
		$i += $divider == 10 ? 1 : 2;
		if ($number) {
			$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
			$hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
			$str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
		} else $str[] = null;
	}
	$Rupees = implode('', array_reverse($str));
	$paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
	return ($Rupees ? $Rupees  : '') . $paise . " only";
}
