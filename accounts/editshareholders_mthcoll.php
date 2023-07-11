<?php

// editshareholders_mthcoll.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 12.01.2020
//	
	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();
	$PanelHeading = "Edit ShareHolders MthColl";
	if(isset($_POST['submit'])) {
		$Mode = $_POST['memberid'];
		$FMID = $_POST['mthcoll'];
	} else{
		$Table = "";
		$Table = "<table class='table table-bordered table-striped'>";
		$Table .= "<tr>";
		$Table .= "<td>MemberID</td>";
		$Table .= "<td>Name</td>";
		$Table .= "<td>Status</td>";
		$Table .= "<td>MthColl</td>";
		$Table .= "</tr>";
		$ResultSet = getResultSet($db,"Select MemberID,Name,Status,MthColl from shareholders Order By MemberID");
		foreach($ResultSet as $Row){
			$MemberID = $Row['MemberID'];
			$Name     = $Row['Name'];
			$Status   = $Row['Status'];
			$MthColl  = $Row['MthColl'];
			$Table .= "<tr>";
			$Table .= "<td><input type='text' name='memberid[]' class='form-control' value='$MemberID' readonly/></td>";
			$Table .= "<td>".$Name."</td>";
			$Table .= "<td>".$Status."</td>";
			$Table .= "<td><input type='number' name='mthcoll[]' class='form-control' value='$MthColl' required/></td>";
			$Table .= "</tr>";			
		}
	}
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
	<link  href="../includes/avd.css"  rel="stylesheet"></link>   

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
		</div>
		<div class='col-md-6 col-md-offset-3'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
    	        	<center><h4><?php echo $PanelHeading; ?></h4></center>
				</div>
				<div class='panel-body'>
					<form id="genacc" name="genacc" role="form" method="post" action="genacc.php" enctype="multipart/form-data">

						<?php echo $Table; ?>
					<input type="submit" value="Save" name="submit" data-toggle="tooltip" data-placement="top" title="Click to Save" class="btn btn-success">
					</form>
				</div>
			</div>
			<div class='panel-footer'>
			</div>
		</div>
	</div>
</body>
