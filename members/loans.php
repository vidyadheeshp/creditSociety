<?php
include_once("LoanPDF.php");
// applyloan.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 22.12.2019
	//echo "Inside loanaccounts.php1";	
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
	$PanelHeading 		= "New Loan Application";
	$db = connectPDO();
	$MemberID 	= $_SESSION['MemberID'];
	$MemberName = $_SESSION['UserName'];
	$approvedOn="";
	$Count = getSingleField($db,"Select count(*) from loanappl Where MemberID='$MemberID' AND Status='New'");
	if($Count>0){
		CreateLog(" $MemberID $MemberName wanted to apply for loan ");
		//MsgBox("You have already applied for loan: Application is pending","membershome.php",true);
		//exit();	
	}
	
	$newappnID=getSingleField($db,"select RowID from loanappl order by RowID Desc limit 1");

	$Mode 				= "Add";
	$PanelHeading 		= "New Loan Application";
	$LoanFMID="";
	$G1MemberID="";
	$G2MemberID="";	
	$Data = array();
	$Data['MemberID'] 	= $MemberID;

	if($newappnID==null){
		$Data['LoanID']='AID1';
	}
	else{
		$Data['LoanID']		= "AID".$newappnID+1;
	}
	$Data['Particulars']= "";
	$Data['G1MemberID'] = "";
	$Data['G2MemberID'] = "";
	$Data['Months'] 	= "";
	$Data['MthEMI']     = "";
	$Data['IntRate']    = 0;
	$Data['LoanAmt']    = 0;

	$Data['ClosBal']    = getSingleField($db,"Select ClosBal from shareholders Where MemberID='$MemberID'");
	$FMList          	= genSelectFM($db,"loanfmid"," AcType='Cust' ", " required ");
	$Guarantors1	 	= genShareHoldersSelect3($db," Where ClosBal>0 ","g1memberid");   
	$Guarantors2 		= genShareHoldersSelect3($db," Where ClosBal>0 ","g2memberid");   
    	$PanelHeading 	= "New Loan Account";
	//print_r($_POST);	
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
		//echo $_POST['months'];
		
		try {
				// 
			$db->BeginTransaction();
			$PreStmt = 	"INSERT INTO loanappl(FMID,MemberID,Description,LoanAmt,IntRate,MthEMI,G1MemberID,G2MemberID,Status,Months,LoanID,ApplOn) 
				VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
			//echo $PreStmt;
			$stmt = $db->prepare($PreStmt); 
			$Array = array(
					$LoanFMID,$MemberID,$Data['Particulars'],$Data['LoanAmt'],
					$Data['IntRate'],$Data['MthEMI'],$Data['G1MemberID'],
					$Data['G2MemberID'],"New",$Data['Months'],$Data['LoanID'],date("Y-m-d H:i:s"));
			$stmt->execute($Array);
		//	print_r($Array);
			$affected_rows 	= $stmt->rowCount();
			$InsertID 		= $db->lastInsertId();
			$LogDesc = "New Loan Appl added FMID $LoanFMID $MemberID $MemberName";
			$RetVal = insert($db,"logfile",array("LogType"=>'NewLoanApp',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));					
			$db->commit();
	 		echo "<script type='text/javascript'>alert('Successfully Saved..');
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

    
    //includes the html tag with head section of the web page. (Starts with Body Tag)
    include_once 'Layout/header.php';
    
    //includes the navbar consisting of Nav menus for home page.
    include_once 'Layout/sidebar.php';
    ?>

<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
		<div class="row">
			<div class="col-lg-3 col-xs-6">
				<!-- small box -->
				<div class="small-box bg-green">
					<div class="inner">
					<h3>1</h3>

					<p>Loans</p>
					</div>
					<div class="icon">
					<i class="ion ion-bag"></i>
					</div>
					<a href="#" class="small-box-footer" data-toggle="modal" data-target="#new_loan_app">Apply <i class="fa fa-arrow-circle-right"></i></a>
					<div class="modal fade" id="new_loan_app" role="dialog">
                      	<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title"> <i class="fa fa-plus"></i><?php echo $PanelHeading; ?></h4>
								</div>
							
								<div class="modal-body">
							
									<form id="addloan" name="addloan" role="form" method="post" action="loans.php" enctype="multipart/form-data">
											<input type='hidden' id='mode' name='mode' value="<?php echo $Mode;?>"/>
												<div class="form-group">
													<label class="help-block">Message</label>
													<div id="showerror" class='text-white'></div>
												</div>
												<div class="form-group">
													<label class="help-block">Loan Account</label>
													<?php echo $FMList;?>
												</div>
												<div class="form-group">
													<label class="help-block">MemberID</label>
													<h4 class="help-block"><?php echo $MemberID;?></h4>
												</div>
												<div class="form-group">
													<label class="help-block">Share Balance</label>
													<input type='text' class='form-control' id='sharebalance' value='<?php echo $Data['ClosBal']; ?>' readonly/>
												</div>
												<div class="form-group">
													<label class="help-block">Particulars</label>
													<input type="text" class="form-control" id="particulars" name="particulars" placeholder="Your Remarks" data-toggle="tooltip" data-placement="top" autocomplete="off" value='<?php echo $Data['Particulars'];?>' autofocus required/>
												</div>
												<div class="form-group">
													<label class="help-block">Interest Rate</label>
													<input type="number" readonly class="form-control" id="intrate" name="intrate"  step='0.01' value='<?php echo $Data['IntRate'];?>' required />
												</div>
												<div class="form-group">
													<label class="help-block">Period in Months</label>
													<input type="number" class="form-control" id="months" name="months" value='<?php echo $Data['Months'];?>' max='240' required />
												</div>
												<div class="form-group">
													<label class="help-block">Loan Amount</label>
													<input type="number" class="form-control" id="loanamt" name="loanamt" placeholder="Loan Amount" data-toggle="tooltip" data-placement="top"  value='<?php echo $Data['LoanAmt'];?>' autocomplete="off" required/>
												</div>

												<div class="form-group">
													<label class="help-block">Monthly EMI</label>
													<input type="number" class="form-control" id="mthemi" name="mthemi" data-toggle="tooltip"  value='<?php echo $Data['MthEMI'];?>' data-placement="top" step="1" title="Monthly EMI" maxlength="5" required />
													</div>
												<div class="form-group">
													<label class="help-block">Guarantor-1</label>
													<?php echo $Guarantors1;?>
													</div>
												<div class="form-group">
													<label class="help-block">Guarantor-2</label>
													<?php echo $Guarantors2;?>
												</div>
											
										<div class="modal-footer">
											<input type="submit" id='Submit' value="Apply" name="Submit" data-toggle="tooltip" data-placement="top" title="Click to Save" class="btn btn-success btn-lg">
											<button type="button" class="btn btn-default btn-lg pull-right btn-flat" data-dismiss="modal">Close</button>
										</div>
									</form>
                        		</div>
                    			<!-- /.modal-content -->
                    		</div>
						<!-- /.modal-dialog -->
					</div>
					<!-- /.modal -->
				</div>
			</div>
				<!-- ./col -->
		</div>
		<div class="clearfix"></div>
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title">Loans History</h3>
				</div>
				<!-- /.box-header -->
					<div class="box-body">
					</div>
			</div>
</section>

</div>
  <?php
  //includes the footer section with closure of body tag, the javascript and the html tag
include_once 'Layout/footer.php';
?>