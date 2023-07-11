<?php
	// Author: Anand V Deshpande
	// Date Written : 08.11.2019
	// sharetrans_del.php
	// called from sharetrans_list.php

	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$db  	= connectPDO();
	
	$TranID = filter_input(INPUT_POST, 'TranID',FILTER_SANITIZE_NUMBER_INT);
	if($TranID<=0) {
		echo "Sorry: Invalid Transaction ID";
		exit();
	}
	CreateLog("Inside sharetrans_del.php for deleting TranID:$TranID");

	// code to be written
	try {

		$TranRow = getResultSet($db,"Select * from sharetrans Where TranID='$TranID' LIMIT 1");
		foreach($TranRow as $Row){
			$TrnCode    = $Row['TrnCode'];
			$TrnDate    = date("Y-m-d",strtotime($Row['TrnDate']));
			$TrnType	= $Row['TrnType'];
			$MemberID 	= $Row['MemberID'];
			$Debit 		= $Row['Debit'];
			$Credit  	= $Row['Credit'];
			$DebitAccID = $Row['DebitAccID'];
			$CreditAccID= $Row['CreditAccID'];
			$TrnAmt 	= $Row['TrnAmt'];

			$db->BeginTransaction();
        	$stmt = $db->prepare( "DELETE FROM sharetrans WHERE TranID=:id" );
        	$stmt->bindParam(':id', $TranID);
        	$stmt->execute();
        	if( ! $stmt->rowCount() ) {
        		echo "Deletion failed";
        		exit();
        	}
        	CreateLog("Deleted sharetrans TranID:$TranID");
        	$Where = "MemberID='$MemberID'";
			$RetValue 		= updateShareHolders($db,$Where,-$Debit,-$Credit);
			CreateLog("Updated ShareHolders $MemberID deleting TranID:$TranID");
			// process debit amount

			$Qury  = "Select count(*) from ft Where TrnCode='$TrnCode' AND TrnDate='$TrnDate' 
				AND Debit>=$TrnAmt AND FMID = '$DebitAccID'";
			CreateLog($Qury);
			$Count = getSingleField($db,$Qury);
			if($Count>0){
				$Qury = "Select * from ft Where TrnCode='$TrnCode' AND TrnDate='$TrnDate' AND 
					Debit>=$TrnAmt AND FMID = '$DebitAccID' ";
				CreateLog($Qury);
				$ResultSet = getResultSet($db,$Qury);
				foreach($ResultSet as $row){
					if($row['Debit']>=$TrnAmt){
						$TempFTRowID = $row['FTID'];
						$RetValue = update($db,"ft",array("Debit"=>$row['Debit']-$TrnAmt),"FTID='$TempFTRowID'");
						CreateLog("Updated Ft $TempFTRowID AccID: $DebitAccID Debit: $TrnAmt TranID:$TranID");
						break;
					}
				}
			}
			// process credit amount
			$Qury = "Select count(*) from ft Where TrnCode='$TrnCode' AND TrnDate='$TrnDate' 
				AND Credit>=$TrnAmt AND FMID = '$CreditAccID' ";
			CreateLog($Qury);
			$Count = getSingleField($db,$Qury);
			if($Count>0){
				$Qury = "Select * from ft Where TrnCode='$TrnCode' AND TrnDate='$TrnDate' AND 
					Credit>=$TrnAmt AND FMID = '$CreditAccID' ";
				CreateLog($Qury);
				$ResultSet = getResultSet($db,$Qury);
				foreach($ResultSet as $row){
					if($row['Credit']>=$TrnAmt){
						$TempFTRowID = $row['FTID'];
						$RetValue = update($db,"ft",array("Credit"=>$row['Credit']-$TrnAmt),"FTID='$TempFTRowID'");
						CreateLog("Updated Ft $TempFTRowID AccID: $CreditAccID Credit: $TrnAmt TranID:$TranID");
						break;
					}
				}
			}

			// update fm

			$Where 		= "FMID=".$DebitAccID;
			$RetValue 	= updateFM($db,$Where,-$TrnAmt,0);
			CreateLog("Updated FM AccID: $DebitAccID Debit: $Debit TranID:$TranID");

			$Where 		= "FMID=".$CreditAccID;
			$RetValue 	= updateFM($db,$Where,0,-$TrnAmt);
			CreateLog("Updated FM AccID: $CreditAccID Credit: $Credit TranID:$TranID");
			
			$db->commit();
			// create entry in logfile
			$LogDesc = "ShareTran Deleted MemberID:$MemberID TranID:$TranID $TrnDate Deb:$Debit Cr:$Credit ";
			CreateLog("Successfully Deleted ");
			$LogArray = array("UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc);
			$RetValue = insert($db,"logfile",$LogArray);

	 		echo "<script type='text/javascript'>alert('Successfully Deleted..');
				window.location='sharetrans_list.php';</script>";	   					
			exit();
		} 
	} catch(PDOException $ex) {
		    //Something went wrong rollback!
		    $db->rollBack();

		    $Msg = $ex->getMessage();
		    CreateLog("Error deleting ShareTran MemberID:$MemberID TranID:$TranID $TrnDate Deb:$Debit Cr:$Credit");
	 		echo "<script type='text/javascript'>alert('Something went wrong..');
				window.location='sharetrans_list.php';</script>";	   					
			exit();
	} catch(Exception $ex) {
		    $db->rollBack();
		    $Msg = $ex->getMessage();
		    CreateLog("Error deleting ShareTran MemberID:$MemberID TranID:$TranID $TrnDate Deb:$Debit Cr:$Credit");
	 		echo "<script type='text/javascript'>alert('Something went wrong..');
				window.location='sharetrans_list.php';</script>";	   					
			exit();
	}	
	
?>
