<?php
	// Author : Anand V Deshpande,Belagavi
	// Date Written: 26.10.2019
	// getbanktranlist.php
	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");

	$db = connectPDO();	
	$FromDate 	= $_POST['fromdate'];
	$ToDate 	= $_POST['todate'];

	//$Sql = "Select * B.FTID,B.TrnDate,B.TrnCode,B.TrnType,B.TrnNo,B.MemberID,B.LoanID,A.Name,B.Debit,B.Credit,B.ForMonth,B.ForYear from ft B, fm A Where B.FMID = A.FMID and
	//	B.TrnDate between '$FromDate' and '$ToDate' AND B.FixFMID>0 
	//	Order By B.TrnDate,B.TrnCode,B.TrnNo ";

	//	AND B.TrnType NOT IN('MthContr','MthEMI')   
	//echo $Sql;
	// function build_table($db,$Sql,$SerialNoReqd,$EditReqd='No',$DelReqd='No',$PrimaryID) {
	//$Html = build_table_bankentries($db,$Sql,"SNo","No","Yes","FTID");
	//echo $Html;
	// modified for actual bildig of list
	// on 07.04.2020
	$Sql = "Select * from ft Where TrnDate between '$FromDate' and '$ToDate' AND FixFMID>0 
			Order By TrnDate,TrnCode,TrnNo ";
	$Html = "<table id='gridtable' class='tbl'>";
	$Html .= "<thead>";
	$Html .= "<tr>";
	$Html .= "<th>SNo</th>";
	$Html .= "<th>FTID</th>";
	$Html .= "<th>TrnDate</th>";
	$Html .= "<th>TrnCode</th>";
	$Html .= "<th>TrnType</th>";
	$Html .= "<th>TrnNo</th>";
	$Html .= "<th>LoanID</th>";
	$Html .= "<th>MemberID</th>";
	$Html .= "<th>Name</th>";
	$Html .= "<th>Debit</th>";
	$Html .= "<th>Credit</th>";
	$Html .= "<th>Int</th>";
	$Html .= "<th>Days</th>";
	$Html .= "<th>Month</th>";
	$Html .= "<th>Year</th>";
	$Html .= "<th>IntUpto</th>";
	$Html .= "<th></th>";
	$Html .= "</tr>";
	$Html .= "</thead>";

	$Html .= "<tbody>";
	$SerialNo = 1;
	$TotPrin = 0;
	$TotInt  = 0;
	$Total   = 0;
	$TotDebit = 0;
	$TotCredit=0;
	$result  = getResultSet($db,$Sql);
	foreach($result as $row) {
		$FTID 		= $row['FTID'];
		$FMID       = $row['FMID'];
		$LoanID 	= $row['LoanID'];
		$MemberID  	= $row['MemberID'];
		$VchAmt     = ($row['Principal']) + ($row['Interest']);
		$AccName    = getSingleField($db,"Select Name from fm Where FMID='$FMID'");
		$AccName   .= " ".getLoanShareAccName($db,$row['LoanID'],$row['MemberID']);
		$IntUptoDt  = "";
		if(!is_null($row['IntUptoDt'])){
			$IntUptoDt = date("d-m-Y",strtotime($row['IntUptoDt']));
		}
		$Html .= "<tr>";
		$Html .= "<td>$SerialNo</td>";
		$Html .= "<td>".$row['FTID']."</td>";
		$Html .= "<td nowrap>".date("d-m-Y",strtotime($row['TrnDate']))."</td>";
		$Html .= "<td>".$row['TrnCode']."</td>";
		$Html .= "<td>".$row['TrnType']."</td>";
		$Html .= "<td>".$row['TrnNo']."</td>";
		$Html .= "<td>".$row['LoanID']."</td>";
		$Html .= "<td>".$row['MemberID']."</td>";
		$Html .= "<td>".$AccName."</td>";

		$Html .= "<td align='right'>".$row['Debit']."</td>";
		$Html .= "<td align='right'>".$row['Credit']."</td>";
		$Html .= "<td align='right'>".$row['Interest']."</td>";
		$Html .= "<td align='center'>".$row['Days']."</td>";
		$Html .= "<td align='center'>".$row['ForMonth']."</td>";
		$Html .= "<td align='center'>".$row['ForYear']."</td>";
		$Html .= "<td nowrap>".$IntUptoDt."</td>";
        $Html .= "<td><button class='btn btn-danger btn-sm' onclick=js_delbanktran('".$FTID."') >Del</button></td>";
		$Html .= "</tr>";

		$SerialNo++;
		$TotDebit       += $row['Debit'];
		$TotCredit      =+ $row['Credit'];
		$TotInt		    += $row['Interest'];
		$Total 			+= $VchAmt;
	}
	$Html .= "</tbody>";
	$Html .= "<tfoot>";
	$Html .= "<td colspan=9>Totals</td>";
	

	$Html .= "<td align='right'>".$TotDebit."</td>";
	$Html .= "<td align='right'>".$TotCredit.'.00'."</td>";
	$Html .= "<td align='right'>".$TotInt."</td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .="<td></td>";
	$Html .= "</tr>";
	$Html .= "</tfoot>";
	$Html .= "</table>";
	echo $Html;
?>