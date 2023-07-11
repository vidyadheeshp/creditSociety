<?php
	// Author: Anand V Deshpande,Belagavi
	// Date Written : 08.12.2019
	// emicalculator.php
	session_start();
	include('../includes/pdofunctions_v1.php');
	$db = connectPDO();
	include('../includes/functions.php'); 
	include('../includes/loans.php'); 
	$PanelHeading = " EMI Calculator";
	//MsgBox("accountsmenu.php","",True);
	$Report     = "";
	$Principal 	= "";
	$ROI 		= "";
	$Years 	    = "";
	if(isset($_POST['submit'])){
		//echo "Inside post";
		$Principal 	= intval($_POST['principal']);
		$ROI 		= floatval($_POST['roi']);
		$Years 		= intval($_POST['years']);
		$EMI 		= intval(emi_calculator($Principal,$ROI,$Years));
		$Report  = " Monthly EMI is " . $EMI."<br>";
		$Report .= "<table class='table table-striped table-bordered table-responsive'>";
		$Report .= "<tr>";
		$Report .= "<th>Month</th>";
		$Report .= "<th>Interest</th>";
		$Report .= "<td>Principal</th>";
		$Report .= "<td>Balance</th>";
		$Report .= "</tr>";		
		$Balance = $Principal;
		for($i=1;$i<=$Years*12;$i++){
			$Int = intval($Balance * $ROI / 1200);
			//echo $Int;
			$prin = $EMI - $Int;
			$Balance = $Balance - $prin;
			$Report .= "<tr>";
			$Report .= "<td>$i</td>";
			$Report .= "<td>".$Int."</td>";
			$Report .= "<td>".$prin."</td>";
			$Report .= "<td>".$Balance."</td>";
			$Report .= "</tr>";
		}
		$Report .= "</table>";
		CreateLog($Report);
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
                    <center><h4>EMI Calculator</h4></center>
				</div>
				<div class='panel-body'>
					<div class='form-inline'>
						<form name='emicalculator' method='post' action='emicalculator.php'>
						<label>Principal</label>
						<input type='text' id='principal' name='principal' value='<?php echo $Principal;?>' required/>
						<label>Period in Years</label>
						<input type='text' id='years' name='years' value='<?php echo $Years;?>'required/>
						<label>IntRate</label>
						<input type='text' id='roi' name='roi' value='<?php echo $ROI;?>' step='0.01' required/>
						<input type='submit' name='submit' value='submit'/>
						</form>
					</div>
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
 