<?php
	// gitsociety
	// Author : Anand V Deshpande, Belagavi
	// Started : 27.10.2019
	// ajax_getfmwithsubaccs.php
	// modified for LoanType
	session_start();
	require_once("../includes/functions.php");	
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();
	$FMID	= $_POST['fmid'];
	$sql = "Select ClosBal from fm Where FMID='$FMID' LIMIT 1";
	$ClosBal = getSingleField($db,$sql);
	$ConvAmt = ConvBalance($ClosBal);
	$LoanType = "";
	$sql = "Select AcType from fm Where FMID='$FMID' LIMIT 1";
	$AcType = getSingleField($db,$sql);
	CreateLog("Inside ajax_getfmwithsubaccs AcType: $AcType fmid $FMID ");
	if($AcType=='SC'){
		$SubAccList = genShareHoldersSelect($db);   //genShareHoldersSelect($db);
		//CreateLog($SubAccList);
		//$SubAccList = "Anand";
	} elseif($AcType=='Cust'){
		$SubAccList = genCustSelect($db,$FMID); //genCustSelect($db,$FMID);
		$LoanType = getSingleField($db,"Select LoanType from loansettings Where LoanFMID='$FMID'");
	} else{
		$SubAccList = "";
	}
echo json_encode(array('AcType'=>$AcType,'ConvAmt'=> $ConvAmt,'ClosBal'=> $ClosBal,'LoanType'=>$LoanType,'SubAccList'=>$SubAccList), JSON_FORCE_OBJECT);

