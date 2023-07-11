<?php
	// gitsociety
	// Author : Anand V Deshpande, Belagavi
	// Started : 27.10.2019
	// ajax_getloandets.php
	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();
	$LoanID		= $_POST['LoanID'];
	$TrnCode 	= $_POST['TrnCode'];
	$TrnAmount  = intval($_POST['Amount']);
	$TrnDate    = date("Y-m-d",strtotime($_POST['TrnDate']));
	$IntUptoDt  = date("Y-m-d",strtotime($_POST['IntUptoDt']));

	CreateLog("Cust: $LoanID $TrnCode $IntUptoDt $TrnAmount ");
	$Interest  	= 0;
	$Principal  = 0;
	
	$ResultCust = getResultSet($db,"Select * from customers Where LoanID='$LoanID' LIMIT 1");
	foreach($ResultCust as $Cust){
		$IntRate 	 = $Cust['IntRate'];
		$LastRecDate = $Cust['LastRecDate'];
		$LoanDate    = $Cust['LoanDate'];
		$ClosBal     = $Cust['ClosBal'];
		$MthEMI 	 = $Cust['MthEMI'];
	}
	CreateLog("Cust: $IntRate $LastRecDate $LoanDate $ClosBal ");
	$ConvAmt 	= ConvBalance($ClosBal);
	
	if($TrnCode=='REC' and $ClosBal<=0){
		if(is_null($LastRecDate)) {
			$Days = getDaysDiff($LoanDate,$IntUptoDt) ;
			CreateLog("Cust: int calc TrnDate $TrnDate LoanDate $LoanDate IntUptoDt $IntUptoDt $Interest days: $Days");
		} else{
			$Days = getDaysDiff($LastRecDate,$IntUptoDt);
		CreateLog("Cust: int calc  Trndate $TrnDate LastRecDate $LastRecDate IntUptoDt $IntUptoDt $Interest days: $Days");
		}
		$Interest = round( ($ClosBal*-1) * $IntRate * $Days / 36500,0);
		//CreateLog("Cust: int calc $Interest days: $Days");
		/* just calculate and show
		if($TrnAmount >$Interest){
			$Principal = $TrnAmount - $Interest;
		} else{
			$Principal = 0;
			$Interest  = $TrnAmount;
		}
		*/
	}
	CreateLog("Cust: Int-> $Principal  $Interest ");
	$LastRecDate = date("d-m-Y",strtotime($LastRecDate)) ;
	echo json_encode(array('ConvAmt'=> $ConvAmt,
			'ClosBal'  => $ClosBal,
			'Interest' => $Interest,
			'Days'     => $Days,
			'LastRecDate' => $LastRecDate,
			'MthEMI'   => $MthEMI), JSON_FORCE_OBJECT);	
?>