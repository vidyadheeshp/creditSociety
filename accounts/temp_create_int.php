
<?php
	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();

	//$SqlDel = "Delete from ft Where Month(TrnDate)=4 and Year(TrnDate)=2019 AND FMID in(27,28,29) 
	//		   AND Credit>0";
	$SqlDel = "Delete from ft Where FMID in(27,28,29) 
			   AND Credit>0 ";
	$stmt = $db->prepare($SqlDel);
   	$stmt->execute();	


   	/*
	$SqlDel = "Delete from ft Where Month(TrnDate)=4 and Year(TrnDate)=2019 AND FMID in(27,28,29) 
				AND TrnType='' AND Credit>0";
	$stmt = $db->prepare($SqlDel);
   	$stmt->execute();	
	*/

   		
	//$Sql = "Select * from ft Where Month(TrnDate)=4 and Year(TrnDate)=2019 AND LENGTH(LoanID)>0 
	//		AND FMID in(18,19,20) AND TrnType='' AND Credit>0 ";
	$Sql = "Select * from ft Where LENGTH(LoanID)>0 
			AND FMID in(18,19,20) AND TrnType='' AND (Credit>0 or Interest>0) ";
	$EditResultSet = getResultSet($db,$Sql);
	$db->BeginTransaction();
	foreach($EditResultSet as $EditRow){
		$FMID 		= $EditRow['FMID'];
		if($FMID==18) {
			$LoanAccID = 27;
		} elseif($FMID==19){
			$LoanAccID = 28;
		} elseif($FMID==20){
			$LoanAccID = 29;
		}

		$FixFMID 	= $EditRow['FixFMID'];
		$TrnDate 	= date("Y-m-d",strtotime($EditRow['TrnDate']));		
		$TrnType 	= $EditRow['TrnType'];
		$TrnNo 		= $EditRow['TrnNo'];
		$Particulars= $EditRow['LoanID'];
		$TrnCode  	= $EditRow['TrnCode'];
		$Credit 	= $EditRow['Interest'];
		$MemberID   = $EditRow['MemberID'];
		$LoanID    	= $EditRow['LoanID'];
		$FinYear    = $EditRow['FinYear'];	
		try {
			// 

			$PreStmt = 	"INSERT INTO ft(FMID,TrnCode,TrnType,TrnDate,TrnNo,Credit,Particulars,FinYear,MemberID,FixFMID) 
						VALUES(?,?,?,?,?,?,?,?,?,?)";
			//echo $PreStmt;
			$Array = array(
					$LoanAccID,$TrnCode,$TrnType,
					$TrnDate,$TrnNo,$Credit,
					$Particulars,$FinYear,$MemberID,
					$FixFMID);
			//print_r($Array);
			$stmt = $db->prepare($PreStmt); 
			$stmt->execute($Array);
			echo "Created for loan ".$TrnDate. ":". $LoanAccID. ":".$LoanID. ": Int ".$Credit. "<br>";

		} catch(PDOException $ex) {
		    //Something went wrong rollback!
		    $db->rollBack();

		    echo $ex->getMessage();
	 		echo "Something went wrong..";
			exit();
		}	
		catch(Exception $ex) {
		    $db->rollBack();

		    echo $ex->getMessage();
	 		echo "Something went wrong..";
			exit();
		}	
	}
	$db->commit();
	


	//$Sql = "Select FMID,FixFMID,TrnCode,TrnDate,FinYear,sum(Interest) as S_Interest from ft Where Month(TrnDate)=4 and Year(TrnDate)=2019 AND LENGTH(LoanID)>0 
	//		AND FMID in(18,19,20) AND TrnType='MthEMI'  Group By FMID,FixFMID,TrnCode,TrnDate,FinYear ";
	

	$Sql = "Select FMID,FixFMID,TrnCode,TrnDate,FinYear,sum(Interest) as S_Interest from ft Where LENGTH(LoanID)>0 
			AND FMID in(18,19,20) AND TrnType='MthEMI'  Group By FMID,FixFMID,TrnCode,TrnDate,FinYear ";
	$EditResultSet = getResultSet($db,$Sql);
	$db->BeginTransaction();
	foreach($EditResultSet as $EditRow){
		$FMID 		= $EditRow['FMID'];
		if($FMID==18) {
			$LoanAccID = 27;
		} elseif($FMID==19){
			$LoanAccID = 28;
		} elseif($FMID==20){
			$LoanAccID = 29;
		}

		//$FixFMID 	= $EditRow['FixFMID'];
		$TrnDate 	= date("Y-m-d",strtotime($EditRow['TrnDate']));		
		$TrnType 	= "MthEMI";
		$Particulars= "By MthEMI ";
		$TrnCode  	= $EditRow['TrnCode'];
		$Credit 	= $EditRow['S_Interest'];
		$FinYear    = $EditRow['FinYear'];	
		$FixFMID    = $EditRow['FixFMID'];
		try {
			// 

			$PreStmt = 	"INSERT INTO ft(FMID,TrnCode,TrnType,TrnDate,Credit,Particulars,FinYear,FixFMID) 
						VALUES(?,?,?,?,?,?,?,?)";
			//echo $PreStmt;
			$Array = array(
					$LoanAccID,$TrnCode,$TrnType,
					$TrnDate,$Credit,
					$Particulars,$FinYear,$FixFMID);
			print_r($Array);
			$stmt = $db->prepare($PreStmt); 
			$stmt->execute($Array);
			echo "Created for loan ".$LoanAccID. ":"." Int ".$Credit. "<br>";

		} catch(PDOException $ex) {
		    //Something went wrong rollback!
		    $db->rollBack();

		    echo $ex->getMessage();
	 		echo "Something went wrong..";
			exit();
		}	
		catch(Exception $ex) {
		    $db->rollBack();

		    echo $ex->getMessage();
	 		echo "Something went wrong..";
			exit();
		}	
	}
	$db->commit();


