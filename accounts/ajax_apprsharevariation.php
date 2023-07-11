<?php
	// gitsociety
	// Author : Anand V Deshpande, Belagavi
	// Started : 22.12.2019
	// ajax_apprsharevariation.php
	// Change status and Update MthContr in shareholders table
	session_start();
	require_once("../includes/pdofunctions_v1.php");
	require_once("../includes/functions.php");
	$db = connectPDO();
	$RowID 	= $_POST['RowID'];

	CreateLog("Inside ajax sharevariation RowID $RowID");
	$sql = "Select A.*,B.Name,B.DOR 
		from sharevariations A, shareholders B Where A.RowID='$RowID' AND A.MemberID=B.MemberID 
		";
	CreateLog($sql);
	$ResultSet = getResultSet($db,$sql);
	foreach($ResultSet as $row) {
		try {
			// 
			$db->BeginTransaction();
			$MemberID 	= $row['MemberID'];
			$PrevContr 	= $row['PrevContr'];
			$NewContr 	= $row['NewContr'];
			$Array 		= array("MthContr"=>$NewContr);
			$Where 		= "MemberID='$MemberID'";
			$RetVal 	= update($db,"shareholders",$Array,$Where);

			$Where 		= "RowID='$RowID'";
			$Status 	= 'Approved';
			$Array 		= array("Status"=>$Status);
			$RetVal 	= update($db,"sharevariations",$Array,$Where);

			$LogDesc 	= "ShareVariations Approved MemberID:$MemberID Prev:$PrevContr New:$NewContr ";
			$RetVal 	= insert($db,"logfile",array("LogType"=>'ShareVari',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));	
			$db->commit();
			$Response = "Success";
		} catch (PDOException $ex) {
		    $db->rollBack();
		    $Msg = $ex->getMessage();
			$LogDesc = "ShareVariation Error encountered RowID:".$RowID. " MemberID:$MemberID Msg: $Msg";
			CreateLog($LogDesc);
			$RetVal 	= insert($db,"logfile",array("LogType"=>'ShareVari',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));	
			$Response = "Failure to Update";
		}
	}
	echo json_encode(array('Response'=>$Response), JSON_FORCE_OBJECT);
	exit();
?>