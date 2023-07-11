<?php

// loanaccounts.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 03.12.2019
	//echo "Inside loanaccounts.php1";	
	session_start();
	require_once("../includes/functions.php");

	// check usertype
	if(!isset($_SESSION['UserType'])) {
		MsgBox("Direct script access prohibited","../index.php",True);
		exit();
	}

	$UserType 	= $_SESSION['UserType'];
	if(strstr('Loans,Admin',$UserType)){

	} else{
		MsgBox("Access for Authorised Users only","accountsmenu.php",True);
		exit();
	}
	//	
	//require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");

	$db = connectPDO();
	//echo "Inside loanaccounts.php";
	$Mode 				= "Add";
	$PanelHeading 		= "Loan Account";
	$LoanFMID="";
	$G1MemberID="";
	$G2MemberID="";	
	$ShareBalance		= 0;

	$Data = array();
	$Data['MemberID'] 	= "";
	$Data['LoanID']		= "New";
	$Data['OpenBal'] 	= "";
	$Data['Debits'] 	= 0;
	$Data['Credits'] 	= 0;
	$Data['ClosBal'] 	= 0;
	$Data['LoanDate'] 	= date("Y-m-d");
	$Data['LoanAmt'] 	= "";
	$Data['IntRate'] 	= "";
	$Data['Particulars']= "";
	$Data['G1MemberID'] = "";
	$Data['G2MemberID'] = "";
	$Data['LastRecDate'] = date("Y-m-d");
	$Data['Months'] 	= "";
	$Data['MthEMI']     = "";
	$Data['Status']     = "";
	$Status 			= "";
	$FMList          	= genSelectFM($db,"loanfmid"," AcType='Cust' ", " required ");
	$ShareHolderList 	= genShareHoldersSelect($db," Where ClosBal>0 ");   
	$Guarantors1	 	= genShareHoldersSelect2($db," Where ClosBal>0 ","g1memberid");   
	$Guarantors2 		= genShareHoldersSelect2($db," Where ClosBal>0 ","g2memberid");   
    
	if(isset($_POST['hiddenFormLoanID'])) {
		$Mode 			= "Edit";
		$MemberReadonly = " readonly ";
		$LoanID 		= $_POST['hiddenFormLoanID'];
		$PanelHeading 	= "Edit Loan Account ".$LoanID;
		$result = getResultSet($db,"Select * from customers Where LoanID='$LoanID' LIMIT 1");
		foreach($result as $row) {
			$LoanFMID 			= $row['FMID'];
			$LoanName 			= getSingleField($db,"Select Name from fm Where FMID='$LoanFMID'");
			$Data['FMID'] 		= $row['FMID'];
			$MemberID 			= $row['MemberID'];
			$MemberName 		= getSingleField($db,"Select Name from shareholders Where MemberID='$MemberID'");
			$G1MemberID         = $row['G1MemberID'];
			$G2MemberID 		= $row['G2MemberID'];
			$ShareBalance 		= getSingleField($db,"Select ClosBal from shareholders Where MemberID='$MemberID'");
			$Data['LoanID']		= $LoanID;
			$Data['MemberID'] 	= $row['MemberID'];
			$Data['MthEMI']		= $row['MthEMI'];
			$Data['OpenBal'] 	= $row['OpenBal'];
			$Data['Debits']   	= $row['Debits'];
			$Data['Credits']  	= $row['Credits'];
			$Data['ClosBal']  	= $row['ClosBal']; 
			$Data['LoanDate'] 	= date("Y-m-d",strtotime($row['LoanDate']));
			$Data['LoanAmt']  	= $row['LoanAmt'];
			$Data['IntRate']  	= $row['IntRate'];
			$Data['Particulars']= $row['Description'];
			$Data['G1MemberID'] = $row['G1MemberID'];
			$Data['G2MemberID'] = $row['G2MemberID'];
			$Data['LastRecDate']= date("Y-m-d",strtotime($row['LastRecDate']));
			$Data['Months'] 	= $row['Months'];
			$Data['Status'] 	= $row['Status'];

			$Status 			= $row['Status'];

			//print_r($Data);
			$FMList = "<select id='loanfmid' name='loanfmid' class='form-control' readonly>";
			$FMList .= "<option value='$LoanFMID'>".$LoanName."</option>";
			$FMList .= "</select>";			
		
			$ShareHolderList  = "<select id='memberid' name='memberid' class='form-control' readonly>";
			$ShareHolderList .= "<option value='$MemberID'>".$MemberName."(".$MemberID.")"."</option>";
			$ShareHolderList .= "</select>";			
		}
	}else {
		$MemberID 		= "";
		$LoanID    		= "New";
		$PanelHeading 	= "New Loan Account";
	}
	if(isset($_POST['loanamt']) and isset($_POST['submit'])) {
		echo "Inside Post...";
		//print_r($_POST);
		$Mode 				= filter_input(INPUT_POST,'mode',    FILTER_SANITIZE_STRING);
		$Data['LoanID']     = filter_input(INPUT_POST,'loanid',  FILTER_SANITIZE_STRING);
		$LoanID 			= $Data['LoanID'];
		$MemberID     		= filter_input(INPUT_POST,'memberid',FILTER_SANITIZE_STRING);
		$LoanFMID   		= filter_input(INPUT_POST,'loanfmid',FILTER_SANITIZE_NUMBER_INT);
		$Data['MthEMI']		= filter_input(INPUT_POST,'mthemi',  FILTER_SANITIZE_NUMBER_INT);
		$Data['OpenBal'] 	= filter_input(INPUT_POST,'openbal', FILTER_SANITIZE_NUMBER_INT);
		$Data['MemberID'] 	= filter_input(INPUT_POST,'memberid',FILTER_SANITIZE_STRING);
		$Data['OpenBal']  	= intval($_POST['openbal']);
		$Data['Debits']   	= 0;
		$Data['Credits']  	= 0;
		$Data['ClosBal']  	= $Data['OpenBal']; 
		$Data['LoanDate'] 	= date("Y-m-d",strtotime($_POST['loandate']));
		$Data['LoanAmt']  	= intval($_POST['loanamt']);
		$Data['IntRate']  	= floatval($_POST['intrate']);
		$Data['Particulars']= filter_input(INPUT_POST,'particulars',FILTER_SANITIZE_STRING);
		$Data['G1MemberID'] = $_POST['g1memberid'];
		$Data['G2MemberID'] = $_POST['g2memberid'];
		$Data['LastRecDate']= date("Y-m-d",strtotime($_POST['lastrecdate']));
		$Data['MthEMI']		= filter_input(INPUT_POST,'mthemi',FILTER_SANITIZE_NUMBER_INT);
		$Data['Months'] 	= intval($_POST['months']);

		$Data['Status'] 	= $_POST['status'];

		//echo "Input_LoanID ".$Input_LoanID;
		if($Mode=='Add') {
			// insert new record
			$Data['LoanID']	= "New";
			try {
				// 
				$db->BeginTransaction();
				$LoanType = getSingleField($db,"Select LoanType from loansettings Where LoanFMID='$LoanFMID'");
				$x  = getSingleField($db,"Select max(LoanID) from customers Where LoanID Like '$LoanType%' ");
				$temp_no = substr($x,-4);  //substr($temp_isr_no,-4);
				$temp_no = intval($temp_no) + 1 ;
				$LoanID = $LoanType.sprintf("%04d", $temp_no);
				$PreStmt = 	"INSERT INTO customers(FMID,MemberID,LoanID,Description,OpenBal,LoanAmt,IntRate,LastRecDate,MthEMI,G1MemberID,G2MemberID,Status,LoanDate,ClosBal,Months) 
					VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				echo $PreStmt;
				$stmt = $db->prepare($PreStmt); 
				$Array = array(
						$LoanFMID,$MemberID,$LoanID,$Data['Particulars'],$Data['OpenBal'],$Data['LoanAmt'],
						$Data['IntRate'],$Data['LastRecDate'],$Data['MthEMI'],$Data['G1MemberID'],
						$Data['G2MemberID'],"New",$Data['LoanDate'],$Data['ClosBal'],$Data['Months']);
				$stmt->execute($Array);
				//print_r($Array);
				$affected_rows 	= $stmt->rowCount();
				$InsertID 		= $db->lastInsertId();
				$MemberName     = getSingleField($db,"Select Name from shareholders Where MemberID='$MemberID'");
				//$AddedRecJSon   = getResultSet_json($db,"Select * from customers Where LoanID='$LoanID'");
				$LogDesc = "New Loan Added FMID $LoanFMID $LoanType $LoanID $MemberID $MemberName "; //.$AddedRecJSon;
				$RetVal = insert($db,"logfile",array("LogType"=>'NewLoan',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));					
				$db->commit();
		 		echo "<script type='text/javascript'>alert('Successfully Saved..');
					window.location='getloanaccounts_list.php';</script>";	   					
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
					window.location='getloanaccounts_list.php';</script>";	   					
				exit();
			}	
			// here all the sql commands have executed correctly
			// so commit changes to the database
			//exit();
		} elseif($Mode=='Edit') {

			echo "Editing record";

			$PreStmt = 	"UPDATE customers Set Description=?,OpenBal=?,LoanAmt=?,IntRate=?,LastRecDate=?,MthEMI=?,G1MemberID=?,G2MemberID=?,LoanDate=?,Months=?,Status=?  Where LoanID=? ";
			//echo $PreStmt;
			$stmt = $db->prepare($PreStmt); 
			$Array = array(
					$Data['Particulars'],$Data['OpenBal'],$Data['LoanAmt'],
					$Data['IntRate'],$Data['LastRecDate'],$Data['MthEMI'],$Data['G1MemberID'],
					$Data['G2MemberID'],$Data['LoanDate'],$Data['Months'],$Data['Status'],$LoanID);
			$stmt->execute($Array);
			//print_r($Array);
			$Where = "LoanID='" . $LoanID . "'";
			//echo $Where;
			$RetVal = updateCustomers($db,$Where,0,0);
			$EditedRecJSon = getResultSet_json($db,"Select * from customers where LoanID='$LoanID'");
			//echo $EditedRecJSon;
			$LogDesc = "Edit $LoanID ".$EditedRecJSon ;
			$RetVal = insert($db,"logfile",array("LogType"=>'EditLoan',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));			
			echo "Successfully $LogDesc ";
		 	echo "<script type='text/javascript'>alert('Successfully Saved..');
					window.location='getloanaccounts_list.php';</script>";			
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
	<link  href="../includes/avd.css"  rel="stylesheet"></link>   

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
					<form id="addloan" name="addloan" role="form" method="post" action="loanaccounts.php" enctype="multipart/form-data">
					<input type='hidden' id='mode' name='mode' value="<?php echo $Mode;?>"/>
					<table id='table1' class='table table-bordered table-condensed bluecolor'>
						<tr>
							<td>Message</td>
							<td><div id="showerror" class='text-white'></div></td>
						</tr>
						<tr>
							<td>LoanID</td>
							<td><input id='loanid' name='loanid' class='form-control text-dark' value='<?php echo $Data['LoanID'];?>' readonly />
						</tr>
						<tr>
							<td>Loan Account</td>
							<td><?php echo $FMList;?>
						</tr>
						<tr>
							<td>MemberID</td>
							<td><?php echo $ShareHolderList;?></td>
						</tr>
						<tr>
							<td>Share Balance</td>
							<td><input type='text' class='form-control' id='sharebalance' readonly/></td>
						</tr>
						<tr>
							<td>Particulars</td>
							<td><input type="text" class="form-control" id="particulars" name="particulars" placeholder="Your Remarks" data-toggle="tooltip" data-placement="top" autocomplete="off" value='<?php echo $Data['Particulars'];?>' autofocus required/></td>
						</tr>
						<tr>
							<td>Opening Balance(1-4-2019)</td>
							<td><input type="number" class="form-control" id="openbal" name="openbal" data-toggle="tooltip"  value='<?php echo $Data['OpenBal'];?>' data-placement="top" title="Opening Balance" maxlength="15" /></td>
						</tr>
						<tr>
							<td>Debits</td>
							<td><input type="number" class="form-control" id="debits" name="debits" data-toggle="tooltip"  value='<?php echo $Data['Debits'];?>' data-placement="top" title="Debits" maxlength="15" readonly /></td>
						</tr>
						<tr>
							<td>Credits</td>
							<td><input type="number" class="form-control" id="credits" name="credits" data-toggle="tooltip"  value='<?php echo $Data['Credits'];?>' data-placement="top" title="Opening Balance" maxlength="15" readonly /></td>
						</tr>
						<tr>
							<td>Closing Balance</td>
							<td><input type="number" class="form-control" id="closbal" name="closbal" data-toggle="tooltip"  value='<?php echo $Data['ClosBal'];?>' data-placement="top" title="Closing Balance" maxlength="15" readonly /></td>
						</tr>

						<tr>
							<td>Monthly EMI</td>
							<td><input type="number" class="form-control" id="mthemi" name="mthemi" data-toggle="tooltip"  value='<?php echo $Data['MthEMI'];?>' data-placement="top" step="1" title="Monthly EMI" maxlength="5" required /></td>
						</tr>
						<tr>
							<td>Loan Amount</td>
							<td><input type="text" class="form-control" id="loanamt" name="loanamt" placeholder="Loan Amount" data-toggle="tooltip" data-placement="top"  value='<?php echo $Data['LoanAmt'];?>' autocomplete="off" required/></td>
						</tr>
						<tr>
							<td>Loan Date</td>
							<td><input type="date" class="form-control" id="loandate" name="loandate" placeholder="Loan Date" data-toggle="tooltip" data-placement="top"  value='<?php echo $Data['LoanDate'];?>' autocomplete="off" required/></td>
						</tr>
						<tr>
							<td>Interest Rate</td>
							<td> <input type="number" class="form-control" id="intrate" name="intrate"  step='0.01' value='<?php echo $Data['IntRate'];?>' required /></td>
						</tr>
						<tr>
							<td>Period in Months</td>
							<td> <input type="number" class="form-control" id="months" name="months" value='<?php echo $Data['Months'];?>' max='240' required /></td>
						</tr>
						<tr>
							<td>Last Rec.Date</td>
							<td><input type="date" class="form-control" id="lastrecdate" name="lastrecdate" value='<?php echo $Data['LastRecDate'];?>' required/></td>
						</tr>
						<tr>
							<td>Guarantor-1</td>
							<td> <?php echo $Guarantors1;?></td>
						</tr>
						<tr>
							<td>Information</td>
							<td>
								<div id='g1info'></div>
							</td>
								<!--<input type='text' readonly id='g1info' name='g1info' class='form-control' value=''></td> -->
						</tr>

						<tr>
							<td>Guarantor-2</td>
							<td><?php echo $Guarantors2;?></td>
						</tr>
						<tr>
							<td>Information</td>
							<td><div id='g2info'></div></td>
								<!--<input type='text' readonly id='g2info' name='g2info' class='form-control' value=''></td>-->
						</tr>
						<tr>
							<td>Status</td>
							<td><Select id='status' name='status' required>
								<option value='New'>New</option>
								<option value='Active'>Active</option>
								<option value='ToBeSettled'>ToBeSettled</option>
								<option value='Closed'>Closed</option>
							</td>
						</tr>

						<tr>
							<td></td>
							<td><input type="submit" id='submit' value="Save" name="submit" data-toggle="tooltip" data-placement="top" title="Click to Save" class="btn btn-success">
							<span class="pull-right">
							<a href='getshareholders_list.php' class='btn btn-danger'>Back to List</a>
							<a href="index.php" data-toggle="tooltip" data-placement="top" class="btn btn-danger">Log Out</a> 
							</span></td>
						</tr>
						<tr>
							<td></td>
							<td></td>
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
<script type='text/javascript'>
	function preview_image(event) 
	{
	 var reader = new FileReader();
	 reader.onload = function()
	 {
	  var output = document.getElementById('output_image');
	  output.src = reader.result;
	 }
	 reader.readAsDataURL(event.target.files[0]);
	}
</script>  

<script>
	debugger;
  	$(document).ready(function() {
   		$('#loanid').val('<?php echo $LoanID;?>');
   		$('#loanfmid').val('<?php echo $LoanFMID;?>');
   		$("#memberid").val('<?php echo $MemberID; ?>');
   		$('#g1memberid').val('<?php echo $G1MemberID;?>');
   		$('#g2memberid').val('<?php echo $G2MemberID;?>');
   		$("#status").val('<?php echo $Status;?>')
   		$("#sharebalance").val('<?php echo $ShareBalance;?>')
   		console.log($("#loanid").val());
   		console.log($("#loanfmid").val());
   		console.log($("#g1memberid").val());
   		console.log($("#g2memberid").val());

   		//$('#gridtable').DataTable();
   	} );
  	//debugger;
	$("#memberid").change(function(){
		$("#showerror").html("");
		var memberid = $("#memberid").val();
		//document.getElementById("submit").disabled = false;
		$.ajax({
	        type: "POST",
	        url: "ajax_checkmemberforloan.php",
	        data: "MemberID="+memberid,
	        success : function(text){
	        	//alert(text);
				var ret = JSON.parse(text);
				$("#sharebalance").val(ret['ClosBal']);
				var totloans = parseInt(ret['TotLoans']);
				var loanacids = ret['LoanAcIDs'];
				var message =  "Total Loans " + totloans + " IDs " + loanacids;
				if(totloans>0){
					message = message + " Loan Cannot be given ";
					//document.getElementById("submit").disabled = true;
				} else{
					message = "No Loans : Date of Retirement : " + ret['DOR'];
					//document.getElementById("submit").disabled = false;
				}
				//alert(message);
				$("#showerror").html(message);
	        }
		});
	});
	$("#g1memberid").change(function(){
		$("#showerror").html("");
		var g1memberid = $("#g1memberid").val();
		//alert(g1memberid);
		$.ajax({
	        type: "POST",
	        url: "ajax_checkguarantor.php",
	        data: "MemberID="+g1memberid,
	        success : function(text){
	        	alert(text);
				var ret = JSON.parse(text);
				var totloans = parseInt(ret['TotLoans']);
				var loanacids = ret['LoanAcIDs'];
				var description = ret['Description'];
				var message =  "MonthsLeft:" + ret['MonthsLeft'] + " Retr:" + ret['DOR'] + "<br> Own Loans:" + totloans + " <br>" + description;
				//if(totloans>0){
				//	message = message + " Loan Cannot be given ";
				//	document.getElementById("submit").disabled = true;
				//} else{
				//	message = "No Loans : Date of Retirement : " + ret['DOR'];
				//	document.getElementById("submit").disabled = false;
				//}
				//alert("Daysleft" + ret['DaysLeft']);
				//alert(message);
				$("#g1info").html(message);
	        }
		});
	});
	$("#g2memberid").change(function(){
		$("#showerror").html("");
		var g2memberid = $("#g2memberid").val();
		//alert(g2memberid);
		$.ajax({
	        type: "POST",
	        url: "ajax_checkguarantor.php",
	        data: "MemberID="+g2memberid,
	        success : function(text){
	        	//alert(text);
				var ret = JSON.parse(text);
				
				var totloans = parseInt(ret['TotLoans']);
				var loanacids = ret['LoanAcIDs'];
				var description = ret['Description'];

				var message =  "MonthsLeft:" + ret['MonthsLeft'] + " Retr:"+ ret['DOR'] + "<br> Own Loans:" + totloans + "  <br>" + description;
				//if(totloans>0){
				//	message = message + " Loan Cannot be given ";
				//	document.getElementById("submit").disabled = true;
				//} else{
				//	message = "No Loans : Date of Retirement : " + ret['DOR'];
				//	document.getElementById("submit").disabled = false;
				//}
				//alert(message);
				$("#g2info").html(message);
	        }
		});
	});

</script>
