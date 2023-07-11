<?php
	// divdcalcform.php
	// Author : Anand V Deshpande,Belagavi
	// Date Written: 09.11.2019
	//	
	session_start();
	// check usertype
	if(!isset($_SESSION['UserType'])) {
		MsgBox("Direct script access prohibited","../index.php",True);
		exit();
	}

	$UserType 	= $_SESSION['UserType'];
	if(strstr('Shares,Admin,Chairman',$UserType)){
	} else{
		MsgBox("Access for Authorised Users only","accountsmenu.php",True);
		exit();
	}
	//
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();
	$PanelHeading = " Dividend Calculation Program ";
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
		<div class='col-md-8 col-md-offset-2'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
    	        	<center><h4><?php echo $PanelHeading; ?></h4></center>
				</div>
				<div class='panel-body'>
					<form id="divdcalc" class="form-inline" name="divdcalc" role="form" method="post" action="divdcalc_rd.php" enctype="multipart/form-data">
					<label>Fin.Year</label>
					<input type='text' class='form-control' id='finyear' name='finyear' 
									maxlength="4" minlength="4" />
					<label>Divd%</label>
					<input type='number' class='form-control' id='divdper' name='divdper' 
							step='0.01' />
					<label>Divd%(2)</label>
					<input type='number' class='form-control' id='divd2per' name='divd2per' 
							step='0.01' />
					<label>Test ShareID</label>
					<input type='text' class='form-control' id='testshareid' name='testshareid' 
									maxlength="10"/>
					<input type="submit" value="Calculate" name="submit" data-toggle="tooltip" data-placement="top" title="Click to Calculate" class="btn btn-success">

					<!--
					<table id='table0'>
						<tr>
							<td><label>For Financial Year</label></td>
							<td><input type='text' class='form-control' id='finyear' name='finyear' 
									maxlength="4" minlength="4" />
							</td>
							<td><label>Dividend %</label></td>
							<td><input type='number' class='form-control' id='divdper' name='divdper' 
							step='0.01' />
							<td><input type='number' class='form-control' id='divd2per' name='divd2per' 
							step='0.01' />
							</td>
						</tr>
					</table>
					<table>
						<tr>
							<td></td>
							<td><input type="submit" value="Calculate" name="submit" data-toggle="tooltip" data-placement="top" title="Click to Calculate" class="btn btn-success">
							<span class="pull-right">
							<a href="index.php" data-toggle="tooltip" data-placement="top" class="btn btn-danger">Log Out</a>
							</span></td>
						</tr>
					</table>
					-->
					</form>
					
				</div>
			</div>
			<div class='panel-footer'>
			</div>
		</div>
	</div>
</body>
</html>

