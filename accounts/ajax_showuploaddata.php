<?php
	// gitsociety
	// Author : Anand V Deshpande, Belagavi
	// Started : 31.10.2019
	// ajax_showuploaddata.php

	// included on uploadmthsharecontr.php
	// not in use now



	session_start();
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();
	require_once("../includes/functions.php");
	//$InputFile = $_FILES['uploadregular']['tmp_name'];
	$FileName = $_POST['file'];
	print_r($FileName);
	$InputFile = $FileName['tmp_name'];
	//echo $InputFile;
	$Html = "<strong>Share Holders Variations From Monthly Contribution</strong>";
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



	$TotalCount  =0;
	$TotalMthContr = 0;
	// Columns : 0->MemberID, 1->Name 2->MthContr
	$file = fopen($InputFile,"r");
	while(! feof($file)) {
	    $row = fgetcsv($file);
		$MemberID = $row[0];
		$Name     = $row[1];
		$MthContr = $row[2];
		$TotalMthContr += $MthContr;
		$MemberStyle = "";
		$MemberName= getSingleField($db,"Select Name from shareholders Where MemberID='$MemberID'");
		if(strlen($MemberName)<=0){
			$MemberName  = " Invalid MemberID ";
			$MemberStyle = " style='color:red'";
		}
		$Department= getSingleField($db,"Select DeptID from shareholders Where MemberID='$MemberID'");
		$Html .= "<tr>";
		$Html .= "<td>$SerialNo</td>";
		$Html .= "<td>".$Department."</td>";
		$Html .= "<td>".input_readonly('memberid[]',$MemberID,' required ')."</td>";
		$Html .= "<td>".$MemberName."</td>";
		$Html .= "<td>".input_readonly('mthcontr[]',$MthContr,' required')."</td>";
		$Html .= "</tr>";

		$SerialNo++;
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