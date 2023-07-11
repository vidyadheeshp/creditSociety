<?php

// bankentries_list.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 26.11.2019
//	
session_start();
require_once("../includes/functions.php");
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

require_once("../includes/pdofunctions_v1.php");

$db = connectPDO();
$PanelHeading = "Bank Transactions List ";
$Report       = "";
$FromDate     = date("Y-m-d");
$ToDate       = date("Y-m-d");
?>

	<?php
		include_once('navbar.php');
	?>
			<div class="main">
	

    	
		
				<div>
                    <center><h3><b><?php echo $PanelHeading; ?></h3></b></center>
				</div>
				
				<div class='bankentrieslistbg'>
					
					<div>
						<center>
							<label class="mylabel">Enter From Date:</label>
							<input type='date' class='smallinput' id='fromdate' name='fromdate' value='<?php echo $FromDate;?>'/>
							<label class="mylabel">Enter To Date:</label>
							<input type='date' id='todate' class='smallinput' name='todate' value='<?php echo $ToDate;?>' />
							<button type='button' id='showtrnlist' class='smallinput' onclick='showBankTranList()'>Show Transaction List</button>
							<button type='button' class='smallinput' onclick="js_addBankTran()">Add Bank Transaction</button>
						</center>
						</div>
					
						<div id='banktranlist' class='table-responsive'>
							<?php echo $Report; ?>
						</div>
					</div>
					
				</div>

		</div>
		<form id="hiddenform" method="post" action="bankentries.php">
		<input type="hidden" id="hiddenFTID" name="hiddenFTID" readonly/>
		<input type="hidden" id="hidden_type"  name="hidden_type"  readonly/>
		<input type="submit" id="hiddenformSubmit">
	</form>
</body>

<script>
$("#hiddenformSubmit").prop("hidden",true);
  	$(document).ready(function() {
   		$('#gridtable').DataTable({"lengthMenu": [ 10, 25, 50, 75, 100,500,1000]});
		   	
		   	});	

	function showBankTranList(){
		var fromdate = $("#fromdate").val();
		var todate   = $("#todate").val();
		//alert(fromdate);
		$.ajax({
	        type: "POST",
	        url: "getbanktranlist.php",
	        data: "fromdate="+fromdate+"&todate="+todate,
	        success : function(text){
				
	        	$("#banktranlist").html(text);
		   		$('#gridtable').DataTable({"lengthMenu": [ 10, 25, 50, 75, 100,500,1000]});
	        }
		});
	};	
	
	// modify following as reqd
	function js_addBankTran() {
		$("#hiddenFTID").val(0);
       	$("#hidden_type").val("Add");
       	$("#hiddenform").submit();
		//window.location.href="shareholders.php?MemberID="+memberid;
	}
	/*
	function js_editbanktran(tranid) {
		alert("Edit TranID:" + tranid);
       	$("#hiddenFTID").val(tranid);
       	$("#hidden_type").val("Edit");
       	$("#hiddenform").submit();
		//window.location.href="shareholders.php?MemberID="+memberid;
	}
	*/
	function js_delbanktran(ftid) {
		if(confirm("Delete this Transaction ID="+ftid + " ?")){
			$.ajax({
		        type: "POST",
		        url: "bankentries_del.php",
		        data: "FTID="+ftid,
		        success : function(text){
		        	alert(text);
		        	location.reload(true);
		        }
			});
		}
	}
</script>
