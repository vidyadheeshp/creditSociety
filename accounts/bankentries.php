<?php
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
	//require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$PanelHeading = " Bank Transactions";

	$db 			   	= connectPDO();
	$Mode 				= "Add";
	$Data 				= array();
	$Data['FMID'] 		= 0;
	$Data['FixFMID'] 	= 0;
	$Data['TrnDate'] 	= date("Y-m-d");		
	$Data['TrnType'] 	= "";
	$Data['TrnNo'] 		= 0;
	$Data['Particulars']= "";
	$Data['Amount'] 	= 0;
	$Data['ChqNo']		= "";
	$Data['ChqDt'] 		= date("Y-m-d");
	$Data['AcType'] 	= "";
	$Data['TrnCode'] 	= "";
	$Data['Credit']		= 0;
	$Data['Debit'] 		= 0;
	$Data['MemberID'] 	= "";
	$Data['LoanID']   	= "";
	$Data['UserID'] 	= $_SESSION['UserID'];
	$Data['FinYear'] 	= "";	
	$Data['ChallanNo']	= "";
	$Data['ChallanDt']	= date("Y-m-d");
	$Data['IntUptoDt']  = date("Y-m-d");

	if(isset($_POST['hiddenFTID']) and isset($_POST['hidden_type'])) {
		if($_POST['hidden_type']=="Edit"){
			$FTID 	= $_POST['hiddenFTID'];
			MsgBox("Edit transaction tranid ".$_POST['hiddenFTID'],"",true);
			$Mode 			= "Edit";
			$PanelHeading 	= "Edit Bank Transaction";
			$EditResultSet = getResultSet($db,"Select * from ft Where FTID='$FTID'");
			foreach($EditResultSet as $EditRow){
				$Data['FTID'] 		= $FTID;
				$Data['FMID'] 		= $EditRow['FMID'];
				$FMID  				= $Data['FMID'];
				$Data['FixFMID'] 	= $EditRow['FixFMID'];
				$Data['TrnDate'] 	= date("Y-m-d",strtotime($EditRow['TrnDate']));		
				$Data['TrnType'] 	= $EditRow['TrnType'];
				$Data['TrnNo'] 		= $EditRow['TrnNo'];
				$Data['Particulars']= $EditRow['Particulars'];
				$Data['Amount'] 	= $EditRow['Amount'];
				$Data['ChqNo']		= $EditRow['ChqNo'];
				$Data['ChqDt'] 		= date("Y-m-d",strtotime($EditRow['ChqDt']));
				$Data['AcType'] 	= getSingleField($db,"Select AcType from fm Where FMID='$FMID'");
				$Data['TrnCode'] 	= $EditRow['TrnCode'];
				$Data['Credit']		= $EditRow['Credit'];
				$Data['Debit'] 		= $EditRow['Debit'];
				$Data['MemberID'] 	= $EditRow['MemberID'];
				$Data['LoanID']   	= $EditRow['LoanID'];
				$Data['UserID'] 	= $_SESSION['UserID'];
				$Data['FinYear'] 	= $EditRow['FinYear'];	
				$Data['ChallanNo']	= $EditRow['ChallanNo'];
				$Data['ChallanDt']	= date("Y-m-d",strtotime($EditRow['ChallanDt']));
				$Data['IntUptoDt']  = date("Y-m-d",strtotime($EditRow['IntUptoDt']));
			}
		}
	}else if(isset($_POST['hidden_type'])) {
		if($_POST['hidden_type']=="Add") {
			$TranID = 0;
			$Data['FTID']=0;
			$PanelHeading = "New Bank Transaction";
			MsgBox("Add new Bank Transaction","",true);
		}
	}	
	$PanelHeading = "Bank Transactions";
	//MsgBox("accountsmenu.php","",True);
	$BankList = genSelectFM($db,"fixfmid","AcType='Bank'"," required ");
	$AccList  = genSelectFM($db,"fmid",1," required ");

	$Mode     = "Add";
	$TrnDate  = date("Y-m-d");
	$ChqDt    = date("Y-m-d");

	if(isset($_POST['Submit']) and isset($_POST['amount']) and isset($_POST['fixfmid'])){

		$Data     = array();
		$Mode     = $_POST['mode'];
		if($Mode == 'Add') {
			$Data['FMID'] 		= $_POST['fmid'];
			$Data['FixFMID'] 	= $_POST['fixfmid'];
			$Data['TrnDate'] 	= date("Y-m-d",strtotime($_POST['trndate']));		
			$Data['Particulars']= filter_input(INPUT_POST, 'particulars',FILTER_SANITIZE_STRING);
			$Data['Amount'] 	= floatval($_POST['amount']);
			$Data['ChqNo']		= filter_input(INPUT_POST, 'chqno',FILTER_SANITIZE_STRING);
			$Data['ChqDt'] 		= date("Y-m-d",strtotime($_POST['chqdate']));
			$Data['AcType'] 	= $_POST['actype'];
			$Data['Principal']  = floatval($_POST['principal']);
			$Data['Interest'] 	= floatval($_POST['interest']);
			$Data['Days'] 		= floatval($_POST['days']);
			$Data['TrnCode'] 	= $_POST['trncode'];
			$Data['ChallanNo'] 	= $_POST['challanno'];
			$Data['ChallanDt']  = date("Y-m-d",strtotime($_POST['challandt']));			
			$Data['IntUptoDt']  = date("Y-m-d",strtotime($_POST['intuptodt']));			

			// if rec then FMID Credit FixFMID Debit
			// if pmt then FMID Debit  FixFMID Credit
			if($Data['TrnCode']=='REC'){
				if($Data['AcType']=='Cust' and $Data['Principal']>0 AND $Data['Interest']>0){
					// create two ft entries One for principal credit and other for Interest credit
					$Data['Credit'] = $Data['Principal'];
					$Data['Debit']  = 0;
				}else{
					$Data['Credit']	= $Data['Amount'];
					$Data['Debit'] 	= 0;
				}
			} elseif($Data['TrnCode']=='PMT') {
				$Data['Credit']	= 0;
				$Data['Debit'] 	= $Data['Amount'];
			}
			if($Data['AcType']=='SC') {
				$Data['MemberID'] = $_POST['memberid'];
				$Data['LoanID']   = "";
			} elseif($Data['AcType']=='Cust') {
				$Data['MemberID'] = "";
				$Data['LoanID']   = $_POST['loanid'];
			} else{
				$Data['MemberID'] = "";
				$Data['LoanID']   = "";
			}	
			$Data['UserID'] 	= $_SESSION['UserID'];
			$Data['FinYear'] 	= genFinYear($Data['TrnDate']);	
			$Data['TrnNo'] 		= genMaxTrnNo($db,$Data['TrnCode'],$Data['FinYear'],"ft");
			$Data['TrnType'] 	= "";

			try {
				// 
				$db->BeginTransaction();
				$PreStmt = 	"INSERT INTO ft(FMID,TrnCode,TrnType,TrnDate,TrnNo,Debit,Credit,Particulars,UserID,FinYear,MemberID,LoanID,FixFMID,Principal,Interest,Days,ChqNo,ChqDt,ChallanNo,ChallanDt,IntUptoDt) 
							VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				//echo $PreStmt;
				$Array = array(
						$Data['FMID'],$Data['TrnCode'],$Data['TrnType'],
						$Data['TrnDate'],$Data['TrnNo'],$Data['Debit'],$Data['Credit'],
						$Data['Particulars'],$Data['UserID'],$Data['FinYear'],$Data['MemberID'],
						$Data['LoanID'],$Data['FixFMID'],$Data['Principal'],$Data['Interest'],
						$Data['Days'],$Data['ChqNo'],$Data['ChqDt'],$Data['ChallanNo'],$Data['ChallanDt'],$Data['IntUptoDt']);
				//print_r($Array);
				$stmt = $db->prepare($PreStmt); 
				$stmt->execute($Array);
				// update FM 
				$Where 		= "FMID=".$Data['FMID'];
				$RetValue 	= updateFM($db,$Where,$Data['Debit'],$Data['Credit']);

				$Where 		= "FMID=".$Data['FixFMID'];
				$RetValue 	= updateFM($db,$Where,$Data['Credit'],$Data['Debit']);

				$Amount 	= $_POST['amount'];
				$UserID 	= $_SESSION['UserID'];

				// update shareholders table for debit/credit trans
				if(strlen($Data['MemberID'])==6){
					$Where = "MemberID='".$Data['MemberID']."'";
					updateShareHolders($db,$Where,$Data['Debit'],$Data['Credit']);
				}
				if(strlen($Data['LoanID'])==6){
					$Where = "LoanID='".$Data['LoanID']."'";
					updateCustomers($db,$Where,$Data['Debit'],$Data['Credit']);
					if($Data['TrnCode']=='REC'){
						$LastRecDate = date("Y-m-d",strtotime($_POST['intuptodt']));
						$sql  = "UPDATE customers Set LastRecDate='$LastRecDate' Where $Where";
						CreateLog($sql);
						$stmt = $db->prepare($sql);
					    $stmt->execute();					
					}
				}

				// post interest entry for loan receipt
				if($Data['TrnCode']=='REC' and $Data['AcType']=='Cust' and $Data['Principal']>0 AND $Data['Interest']>0){
					// get Interest FMID from loan settings table
					// create two ft entries One for principal credit and other for Interest credit
					$Sql1 = "Select LoanIntFMID from loansettings Where LoanFMID=".$Data['FMID'];
					$InterestFMID = getSingleField($db,$Sql1);		
					$Data['Credit'] 	= $Data['Interest'];
					$Data['Debit'] 		= 0;
					$Data['Interest'] 	= 0;
					$Data['Principal']	= 0;
					$Data['Days'] 		= 0;
					$Data['IntUptoDt']  = date("Y-m-d",strtotime($_POST['intuptodt']));
					$Data['Particulars']= $Data['LoanID']." ".trim($Data['Particulars']);
					$Data['LoanID']     = "";
					$PreStmt = 	"INSERT INTO ft(FMID,TrnCode,TrnType,TrnDate,TrnNo,Debit,Credit,Particulars,UserID,FinYear,MemberID,LoanID,FixFMID,Principal,Interest,Days,ChqNo,ChqDt,ChallanNo,ChallanDt) 
								VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
					//echo $PreStmt;
					$Array = array(
							$InterestFMID,$Data['TrnCode'],$Data['TrnType'],
							$Data['TrnDate'],$Data['TrnNo'],$Data['Debit'],$Data['Credit'],
							$Data['Particulars'],$Data['UserID'],$Data['FinYear'],"","",
							$Data['FixFMID'],$Data['Principal'],$Data['Interest'],$Data['Days'],
							$Data['ChqNo'],$Data['ChqDt'],$Data['ChallanNo'],$Data['ChallanDt']);
					//print_r($Array);
					$stmt = $db->prepare($PreStmt); 
					$stmt->execute($Array);
					// update FM 
					$Where 		= "FMID=".$InterestFMID;
					$RetValue 	= updateFM($db,$Where,$Data['Debit'],$Data['Credit']);
					$Where 		= "FMID=".$Data['FixFMID'];
					$RetValue 	= updateFM($db,$Where,$Data['Credit'],$Data['Debit']);
					// int calc 1 days less : till yesterday
					$LastRecDate= date("Y-m-d",strtotime($_POST['intuptodt']));
					//date_add($LastRecDate,date_interval_create_from_date_string("-1 days"));
					//$LastRecDate = date("Y-m-d",$LastRecDate);
					//echo date_format($date,"Y-m-d");
					//$LastRecDate = $Data['TrnDate'];
					$LoanID      = $Data['LoanID'];
					$sql  = "UPDATE customers Set LastRecDate='$LastRecDate' Where LoanID='$LoanID'";
					$stmt = $db->prepare($sql);
	      			$stmt->execute();					
	      			CreateLog("Update $LoanID for LastRecDate $LastRecDate");
				}
				// 



				// create logfile

				$Amount 	= $_POST['amount'];
				$UserID 	= $_SESSION['UserID'];

				$LogDesc = "Accounting".$Data['TrnDate']." ".$Data['TrnCode']." TrnNo:".$Data['TrnNo'].
				" posted Amount:".$Amount." User:".$UserID;
				$RetVal = insert($db,"logfile",array("LogType"=>'BankEntry',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));	
				CreateLog($LogDesc);
				$db->commit();

		 		echo "<script type='text/javascript'>alert('Successfully Saved..');
					window.location='bankentries_list.php';</script>";	   					
				exit();
			} catch(PDOException $ex) {
			    //Something went wrong rollback!
			    $db->rollBack();

			    echo $ex->getMessage();
				$LogDesc = "Accounting Error encountered ".$Data['TrnDate']. " ". $Data['TrnCode']. " TrnNo:".$Data['TrnNo']. " Amount:".$Amount."  User:".$UserID .$ex->getMessage();
				CreateLog($LogDesc);
		 		echo "<script type='text/javascript'>alert('Something went wrong..');
					window.location='bankentries_list.php';</script>";	   					
				exit();
			}	
			catch(Exception $ex) {
			    $db->rollBack();
			    echo $ex->getMessage();
				$LogDesc = "Accounting Error encountered ".$Data['TrnDate']. " ". $Data['TrnCode']. " TrnNo:".$Data['TrnNo']. " Amount:".$Amount."  User:".$UserID .$ex->getMessage();
				CreateLog($LogDesc);
		 		echo "<script type='text/javascript'>alert('Something went wrong..');
					window.location='bankentries_list.php';</script>";	   					
				exit();
			}	

		}
		//print_r($_POST);
	}
?>

   
	<?php include_once('navbar.php'); ?>	
				<div class='panel-heading'>
    	        	<center><h3><b><?php echo $PanelHeading; ?></b></h3></center>
				</div>
				
				<div class='main'>
					<center>
					<form id="ftentries" class="bg" name="ftentries" role="form" method="post" action="bankentries.php" >
					<input type='hidden' id='mode' name='mode' value="<?php echo $Mode;?>"/>
					<a href='bankentries_list.php' class='btn btn-danger' style="float:right">Back to List</a>
					<table id='mytable'>
						<tr>
							<td><label class='mylabel'>Messages</td>
							<td colspan='2'><input type='text' id='showerror' name='showerror' class='smallinput' readonly required='false' /></td>
						</tr>
						<tr>
							<td><label class="mylabel">Transaction Date</label></td>
							<td><input type='date' class='smallinput' id='trndate' name='trndate' 
							value='<?php echo $TrnDate;?>' required />
							</td>
							<td></td>
						</tr>
						<tr>
							<td><label class="mylabel">Int Upto (Loans)</label></td>
							<td><input type='date' class='smallinput' id='intuptodt' name='intuptodt' 
							value='<?php echo $Data['IntUptoDt']; ?>' required />
							</td>
							<td></td>
						</tr>
						<tr>
							<td><label class="mylabel">Tran.Type</label></td>
							<td><select id='trncode' name='trncode' class='drop' >
								<option value='REC'>Receipt</option>
								<option value='PMT'>Payment</option>
								</select>
							</td>
							<td></td>
						</tr>
						<tr>
							<td><label class="mylabel">Bank A/c</label></td>
							<td style="coloumn-width:fit-content;"><?php echo $BankList; ?></td>
							<td><input type='hidden' id='actualfixfmidbal' name='actualfixfmidbal' class='smallinput' readonly/>
							<input type='text' id='fixfmidbal' name='fixfmidbal' class='smallinput' readonly/></td>
						</tr>
						<tr>
							<td><label class="mylabel">Account</label></td>
							<td><?php echo $AccList; ?></td>
							<td><input type='hidden' id='actype' name='actype' required/>
								<input type='hidden' id='actualfmidbal' name='actualfmidbal' class='smallinput'/>
								<input type='text' id='fmidbal' name='fmidbal' class='smallinput' readonly/></td>
						</tr>
						<tr>
							<td><label class="mylabel">Share/Loan A/c</label></td>
							<td><div id='div_subaccslist'></div>
					
							</td>
							<td><input type='hidden' id='actualsubaccsbal' name='actualsubaccsbal' class='smallinput' 		readonly/>
								<input type='text' id='subaccsbal' name='subaccsbal' class='smallinput' readonly/>
								<button id='ledger' type='button' class='btn btn-sm' onclick='showledger()'>Ledger</button>
							</td>
						</tr>
						<tr>
							<td><label class="mylabel">Tran.No</label></td>
							<td><input type='number' id='trnno' name='trnno' class='smallinput' readonly />
							</td>
						</tr>
						<!--
						<tr>
							<td><label class="mylabel">Particulars</label></td>
							<td colspan='2'><textarea id='particulars' name='particulars' class='smallinput' rows='3'></textarea>
							</td>
						</tr>
						-->
						<tr>
							<td><label class="mylabel">Particulars</label></td>
							<td><input id='particulars' name='particulars' list='datalistparticulars' class='smallinput' maxlength='255' ></input>
							<datalist id="datalistparticulars">
								<option value="Long Term Loan Given">
								<option value="Short Term Loan Given">
								<option value="Emergency Term Loan Given">
								<option value="Part Payment of Long Term Loan">
								<option value="Part Payment of Short Term Loan">
								<option value="Part Payment of Emergency Loan">
								<option value="Excess Loan Recd Refunded">
								<option value="Partial withdrawl">
								<option value="Additional Shares Contribution">
								<option value="Share adjusted against loan">
								<option value="Share Closure">
							</datalist>	
							</td>
						</tr>
						<tr>
							<td><label class="mylabel">Tran.Amount</label></td>
							<td><input type='number' id='amount' name='amount' step='.01' class='smallinput input-lg' required />
							</td>
						</tr>
						<div id='div_loan'>
						<tr>
							<td><label class="mylabel">Details</label></td>
							<td><div id='div_loan_dets' class='info'></div>
								<input type='hidden' class='smallinput' id='loantype' name='loantype' value=''/>
							</td>
						</tr>
						<tr>
							<td><label class="mylabel">Interest Amount</label></td>
							<td><input type='number' id='interest' name='interest' class='smallinput' readonly  />
							</td>
						</tr>
						<tr>
							<td><label class="mylabel">Principal Amount</label></td>
							<td><input type='number' id='principal' name='principal' class='smallinput' readonly />
							</td>
						</tr>
						<tr>
							<td><label class="mylabel">Days</label></td>
							<td><input type='number' id='days' name='days' class='smallinput' readonly />
							</td>
						</tr>

						</div>
						<tr>
							<td><label class="mylabel">Cheque No</label></td>
							<td><input type='number' id='chqno' name='chqno' class='smallinput'/></td>
						</tr>
						<tr>
							<td><label class="mylabel">Cheque Date</label></td>
							<td><input type='date' id='chqdate' name='chqdate' class='smallinput' value='<?php echo $ChqDt; ?>' />
							</td>
						</tr>
						<tr>
							<td><label class="mylabel">Challan No</label></td>
							<td><input type='text' id='challanno' name='challanno' maxlength='10' class='smallinput' value="<?php echo $Data['ChallanNo'];?>"/></td>
						</tr>
						<tr>
							<td><label class="mylabel">Challan Date</label></td>
							<td><input type='date' id='challandt' name='challandt' class='smallinput' value="<?php echo $Data['ChallanDt']; ?>" />
							</td>
						</tr>

						<tr>
							<td></td>
							<td><input type="submit" value="Save" name="Submit" data-toggle="tooltip" data-placement="top" title="Click to Save" class="btn btn-success">
							<span class="pull-right">
							
						
							</span></td>
						</tr>

					</table>
					</form>
</center>
				</div>
			

		</div>
		<div id='div_ledger' class='col-md-10 col-md-offset-1'>
			
		</div>						
	</div>
</body>
<?php include('../includes/modal.php'); ?>
<script type='text/javascript'>
	var errors = 0;
	$("#div_loan_dets").html("");
	$("#fixfmid").change(function(){
		$("#showerror").val("");
		var fixfmid = $("#fixfmid").val();
		$.ajax({
	        type: "POST",
	        url: "ajax_getfield2.php",
	        data: "fmid="+fixfmid,
	        success : function(text){
	        	var ret = JSON.parse(text);
	        	$("#actualfixfmidbal").val(ret['ClosBal']);
	        	$("#fixfmidbal").val(ret['ConvAmt']);
	        }
		});
	});	
	$("#fmid").change(function(){
		$("#showerror").html("");
		var fmid = $("#fmid").val();
		
		$.ajax({
	        type: "POST",
	        url: "ajax_getfmwithsubaccs.php",
	        data: "fmid="+fmid,
	        success : function(text){
	        	//alert(text);
				var ret = JSON.parse(text);
				//alert(ret['SubAccList']);
	        	$("#fmidbal").val(ret['ConvAmt']);
	        	$("#actualfmidbal").val(ret['ClosBal']);
	        	$("#actype").val(ret['AcType']);
	        	//$("#div_subaccslist").html(ret['SubAccList']);
	        	$("#div_subaccslist").html("");
	        	$("#div_subaccslist").append(ret['SubAccList']);
	        	$("#loantype").val(ret['LoanType']);
	        	var loantype = ret['LoanType'];
	        	//alert(loantype);
	        	if(loantype=='LE'){
	        		$("#interest").prop("readonly",false);
	        	} else{
	        		$("#interest").prop("readonly",true);
	        	}
	        }
		});
	});	
	// write another for subacclist change
	// balance in id=subaccsbal
	$(document).ready(function(){	
		$("#div_subaccslist").on('click','select',function(){
		$("#memberid").change(function(){
			$("#showerror").html("");
			var memberid = $("#memberid").val();
			//alert("inside ajax function");
        	$("#amount").val(0);
        	$("#principal").val(0);
        	$("#interest").val(0);
        	$("#days").val(0);
			if((memberid.length)==6){
				$.ajax({
			        type: "POST",
			        url: "ajax_getshareholderfield.php",
			        data: "MemberID="+memberid+"&FieldName=ClosBal",
			        success : function(text){
			        	$("#actualsubaccsbal").val(text);
			        	$("#subaccsbal").val(text);
			        }
				});
			}
		});
		$("#loanid").change(function(){
			//alert("inside loanid change");
			$("#showerror").html("");
			var loanid = $("#loanid").val();
			var trncode= $("#trncode").val();
			if(trncode.length!=3){
				alert("Please select Tran Code");
				return false;
			}
			//alert(loanid);
			var amount 	= $("#amount").val();
			var trndate = $("#trndate").val();
			var intuptodt= $("#intuptodt").val();
			//alert("inside ajax function");
        	$("#amount").val(0);
        	$("#principal").val(0);
        	$("#interest").val(0);
        	$("#days").val(0);
			if((loanid.length)==6){
				$.ajax({
			        type: "POST",
			        url: "ajax_getloandets.php",
			        data: "LoanID="+loanid+"&TrnCode="+trncode+"&Amount="+amount+"&TrnDate="+trndate+"&IntUptoDt="+intuptodt,
			        success : function(text){
			        	//alert(text);
						var ret = JSON.parse(text);
			        	$("#actualsubaccsbal").val(ret['ClosBal']);
			        	$("#subaccsbal").val(ret['ConvAmt']);
			        	$("#interest").val(ret['Interest']);
			        	$("#days").val(ret['Days']);
			        	var loandets = "Last Rec.Date "+ret['LastRecDate'] + " EMI " + ret['MthEMI'];
			        	$("#div_loan_dets").html(loandets);
			        }
				});
			}
		});
		});
	});
	$("#trndate").change(function(){
		var trndate = $("#trndate").val();
		$("#intuptodt").val(trndate);
		$("#chqdate").val(trndate);
		$("#challandt").val(trndate);
	});

	function showledger() {
		//alert("Inside Show Ledger Function");
		var loanid = $("#loanid").val();
		if(loanid.length==6){
			$.ajax({
		        type: "POST",
		        url: "ajax_loanaccountledger.php",
		        data: "LoanID="+loanid,
		        success : function(text){
		        	//alert(text);
			        var arrayReturned = JSON.parse(text);
		        	var header = arrayReturned['Header'];
		        	var html   = arrayReturned['Body'];
		        	//$("#avdModalLabel").html(header);
		        	//$("#modalbody_html").html(html);
		        	//$("#avdModal").modal("show");
		        	$("#div_ledger").html(header + html);
        			//event.preventDefault();		        	
		        }
			});	
		}
	}

	$("#amount").change(function(){
		var actype 		= $("#actype").val();
		var trncode 	= $("#trncode").val();
		var amount  	= parseInt($("#amount").val());
		var interest 	= parseInt($("#interest").val());
		var principal 	= parseInt($("#principal").val());
		if(actype=='Cust' && trncode=='REC'){
			if(amount>=interest){
				principal = amount - interest;
				$("#principal").val(principal);
			}else{
				$("#showerror").val("Please collect full Interest");
				$("#amount").val(0);
			}
			amount = principal + interest;
			$("#amount").val(amount);
		}else{
			$("#principal").val(0);
			$("#interest").val(0);

		}
	});
	$("#interest").blur(function(){
		var actype 		= $("#actype").val();
		var trncode 	= $("#trncode").val();
		var amount  	= parseInt($("#amount").val());
		var interest 	= parseInt($("#interest").val());
		var principal 	= parseInt($("#principal").val());
		if(actype=='Cust' && trncode=='REC'){
			if(amount>=interest){
				principal = amount - interest;
				$("#principal").val(principal);
			}else{
				$("#showerror").val("Please collect full Interest");
				$("#amount").val(0);
			}
		}else{
			$("#principal").val(0);
			$("#interest").val(0);

		}
	});


    $("form").submit(function(event){
        // Stop form from submitting normally
        //check all the element values 
        if(errors>0){
        	alert("Please solve errors in entry ");
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

