<?php
	// AUthor: Anand V Deshpande
	// Date Written : 22.12.2019
	// sharevariations_list.php

	session_start();
	require_once("../includes/functions.php");

	if(!isset($_SESSION['UserType'])) {
		MsgBox("Direct script access prohibited","../index.php",True);
		exit();
	}
	
	$UserType 	= $_SESSION['UserType'];
	if(strstr('Accounts,Admin',$UserType)){

	} else{
		MsgBox("Access for Authorised Users only","accountsmenu.php",True);
		exit();
	}
	//require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();
	$PanelHeading = " Month Share Contr Variations List";
	$Report = "";
	$Table = "<table id='gridtable' class='table table-striped table-responsive table-bordered table-condensed'>";
	$Table .= "<thead><tr style='background-color:yellow;'>";
	$Table .= "<td>SNo</td>";
	$Table .= "<td>MemberID</td>";
	$Table .= "<td>Name</td>";
	$Table .= "<td>PrevContr</td>";
	$Table .= "<td>NewContr</td>";
	$Table .= "<td>Requested</td>";
	$Table .= "<td>Status</td>";
	$Table .= "<td>DateTime</td>";
    $Table .= "<td>Edit</td>";
	$Table .= "</tr></thead><tbody>";
	$SerialNo=1;
	$rs  = getResultSet($db,"Select A.*,B.Name,B.DOR 
		from sharevariations A, shareholders B Where A.MemberID=B.MemberID 
		Order By Requested Desc");	
	foreach($rs as $row) {
		//print_r($row);
		$MemberID = $row['MemberID'];
		$RowID    = $row['RowID'];
		$Table .= "<tr>";
        $Table .= "<td>".$SerialNo."</td>";
        $Table .= "<td>".$MemberID."</td>";
        $Table .= "<td>".$row['Name']."</td>";
        $Table .= "<td>".$row['PrevContr']."</td>";
        $Table .= "<td>".$row['NewContr']."</td>";
        $Table .= "<td>".date("d-m-Y H:i:s",strtotime($row['Requested']))."</td>";
        $Table .= "<td>".$row['Status']."</td>";
        $Table .= "<td>".date("d-m-Y H:i:s",strtotime($row['DateStmp']))."</td>";
		if($row['Status']=='Applied'){
	        $Table .= "<td><button class='btn btn-danger btn-sm' onclick=js_appr('".$RowID."') >Approve</button></td>";
		} else{
			$Table .= "<td></td>";			
		}
		$Table .= "</tr>";
		$SerialNo++;
	}
	$Table .= "</tbody></table>";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style>
</style>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KLS GIT employees Co-Op Credit Society Ltd.</title>
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/git.png">
	<link  href="../bootstrap/dist/css/bootstrap.css" rel="stylesheet" />
	<script src="../bootstrap/dist/js/jquery-1.10.2.js"></script>
	<script src="../bootstrap/dist/js/bootstrap.min.js"></script>   
</head>
<style>
	body { 
	  background: url(assets/images/entrance.jpg) no-repeat center center fixed; 
	  -webkit-background-size: cover;
	  -moz-background-size: cover;
	  -o-background-size: cover;
	  background-size: cover;
	}
</style>
<body>
    <div class="container-fluid">
    	<div class="col-md-12">
    		<div class='row'>
				<?php include('accountsmenu.ini'); ?>
			</div>
			<div class='col-md-12'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
                    <center><h4><?php echo $PanelHeading; ?></h4></center>
				</div>
				<div class='panel-body'>
					<?php echo $Table; ?>
				</div>
				<div class='panel-footer text-primary'>
					<strong><center>Software Developed By Anand V Deshpande,Belagavi</center></strong>
				</div>
			</div>			
			</div>
		</div>
	</div>

</body>
<script>
	function js_appr(rowid) {
		alert("inside rowid change: "+rowid);
		//$("#showerror").html("");
		$.ajax({
	        type: "POST",
	        url: "ajax_apprsharevariation.php",
	        data: "RowID="+rowid,
	        success : function(text){
				var ret = JSON.parse(text);
				alert(ret['Response']);
				location.reload(true);
	        }
		});
	}
</script>

