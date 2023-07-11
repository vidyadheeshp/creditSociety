<?php

// ftenties.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 09.11.2019
//	
	session_start();
	// check usertype
	if(!isset($_SESSION['UserType'])) {
		MsgBox("Direct script access prohibited","../index.php",True);
		exit();
	}

	$UserType 	= $_SESSION['UserType'];
	if(strstr('Accounts,Admin,Chairman',$UserType)){
	} else{
		MsgBox("Access for Authorised Users only","accountsmenu.php",True);
		exit();
	}
	//
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();
	$PanelHeading = " General Ledger Voucher Entry";
	$userErr="";
	$Mode   = "";
	$FTHtml = "";
	$TrnDate = date("Y-m-d");
	$Data['UserID'] = $_SESSION['UserID'];
	//$FTHtml = "<table id='fttable1' name='fttable1' class='table table-condensed table-striped table-bordred table-responsive'>";
	//$FTHtml .= "<tr><thead>";
	//$FTHtml .= "<th>Account Name</th>";
	//$FTHtml .= "<th>Particulars</th>";
	//$FTHtml .= "<th>Balance</th>";
	//$FTHtml .= "<th>Debit</th>";
	//$FTHtml .= "<th>Credit</th>";
	//$FTHtml .= "</tr></thead><tbody>";
	for($i=0;$i<=9;$i++){
		$FTHtml .= "<tr>";
		$FTHtml .= "<td>".genSelectFM($db,"fmid[]",1,"")."</td>";
		//$FTHtml .= "<td>"."<input class='form-control smallwidth' type='text' id='particulars[]'    //name='particulars[]' cols='50' maxlength='100' />"."</td>";
		$FTHtml .= "<td>"."<input class='form-control smallwidth' type='text' id='balance[]'   name='balance[]' readonly value='0' />"."</td>";
		$FTHtml .= "<td>"."<input class='form-control smallwidth' type='number' id='debit[]'   name='debit[]' value='0' />"."</td>";
		$FTHtml .= "<td>"."<input class='form-control smallwidth' type='number' id='credit[]' name='credit[]' value='0' />"."</td>";
		$FTHtml .= "</tr>";
	}
	//$FTHtml .= "</tbody></table>";
	//echo $FTHtml;
	if(isset($_POST['submit']) and isset($_POST['totaldebit']) and isset($_POST['totalcredit'])){
		print_r($_POST);
		$ArrayFMID 		= $_POST['fmid'];
		$Particulars 	= $_POST['particulars'];
		$ArrayDebit 	= $_POST['debit'];
		$ArrayCredit 	= $_POST['credit'];
		$Data['TrnDate']= date("Y-m-d",strtotime($_POST['trndate']));
		$Data['TrnCode']= $_POST['trncode'];
		$Data['TrnType']= "";
		$TrnCode        = $_POST['trncode'];
		$TrnType 		= "";
		$TrnDate        = date("Y-m-d",strtotime($_POST['trndate']));
		$Data['FinYear']= genFinYear($TrnDate);
		$FinYear 		= $Data['FinYear'];
		try {
			// 
			$db->BeginTransaction();
			$MaxTrnNo = genMaxTrnNo($TrnCode,$FinYear,"ft");
			$Data['TrnNo'] = $MaxTrnNo;
			for($i=0;$i<count($ArrayFMID);$i++){
				$Data['FMID'] 		= intval($ArrayFMID[$i]);
				$Data['Particulars']= $Particulars;
				$Data['Debit'] 		= intval($ArrayDebit[$i]);
				$Data['Credit'] 	= intval($ArrayCredit[$i]);				
				if($Data['FMID']>0 and ($Data['Debit']+$Data['Credit']>0)) {

					$PreStmt = 	"INSERT INTO ft(FMID,TrnCode,TrnType,TrnDate,TrnNo,Debit,Credit,Particulars,UserID,FinYear) 
						VALUES(?,?,?,?,?,?,?,?,?,?)";
					echo $PreStmt;
					if($Data)
					$Array = array(
							$Data['FMID'],$Data['TrnCode'],$Data['TrnType'],
							$Data['TrnDate'],$Data['TrnNo'],$Data['Debit'],$Data['Credit'],
							$Data['Particulars'],$Data['UserID'],$Data['FinYear']);
					print_r($Array);
					$stmt = $db->prepare($PreStmt); 
					$stmt->execute($Array);
					echo "ft1";
					echo "ft2";
					// update fm
					$Where 		= "FMID=".$Data['FMID'];
					if($Data['Debit']>0){
						$RetValue 	= updateFM($db,$Where,$Data['Debit'],0);
					} else{
						$RetValue 	= updateFM($db,$Where,0,$Data['Credit']);
					}
				}
			}
			// create logfile
			$Amount = $_POST['totaldebit'];
			$UserID = $_SESSION['UserID'];

			$LogDesc = "Accounting $TrnDate $TrnCode TrnNo:$TrnNo posted Amount:$Amount  User:$UserID ";
			$RetVal = insert($db,"logfile",array("LogType"=>'MthShare',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));	
			CreateLog($LogDesc);
			$db->commit();

	 		echo "<script type='text/javascript'>alert('Successfully Saved..');
				window.location='ftentries_list.php';</script>";	   					
			exit();
		} catch(PDOException $ex) {
		    //Something went wrong rollback!
		    $db->rollBack();

		    echo $ex->getMessage();
			$LogDesc = "Accounting Error encountered $TrnDate $TrnCode TrnNo:$TrnNo Amount:$Amount  User:$UserID ".$ex->getMessage();
			CreateLog($LogDesc);
	 		echo "<script type='text/javascript'>alert('Something went wrong..');
				window.location='ftentries_list.php';</script>";	   					
			exit();
		}	
		catch(Exception $ex) {
		    $db->rollBack();
		    echo $ex->getMessage();
			$LogDesc = "Accounting Error encountered $TrnDate $TrnCode TrnNo:$TrnNo Amount:$Amount  User:$UserID ".$ex->getMessage();
			CreateLog($LogDesc);
	 		echo "<script type='text/javascript'>alert('Something went wrong..');
				window.location='ftentries_list.php';</script>";	   					
			exit();
		}	
		// here all the sql commands have executed correctly
		// so commit changes to the database
		exit();
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
		<div class='col-md-8 col-md-offset-2'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
    	        	<center><h4><?php echo $PanelHeading; ?></h4></center>
				</div>
				<div class='panel-body'>
					<form id="ftentries" name="ftentries" role="form" method="post" action="ftentries.php" enctype="multipart/form-data">
					<input type='hidden' id='mode' name='mode' value="<?php echo $Mode;?>"/>
					<table id='table0'>
						<tr>
							<td><label>Transaction Date</label></td>
							<td><input type='date' class='form-control' id='trndate' name='trndate' 
							value='<?php echo $TrnDate;?>' />
							</td>
							<td><label>Tran.Type</label></td>
							<td><select id='trncode' name='trncode' class='form-control' >
								<option value='REC'>Receipt</option>
								<option value='PMT'>Payment</option>
								<option value='JV'>Journal Voucher</option>
							</select>
							</td>
							<td><label>Particulars</label>
							<td>
								<textarea id='particulars' name='particulars' rows='2' class='form-control' required></textarea>
							</td>
						</tr>
					</table>
					
					
					<table id='fttable1' name='fttable1' class='table table-condensed table-striped table-bordred table-responsive'>
						<thead><tr>
						<th>Account Name</th>
						<th>Balance</th>
						<th>Debit</th>
						<th>Credit</th>
						</tr>
						</thead>
						<tbody>
							<?php echo $FTHtml; ?>
						</tbody>
					</table>
					<table>
						<tr>
							<td>Total Debit: <input id='totaldebit' name='totaldebit' class='form-control' readonly/></td>
							<td>Total Credit: <input id='totalcredit' name='totalcredit' class='form-control' readonly/></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" value="Save" name="submit" data-toggle="tooltip" data-placement="top" title="Click to Save" class="btn btn-success">
							<span class="pull-right">
							<a href='ftentries_list.php' class='btn btn-danger'>Back to List</a>
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
	$("#name").change(function(){
		$("#showerror").html("");
		var accname = $("#name").val();
		$.ajax({
	        type: "POST",
	        url: "checkdata.php",
	        data: "FileName=fm&FieldName=Name&FieldVal="+accname,
	        success : function(text){
				var ret = JSON.parse(text);
				var ret_val = ret['FieldVal'];
				if (ret_val =='Found') {
					$("#showerror").html("<p style='color:red';>Account already exists</p>");
					$("#name").val("");
				}
	        }
		});
	});
	$("#fmid").change(function(){
		$("#showerror").html("");
		var fmid = $("#fmid").val();
		alert(fmid);
		/*
		$.ajax({
	        type: "POST",
	        url: "checkdata.php",
	        data: "FileName=fm&FieldName=Name&FieldVal="+accname,
	        success : function(text){
				var ret = JSON.parse(text);
				var ret_val = ret['FieldVal'];
				if (ret_val =='Found') {
					$("#showerror").html("<p style='color:red';>Account already exists</p>");
					$("#name").val("");
				}
	        }
		});*/
	});	
    $(document).on('change', '#fttable1 tbody td', function () {
        var colIndex = parseInt( $(this).index() );
        var rowIndex = parseInt( $(this).parent().index() )+1;  
        //alert("Row:"+rowIndex + " ColIndex: "+colIndex)    	;
    	var fmid;
    	if(colIndex==0) {
	    	fmid = document.getElementById("fttable1").rows[rowIndex].cells[0].childNodes[0].value;
	    	if(fmid) {
				$.ajax({
			        type: "POST",
			        url: "ajax_getfield.php",
			        data: "fmid="+fmid,
			        success : function(text){
			        	document.getElementById("fttable1").rows[rowIndex].cells[1].childNodes[0].value=text;
					}
				});
	    	}
    	}
    	//js_calcTotals();
    });
    function js_calcTotals() {
        var i = 0;
        var errors=0;
        var fmid="";
        console.log("Entered calcTotals");
        //var t = document.getElementById('fttable1'); 
        js_TotalDebit 	= 0;
        js_TotalCredit	= 0;
        $("#fttable1 tr").each(function() {
	    	fmid = document.getElementById("fttable1").rows[i].cells[0].childNodes[0].value;        	
        	console.log("row"+i);
            var tempdebit = document.getElementById("fttable1").rows[i].cells[2].childNodes[0].value;
            var tempcredit= document.getElementById("fttable1").rows[i].cells[3].childNodes[0].value;
            console.log("FmID "+fmid+ " tempdebit "+tempdebit+ " tempcredit "+tempcredit);
            if((tempdebit) && (tempcredit)){
            	if(tempdebit>0 && tempcredit>0) {
	            	alert("Please enter either debit or credit");
	            	document.getElementById("fttable1").rows[i].cells[2].childNodes[0].value ="";
	            	document.getElementById("fttable1").rows[i].cells[3].childNodes[0].value ="";
	            	tempdebit=0;
	            	tempcredit=0;
	            	errors++;
            	}
            }
            if(tempdebit) {
	           	if(parseInt(tempdebit<0)){
	           		alert("-ve not allowed");
	            	document.getElementById("fttable1").rows[i].cells[2].childNodes[0].value ="";
	            	tempdebit=0;
	            	errors++;
	           	}
	        }
	        if(tempcredit){
	           	if(parseInt(tempcredit<0)){
	           		alert("-ve not allowed");
	            	document.getElementById("fttable1").rows[i].cells[3].childNodes[0].value ="";
	            	tempcredit=0;
	            	errors++;
	           	}
	        }

	    	if(fmid) {
	    		if(fmid >0 && (tempdebit+tempcredit<=0)){
	    			alert("Please enter Either Debit/Credit");
	    			errors++;
	    		}
	    	}
            if(tempdebit){
	            js_TotalDebit += parseInt(tempdebit);
            }
            if(tempcredit){
	            js_TotalCredit += parseInt(tempcredit);
            }
            i++;
        });  
        $("#totaldebit" ).val(js_TotalDebit);                
        $("#totalcredit").val(js_TotalCredit);  
        alert("Errors found "+errors);              
        return errors;
    };

    $("form").submit(function(event){
        // Stop form from submitting normally
        //check all the element values 
        alert("Inside submit");
		var errors = js_calcTotals();
		alert("Errors found in submit "+errors);
        var debit 	= parseInt($("#totaldebit").val());
        var credit 	= parseInt($("#totalcredit").val());
        alert(debit);
        alert(credit);
        if((debit>0) && (debit != credit)) {
        	alert("Total Debit/Credit do not tally");
        	event.preventDefault();
        	return false;
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
            $("#result").html(data);
        });
    });

</script>
<script>

</script>

