<?php
	// Author: Anand V Deshpande,Belagavi
	// uploadmthsharecontr.php
	// modified on 23.11.2020 for location 

	session_start();
	include('../includes/functions.php');	
	// check usertype
	if(!isset($_SESSION['UserType'])) {
		MsgBox("Direct script access prohibited","../index.php",True);
		exit();
	}

	$UserType 	= $_SESSION['UserType'];
	if($UserType == 'Shares' or $UserType=='Admin'){

	} else{
		MsgBox("Access for Authorised Users only","accountsmenu.php",True);
		exit();
	}
	//	
	$PanelHeading = "Create Monthly Share Contributions";
	include('../includes/pdofunctions_v1.php');

	include('../includes/shares.php');

	$db = connectPDO();
	$Month="";
	$Year = "";
	$PostDate="";
	$InputFile="";
	$FinalHtml="";
	//$ContrHtml = "";
	//$UploadHtml="";
	if(isset($_POST['checkdata'])) {
		$Location   = $_POST['loca'];		
		$Month 		= $_POST['mm'];
		$Year  		= $_POST['yy'];
		$PostDate 	= date("Y-m-d",strtotime($_POST['entrydate']));
		if($_FILES['uploadvariations']) {
			$FileName = $_FILES['uploadvariations'];
			$InputFile = $FileName['name'];		
		} else{
			$FileName = "NoVariations";
			$InputFile = "Nil";
		}
		$sql = "Select Count(*) from ft Where Location='$Location' AND ForMonth='$Month' AND ForYear='$Year' AND Credit>0 and TrnType='MthContr' LIMIT 1";
		$noofrecs = getSingleField($db,$sql);
		//echo "Noofrecs".$noofrecs;

		if ($noofrecs >0) {
			$FinalHtml = "<h4>Entries are already posted for $Month/$Year</h4>";
		}else {
			//$FileName = $_FILES['uploadvariations'];
			//$InputFile = $FileName['tmp_name'];
			$FinalHtml = genMthShareContrTable($db,$Month,$Year,$PostDate,$InputFile,$Location);
		}
	}
	if(isset($_POST['Submit'])) {
		// create entries in ft 
		// update shareholders table for credits and closbal
		// send email to each shareholder with updated share balance
		//echo "in submit";
		$Location       = filter_input(INPUT_POST,'loca',FILTER_SANITIZE_STRING);
		$Month 			= filter_input(INPUT_POST,'mm',FILTER_SANITIZE_NUMBER_INT);
		$Year 			= filter_input(INPUT_POST,'yy',FILTER_SANITIZE_NUMBER_INT);
		$TrnDate 		= date("Y-m-d",strtotime($_POST['entrydate']));
		$ArrayMemberID = $_POST['memberid'];
		$ArrayMthContr = $_POST['mthcontr'];
		$TrnCode 		= "REC";
		$TrnType 		= "MthContr";
		$FMShareAcc	    = filter_input(INPUT_POST,'shareacc',FILTER_SANITIZE_NUMBER_INT);
		$FMBankAcc 		= filter_input(INPUT_POST,'bankacc' ,FILTER_SANITIZE_NUMBER_INT);
		//print_r($ArrayMemberID);
		//print_r($ArrayMthContr);
		//echo $TrnDate;
		$TransCreated = 0;
		$TotalCreditTrn  = 0;
		$FinYear  = genFinYear($TrnDate);
		$MaxTrnNo = genMaxTrnNo($db,$TrnCode,$FinYear,"ft");		
		$db->BeginTransaction();
		for($i=0;$i<count($ArrayMemberID);$i++){
			$MemberID 	= $ArrayMemberID[$i];
			$MthContr 	= intval($ArrayMthContr[$i]);
			//$TotalCreditTrn += $MthContr;
			//echo "MemberID".$MemberID.":MonthContr:".$MthContr;
			if(strlen($MemberID)>0 and $MthContr>0){
				$TotalCreditTrn += $MthContr;
				//$SHID 	= getSingleField($db,"Select SHID From shareholders Where MemberID='$MemberID'");
				$Array 	= array("MemberID"	=> $MemberID,
						"TrnDate"	=> $TrnDate,
						"TrnCode"	=> $TrnCode,
						"TrnType"	=> $TrnType,
						"Credit"	=> $MthContr,
						"Particulars"=>"By Monthly Contribution from salary for $Month/$Year ",
						"UserID" 	=> $_SESSION['UserID'],
						"Status"	=> "Ok",
						"ForMonth"	=> $Month,
						"ForYear"	=> $Year,
						"MemberID"  => $MemberID,
						"TrnNo"     => $MaxTrnNo,
						"FixFMID"   => $FMBankAcc,
						"FMID" 		=> $FMShareAcc,
						"Location"  => $Location,
						"FinYear"	=> $FinYear);
				$RetValue = insert($db,"ft",$Array);
				$Where 		= "MemberID='$MemberID'";
				$RetValue 	= updateShareHolders($db,$Where,0,$MthContr);
				$TransCreated++;
			}
		}

		//$Array = array("FMID"=>$FMShareAcc,"TrnCode"=>$TrnCode,"TrnDate"=>$TrnDate,"TrnType"=>$TrnType,"Credit"=>$TotalCreditTrn,"TrnNo"=>$MaxTrnNo,"Particulars"=>"By Monthly Contribution from salary for $Month/$Year ","UserID"=>$_SESSION['UserID'],"FinYear"=>$FinYear);
		//$RetValue = insert($db,"ft",$Array);

		$Where    = "FMID='$FMShareAcc'";
		$RetValue = updateFM($db,$Where,0,$TotalCreditTrn);  // Debits,Credits

		// change for BankAcc
		//$Array = array("FMID"=>$FMBankAcc,"TrnCode"=>$TrnCode,"TrnDate"=>$TrnDate,"TrnType"=>$TrnType,"Debit"=>$TotalCreditTrn,"TrnNo"=>$MaxTrnNo,"Particulars"=>"By Monthly Contribution from salary for $Month/$Year ","UserID"=>$_SESSION['UserID'],"FinYear"=>$FinYear);
		//$RetValue = insert($db,"ft",$Array);
		
		$Where    = "FMID='$FMBankAcc'";
		$RetValue = updateFM($db,$Where,$TotalCreditTrn,0);  // bank debit

		$LogDesc = "Monthly Share Contr for $Month/$Year nos:$TransCreated amt:$TotalCreditTrn";
		$RetVal = insert($db,"logfile",array("LogType"=>'MthShare',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));	

		$db->commit();
		$RetVal = SendEmailShareMthContr($db,$Month,$Year);
		echo "<script type='text/javascript'>alert('Share Contributions created $TransCreated');
		window.location.href='accountsmenu.php';</script>";
	}
?>
<?php 

include_once('navbar.php');
?>

<center>
	<h3><b>Upload Monthly Share Contribution Variations CSV File</b></h3>

	<div class="main">
		<div id='showerror'></div>
		<form id='uploadsharecontr' class="myform" name='uploadsharecontr' method='post' enctype='multipart/form-data'  action='uploadmthsharecontr.php'>
						
			<table class="mytable">
				<tr>	
					<td><label class='mylabel'>Location</label></td>
					<td><select id='loca' name='loca' class='drop' required>
							<option class='drop' value='GIT'>GIT</option>
							<option class='drop' value='OTH'>Others</option>
						</select>
					</td>
				</tr>
				<tr>
					<td><label class='mylabel'>Enter Month</label></td>
					<td><input id='mm' name='mm' class="smallinput" type='number' min="01" max="12" value='<?php echo $Month;?>'  required></input></td>
				</tr>
				<tr>
					<td><label class='mylabel'>Enter Year &nbsp;&nbsp;&nbsp;&nbsp;</label></td>
					<td><input id='yy' name='yy' class="smallinput" type='number' min='2018' max='2030'  value='<?php echo $Year;?>'  required></input></td>
				</tr>
				<tr>
					<td><label class='mylabel'>Date of Posting </label></td>
					<td><input id='entrydate' class="smallinput" name='entrydate' type='date'  value='<?php echo $PostDate;?>' required /></td>
				</tr>
				
				<tr>
					<td colspan=2 ><center><input type='file' class="smallinput" id='uploadvariations' name='uploadvariations' value='<?php echo $InputFile;?>'></input></center></td>
				</tr>
				<tr>
					<td colspan=2><input type='submit' id="checkdata" name='checkdata' value='Check Data'></input></td>
				</tr>
			</table>
		</form>
		<div class='row'>
				<div id='datatable2' class='table-responsive'>
					<?php echo $FinalHtml; ?>
				</div>
			</div>
	</div>			
</center>			

<script>
$(document).ready(function() {
    $("#loca").val('<?php echo $Location;?>');
});
</script>

