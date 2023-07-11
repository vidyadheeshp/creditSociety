<?php
//include_once("LoanPDF.php");
// applyloan.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 22.12.2019
	//echo "Inside loanaccounts.php1";	
	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");		

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
    if(isset($_GET['LoanID'])){
        $LoanID=$_GET['LoanID'];
	    $result = getResultSet($db,"Select * from loanappl Where LoanID='$LoanID'");
		$G1Approved=getSingleField($db,"select G1Approved from loanappl where LoanID='$LoanID'");
		$G2Approved=getSingleField($db,"select G2Approved from loanappl where LoanID='$LoanID'");
	    if(count($result)==0){
		    CreateLog(" $MemberID $MemberName wanted to apply for loan ");
		    echo "Invalid LoanID=$LoanID";
		    exit();	
	    }
        else{
            
	

	$Mo1de 				= "Edit";
	$PanelHeading 		= "Edit Loan Application";
	$LoanFMID="";
	$G1MemberID="";
	$G2MemberID="";	
	$Data = array();
	$Data['FMID'] = $result[0]['FMID'];
	$Data['MemberID'] 	= $MemberID;
	$Data['Particulars']= $result[0]['Description'];
	$Data['G1MemberID'] = $result[0]['G1MemberID'];
	$Data['G2MemberID'] = $result[0]['G2MemberID'];
	$Data['Months'] 	= $result[0]['Months'];
	$Data['MthEMI']     = $result[0]['MthEMI'];
	$Data['IntRate']    = $result[0]['IntRate'];
	$Data['LoanAmt']    = $result[0]['LoanAmt'];

	$Data['ClosBal']    = getSingleField($db,"Select ClosBal from shareholders Where MemberID='$MemberID'");
	$FMList         	= setSelectFM($db,"loanfmid"," AcType='Cust' ", " required ",$Data['FMID']);
	$Guarantors1	 	= setShareHoldersSelect3($db," Where ClosBal>0","g1memberid",$Data['G1MemberID']);   
	$Guarantors2 		= setShareHoldersSelect3($db," Where ClosBal>0 ","g2memberid",$Data['G2MemberID']);   
    	$PanelHeading 	= "Edit Loan Account";
	//print_r($_POST);	
	

?>
<style>
    th, td {
  border-style: none;
  width: 40%;
}
.inpt{
    width: 60%;
}
form{
  position: relative;
  z-index: 1;
  background: #f5deec;
  max-width: 40%;
  margin: 0 auto 100px;
  padding: 45px;
  text-align: center;
  align-content:center;
  border-radius: 10px;
  box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
}
input {
  font-family: "Roboto", sans-serif;
  outline: 0;
  color: rgb(105, 74, 74);
  width: 100%;
  border: 0;
  margin: 0 0 10px;
  padding: 10px;
  box-sizing: border-box;
  border-radius: 5px;
  font-size: 16px;
}
input[type=submit],select,option {
    padding:10px 15px; 
    background: #454545; 
	color:white;
    border:0 none;
    cursor:pointer;
    width: 100%;
    -webkit-border-radius: 5px;
    border-radius: 5px; 
    font-size: 18px;
    font-weight: bold;
}
</style>
<?php include_once("../header.html");
include_once("navbar.php");
?>
<center><h3><?php echo $PanelHeading; ?></h3>
				</div>
				
				<div class='panel-body'>
					<form id="addloan" name="addloan" role="form" method="post" action="editloan.php" enctype="multipart/form-data">
					<input type='hidden' id='mode' name='mode' value="<?php echo $Mode;?>"/>
					<input type='hidden' id='loanid' name='LoanID' value="<?php echo $LoanID;?>"/>
					<table id='table1' class='table table-bordered table-condensed bluecolor'>
						<tr>
							<td>Message</td>
							<td><div id="showerror" class='text-white'></div></td>
						</tr>
						<tr>
							<td>Loan Account</td>
							<td><?php echo $FMList;?>
						</tr>
						<tr>
							<td>MemberID</td>
							<td><?php echo $MemberID;?></td>
						</tr>
						<tr>
							<td>Share Balance</td>
							<td><input type='text' class='form-control' id='sharebalance' value='<?php echo $Data['ClosBal']; ?>' readonly/></td>
						</tr>
						<tr>
							<td>Particulars</td>
							<td><input type="text" class="form-control" id="particulars" name="particulars" placeholder="Your Remarks" data-toggle="tooltip" data-placement="top" autocomplete="off" value='<?php echo $Data['Particulars'];?>' autofocus required/></td>
						</tr>
						<tr>
							<td>Interest Rate</td>
							<td> <input type="number" readonly class="form-control" id="intrate" name="intrate"  step='0.01' value='<?php echo $Data['IntRate'];?>' required /></td>
						</tr>
						<tr>
							<td>Period in Months</td>
							<td> <input type="number" class="form-control" id="months" name="months" value='<?php echo $Data['Months'];?>' max='240' required /></td>
						</tr>
						<tr>
							<td>Loan Amount</td>
							<td><input type="number" class="form-control" id="loanamt" name="loanamt" placeholder="Loan Amount" data-toggle="tooltip" data-placement="top"  value='<?php echo $Data['LoanAmt'];?>' autocomplete="off" required/></td>
						</tr>

						<tr>
							<td>Monthly EMI</td>
							<td><input type="number" class="form-control" id="mthemi" name="mthemi" data-toggle="tooltip"  value='<?php echo $Data['MthEMI'];?>' data-placement="top" step="1" title="Monthly EMI" maxlength="5" required /></td>
						</tr>
						<tr>
							<td>Guarantor-1</td>
							<td> <?php echo $G1Approved;
							if(strtolower($G1Approved)=='no'||strtolower($G1Approved)=='')
							 echo $Guarantors1;?></td>
						</tr>
						<tr>
							<td>Guarantor-2</td>
							<td><?php echo $G2Approved;
							if(strtolower($G2Approved)=='' ||strtolower($G2Approved)=='no' )
							echo $Guarantors2;?></td>
						</tr>
						
						<tr>
							<td></td>
							<td><input type="submit" id='Submit' value="Save" name="Submit" data-toggle="tooltip" data-placement="top" title="Click to Save" class="btn btn-success">
							<span class="pull-right">
							</span></td>
						</tr>
						</table>
					</form>
				</div>
				

			</div>
</center>

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
  	//debugger;
	$("#loanfmid").change(function(){
		$("#showerror").html("");
		var loanfmid = $("#loanfmid").val();
		var message  = "";
		$.ajax({
	        type: "POST",
	        url: "ajax_getloansettings.php",
	        data: "LoanFMID="+loanfmid,
	        success : function(text){
	        	$("#intrate").val(text);
				$("#showerror").html(message);
	        }
		});
	});
	$("#loanamt").change(function(){
		
		$("#showerror").html("");
		var loanamt = $("#loanamt").val();
		var intrate = $("#intrate").val();
		var months  = $("#months").val();
		var years   = months / 12;
		var message  = "";
		
		$.ajax({
	        type: "POST",
	        url: "ajax_getemi.php",
	        data: "LoanAmt="+loanamt+"&IntRate="+intrate+"&Years="+years,
	        success : function(text){
			
	        	$("#mthemi").val(text);
				$("#showerror").html(message);
	        }
		});
	});
    $("form").submit(function(event){
        // Stop form from submitting normally
        //check all the element values 
        var errors = 0;
        if($("#g1memberid").val() == $("#g2memberid").val()) {
        	alert("Select two different Guarantors");
        	errors++;
        }
        if($("#loanamt").val()<=0){
        	alert("Please enter valid Loan Amt");
        	errors++;
        }
        if($("#intrate").val()<=0){
        	alert("Please Select Loan Account");
        	errors++;
        }
        if($("#months").val()<=0){
        	alert("Please enter Number of Months");
        	errors++;
        }
        if($("#mthemi").val()<=0){
        	alert("Please enter all details correctly");
        	errors++;
        }

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
            // $("#result").html(data);
        });
    });


</script>
<?php 
    }
}
    ?>