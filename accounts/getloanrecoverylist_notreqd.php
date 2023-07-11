<?php
	// getloanrecoverylist.php

	$FromDate = date("Y-m-d",strtotime($_POST['fromdate']));
	$ToDate   = date("Y-m-d",strtotime($_POST['todate']));
	// modify code for loan recovery statement
	
	$Html = "";
	$Sql  = "Select F.*,C.MemberID as CustMemberID,S.Name,from ft F,customers C, shareholders S 
			Where F.LoanID = C.LoanID AND C.MemberID = S.MemberID  
			AND F.TrnDate Between '$FromDate' AND '$ToDate'    
			Order By TrnDate ";
    echo $Sql;
	$result = getResultSet($db,$Sql);
	$Html = "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed'>";
	$Html .= "<thead>";
	$Html .= "<tr>";
	$Html .= "<th>SNo</th>";
	$Html .= "<th>LoanID</th>";
	$Html .= "<th>MemberID</th>";
	$Html .= "<th>Name</th>";
	$Html .= "<th>Date</th>";
	$Html .= "<th>Total</th>";
	$Html .= "<th>Principal</th>";
	$Html .= "<th>Interest</th>";
	$Html .= "<th>Days</th>";
	$Html .= "<th>IntRate</th>";
	$Html .= "<th>IntUpto</th>";
	$Html .= "</tr>";
	$Html .= "</thead>";

	$Html .= "<tbody>";
	$SerialNo = 1;
	$TotPrin = 0;
	$TotInt  = 0;
	$Total   = 0;
	echo $Html;
	foreach($result as $row) {
		$LoanID 	= $row['LoanID'];
		$DeptID    	= $row['DeptID'];
		$MemberID  	= $row['CustMemberID'];
		$VchAmt     = ($row['Principal']) + ($row['Interest']);
		$Html .= "<tr>";
		$Html .= "<td>$SerialNo</td>";
		$Html .= "<td>".$row['LoanID']."</td>";
		$Html .= "<td>".$row['CustMemberID']."</td>";
		$Html .= "<td>".$row['Name']."</td>";
		$Html .= "<td align='right' nowrap>".date("d-m-Y",strtotime($row['TrnDate']))."</td>";
		$Html .= "<td align='right'>".$VchAmt."</td>";
		$Html .= "<td align='right'>".$row['Principal']."</td>";
		$Html .= "<td align='right'>".$row['Interest']."</td>";
		$Html .= "<td align='right' nowrap>".$row['Days']."</td>";
		$Html .= "<td align='right'>".$row['IntRate']."</td>";
		$Html .= "<td align='right' nowrap>".date("d-m-Y",strtotime($row['IntUpto']))."</td>";
		$Html .= "</tr>";

		$SerialNo++;
		$TotPrin  		+= $row['Principal'];
		$TotInt		    += $row['Interest'];
		$Total 			+= $VchAmt;
	}
	$Html .= "</tbody>";
	$Html .= "<tfoot>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td align='right'>".ConvBalance($TotalClosBal)."</td>";
	$Html .= "<td></td>";
	$Html .= "<td align='right'>".$TotalMthEMI."</td>";
	$Html .= "</tr>";
	$Html .= "</tfoot>";
	$Html .= "</table>";
	echo $Html;

?>
