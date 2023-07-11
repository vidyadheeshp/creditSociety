<?php

// genacc.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 09.11.2019
//	
	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$db = connectPDO();

	$PlBSList = genSelectPlBs($db);

	$PanelHeading = " General Ledger Account";
	$Mode = "Add";
	$Data = array();
	$Data['FMID'] = 0;
	$Data['Name'] = "";
	$Data['AcType']="";
	$Data['PlBsCode']="";
	$Data['OpenBal']=0;
	$Data['Debits']=0;
	$Data['Credits']=0;
	$Data['ClosBal']=0;
	if(isset($_POST['hiddenFormAction'])) {
		// add,edit,del from genacc_list
		//MsgBox("hiddenFormAction Set","",true);
		//print_r($_POST);
		$Mode = $_POST['hiddenFormAction'];
		$FMID = $_POST['hiddenFormFMID'];
		CreateLog("Edit FMID $FMID");
		if($Mode=='Edit'){
		//'MsgBox("hiddenFormAction Set Edit $FMID ","",true);			
			$result = getResultSet($db,"Select * from fm Where FMID='$FMID' LIMIT 1");
			foreach($result as $row) {

				$Data['FMID'] 		= $row['FMID'];
				$Data['Name'] 		= $row['Name'];
				$Data['AcType']		= $row['AcType'];
				$Data['PlBsCode']	= $row['PlBsCode'];
				$Data['OpenBal']	= $row['OpenBal'];
				$Data['Debits']		= $row['Debits'];
				$Data['Credits']	= $row['Credits'];
				$Data['ClosBal']	= $row['ClosBal'];
				print_r($Data);
			}
		}
	}elseif(isset($_POST['submit'])){
		// for saving,deleting from genacc.php
		$Data['FMID'] 		= filter_input(INPUT_POST,'fmid',FILTER_SANITIZE_NUMBER_INT);
		$Data['AcType'] 	= filter_input(INPUT_POST,'actype',FILTER_SANITIZE_STRING);
		$Data['PlBsCode'] 	= filter_input(INPUT_POST,'plbscode',FILTER_SANITIZE_STRING);
		$Data['OpenBal']  	= filter_input(INPUT_POST,'openbal',FILTER_SANITIZE_NUMBER_INT);
		$Data['Name'] 		= filter_input(INPUT_POST,'name',FILTER_SANITIZE_STRING);
		if($_POST['mode']=='Add'){
			try {
				// 
				$db->BeginTransaction();
				$Data['ClosBal'] = $Data['OpenBal'];
				$Data['Debits']	 = 0;
				$Data['Credits'] = 0;
				$PreStmt = 	"INSERT INTO fm(Name,AcType,PlBsCode,OpenBal,ClosBal) 
					VALUES(?,?,?,?,?)";
				//echo $PreStmt;
				$stmt = $db->prepare($PreStmt); 
				$Array = array(
						$Data['Name'],$Data['AcType'],$Data['PlBsCode'],
						$Data['OpenBal'],$Data['ClosBal']);
				$stmt->execute($Array);
				//print_r($Array);
				$affected_rows 	= $stmt->rowCount();
				//echo "Aff.rows".$affected_rows;
				$InsertID 		= $db->lastInsertId();
				$db->commit();
		 		echo "<script type='text/javascript'>alert('Successfully Saved..');
					window.location='genacc_list.php';</script>";	   					
				exit();
			} catch(PDOException $ex) {
			    //Something went wrong rollback!
			    $db->rollBack();

			    $Msg = $ex->getMessage();
		 		MsgBox('Add Gen Acc Something went wrong..',"",True);
		 		MsgBox("$Msg","genacc_list.php",True);
				exit();
			}
			catch(Exception $ex) {
			    $db->rollBack();
			    $Msg = $ex->getMessage();
		 		MsgBox('Add Gen Acc Something went wrong..',"",True);
		 		MsgBox("$Msg","genacc_list.php",True);
				exit();
			}	
		}elseif($_POST['mode']=='Edit'){
			try {
				// 
				$FMID = $Data['FMID'];
				$db->BeginTransaction();
				$Data['ClosBal'] = $Data['OpenBal'];
				$Data['Debits']	 = getSingleField($db,"Select Debits  from fm Where FMID='$FMID'");
				$Data['Credits'] = getSingleField($db,"Select Credits from fm Where FMID='$FMID'");
				$PreStmt = 	"UPDATE fm Set Name=?,AcType=?,PlBsCode=?,OpenBal=?,ClosBal=? Where FMID=?";
				//echo $PreStmt;
				$stmt = $db->prepare($PreStmt); 
				$Array = array(
						$Data['Name'],$Data['AcType'],$Data['PlBsCode'],
						$Data['OpenBal'],$Data['ClosBal'],$FMID);
				$stmt->execute($Array);
				//print_r($Array);
				$affected_rows 	= $stmt->rowCount();
				//echo "Aff.rows".$affected_rows;
				$InsertID 		= $db->lastInsertId();
				$db->commit();
				MsgBox("Successfully Saved..AccID $FMID","genacc_list.php",True);
				exit();
			} catch(PDOException $ex) {
			    //Something went wrong rollback!
			    $db->rollBack();

			    $Msg = $ex->getMessage();
				MsgBox("Something went wrong Editing..AccID $FMID ","genacc_list.php",True);
				MsgBox("$Msg","",True);
				exit();
			}
			catch(Exception $ex) {
			    $db->rollBack();
			    $Msg = $ex->getMessage();
				MsgBox("Something went wrong Editing..AccID $FMID","genacc_list.php",True);
				MsgBox("$Msg","",True);
				exit();
			}	
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
					<form id="genacc" name="genacc" role="form" method="post" action="genacc.php" enctype="multipart/form-data">
					<input type='hidden' id='mode' name='mode' value="<?php echo $Mode;?>"/>
					<table id='table1' class='table table-bordered table-condensed bluecolor'>
						<tr>
							<td>Message</td>
							<td><div id="showerror"></div></td>
						</tr>
						<tr>
							<td>AccountID</td>
							<td><input type="number" class="form-control" id="fmid" name="fmid" data-toggle="tooltip" data-placement="top" value='<?php echo $Data['FMID'];?>' readonly/></td>
						</tr>
						<tr>
							<td>Name</td>
							<td><input type="text" class="form-control" id="name" name="name" placeholder="Name of General Ledger " data-toggle="tooltip" data-placement="top" autocomplete="off" value='<?php echo $Data['Name'];?>' autofocus required/></td>
						</tr>
						<tr>
							<td>Account Type</td>
							<td><Select class="form-control" id="actype" name="actype" required>
									<option value='Others'>Others</option>
									<option value='SC'>ShareHolder Control A/c</option>
									<option value='Bank'>Bank Account</option>
									<option value='Cash'>Cash Account</option>
									<option value='Cust'>Customers Control A/c</option>
								</Select>
							</td>
						</tr>
						<tr>
							<td>P&L B/S</td>
							<td><?php echo $PlBSList; ?>
							</td>
						</tr>
						<tr>
							<td>Opening Balance</td>
							<td><input type="number" class="form-control" id="openbal" name="openbal" data-toggle="tooltip"  value='<?php echo $Data['OpenBal'];?>' data-placement="top" title="Opening Balance" maxlength="15" required /></td>
						</tr>
						<tr>
							<td>Debits</td>
							<td><input type="number" class="form-control" id="debits" name="debits" data-toggle="tooltip"  value='<?php echo $Data['Debits'];?>' data-placement="top" title="Total Debits" maxlength="15" readonly /></td>
						</tr>
						<tr>
							<td>Credits</td>
							<td><input type="number" class="form-control" id="credits" name="credits" data-toggle="tooltip"  value='<?php echo $Data['Credits'];?>' data-placement="top" title="Total Credits" maxlength="15" readonly /></td>
						</tr>
						<tr>
							<td>Closing Balance</td>
							<td><input type="number" class="form-control" id="closbal" name="closbal" data-toggle="tooltip"  value='<?php echo $Data['ClosBal'];?>' data-placement="top" title="Closing Balance" maxlength="15" readonly /></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" value="Save" name="submit" data-toggle="tooltip" data-placement="top" title="Click to Save" class="btn btn-success">
							<span class="pull-right">
							<a href='genacc_list.php' class='btn btn-danger'>Back to List</a>
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

</script>
<script>

	$("#plbscode").val('<?php echo $Data['PlBsCode'];?>');	
	$("#fmid").val(    <?php echo $Data['FMID'];?>);
	$("#actype").val(  '<?php echo $Data['AcType'];?>');
</script>
