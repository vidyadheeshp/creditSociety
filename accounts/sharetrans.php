<?php

// sharetrans.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 04.11.2019
//	
	session_start();
	require_once("../includes/functions.php");

	if(!isset($_SESSION['UserType'])) {
		MsgBox("Direct script access prohibited","../index.php",True);
		exit();
	}
	
	$UserType 	= $_SESSION['UserType'];
	if($UserType == 'Shares' or $UserType=='Admin'){

	} else{
		MsgBox("Access for Authorised Users only","sharetrans_list",True);
		exit();
	}
	//require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$PanelHeading = " Share Holder Transactions";


	$db 			   	= connectPDO();

	$DebitAccList     	= genSelectFM($db,"debitaccid",1," required ");
	$CreditAccList    	= genSelectFM($db,"creditaccid",1," required ");	
	$Mode 				= "Add";
	$Data 				= array();
	$Data['UserID'] 	= $_SESSION['UserID'];
	$Data['TranID'] 	= 0;
	$Data['SHID']		= 0;
	$Data['MemberID'] 	= "";
	$Data['Name']		= "";
	$Data['TrnDate']  	= date("Y-m-d");
	$Data['TrnType']  	= "";
	$Data['TrnCode']  	= "";
	$Data['Debit']    	= "";
	$Data['Credit'] 	= "";
	$Data['Particulars']= "";
	$Data['Status'] 	= "";
	$Data['ClosBal'] 	= 0;
	$Data['DebitAccID'] = 0;
	$Data['CreditAccID']= 0;
	$ShareHoldersList   = genShareHoldersSelect($db);

	if(isset($_POST['hiddenTranID']) and isset($_POST['hidden_type'])) {
		if($_POST['hidden_type']=="Edit"){
			MsgBox("Edit transaction tranid ".$_POST['hiddenTranID'],"",true);
			$Mode 			= "Edit";
			$TranID 		= $_POST['hiddenTranID'];
			$PanelHeading 	= "Edit Share Holder Transaction";
			$result = getResultSet($db,"Select * from sharetrans Where TranID='$TranID' LIMIT 1");
			foreach($result as $row) {
				$MemberID    		= $row['MemberID'];
				$Data['MemberID'] 	= $row['MemberID'];
				$Data['TranID'] 	= $row['TranID'];
				$Data['SHID']		= $row['SHID'];
				$Data['Name']   	= getSingleField($db,"Select Name from shareholders Where MemberID='$MemberID'");
				$Data['TrnDate']	= date("Y-m-d",strtotime($row['TrnDate']));
				$Data['TrnType']	= $row['TrnType'];
				$Data['TrnCode']	= $row['TrnCode'];
				$Data['Debit']		= $row['Debit'];
				$Data['Credit'] 	= $row['Credit'];
				$Data['Particulars']= $row['Particulars'];
				$Data['Status'] 	= $row['Status'];
				$Data['ClosBal'] 	= getSingleField($db,"Select ClosBal from shareholders Where MemberID='$MemberID'");
				$Data['TrnAmt'] 	= $row['TrnAmt'];
				$Data['DebitAccID'] = $row['DebitAccID'];
				$Data['CreditAccID']= $row['CreditAccID'];
				print_r($Data);
			}
		}
	}else if(isset($_POST['hidden_type'])) {
		if($_POST['hidden_type']=="Add") {
			$TranID = 0;
			$Data['TranID']=0;
			$PanelHeading = "New Share Transaction";
			MsgBox("Add new transaction","",true);
		}
	}
	print_r($_POST);
	if(isset($_POST['trndate']) and isset($_POST['submit'])) {
		//MsgBox("Inside Post...","",true);
		//print_r($_POST);
		$Input_TranID       = 	filter_input(INPUT_POST,'trainid',FILTER_SANITIZE_NUMBER_INT);
		$Data['MemberID']	=  	filter_input(INPUT_POST,'memberid',FILTER_SANITIZE_STRING);
		$Data['TrnType'] 	= 	filter_input(INPUT_POST,'trntype',FILTER_SANITIZE_STRING);
		$Data['TrnCode']	=	filter_input(INPUT_POST,'trncode',FILTER_SANITIZE_STRING);
		$Data['Particulars']= 	filter_input(INPUT_POST,'particulars',FILTER_SANITIZE_STRING);
		$Data['TranDate']	=	date("Y-m-d",strtotime(filter_input(INPUT_POST,'dob',FILTER_SANITIZE_STRING)));
		$Data['Debit']		=	filter_input(INPUT_POST,'debit',FILTER_SANITIZE_NUMBER_INT);
		$Data['Credit'] 	=	filter_input(INPUT_POST,'credit',FILTER_SANITIZE_NUMBER_INT);
		$Data['DebitAccID']	=	filter_input(INPUT_POST,'debitaccid',FILTER_SANITIZE_NUMBER_INT);
		$Data['CreditAccID']=	filter_input(INPUT_POST,'creditaccid',FILTER_SANITIZE_NUMBER_INT);
		$Data['Status']     = "OK";
		if($Data['Debit']>0) {
			$Data['TrnAmt'] = $Data['Debit'];
			$Data['TrnCode'] = "PMT";
		}else{
			$Data['TrnAmt'] = $Data['Credit'];
			$Data['TrnCode'] = "REC";
		}

		if($Input_TranID==0) {
			// insert new record
			try {
				// 
				$MemberID 		= $Data['MemberID'];
				$Data['FinYear']= genFinYear($Data['TrnDate']); 
				$db->BeginTransaction();
				$SHID    = getSingleField($db,"Select SHID from shareholders Where MemberID='$MemberID'");
				$PreStmt = 	"INSERT INTO sharetrans(SHID,MemberID,TrnCode,TrnType,TrnDate,Debit,Credit,Particulars,UserID,TrnAmt,DebitAccID,CreditAccID,Status,FinYear) 
					VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				echo $PreStmt;
				$stmt 	= $db->prepare($PreStmt); 
				$Array 	= array(
						$SHID,$Data['MemberID'],$Data['TrnCode'],$Data['TrnType'],
						$Data['TrnDate'],$Data['Debit'],$Data['Credit'],$Data['Particulars'],
						$Data['UserID'],$Data['TrnAmt'],$Data['DebitAccID'],$Data['CreditAccID'],
						$Data['Status'],$Data['FinYear']);
				$stmt->execute($Array);
				//print_r($Array);
				$affected_rows 	= $stmt->rowCount();
				//echo "Aff.rows: ".$affected_rows;
				if($affected_rows<=0) {
					CreateLog("ShareTrans couldnot insert $MemberID $Array ");
					$db->rollBack();
			 		echo "<script type='text/javascript'>alert('Something went wrong..');
						window.location='sharetrans_list.php';</script>";	   					
					exit();
				}
				CreateLog("ShareTrans inserted $MemberID");
				$InsertID 		= $db->lastInsertId();
				$Where 			= "MemberID='$MemberID'";
				$RetValue 		= updateShareHolders($db,$Where,$Data['Debit'],$Data['Credit']);

				// create two ft records
				$Data['TrnNo'] 	= genMaxTrnNo($db,$Data['TrnCode'],$Data['FinYear'],"ft");
				$PreStmt = 	"INSERT INTO ft(FMID,TrnCode,TrnType,TrnDate,Debit,Credit,Particulars,UserID,TrnNo,FinYear) 
					VALUES(?,?,?,?,?,?,?,?,?,?)";
				//echo $PreStmt;
				$Array = array(
						$Data['DebitAccID'],$Data['TrnCode'],$Data['TrnType'],
						$Data['TrnDate'],$Data['TrnAmt'],0,
						$Data['Particulars'],$Data['UserID'],$Data['TrnNo'],$Data['FinYear']);
				CreateLog(implode(",",$Array));
				$stmt = $db->prepare($PreStmt); 
				$stmt->execute($Array);
				//echo "ft1";
				$PreStmt 	= 	"INSERT INTO ft(FMID,TrnCode,TrnType,TrnDate,Debit,Credit,Particulars,UserID,TrnNo,FinYear) 
					VALUES(?,?,?,?,?,?,?,?,?,?)";
				//echo $PreStmt;
				$stmt 	= $db->prepare($PreStmt); 
				$Array 	= array(
						$Data['CreditAccID'],$Data['TrnCode'],$Data['TrnType'],
						$Data['TrnDate'],0,$Data['TrnAmt'],
						$Data['Particulars'],$Data['UserID'],$Data['TrnNo'],$Data['FinYear']);
				//print_r($Array);
				$stmt->execute($Array);
				//echo "ft2";
				// update fm
				CreateLog(implode(",",$Array));
				$Where 		= "FMID=".$Data['DebitAccID'];
				$RetValue 	= updateFM($db,$Where,$Data['TrnAmt'],0);
				$Where 		= "FMID=".$Data['CreditAccID'];
				$RetValue 	= updateFM($db,$Where,0,$Data['TrnAmt']);

				$LogDesc = "Shares Tran Added Member: $MemberID". $Data['TrnCode']." ". $Data['TrnDate']." ". $Data['TrnAmt'];
				$RetVal = insert($db,"logfile",array("LogType"=>'NewMember',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));				
				$db->commit();

		 		echo "<script type='text/javascript'>alert('Successfully Saved..');
					window.location='sharetrans_list.php';</script>";	   					
				exit();
			} catch(PDOException $ex) {
			    //Something went wrong rollback!
			    $db->rollBack();

			    echo $ex->getMessage();
		 		echo "<script type='text/javascript'>alert('Something went wrong..');
					window.location='sharetrans_list.php';</script>";	   					
				exit();
			}	
			catch(Exception $ex) {
			    $db->rollBack();
			    echo $ex->getMessage();
		 		echo "<script type='text/javascript'>alert('Something went wrong..');
					window.location='sharetrans_list.php';</script>";	   					
				exit();
			}	
			// here all the sql commands have executed correctly
			// so commit changes to the database
			exit();
		} else {
			// Editing to be implemented 
			// Can be done later on 07.11.2019
			// edit existing record
			echo "Editing record";
			// save tranid 
			// 
			// delete , update all records pertaining to this tranid
			// then add new entry
			$SaveTranID = $Data['TranID'];
			$MemberID 	= $Data['MemberID'];
			$Debits 	= getSingleField($db,"Select Debits  from shareholders where MemberID='$MemberID'");
			$Credits 	= getSingleField($db,"Select Credits from shareholders where MemberID='$MemberID'");
			$Data['ClosBal'] 	=	$Data['OpenBal'] - $Debits + $Credits;
			$PreStmt 	= 	"UPDATE shareholders Set Name=?,Mobile=?,Mobile2=?,EmailID=?,Address1=?,Address2=?,DeptID=?,DesignID=?,Aadhar=?,PAN=?,BankIFSC=?,BankAcNo=?,Nominee=?,NomineeAddr=?,NomineeRel=?,DOB=?,OpenDate=?,MthContr=?,OpenBal=?,ClosBal=?,DOR=? Where MemberID=? ";
			echo $PreStmt;
			$stmt = $db->prepare($PreStmt); 
			$Array = array(
					$Data['Name'],$Data['Mobile'],$Data['Mobile2'],
					$Data['EmailID'],$Data['Address1'],$Data['Address2'],$Data['DeptID'],
					$Data['DesignID'],$Data['Aadhar'],$Data['PAN'],$Data['BankIFSC'],$Data['BankAcNo'],
					$Data['Nominee'] ,$Data['NomineeAddr'],$Data['NomineeRel'],$Data['DOB'],
					$Data['OpenDate'],$Data['MthContr'],$Data['OpenBal'],$Data['ClosBal'],$Data['DOR'],$Input_MemberID);
			$stmt->execute($Array);
		 	echo "<script type='text/javascript'>alert('Successfully Saved..');
					window.location='getshareholders_list.php';</script>";			
			exit();
		}
	}

?>





<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style>
</style>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KLS GIT employees Co-Op Credit Society Ltd.</title>
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/git.png">
	<link  href="../bootstrap/dist/css/bootstrap.css" rel="stylesheet" />
	<script src="../bootstrap/dist/js/jquery-1.10.2.js"></script>
	<script src="../bootstrap/dist/js/bootstrap.min.js"></script>   
	<link  href="../includes/avd.css" rel="stylesheet" />
</head>
<style>
	body { 
	  background: url(assets/images/entrance.jpg) no-repeat center center fixed; 
	  -webkit-background-size: cover;
	  -moz-background-size: cover;
	  -o-background-size: cover;
	  background-size: cover;
	}
</style>
<body>
    <div class="container-fluid">
    	<div class="col-md-12">
    		<div class='row'>
				<?php include('accountsmenu.ini'); ?>
			</div>
		</div>
		<div class='col-md-6 col-md-offset-3'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
    	        	<center><h4><?php echo $PanelHeading; ?></h4></center>
				</div>
				<div class='panel-body'>
					<form id="addshareholder" name="addshareholder" role="form" method="post" action="sharetrans.php" enctype="multipart/form-data">
		            <input type='hidden' id='mode' name='mode' value="<?php echo $Mode;?>"/> 
               		<input type='hidden' id='trantype' name='trantype' value="<?php echo $Data['TrnType'];?>"/> 		            				
					<table id='table1' class='table table-bordered table-condensed bluecolor'>
						<tr>
							<td>Message</td>
							<td><div id="showerror"></div></td>
						</tr>						
						<tr>
							<td>Transaction ID</td>
							<td><input type='text' class='form-control' id='tranid' name='tranid' 
		               		value="<?php echo $Data['TranID'];?>" readonly/> </td>
						</tr>						
						<tr>
							<td>ShareHolder</td>
							<td><?php echo $ShareHoldersList; ?></td>
						</tr>						
						<tr>
							<td>MemberID</td>
							<td><input type='text' class='form-control' id='showmemberid' name='showmemberid' readonly/> </td>
						</tr>						
						<tr>
							<td>Closing Balance</td>
							<td><input type="number" class="form-control" id="closbal" name="closbal" data-toggle="tooltip"  value='<?php echo $Data['ClosBal'];?>' data-placement="top" title="Closing Balance" maxlength="15" readonly/></td>
						</tr>						
						<tr>
							<td>Transaction Date</td>
							<td><input type="date" class="form-control" id="trndate" name="trndate" data-toggle="tooltip" data-placement="top"  value='<?php echo $Data['TrnDate'];?>'  title="Date of Birth" maxlength="10" required /></td>
						</tr>						
						<tr>
							<td>Debit</td>
							<td><input type="number" class="form-control" id="debit" name="debit" value='<?php echo $Data['Debit'];?>' required/></td>
						</tr>						
						<tr>
							<td>Credit</td>
							<td><input type="number" class="form-control" id="credit" name="credit" 
		                    value='<?php echo $Data['Credit'];?>' required/></td>
						</tr>						
						<tr>
							<td>Particulars</td>
							<td><input type="text" class="form-control" id="particulars" name="particulars" placeholder="Particulars" data-toggle="tooltip" data-placement="top" 
		                    value='<?php echo $Data['Particulars'];?>' maxlength="255" required/></td>
						</tr>						
						<tr>
							<td>Debit Account</td>
							<td><?php echo $DebitAccList; ?></td>
						</tr>
						<tr>
							<td>Credit Account</td>
							<td><?php echo $CreditAccList; ?></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" value="Save" id='submit' name="submit" data-toggle="tooltip" data-placement="top" title="Click to Save" class="btn btn-success">
							<span class="pull-right">
							<a href='sharetrans_list.php' class='btn btn-danger'>Back to Transaction List</a>
							<a href="../index.php" data-toggle="tooltip" data-placement="top" class="btn btn-danger">Log Out</a> </td>
						</tr>

					</table>
					</form>
				</div>
			</div>
			<div class='panel-footer'>
			</div>
		</div>
	</div>
</body>
<?php include('../includes/modal.php'); ?>
<script>
	$(window).on("load", function(){
	  	// Handler when all assets (including images) are loaded
		$("#memberid").val(<?php echo "'".$Data['MemberID']."'";?>);
		$("#showmemberid").val(<?php echo "'".$Data['MemberID']."'";?>);
		$("#debitaccid").val(<?php echo $Data['DebitAccID'];?>);
		$("#creditaccid").val(<?php echo $Data['CreditAccID'];?>);
		console.log("memberid:"+$("#memberid").val());
	});	
	//$( document ).ready(function() {
	//});
	console.log('debitaccid'+$("#debitaccid").val());
	$("#memberid").change(function(){
		$("#showerror").html("");
		var memberid = $("#memberid").val();
		if((memberid.length)==6){
			$("#showmemberid").val(memberid);
			$.ajax({
		        type: "POST",
		        url: "ajax_getshareholderfield.php",
		        data: "MemberID="+memberid+"&FieldName=ClosBal",
		        success : function(text){
		        	$("#closbal").val(text);
		        }
			});
		}
	});


    $("form").submit(function(event){
        // Stop form from submitting normally
        //check all the element values 
        var debit 	= parseInt($("#debit").val());
        var credit 	= parseInt($("#credit").val());
        var closbal = parseInt($("#closbal").val());
        if(debit > 0 && credit >0) {
        	alert("Please enter either debit or credit");
        	event.preventDefault();
        	return false;
        }
        if(debit==0 && credit==0){
        	alert("Please enter either debit or credit");
        	event.preventDefault();        	
        	return false;
        }
        if(debit > closbal) {
        	alert("Insufficient balance : max allowed is "+closbal);
        	event.preventDefault();        	
        	return false;
        }

        // Get action URL
        var actionFile = $(this).attr("action");
        var formValues = $(this).serialize();
        // Send the form data using post
        $.post(actionFile, formValues, function(data){
            // Display the returned data in browser
            $("#result").html(data);
        });
    });
</script>

