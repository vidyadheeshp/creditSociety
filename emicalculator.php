<?php
	// Author: Anand V Deshpande,Belagavi
	// Date Written : 08.12.2019
	// emicalculator.php
	//session_start();
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
		$Report  = " <h3><b>Monthly EMI is " . $EMI."</b></h3><br>";
		$Report .= "<table class='table table-striped table-bordered table-responsive'>";
		$Report .= "<tr>";
		$Report .= "<td>Month</td>";
		$Report .= "<td>Interest</td>";
		$Report .= "<td>Principal</td>";
		$Report .= "<td>Balance</td>";
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




<body>
    
		<center><h2>EMI CALCULATOR</h2>
			
		<div class='panel-body'>
			<div class='form-inline'>
				<form name='emicalculator' class='myform' method='post' action='emicalculator.php'>
					<table>
						<tr>
							<td class='col'><label>Principle Amount</label></td>
							<td class='col'><input type='text' id='principal' name='principal' title="Enter numbers only" pattern="[+]?[0-9]*[.]?[0-9]+"  value='<?php echo $Principal;?>' required/></td>
						</tr>
						<tr>
							<td class='col'><label>Period in Years</label></td>
							<td class='col'><input type='text' id='years' name='years' pattern="[0-9]{1,2}" title="Enter two digit number max" value='<?php echo $Years;?>'required/></td>
						</tr>
						<tr>
							<td class='col'><label>IntRate</label></td>
							<td class='col'><input type='text' id='roi' name='roi' pattern="[0-9]{1,2}" title="Enter two digit number max" value='<?php echo $ROI;?>' step='0.01' required/></td>
						</tr>
						<tr>
							<td class='col' colspan=2><input type='submit' name='submit' value='submit'/></td>
						</tr>
					</table>
					</form>
					</div>
					
					<div id='showreport' class='table-responsive'>
						<?php echo $Report; ?>
			</div>
		</div>
				
	</center>
</body>
