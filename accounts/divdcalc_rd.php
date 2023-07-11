<?php
	// divdcalc_rd.php
	// Author : Anand V Deshpande
	// Date Written/modified original divdcalc.php
	// as divcalc_rd.php  : 31.07.2020
	// modified on 05.11.2020 for negative dividend calc

	session_start();
	include("../includes/functions.php");
	include("../includes/pdofunctions_v1.php");

	$db = connectPDO();

	$ExtraHtml	= "";
	$ExtraHtml  = "<table id='detailed' class='table table-condensed table-bordered'>";
	$ExtraHtml .= "<tr><td>From</td><td>To</td><td>Days</td><td>Balance</td><td>Debits</td><td>Credits</td><td>
	Div1</td><td>Div2</td></tr>";

	$Div1Per	= floatval($_POST['divdper']);
	$Div2Per	= floatval($_POST['divd2per']);
	$FinYear  	= $_POST['finyear'];
	if(strlen($FinYear)==4) {
	} else{
		header("location:divdcalcform.php");
		exit();
	}
	if($Div1Per<=0){
		header("location:divdcalcform.php");
		exit();
	}
	$DateStr1 	= "20".sprintf("%02d",substr($_POST['finyear'],0,2))."-04-01";
	$DateStr2 	= "20".sprintf("%02d",substr($_POST['finyear'],2,2))."-03-31";
	$TestShareID = "";
	if(isset($_POST['testshareid'])){
		$TestShareID = $_POST['testshareid'];
	}

	//echo "<br><br>";
	//echo $DateStr1;
	//echo substr($_POST['finyear'],1,2);
	CreateLog("inside divdcalc_rd.php  FinYear $FinYear Div $Div1Per  $Div2Per $DateStr1 $DateStr2");
	$FromDate 	= date("Y-m-d",strtotime($DateStr1));
	$ToDate   	= date("Y-m-d",strtotime($DateStr2));
	//echo $FromDate;
	
	$TotalDividend = 0;
	$PanelHeading = "Dividend Calculation ".$Div1Per."%". " ($Div2Per%) From ".date("d-m-Y",strtotime($FromDate))." To ".date("d-m-Y",strtotime($ToDate));	
	$SQL = "Delete from dividends Where FinYear='$FinYear'";
	try {
      	$stmt = $db->prepare($SQL);
      	$stmt->execute();
	}catch (Exception $ex) {
		    $Msg = $ex->getMessage();
		    CreateLog("Error while Deleting Dividend Entries $FromDate $ToDate");
	 		echo "<script type='text/javascript'>alert('Something went wrong..');
				window.location='accountsmenu.php';</script>";	   					
			exit();
	}
	      	
	$Html = "<table id='table1' class='table table-responsive table-condensed table-bordered table-striped>'";
	//$Html .= "<caption>".$PanelHeading."</caption>";
	$Html .= "<thead><tr style='background-color:yellow;'>";
	$Html .= "<th>S.No</th>";
	$Html .= "<th>MemberID</th>";
	$Html .= "<th>Name</th>";
	$Html .= "<th>Dept</th>";
	$Html .= "<th>OpenBal</th>";
	$Html .= "<th>Debits</th>";
	$Html .= "<th>Credits</th>";
	$Html .= "<th>ClosBal</th>";
	$Html .= "<th>Div1</th>";
	$Html .= "<th>Div2</th>";
	$Html .= "<th>TotDiv</th>";
	$Html .= "</tr></thead><tbody>";
	$SerialNo=1;
	$Count=0;
	$IndDeb = 0;
	$IndCre = 0;
	$TotalOpenBal=0;
	$TotalClosBal=0;
	$TotalDebit = 0;
	$TotalCredit=0;

	$TotDeb = 0;
	$TotCre = 0;
	$TotDiv1 = 0;
	$TotDiv2 = 0;
	$Errors  = "";
	if(strlen($TestShareID)>1){
		$Rs = getResultSet($db,"Select * from shareholders Where MemberID='$TestShareID'");
	} else{
		$Rs = getResultSet($db,"Select * from shareholders Order By DeptID,MemberID");
	}
	foreach($Rs as $MemberRow){
		$TotDeb = 0;
		$TotCre = 0;
		$MemberID = $MemberRow['MemberID'];
		$Balance  = $MemberRow['OpenBal'];
		$DeptID   = $MemberRow['DeptID'];
		$Dept     = getSingleField($db,"Select ShName from departments where DeptID='$DeptID'");
		$OpenBal  = $Balance;
		// modified on 20.12.2021 for exact opening balance
		//$Balance = getOpenBalShares($db,$MemberID,$FromDate);
		
		//$TotalOpenBal += $Balance;

		$Str = "Select SUM(Credit-Debit) From ft Where MemberID='$MemberID' 
				AND TrnDate < '$FromDate'";
		$DebCre   = getSingleField($db,$Str);
		$Balance  = $Balance + $DebCre;
		$OpenBal  = $Balance;
		$TotalOpenBal += $Balance;

		$Dividend1 	= 0;
		$Dividend2 	= 0;
		$IndDeb 	= 0;
		$IndCre 	= 0;
		$Str = "Select * from ft Where MemberID='$MemberID' AND 
				TrnDate Between '$FromDate' and '$ToDate' Order By TrnDate ";
		$Rs2 = getResultSet($db,$Str);
		$ProcDate = $FromDate;
		$Trns=0;
		foreach($Rs2 as $TransRow){
			$date1 = strtotime($ProcDate);
			$date2 = strtotime($TransRow['TrnDate']);
			$diff = $date2-$date1;
			$diffDays = round($diff / 86400);
			if(round(($Balance-$IndCre+$IndDeb) * $Div1Per * $diffDays / 36500,2) >0) {
	            $Dividend1 = $Dividend1 + round(($Balance-$IndCre+$IndDeb) * $Div1Per * $diffDays / 36500,2);
			}
			if(round(($TotCre-$TotDeb)* $Div2Per * $diffDays / 36500,2)>0) {
	            $Dividend2 = $Dividend2 + round(($TotCre-$TotDeb)* $Div2Per * $diffDays / 36500,2);
			}
            $LogText = date("d-m-Y",strtotime($ProcDate))." ".date("d-m-Y",strtotime($TransRow['TrnDate'])). 
            " days ". $diffDays . 
            " Bal ".$Balance . " Debits ".$TransRow['Debit']. 
            " Credits ".$TransRow['Credit']. " Div1:" . $Dividend1 ." Div2:". $Dividend2 ;
            CreateLog($LogText);
            $ExtraHtml	.= "<tr><td>".date("d-m-Y",strtotime($ProcDate))."</td><td>".date("d-m-Y",strtotime($TransRow['TrnDate']))."</td><td>".
            $diffDays . "</td><td>".
            $Balance . "</td><td>".$TransRow['Debit']."</td><td>".
            $TransRow['Credit']. "</td><td>".$Dividend1 ."</td><td>". $Dividend2 ."</td></tr>";

			//if($TestShareID==$MemberID){            	
            //   	$ExtraHtml .= $LogText. "<br>";		
            //}
            $Balance = $Balance - $TransRow['Debit'] + $TransRow['Credit'];	
            $Trns++;
            $ProcDate = $TransRow['TrnDate'];
			$IndDeb += $TransRow['Debit'];
			$IndCre += $TransRow['Credit'];
			$TotDeb += $TransRow['Debit'];
			$TotCre += $TransRow['Credit'];
		}
		$date1 = strtotime($ProcDate);
		$date2 = strtotime($ToDate);
		$diff = $date2-$date1;
		$diffDays = round($diff / 86400);
		// modified on 05.11.2020 for negative dividend calc
		if(round(($Balance-$TotCre+$TotDeb) * $Div1Per * $diffDays / 36500,2) >0) {
	        $Dividend1 = $Dividend1 + round(($Balance-$TotCre+$TotDeb) * $Div1Per * $diffDays / 36500,2);
		}
        if(round(($TotCre-$TotDeb) * $Div2Per * $diffDays / 36500,2) >0) {
	        $Dividend2 = $Dividend2 + round(($TotCre-$TotDeb) * $Div2Per * $diffDays / 36500,2);
        }
        $Dividend  = $Dividend1 + $Dividend2; 
        if($Dividend1<0 or $Dividend2 <0){
        	$Errors += "Err: ".$MemberID + " Div1:".$Dividend1." Div2:".$Dividend2;
        }
        $LogText = date("d-m-Y",strtotime($ProcDate))." ".date("d-m-Y",strtotime($ToDate)). " days ".$diffDays.  " Bal ".$Balance. " Div1:".$Dividend1." Div2:".$Dividend2. " Debits ".$TotDeb. " Credits ".$TotCre;
        $ExtraHtml	.= "<tr><td>".date("d-m-Y",strtotime($ProcDate))."</td><td>".date("d-m-Y",strtotime($ToDate)). "</td><td>". $diffDays. "</td><td>". $Balance. "</td><td>".$TotDeb."</td><td>". $TotCre. "</td><td>". $Dividend1."</td><td>".$Dividend2. "</td></tr>";
		//if(strlen($TestShareID)>1){
        //	CreateLog($LogText);
        //	$ExtraHtml .= $LogText. "<br>";		
        //}
		//echo $MemberRow['Name']." MemberID:".$MemberRow['MemberID']." Trans:".$Trns. " Balance ".$Balance. " Dividend ".$Dividend."<br>";
		
		// for saving data
		$Array = array("FinYear"=>$FinYear,"MemberID"=>$MemberID,"FromDate"=>$FromDate,"ToDate"=>$ToDate,"Div1Per"=>$Div1Per,"Div2Per"=>$Div2Per,"DivAmt"=>$Dividend,"OpenBal"=>$OpenBal,"Debits"=>$IndDeb,"Credits"=>$IndCre,"ClosBal"=>$Balance,"DeptID"=>$DeptID,
			"Dividend1"=>$Dividend1,"Dividend2"=>$Dividend2);

		$RetVal = insert($db,"dividends",$Array);
		$TotalDividend += ($Dividend1+$Dividend2);
		$TotDiv1 += $Dividend1;
		$TotDiv2 += $Dividend2;
		$Html .= "<tr>";
		$Html .= "<td>".$SerialNo."</td>";
		$Html .= "<td>".$MemberID."</td>";
		$Html .= "<td>".$MemberRow['Name']."</td>";
		$Html .= "<td>".$Dept."</td>";
		$Html .= "<td align='right'>".$OpenBal."</td>";
		$Html .= "<td align='right'>".$IndDeb."</td>";
		$Html .= "<td align='right'>".$IndCre."</td>";
		$Html .= "<td align='right'>".$Balance."</td>";
		$Html .= "<td align='right'>".$Dividend1."</td>";
		$Html .= "<td align='right'>".$Dividend2."</td>";
		$Html .= "<td align='right'>".$Dividend."</td>";
		$Html .= "</tr>";
		$Count++;
		$SerialNo++;
		$TotalCredit += $TotCre;
		$TotalDebit  += $TotDeb;

	}
	$TotalClosBal = $TotalOpenBal - $TotalDebit + $TotalCredit;
	$Html .= "<tr style='background-color:yellow;'>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td>Total</td>";
	$Html .= "<td></td>";
	$Html .= "<td align='right'>".$TotalOpenBal."</td>";
	$Html .= "<td align='right'>".$TotalDebit."</td>";
	$Html .= "<td align='right'>".$TotalCredit."</td>";
	$Html .= "<td align='right'>".$TotalClosBal."</td>";
	$Html .= "<td align='right'>".$TotDiv1."</td>";
	$Html .= "<td align='right'>".$TotDiv2."</td>";
	$Html .= "<td align='right'>".($TotDiv1+$TotDiv2)."</td>";
	$Html .= "</tr></tbody></table>";
	$_SESSION['TempReport'] = $Html . "" . $ExtraHtml;
	CreateLog("Calculation Over");
	CreateLog($Errors);
	$PanelHeading = "Dividend Calculation ".$Div1Per."%". " ($Div2Per%) From ".date("d-m-Y",strtotime($FromDate))." To ".date("d-m-Y",strtotime($ToDate));
	
	$ExtraHtml .= "</table>";



	/*
	require_once('../assets/html2pdf-master/html2pdf.class.php');
	try
    {
    	//echo $content;
		$html2pdf = new HTML2PDF('P', 'A4', 'fr', true);
		$html2pdf->writeHTML($Html);
        $html2pdf->Output('dividend_list.pdf','D');
        //exit();
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        //exit;
    }
    */

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
    	<div class="col-md-10">
    		<div class='row'>
				<?php include('accountsmenu.ini'); ?>
			</div>
		</div>
		<div class='col-md-10 col-md-offset-1'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
    	        	<center><h4><?php echo $PanelHeading; ?></h4></center>
				</div>
				<div class='panel-body'>
					<div class='row'>
						<table id='table0'>
							<tr style='background-color:brown;color:white'>
								<td><label>For Financial Year : </label></td>
								<td><label><?php echo $FinYear;?></label></td>
								<td>&nbsp;&nbsp;&nbsp;</td>
								<td><label>Dividend % : </label></td>
								<td><label><?php echo $Div1Per;?></label></td>
								<td><label>( <?php echo $Div2Per;?>) </label></td>
							</tr>
						</table>						
					</div>
					<div id='report' class='table-responsive'>
						<?php echo $Html; ?>
						<?php echo $ExtraHtml; ?>
					</div>
					<button id='createpdf1' class='btn btn-primary' onclick='js_excel1();'>EntireList</button>
					<button id='createpdf2' class='btn btn-primary' onclick='js_excel2();'>DeptwiseList</button>
					<button id='createpdf3' class='btn btn-primary' onclick='js_excel3();'>DeptSummary</button>
				</div>
			</div>
			<div class='panel-footer'>
			</div>
		</div>
	</div>
</body>
<script>
	var divper  = <?php echo $Div1Per; ?>;
	var div2per = <?php echo $Div2Per; ?>;
	var finyear = <?php echo $FinYear;?>;
	function js_excel1() {
		window.location.href='ex_dividend11.php?FinYear='+finyear+'&Div1Per='+divper+'&Div2Per='+div2per;
	}
	function js_excel2() {
		window.location.href='ex_dividend21.php?FinYear='+finyear+'&Div1Per='+divper+'&Div2Per='+div2per;
	}
	function js_excel3() {
		window.location.href='ex_dividend31.php?FinYear='+finyear+'&Div1Per='+divper+'&Div2Per='+div2per;
	}
</script>
</html>