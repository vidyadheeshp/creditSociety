<?php
	// gitsociety
	// Author : Anand V Deshpande, Belagavi
	// Started : 31.10.2019
	// ajax_getsharedatafrommthcontr.php

	// included on uploadmthsharecontr.php
	// not in use now
	session_start();
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();
	require_once("../includes/functions.php");

	$Month 		= $_POST['mm'];
	$Year 		= $_POST['yy'];

	// now create entries in tempmthsharecontr from shareholders table with MthContr
	$result = getResultSet($db,"Select SHID,MemberID,DeptID,Name,ClosBal,MthContr from shareholders Where Status='Active' AND MthContr >0 Order By DeptID,MemberID");
	
	$Html = "<strong>Share Holders Fixed Monthly Contribution</strong>";
	$Html .= "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed'>";
	$Html .= "<thead>";
	$Html .= "<tr>";
	$Html .= "<th>SNo</th>";
	$Html .= "<th>DeptID</th>";
	$Html .= "<th>Department</th>";
	$Html .= "<th>MemberID</th>";
	$Html .= "<th>Name</th>";
	$Html .= "<th align='right'>Balance</th>";
	$Html .= "<th align='right'>MthContr</th>";
	$Html .= "</tr>";
	$Html .= "</thead>";
	$Html .= "<tbody>";
	$SerialNo = 1;
	$TotalMthContr = 0;
	$TotalClosBal=0;
	$TotalCount  =0;
	foreach($result as $row) {
		$DeptID    	= $row['DeptID'];
		$Dept  		= getSingleField($db,"Select DeptName from departments Where DeptID='$DeptID'");
		$Html .= "<tr>";
		$Html .= "<td>$SerialNo</td>";
		$Html .= "<td>".$row['DeptID']."</td>";
		$Html .= "<td>".$Dept."</td>";
		$Html .= "<td>".input_readonly('memberid[]',$row['MemberID'],' readonly required ')."</td>";
		$Html .= "<td>".$row['Name']."</td>";
		$Html .= "<td>".input_readonly('closbal[]',$row['ClosBal'],'   readonly')."</td>";
		$Html .= "<td>".input_readonly('mthcontr[]',$row['MthContr'],' required')."</td>";
		$Html .= "</tr>";

		$SerialNo++;
		$TotalClosBal  	+= $row['ClosBal'];
		$TotalMthContr 	+= $row['MthContr'];
	}
	$Html .= "</tbody>";
	$Html .= "<tfoot>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td align='right'>".$TotalClosBal."</td>";
	$Html .= "<td align='right'>".$TotalMthContr."</td>";
	$Html .= "</tr>";
	$Html .= "</tfoot>";
	$Html .= "</table>";
	echo $Html;		
	/*
  	$Total = 0;
  	foreach($resultset as $row) {
  		$MemberID 	= $row['MemberID'];
  		$MthContr 	= $row['MthContr'];
  		$Name 		= $row['Name'];
  		$Total 	   += $row['MthContr'];
  		$Array = array("Mth"=>'$Month',"Yr"=>'$Year',"MemberID"=>'$MemberID',"Name"=>'$Name',"MthContr"=>'$MthContr');
  		insert($db,"tempmthsharecontr",$Array);
  	}*/
  	/*






	$sql = "Select Count(*) from sharetrans Where ForMonth='$Month' AND ForYear='$Year' AND Credit>0 and TrnType='MthContr' LIMIT 1";
	$noofrecs = getSingleField($db,$sql);
	if ($noofrecs <=0) {
		echo json_encode(array('Response' => "Not Posted"), JSON_FORCE_OBJECT);
		exit();
	} else {
		echo json_encode(array('Response' => "Already $noofrecs entries posted"), JSON_FORCE_OBJECT);
		exit();
	}
	*/
?>