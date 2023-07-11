<?php
	// bankentries_del.php
	// Author : Anand V Deshpande,Belagavi
	// Date Written: 26.11.2019
		
	session_start();
	require_once("../includes/functions.php");

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
	$UserID = $_SESSION['UserID'];
	//require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$PanelHeading = " Bank Transactions";

	$db = connectPDO();

	$FTID = intval($_POST['FTID']);
	if($FTID<=0) {
		echo "Error ";
		exit();
	}
	$ResultSet = getResultSet($db,"Select * from ft Where FTID='$FTID'");
	foreach($ResultSet as $row){
		$FinYear = $row['FinYear'];
		$TrnDate = date("Y-m-d",strtotime($row['TrnDate']));
		$TrnCode = $row['TrnCode'];
		$TrnNo   = $row['TrnNo'];
	}
	// now delete entries with trncode,trndate,trnno
	$ResultSet = getResultSet($db,"Select * from ft 
				Where FTID='$FTID' AND TrnDate='$TrnDate' AND TrnCode='$TrnCode' AND TrnNo='$TrnNo'");
	$db->BeginTransaction();
	foreach($ResultSet as $row){
		// now delete each entry
		try {
			$FTID 		= $row['FTID'];
			$FMID 		= $row['FMID'];
			$FixFMID    = $row['FixFMID'];
			$MemberID   = $row['MemberID'];
			$LoanID 	= $row['LoanID'];
			$Debit      = $row['Debit'];
			$Credit 	= $row['Credit'];
			$Where 		= "FMID=".$FMID;
			$RetValue 	= updateFM($db,$Where,($Debit*-1),($Credit*-1));

			$Where 		= "FMID=".$FixFMID;
			$RetValue 	= updateFM($db,$Where,($Credit*-1),($Debit*-1));		

			if(strlen($MemberID)==6){
				$Where = "MemberID='".$MemberID."'";
				updateShareHolders($db,$Where,($Debit*-1),($Credit*-1));
			}
			if(strlen($LoanID)==6){
				$Where = "LoanID='".$LoanID."'";
				updateCustomers($db,$Where,($Debit*-1),($Credit*-1));
				if($TrnCode=='REC'){
					// update last rec date with customers
					//$LastRecDate = date("Y-m-d",strtotime($_POST['IntUptoDt']));
					//$sql  = "UPDATE customers Set LastRecDate='$LastRecDate' Where $Where";
					//CreateLog($sql);
					//$stmt = $db->prepare($sql);
				    //$stmt->execute();					
				}
			}

			$sql  = "Delete from ft Where FTID='$FTID'";
			$stmt = $db->prepare($sql);
	      	$stmt->execute();
			$LogDesc = "Accounting $TrnDate  $TrnCode TrnNo: $TrnNo FMID:$FMID FixFMID:$FixFMID deleted Dr $Debit Cr: $Credit MemberID:$MemberID Loan:$LoanID User: $UserID ";
			$RetVal = insert($db,"logfile",array("LogType"=>'BankEntry',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));	
			CreateLog($LogDesc);	      	
		} catch(PDOException $ex) {
		    //Something went wrong rollback!
		    $db->rollBack();

		    $Msg = $ex->getMessage();
			$LogDesc = "Accounting Error encountered while deleting FTID $FTID FMID $FMID FIXFMID $FixFMID User:".$UserID ." ".$Msg;
			CreateLog($LogDesc);
	 		MsgBox("Something went wrong..","",True);
			exit();
		} catch (Exception $ex) {
	      	$Msg = $ex->getMessage();
	      	$db->rollBack();
	      	MsgBox("Something went wrong during delete","",True);
	      	exit();
	  	}	
	}
	$LogDesc = "Accounting above all entries deleted User: $UserID ";
	$RetVal = insert($db,"logfile",array("LogType"=>'BankEntry',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));	
	CreateLog($LogDesc);	      	
	$db->commit();
	echo "Successfully deleted : Please update Last Receipt Date in Loan Account";
?>
