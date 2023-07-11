<?php
	// Author: Anand V Deshpande,Belagavi
	// Date Written : 08.12.2019
	// viewlogfile.php
	session_start();
	include('../includes/pdofunctions_v1.php');
	$db = connectPDO();
	include('../includes/functions.php'); 
	include('../includes/loans.php'); 
	$PanelHeading = " View Log File";
	//MsgBox("accountsmenu.php","",True);
	$Report     = "";
	$Report .= "<table class='table table-striped table-bordered table-responsive'>";
	$Report .= "<tr>";
	$Report .= "<th>SNo</th>";
	$Report .= "<th>DateTime</th>";
	$Report .= "<th>Type</th>";
	$Report .= "<td>Description</th>";
	$Report .= "</tr>";		
	$ResultSet  = getResultSet($db,"Select * from logfile Order By DateStmp Desc");
	$i=1;
	foreach ($ResultSet as $Row) {
		$Report .= "<tr>";
		$Report .= "<td>$i</td>";
		$Report .= "<td nowrap>".date("d-m-Y",strtotime($Row['DateStmp']))."</td>";
		$Report .= "<td>".$Row['LogType']."</td>";
		$Report .= "<td>".$Row['Description']."</td>";
		$Report .= "</tr>";
		$i++;
	}
	$Report .= "</table>";
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
			<div class='col-md-8 col-md-offset-2'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
                    <center><h4>View LogFile</h4></center>
				</div>
				<div class='panel-body'>
					<div id='showreport' class='table-responsive'>
						<?php echo $Report; ?>
					</div>
				</div>
				<div class='panel-footer text-primary'>
					<strong><center>Software Developed By Anand V Deshpande,Belagavi</center></strong>
				</div>
			</div>			
			</div>
		</div>
	</div>
</body>
