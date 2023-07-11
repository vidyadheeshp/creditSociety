<?php
// Author: Anand V Deshpande, Belagavi
// ajax_sharetranslist.php
// 01.11.2019

$Html = "";
$Sql  = "Select * from sharetrans Where TrnCode BETWEEN '' AND '' Order By TranID";
$result = getResultSet($db,$Sql);
$Html = "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed'>";
$Html .= "<thead>";
$Html .= "<tr>";
$Html .= "<th>SNo</th>";
$Html .= "<th>TranID</th>";
$Html .= "<th>TrnDate</th>";
$Html .= "<th>TrnCode</th>";
$Html .= "<th>MemberID</th>";
$Html .= "<th>Name</th>";
$Html .= "<th>Debit</th>";
$Html .= "<th>Credit</th>";
$Html .= "<th>Balance</th>";
$Html .= "</tr>";
$Html .= "</thead>";

$Html .= "<tbody>";
$SerialNo = 1;
$TotalOpenBal=0;
$TotalDebits=0;
$TotalCredits=0;
$TotalClosBal=0;
$TotalMthContr=0;
foreach($result as $row) {
	$DeptID    	= $row['DeptID'];
	$DesignID	= $row['DesignID'];
	$MemberID  	= $row['MemberID'];
	$Dept  		= getSingleField($db,"Select DeptName from departments Where DeptID='$DeptID'");
	$Designation= getSingleField($db,"Select Designation from designation Where DesignID='$DesignID'");
	$Html .= "<tr>";
	$Html .= "<td>$SerialNo</td>";
	$Html .= "<td>".$row['MemberID']."</td>";
	$Html .= "<td>".$row['Name']."</td>";
	$Html .= "<td>".$Dept."</td>";
	$Html .= "<td>".$Designation."</td>";
	$Html .= "<td>".$row['OpenBal']."</td>";
	$Html .= "<td>".$row['Debits']."</td>";
	$Html .= "<td>".$row['Credits']."</td>";
	$Html .= "<td>".$row['ClosBal']."</td>";
	$Html .= "<td>".$row['MthContr']."</td>";
	$Html .= "<td nowrap>".date("d-m-Y",strtotime($row['OpenDate']))."</td>";
	$Html .= "<td nowrap>".date("d-m-Y",strtotime($row['DOR']))."</td>";
	$Html .= "<td>".$row['Status']."</td>";
	$Html .= "<td><button onclick=js_shareholder_edit('$MemberID')>Edit</button>";
	$Html .= "<button onclick=js_shareholder_del('$MemberID')>Del</button>";
	$Html .= "<button onclick=js_shareholder_ledger('$MemberID')>Ledger</button></td>";
	$Html .= "</tr>";

	$SerialNo++;
	$TotalOpenBal  	+= $row['OpenBal'];
	$TotalDebits   	+= $row['Debits'];
	$TotalCredits 	+= $row['Credits'];
	$TotalClosBal  	+= $row['ClosBal'];
	$TotalMthContr  += $row['MthContr'];
}
$Html .= "</tbody>";
$Html .= "<tfoot>";
$Html .= "<td></td>";
$Html .= "<td></td>";
$Html .= "<td></td>";
$Html .= "<td></td>";
$Html .= "<td></td>";
$Html .= "<td>".$TotalOpenBal."</td>";
$Html .= "<td>".$TotalDebits."</td>";
$Html .= "<td>".$TotalCredits."</td>";
$Html .= "<td>".$TotalClosBal."</td>";
$Html .= "<td>".$TotalMthContr."</td>";
$Html .= "<td></td>";
$Html .= "<td></td>";
$Html .= "<td></td>";
$Html .= "<td></td>";
$Html .= "</tr>";
$Html .= "</tfoot>";
$Html .= "</table>";
$Report = $Html;

?>
