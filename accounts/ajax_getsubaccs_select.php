<?php
	// gitsociety
	// Author : Anand V Deshpande, Belagavi
	// Started : 27.10.2019
	// ajax_getsubaccslist.php
	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	CreateLog("Inside ajax_getsubaccs_select.php");
	$db = connectPDO();
	$FMID	= $_POST['fmid'];
	$AcType = $_POST['actype'];
	if($AcType=='SC'){
		echo genShareHoldersSelect($db);
	} elseif($AcType=='Loans'){
		echo genLoansSelect($db,$FMID);
	} else{
		echo "";
	}
?>