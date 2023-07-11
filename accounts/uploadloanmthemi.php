<?php
	// Author: Anand V Deshpande,Belagavi
	// Date Written : 30.11.2019
	// uploadmthloanemi.php
	// modified on 20.11.2020 for Location
	session_start();
	include('../includes/functions.php');	
	// check usertype
	if(!isset($_SESSION['UserType'])) {
		MsgBox("Direct script access prohibited","../index.php",True);
		exit();
	}

	$UserType 	= $_SESSION['UserType'];
	if($UserType == 'Loans' or $UserType=='Admin'){

	} else{
		MsgBox("Access for Authorised Users only","accountsmenu.php",True);
		exit();
	}
	//	
	$PanelHeading = "Create Monthly Loan EMI";
	include("../includes/loans.php");
	include('../includes/pdofunctions_v1.php');

	$db = connectPDO();
	$Month="";
	$Year = "";
	$PostDate="";
	$IntUptoDate = "";
	$InputFile="";
	$FinalHtml="";
	//$ContrHtml = "";
	//$UploadHtml="";
	$FMList = "";
	if(!isset($_POST['Submit'])) {
		$FMList = genSelectFM($db,"loanfmid"," AcType='Cust' ", " required ");		
	} 
	if(isset($_POST['checkdata'])) {
		$Location   = $_POST['loca'];
		echo "Location=". $Location;

		$Month 		= $_POST['mm'];
		$Year  		= $_POST['yy'];
		$LoanFMID 	= $_POST['loanfmid'];
		$PostDate 	= date("Y-m-d",strtotime($_POST['entrydate']));
		if($_FILES['uploadvariations']) {
			$FileName 	= $_FILES['uploadvariations'];
			$InputFile 	= $FileName['name'];		
		} else{
			$FileName 	= "NoVariations";
			$InputFile 	= "Nil";
		}
		$IntUptoDate = date("Y-m-d",strtotime($_POST['intuptodate']));
		$sql = "Select Count(*) from ft Where ForMonth='$Month' AND ForYear='$Year' AND Credit>0 and TrnType='MthEMI' AND FMID=$LoanFMID LIMIT 1";
		$noofrecs = getSingleField($db,$sql);
		//echo "Noofrecs".$noofrecs;
		// modified on 20.11.2020
		// removed if condition

		//if ($noofrecs >0) {
		//	$FinalHtml = "<h4>Entries are already posted for $Month/$Year</h4>";
		//}else {
		//	//$FileName = $_FILES['uploadvariations'];
		//	//$InputFile = $FileName['tmp_name'];
		//	$FinalHtml = genMthEMIContrTable($db,$Month,$Year,$IntUptoDate,$InputFile,$LoanFMID,$Location);
		//}
		$FinalHtml = genMthEMIContrTable($db,$Month,$Year,$IntUptoDate,$InputFile,$LoanFMID,$Location);

		//$FinalHtml = genMthEMIContrTable($db,$Month,$Year,$IntUptoDate,$InputFile,$LoanFMID);

	}
	if(isset($_POST['Submit'])) {
		// create entries in ft 
		// update customers table for credits and closbal
		// send email to each shareholder with updated loan balance
		//echo "in submit";
		CreateLog("Inside Post 1 line");
		$Month 			= filter_input(INPUT_POST,'mm',FILTER_SANITIZE_NUMBER_INT);
		$Year 			= filter_input(INPUT_POST,'yy',FILTER_SANITIZE_NUMBER_INT);
		$TrnDate 		= date("Y-m-d",strtotime($_POST['entrydate']));
		$IntUptoDt      = date("Y-m-d",strtotime($_POST['intuptodate']));
		$ArrayLoanID 	= $_POST['loanid'];
		$ArrayMthEMI 	= $_POST['mthemi'];
		$ArrayIntFMID   = $_POST['intfmid'];
		$ArrayPrincipal = $_POST['principal'];
		$ArrayInterest  = $_POST['interest'];
		$ArrayDays 		= $_POST['days'];
		$TrnCode 		= "REC";
		$TrnType 		= "MthEMI";
		$FMBankAcc 		= filter_input(INPUT_POST,'bankacc' ,FILTER_SANITIZE_NUMBER_INT);
		// 08.09.2020
		$ArrayIntUptoDt = $_POST['calcintupto'];
		CreateLog("Inside post $TrnDate $IntUptoDt");
		//print_r($ArrayMemberID);
		//print_r($ArrayMthContr);
		//echo $TrnDate;
		$TransCreated = 0;
		$TotalCreditTrn  = 0;
		$FinYear  = genFinYear($TrnDate);
		$MaxTrnNo = genMaxTrnNo($db,$TrnCode,$FinYear,"ft");
		// add individual interest amount to $ArrayIntFMID and at the end post these consolidated interest amount entries in ft
		$ArrTmpIntFMID = array();
		$db->BeginTransaction();
		try{
			for($i=0;$i<count($ArrayLoanID);$i++){
				$Principal  = intval($ArrayPrincipal[$i]);
				$Interest   = intval($ArrayInterest[$i]);
				$Days       = intval($ArrayDays[$i]);
				$LoanID 	= $ArrayLoanID[$i];
				$MthEMI 	= intval($ArrayMthEMI[$i]);
				$IntFMID 	= intval($ArrayIntFMID[$i]);
				$LoanFMID   = getSingleField($db,"Select FMID from customers Where LoanID='$LoanID'");
				// 08.09.2020
				$IndIntUptoDt= date("Y-m-d",strtotime($ArrayIntUptoDt[$i]));
				CreateLog($LoanID." ".$IndIntUptoDt. " Days:".$Days);
				
				// find in array intfmid if not add array element
				$Found = 0;
				for($j=0;$j<count($ArrTmpIntFMID);$j++){
					if($ArrTmpIntFMID[$j][0]  == $IntFMID){
						$ArrTmpIntFMID[$j][1] += $Interest;
						$Found=1;
					}
				}
				if($Found==0){
					array_push($ArrTmpIntFMID,array($IntFMID,$Interest));
				}

				//$TotalCreditTrn += $MthContr;
				//echo "MemberID".$MemberID.":MonthContr:".$MthContr;
				if(strlen($LoanID)>0 and $MthEMI>0){
					$TotalCreditTrn += $MthEMI;
					$Array 	= array(
							"TrnDate"	=> $TrnDate,
							"TrnCode"	=> $TrnCode,
							"TrnType"	=> $TrnType,
							"Credit"	=> $Principal,
							"Particulars"=>"By Monthly Contribution from salary for $Month/$Year ",
							"UserID" 	=> $_SESSION['UserID'],
							"Status"	=> "Ok",
							"ForMonth"	=> $Month,
							"ForYear"	=> $Year,
							"LoanID"  	=> $LoanID,
							"TrnNo"     => $MaxTrnNo,
							"FixFMID"   => $FMBankAcc,
							"FMID" 		=> $LoanFMID,
							"FinYear"	=> $FinYear,
							"Principal" => $Principal,
							"Interest"  => $Interest,
							"Days"      => $Days,
							"IntUptoDt" => $IndIntUptoDt);
					$RetValue 	= insert($db,"ft",$Array);
					$Where 		= "LoanID='$LoanID'";
					$RetValue 	= updateCustomers($db,$Where,0,$Principal);
					$Ret1 		= update($db,"customers",array("LastRecDate"=>$IndIntUptoDt),$Where);

					$Where    = "FMID=$LoanFMID";
					$RetValue = updateFM($db,$Where,0,$Principal);  // Debits,Credits
					// update Interest respectively
					$TransCreated++;
				}
			}
			// now foreach IntFMID consolidated pass an ft entry
			for($i=0;$i<count($ArrTmpIntFMID);$i++){
				$Array 	= array("TrnDate"	=> $TrnDate,
						"TrnCode"	=> $TrnCode,
						"TrnType"	=> $TrnType,
						"Credit"	=> $ArrTmpIntFMID[$i][1],
						"Particulars"=>"By Monthly Contribution from salary for $Month/$Year ",
						"UserID" 	=> $_SESSION['UserID'],
						"Status"	=> "Ok",
						"ForMonth"	=> $Month,
						"ForYear"	=> $Year,
						"TrnNo"     => $MaxTrnNo,
						"FixFMID"   => $FMBankAcc,
						"FMID" 		=> $ArrTmpIntFMID[$i][0],
						"FinYear"	=> $FinYear,
						"Principal" => $Principal,
						"Interest"  => $ArrTmpIntFMID[$i][1],
						"Days"      => $Days,
						"IntUptoDt" => $IntUptoDt);
				$RetValue = insert($db,"ft",$Array);
				$Where    = "FMID=".$ArrTmpIntFMID[$i][0];
				$RetValue = updateFM($db,$Where,0,$ArrTmpIntFMID[$i][1]);  // Debits,Credits
				// update Interest respectively
				$TransCreated++;
			}
			
			$Where    = "FMID='$FMBankAcc'";
			$RetValue = updateFM($db,$Where,$TotalCreditTrn,0);  // bank debit

			$LogDesc = "Monthly EMI for $Month/$Year nos:$TransCreated amt:$TotalCreditTrn";
			$RetVal = insert($db,"logfile",array("LogType"=>'MthEMI',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));	
			$db->commit();
			//SendEmailLoanEMI($db,$Month,$Year);
	 		echo "<script type='text/javascript'>alert('Upload of Loan Mth EMI Successful ..');
					window.location='accountsmenu.php';</script>";	
		} catch(PDOException $ex) {
		    $db->rollBack();
		    $Msg = $ex->getMessage();
			$LogDesc = "Monthly EMI for $Month/$Year ".$Msg;
			$RetVal = insert($db,"logfile",array("LogType"=>'MthEMI',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));	
			exit();
		} catch(Exception $ex) {
		    $db->rollBack();
		    $Msg = $ex->getMessage();
			$LogDesc = "Monthly EMI for $Month/$Year ".$Msg;
			$RetVal = insert($db,"logfile",array("LogType"=>'MthEMI',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));	
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
	<!-- <link  href="../includes/avd.css" rel="stylesheet" />-->
</head>
<style>
	body { 
	  background: url(assets/images/entrance.jpg) no-repeat center center fixed; 
	  -webkit-background-size: cover;
	  -moz-background-size: cover;
	  -o-background-size: cover;
	  background-size: cover;
	}
    input,input:valid {
        border-color: blue;
        background-color: lightblue;
        font-size : 12px;
        height: 24px;        
    }
    input,input:invalid {
        border-color: red;
        background-color: pink;
    }
</style>
<body>
    <div class="container-fluid">
    	<div class="col-md-12">
    		<div class='row'>
				<?php include('accountsmenu.ini'); ?>
			</div>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
                    <center><h4><?php echo $PanelHeading; ?></h4></center>
				</div>
				<div class='panel-body'>
					<div id='showerror'></div>
					<form id='uploadloanemi' name='uploadloanemi' method='post' enctype='multipart/form-data' class='form-inline' action='uploadloanmthemi.php'>
						<div class='row'>
							<h4>Upload Monthly EMI Collection Variations CSV File :  
							<button type='button' class='btn btn-danger btn-sm' 
							onclick=window.location.href='accountsmenu.php'>Back to Menu</button>	
							</h4>
							<?php echo $FMList; ?>
							<label>Location</label>
							<select id='loca' name='loca' class='form-control' required>
								<option value='GIT'>GIT</option>
								<option value='OTH'>Others</option>
							</select>
							<label>Enter Month</label>
							<input id='mm' name='mm' type='number' min="01" max="12" value='<?php echo $Month;?>' class='form-control' required></input>
							<label>Enter Year</label>
							<input id='yy' name='yy' type='number' min='2018' max='2030'  value='<?php echo $Year;?>'class='form-control' required></input>
							<label>Int.Upto</label>
							<input id='intuptodate' name='intuptodate' type='date' value='<?php echo $IntUptoDate; ?>' required />
							<label>Date of Posting</label>
							<input id='entrydate' name='entrydate' type='date'  value='<?php echo $PostDate;?>' required />
							<input type='file' id='uploadvariations' name='uploadvariations' class="form-control" value='<?php echo $InputFile;?>'></input>							
							<input type='submit' class='btn btn-success' id="checkdata" name='checkdata' value='Check Data'></input>
						</div>
						<div class='row'>
							<div id='datatable2' class='table-responsive'>
								<?php echo $FinalHtml; ?>
							</div>
						</div>
					</form>
				</div>
				<div class='panel-footer'>
					<strong><center>Software Developed By Anand V Deshpande,Belagavi</center></strong>
				</div>
			</div>			
		</div>
	</div>
</body>
<script>
  	$(document).ready(function() {	
		$("#loanfmid").val( <?php echo $LoanFMID;  ?>);
	});
	$(document).ready(function() {
    	$("#loca").val('<?php echo $Location;?>');
	});
</script>
