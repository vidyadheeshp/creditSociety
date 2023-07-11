<?php
	// divdcalc.php
	// Author : Anand V Deshpande
	// Date Written : 17-11-2019

	session_start();
	include("../includes/functions.php");
	include("../includes/pdofunctions_v1.php");

	$db = connectPDO();

	$DivPer		= intval($_POST['divdper']);
	$FinYear  	= $_POST['finyear'];
	$DateStr1 	= "20".sprintf("%02d",substr($_POST['finyear'],0,2))."-04-01";
	$DateStr2 	= "20".sprintf("%02d",substr($_POST['finyear'],2,2))."-03-31";
	//echo "<br><br>";
	//echo $DateStr1;
	//echo substr($_POST['finyear'],1,2);
	CreateLog("inside divdcalc.php");
	$FromDate 	= date("Y-m-d",strtotime($DateStr1));
	$ToDate   	= date("Y-m-d",strtotime($DateStr2));
	//echo $FromDate;
	$TotalDividend = 0;
	$PanelHeading = "Dividend Calculation ".$DivPer."%". " From ".date("d-m-Y",strtotime($FromDate))." To ".date("d-m-Y",strtotime($ToDate));	
	$SQL = "Delete from dividends Where FromDate='$FromDate'";
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
	$Html .= "<th>Dividend</th>";
	$Html .= "</tr></thead><tbody>";
	$SerialNo=1;
	$Count=0;
	$IndDeb = 0;
	$IndCre = 0;
	$TotalOpenBal=0;
	$TotalClosBal=0;
	$TotDeb = 0;
	$TotCre = 0;
	$Rs = getResultSet($db,"Select * from shareholders");
	foreach($Rs as $MemberRow){
		$MemberID = $MemberRow['MemberID'];
		$Balance  = $MemberRow['OpenBal'];
		$DeptID   = $MemberRow['DeptID'];
		$Dept     = getSingleField($db,"Select ShName from departments where DeptID='$DeptID'");
		$OpenBal  = $Balance;
		$TotalOpenBal += $Balance;

		$Str = "Select SUM(Credit-Debit) From sharetrans Where MemberID='$MemberID' 
				AND TrnDate < '$FromDate'";
		$DebCre   = getSingleField($db,$Str);
		$Balance  = $Balance + $DebCre;

		$Dividend 	= 0;
		$IndDeb 	= 0;
		$IndCre 	= 0;
		$Str = "Select * from sharetrans Where MemberID='$MemberID' AND 
				TrnDate Between '$FromDate' and '$ToDate' Order By TrnDate ";
		$Rs2 = getResultSet($db,$Str);
		$ProcDate = $FromDate;
		$Trns=0;
		foreach($Rs2 as $TransRow){
			$date1 = strtotime($ProcDate);
			$date2 = strtotime($TransRow['TrnDate']);
			$diff = $date2-$date1;
			$diffDays = round($diff / 86400);
            $Dividend = $Dividend + intval($Balance * $DivPer * $diffDays / 36500);
            $ProcDate = $TransRow['TrnDate'];
            $Balance = $Balance - $TransRow['Debit'] + $TransRow['Credit'];			
            $Trns++;
			$IndDeb += $TransRow['Debit'];
			$IndCre += $TransRow['Credit'];
			$TotDeb += $TransRow['Debit'];
			$TotCre += $TransRow['Credit'];
		}
		$date1 = strtotime($ProcDate);
		$date2 = strtotime($ToDate);
		$diff = $date2-$date1;
		$diffDays = round($diff / 86400);
        $Dividend = $Dividend + intval($Balance * $DivPer * $diffDays / 36500);
		//echo $MemberRow['Name']." MemberID:".$MemberRow['MemberID']." Trans:".$Trns. " Balance ".$Balance. " Dividend ".$Dividend."<br>";
		
		// for saving data
		$Array = array("FinYear"=>$FinYear,"MemberID"=>$MemberID,"FromDate"=>$FromDate,"ToDate"=>$ToDate,"DivPer"=>$DivPer,"DivAmt"=>$Dividend,"OpenBal"=>$OpenBal,"Debits"=>$IndDeb,"Credits"=>$IndCre,"ClosBal"=>$Balance);
		$RetVal = insert($db,"dividends",$Array);
		$TotalDividend += $Dividend;
		$Html .= "<tr>";
		$Html .= "<td>".$SerialNo."</td>";
		$Html .= "<td>".$MemberID."</td>";
		$Html .= "<td>".$MemberRow['Name']."</td>";
		$Html .= "<td>".$Dept."</td>";
		$Html .= "<td align='right'>".$OpenBal."</td>";
		$Html .= "<td align='right'>".$IndDeb."</td>";
		$Html .= "<td align='right'>".$IndCre."</td>";
		$Html .= "<td align='right'>".$Balance."</td>";
		$Html .= "<td align='right'>".$Dividend."</td>";
		$Html .= "</tr>";
		$Count++;
		$SerialNo++;
	}
	$Html .= "<tr style='background-color:yellow;'>";
	$Html .= "<td></td>";
	$Html .= "<td></td>";
	$Html .= "<td>Total</td>";
	$Html .= "<td></td>";
	$Html .= "<td align='right'>".$TotalOpenBal."</td>";
	$Html .= "<td align='right'>".$TotDeb."</td>";
	$Html .= "<td align='right'>".$TotCre."</td>";
	$Html .= "<td align='right'>".$TotalClosBal."</td>";
	$Html .= "<td align='right'>".$TotalDividend."</td>";
	$Html .= "</tr></tbody></table>";
	$_SESSION['TempReport'] = $Html;
	CreateLog("Calculation Over");
	//echo $Html;
	//echo "Total Dividend ".$TotalDividend;
	$PanelHeading = "Dividend Calculation ".$DivPer."%". " From ".date("d-m-Y",strtotime($FromDate))." To ".date("d-m-Y",strtotime($ToDate));
	//echo $PanelHeading;
	// create PDF File
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
		<div class='col-md-8 col-md-offset-2'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
    	        	<center><h4><?php echo $PanelHeading; ?></h4></center>
				</div>
				<div class='panel-body'>
					<div id='report' class='table-responsive'>
						<?php echo $Html; ?>
					</div>
					<button id='createpdf1' class='btn btn-primary' onclick='js_createpdf1();'>PDF_EntireList</button>
					<button id='createpdf2' class='btn btn-primary' onclick='js_createpdf2();'>PDF_DeptSummary</button>
					<button id='createpdf3' class='btn btn-primary' onclick='js_createpdf3();'>PDF_DeptList</button>
				</div>
			</div>
			<div class='panel-footer'>
			</div>
		</div>
	</div>
</body>
<script>
	function js_dividendpdf1() {
		$.ajax({
	        type: "POST",
	        url: "ajax_dividendpdf1.php",
	        data: "DivdCalc=1",
	        success : function(text){
	        }
		});
	}
	function js_dividendpdf2() {
		$.ajax({
	        type: "POST",
	        url: "ajax_dividendpdf2.php",
	        data: "DivdCalc=1",
	        success : function(text){
	        }
		});
	}
	function js_dividendpdf3() {
		$.ajax({
	        type: "POST",
	        url: "ajax_dividendpdf3.php",
	        data: "DivdCalc=1",
	        success : function(text){
	        }
		});
	}
</script>
</html>