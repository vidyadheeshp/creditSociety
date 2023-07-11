<?php
	// Author: Anand V Deshpande
	// Date written : 09.11.2019
	//ajax_loanaccount_del.php
	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();

	$LoanID   	= $_POST['LoanID'];

	$LoanRecSet = getResultSet($db,"Select * from customers Where LoanID='$LoanID'");
	$Header = "";
	$Body = "";

	foreach ($LoanRecSet as $LoanRow) {
		$MemberID 	= $LoanRow['MemberID'];
		$MemberName = getSingleField($db,"Select Name from shareholders Where MemberID='$MemberID'");
		$Header = "<center><strong>KLS GIT Employees Co-Op Credit Society Ltd.</strong><center><br>";
		$Header .= "Loan Ledger: <strong>".$LoanID . "  ".$MemberName. " MemberID:".$MemberID." </strong>As On ".date("d-m-Y")."<br>";
		if($LoanRow['OpenBal']==0 and $LoanRow['ClosBal']==0 and $LoanRow['Debits']==0 and $LoanRow['Credits']==0){
			$db->BeginTransaction();
			try {
				$SQL = "Delete from customers Where LoanID='$LoanID'";
		      	$stmt = $db->prepare($SQL);
		      	$stmt->execute();
			    CreateLog("Deleted LoanID $LoanID ");
			    $Body = "Successfully Deleted $LoanID ";
			    $db->commit();
		   	} catch (Exception $ex) {
			    $db->rollBack();
			    $Msg = $ex->getMessage();
			    CreateLog("Error while Deleting LoanID $LoanID");
		 		$Body = "Something went wrong..while deleting";	   					
		  	}		
		} else{
			$Body = "Cannot Delete $LoanID : Has Balance/Transactions ";
		}
	}
	echo json_encode(array('Header'=>$Header,'Body'=> $Body), JSON_FORCE_OBJECT);	
?>
