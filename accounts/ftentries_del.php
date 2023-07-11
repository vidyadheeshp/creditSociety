<?php

// ftentries_list.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 21.11.2019
//	
session_start();
require_once("../includes/functions.php");
// check usertype
	if(!isset($_SESSION['UserType'])) {
		MsgBox("Direct script access prohibited","../index.php",True);
		exit();
	}
	$UserType 	= $_SESSION['UserType'];
	if(strstr('Accounts,Admin',$UserType)){

	} else{
		MsgBox("Access for Authorised Users only","accountsmenu.php",True);
		exit();
	}
//
require_once("../includes/pdofunctions_v1.php");
$db = connectPDO();
MsgBox("Inside php del function 1");
if(isset($_POST['ftid'])){
	$FTID = $_POST['ftid'];
	MsgBox("Inside del php function-2 : $FTID","",False);

	$TrnNo = getSingleField($db,"Select TrnNo from ft Where FTID='$FTID'");
	$TrnDate = getSingleField($db,"Select TrnDate from ft Where FTID='$FTID'");
	$FinYear = getSingleField($db,"Select FinYear from ft Where FTID='$FTID'");
	$TrnCode = getSingleField($db,"Select TrnCode from ft Where FTID='$FTID'");
	// delete records
	$ResultSet = getResultSet($db,"Select * from ft Where FinYear='$FinYear' AND 
		TrnCode='$TrnCode' AND TrnNo='$TrnNo'");
	$db->BeginTransaction();
	$Entries=0;
	foreach($ResultSet as $row){
		$FMID 	= $row['FMID'];
		$FTID 	= $row['FTID'];
		$Debit 	= $row['Debit'];
		$Credit = $row['Credit'];
		$SQL = "Delete from ft Where FTID='$FTID'";
		try {
	      	$stmt = $db->prepare($SQL);
	      	$stmt->execute();
			$Where 		= "FMID=".$FMID;
			$RetValue 	= updateFM($db,$Where,$Debit,$Credit);
		    CreateLog("Deleted AccEntry $FinYear $TrnCode $TrnNo Db:$Debit Cr:$Credit AccID:$FMID");
			$LogDesc = "Accounts entry Deleted $FinYear $TrnDate $TrnCode $TrnNo Dr:$Debit Cr:$Credit AccID:$FMID TrnID:$FTID ";
			$RetVal = insert($db,"logfile",array("LogType"=>'NewMember',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));		    
		    $Entries++;
	   	} catch (Exception $ex) {
		    $db->rollBack();
		    $Msg = $ex->getMessage();
		    CreateLog("Error while Deleting $FinYear $TrnCode $TrnNo FTID:$FTID FMID:$FMID");
	 		echo "<script type='text/javascript'>alert('Something went wrong..');
				window.location='ftentries_list.php';</script>";	   					
			exit();
	  	}		
	}
	$LogDesc = "Accounts Deleted Entries($Entries) $FinYear $TrnDate $TrnCode $TrnNo";
	$RetVal = insert($db,"logfile",array("LogType"=>'NewMember',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));		
	$db->commit();
	echo "Transaction Deleted $Entries Entries Successfully";
}
