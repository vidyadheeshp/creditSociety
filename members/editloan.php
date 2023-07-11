<?php
session_start();
require_once("../includes/functions.php");
    

// check usertype
if(!isset($_SESSION['UserType'])) {

    MsgBox("$refPage Direct script access prohibited","../index.php",True);
    
    exit();
}
$UserType 	= $_SESSION['UserType'];
if(strstr('Member',$UserType)){

} else{
    MsgBox("Access for Authorised Users only","membershome.php",True);
    exit();
}
//	
//require_once("../includes/functions.php");
require_once("../includes/pdofunctions_v1.php");
$PanelHeading 		= "Edit Loan Application";
$db = connectPDO();
$MemberID 	= $_SESSION['MemberID'];
$MemberName = $_SESSION['UserName'];
$approvedOn="";
$G1Approved="";
$G2Approved="";
if(isset($_POST['loanamt']) and isset($_POST['Submit'])) {
		//echo "Inside Post...";
		//print_r($_POST);
		$LoanFMID   		= filter_input(INPUT_POST,'loanfmid',FILTER_SANITIZE_NUMBER_INT);
		$Data['MthEMI']		= filter_input(INPUT_POST,'mthemi',  FILTER_SANITIZE_NUMBER_INT);
		$Data['MemberID'] 	= filter_input(INPUT_POST,'memberid',FILTER_SANITIZE_STRING);
		$Data['LoanAmt']  	= intval($_POST['loanamt']);
		$Data['IntRate']  	= floatval($_POST['intrate']);
		$Data['Particulars']= filter_input(INPUT_POST,'particulars',FILTER_SANITIZE_STRING);
		$Data['G1MemberID'] = $_POST['g1memberid'];
		$Data['G2MemberID'] = $_POST['g2memberid'];
		$Data['Months'] 	= intval($_POST['months']);
		$LoanID=$_POST['LoanID'];
		$G1MemberID=getSingleField($db,"select G1MemberID from loanappl where LoanID=$LoanID");
		$G2MemberID=getSingleField($db,"select G2MemberID from loanappl where LoanID=$LoanID");
		
		
		//echo $_POST['months'];
		
		try {
				// 
			$db->BeginTransaction();
			$PreStmt =$PreStmt = 	"update loanappl set FMID=$LoanFMID, Description='".$Data['Particulars']."', LoanAmt=".$Data['LoanAmt'].",
			IntRate=".$Data['IntRate'].", MthEMI=".$Data['MthEMI'].", G1MemberID='".$Data['G1MemberID']."', 
			G2MemberID='".$Data['G2MemberID']."',Months=".$Data['Months']." where LoanID='$LoanID'"; 
			if($G1MemberID!=$Data['G1MemberID']){
				
				$PreStmt = 	"update loanappl set FMID=$LoanFMID, Description='".$Data['Particulars']."', LoanAmt=".$Data['LoanAmt'].",
			 IntRate=".$Data['IntRate'].", MthEMI=".$Data['MthEMI'].", G1MemberID='".$Data['G1MemberID']."', 
			 G2MemberID='".$Data['G2MemberID']."',G1Approved='',Months=".$Data['Months']." where LoanID='$LoanID'"; 
			}
			if($G2MemberID!=$Data['G2MemberID']){
				
				$PreStmt = 	"update loanappl set FMID=$LoanFMID, Description='".$Data['Particulars']."', LoanAmt=".$Data['LoanAmt'].",
			 IntRate=".$Data['IntRate'].", MthEMI=".$Data['MthEMI'].", G1MemberID='".$Data['G1MemberID']."', 
			 G2MemberID='".$Data['G2MemberID']."',G2Approved='',Months=".$Data['Months']." where LoanID='$LoanID'"; 
			}
			
		
			 $Array=array();
			
			 $stmt=$db->prepare($PreStmt);
			$stmt->execute();
			
			$affected_rows 	= $stmt->rowCount();
			$InsertID 		= $db->lastInsertId();
			$LogDesc = "Loan Appl edited $LoanFMID $MemberID $MemberName";
			$RetVal = insert($db,"logfile",array("LogType"=>'LNAppEdit',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));					
			$db->commit();
	 		echo "<script type='text/javascript'>alert('Successfully Edited..');
			window.location='membershome.php';</script>";	   					
			exit();
		} catch(PDOException $ex) {
		    //Something went wrong rollback!
		    $db->rollBack();

		    echo $ex->getMessage();
		    $Msg = $ex->getMessage();
		    CreateLog($Msg);
	 		//echo "<script type='text/javascript'>alert('Something went wrong..');
			//	window.location='getloanaccounts_list.php';</script>";	   					
			exit();
		}
		catch(Exception $ex) {
		    $db->rollBack();
		    $Msg = $ex->getMessage();
		    CreateLog($Msg);
	 		echo "<script type='text/javascript'>alert('Something went wrong..');
				window.location='membershome.php';</script>";	   					
			exit();
		}	
	}
    ?>