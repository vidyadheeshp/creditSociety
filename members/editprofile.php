
<?php

// editprofile.php
// use PDO
// Author : Anand V Deshpande,Belagavi
// Date Written: 13.12.2019
// for members only
	session_start();
	require_once("../includes/functions.php");
	$_SESSION['rdrurl'] = $_SERVER['REQUEST_URI'];
	// check usertype
	if(!isset($_SESSION['UserType'])) {
		MsgBox("Direct script access prohibited","../index.php",True);
		exit();
	}

	$UserType 	= $_SESSION['UserType'];
	if($UserType == 'Member'){

	} else{
		MsgBox("Access for Members only","membershome.php",True);
		exit();
	}
	//	
	//require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	$PanelHeading = "Edit ShareHolder Profile";
	$db = connectPDO();
	$Mode = "Add";
	$PhotoPath = "";
	$Data = array();
    $MemberRow = $_SESSION['UserRow'];
    foreach($MemberRow as $Row){
        $MemberID = $Row['MemberID'];
        $MemberName = $Row['Name'];
        //echo $MemberName;
    }
	
		$Mode 		= "Edit";
		
		$PanelHeading = "Edit Share Holder";
		$PhotoPath    = "../uploads/$MemberID".".jpeg";
		$handle  	  = @fopen($PhotoPath, 'r');
		// Check if file exists
		if(!$handle){
			if(file_exists("../uploads/$MemberID".".jpg"))
    		$PhotoPath    = "../uploads/$MemberID".".jpg";
			else
			if(file_exists("../uploads/$MemberID".".png"))	
			$PhotoPath = "../uploads/$MemberID".".png";
					
		}
		
		$result = getResultSet($db,"Select * from shareholders Where MemberID='$MemberID' LIMIT 1");
		foreach($result as $row) {

			$Data['MemberID']	=   $MemberID;
			$Data['Name']		=  	$row['Name'];
			$Data['Mobile'] 	= 	$row['Mobile'];
			$Data['Mobile2']	=	$row['Mobile2'];
			$Data['EmailID']	= 	$row['EmailID'];
			$Data['Address1']	=	$row['Address1'];
			$Data['Address2'] 	= 	$row['Address2'];
			$Data['DeptID']		=	$row['DeptID'];
			$Data['DesignID'] 	= 	$row['DesignID'];
			$Data['Aadhar']		=	$row['Aadhar'];
			$Data['PAN'] 		= 	$row['PAN'];
			$Data['BankIFSC']	=	$row['BankIFSC'];
			$Data['BankAcNo'] 	= 	$row['BankAcNo'];
			$Data['DOB']		=	date("Y-m-d",strtotime($row['DOB']));
			$Data['OpenDate'] 	= 	date("Y-m-d",strtotime($row['OpenDate']));
			$Data['DOR'] 		= 	date("Y-m-d",strtotime($row['DOR']));
			$Data['MthContr']	=	$row['MthContr'];
			$Data['OpenBal'] 	=	$row['OpenBal'];
		}
	

	$DepartmentList = "";
	$sql = "Select * from departments Order By DeptName";
	$result = getResultSet($db,$sql);
	$DepartmentList .= "<option value=''>Select Department</option>";
	foreach($result as $row){
		$DepartmentList .= "<option value=".$row['DeptID'].">".$row['DeptName']."</option>";
	}

	$DesignationList = "";
	$sql = "Select * from designation Order By Designation";
	$result = getResultSet($db,$sql);
	$DesignationList .= "<option value=''>Select Designation</option>";
	foreach($result as $row){
		$DesignationList .= "<option value=".$row['DesignID'].">".$row['Designation']."</option>";
	}

	if(isset($_POST['name']) and isset($_POST['submit'])) {
		//echo "Inside Post...";
		//print_r($_POST);
		$Input_MemberID     = 	filter_input(INPUT_POST,'memberid',FILTER_SANITIZE_STRING);
		$MemberID 			=   $Input_MemberID;
		$Data['Name']		=  	filter_input(INPUT_POST,'name',FILTER_SANITIZE_STRING);
		$Data['Mobile'] 	= 	filter_input(INPUT_POST,'mobile',FILTER_SANITIZE_STRING);
		$Data['Mobile2']	=	filter_input(INPUT_POST,'mobile2',FILTER_SANITIZE_STRING);
		$Data['EmailID']	= 	filter_input(INPUT_POST,'emailid',FILTER_SANITIZE_EMAIL);
		$Data['Address1']	=	filter_input(INPUT_POST,'address1',FILTER_SANITIZE_STRING);
		$Data['Address2'] 	= 	filter_input(INPUT_POST,'address2',FILTER_SANITIZE_STRING);
		$Data['DeptID']		=	filter_input(INPUT_POST,'deptid',FILTER_SANITIZE_NUMBER_INT);
		$Data['DesignID'] 	= 	filter_input(INPUT_POST,'desigid',FILTER_SANITIZE_NUMBER_INT);
		$Data['Aadhar']		=	filter_input(INPUT_POST,'aadhar',FILTER_SANITIZE_STRING);
		$Data['PAN'] 		= 	filter_input(INPUT_POST,'pan',FILTER_SANITIZE_STRING);
		$Data['BankIFSC']	=	filter_input(INPUT_POST,'bankifsc',FILTER_SANITIZE_STRING);
		$Data['BankAcNo'] 	= 	filter_input(INPUT_POST,'bankacno',FILTER_SANITIZE_STRING);
		//$Data['Nominee'] 	= 	filter_input(INPUT_POST,'nominee',FILTER_SANITIZE_STRING);
		//$Data['NomineeAddr']=	filter_input(INPUT_POST,'nomineeaddr',FILTER_SANITIZE_STRING);
		//$Data['NomineeRel'] = 	filter_input(INPUT_POST,'nomineerel',FILTER_SANITIZE_STRING);
		$Data['DOB']		=	date("Y-m-d",strtotime(filter_input(INPUT_POST,'dob',FILTER_SANITIZE_STRING)));
		$Data['DOR']		=	date("Y-m-d",strtotime(filter_input(INPUT_POST,'dor',FILTER_SANITIZE_STRING)));
		$Data['OpenDate'] 	= 	date("Y-m-d",strtotime(filter_input(INPUT_POST,'opendate',FILTER_SANITIZE_STRING)));
		$Data['MthContr']	=	filter_input(INPUT_POST,'mthcontr',FILTER_SANITIZE_NUMBER_INT);
		$Data['OpenBal'] 	=	filter_input(INPUT_POST,'openbal',FILTER_SANITIZE_NUMBER_INT);
		//echo "Input_MemberID ".$Input_MemberID;
			// edit existing record
		//echo "Editing record";
		$Debits = getSingleField($db,"Select Debits  from shareholders where MemberID='$MemberID'");
		$Credits= getSingleField($db,"Select Credits from shareholders where MemberID='$MemberID'");
		$Data['ClosBal'] 	=	$Data['OpenBal'] - $Debits + $Credits;
		$PreStmt = 	"UPDATE shareholders Set Name=?,Mobile=?,Mobile2=?,EmailID=?,Address1=?,Address2=?,DeptID=?,DesignID=?,Aadhar=?,PAN=?,BankIFSC=?,BankAcNo=?,DOB=?,OpenDate=?,MthContr=?,OpenBal=?,ClosBal=?,DOR=? Where MemberID=? ";
		//echo $PreStmt;
		$stmt = $db->prepare($PreStmt); 
		$Array = array(
				$Data['Name'],$Data['Mobile'],$Data['Mobile2'],
				$Data['EmailID'],$Data['Address1'],$Data['Address2'],$Data['DeptID'],
				$Data['DesignID'],$Data['Aadhar'],$Data['PAN'],$Data['BankIFSC'],$Data['BankAcNo'],
				$Data['DOB'],
				$Data['OpenDate'],$Data['MthContr'],$Data['OpenBal'],$Data['ClosBal'],$Data['DOR'],$Input_MemberID);
		$stmt->execute($Array);
		if(isset($_FILES['photo'])){
		$Photo = $_FILES['photo'];
		if ($Photo['error']<=0) {
			$FileName = $Photo['name'];
			$newname  = $Input_MemberID; 
			$tmpname  = $Photo['tmp_name'];
			$Target   = '../uploads/'.$newname.".".pathinfo($FileName, PATHINFO_EXTENSION);
			$FileType = $Photo['type'];
			$dname=dirname(getcwd())."\\uploads\\".$Input_MemberID.".jpeg";
			if(file_exists($dname))
			     unlink($dname);
			else
			{
				$dname=dirname(getcwd())."\\uploads\\".$Input_MemberID.".jpg";
				if(file_exists($dname))
					 unlink($dname);
				else{
					$dname=dirname(getcwd())."\\uploads\\".$Input_MemberID.".png";
				if(file_exists($dname))
					 unlink($dname);
				}
			}		
			move_uploaded_file( $tmpname,$Target);
		}			
	}
		$LogDesc = "Self Edit Member $Input_MemberID ".$Data['Name'];
		CreateLog($LogDesc);
		$RetVal = insert($db,"logfile",array("LogType"=>'EditMember',"UserID"=>$_SESSION['UserID'],"Description"=>$LogDesc));			
	 	echo "<script type='text/javascript'>alert('Successfully Saved..');
		 window.location='membershome.php';</script>";			
		exit();
	}

?>

    <?php include_once("navbar.php");?>
		<center>
			<form id="editshareholder" class="myform" name="editshareholder" role="form" method="post" action="editprofile.php" enctype="multipart/form-data">
				<input type='hidden' id='mode' name='mode' value="<?php echo $Mode;?>"/>
				<table id='table1'>
					<tr>
						<td>Message</td>
						<td><div id="showerror"></div></td>
					</tr>
					<tr>
						<td>Photo</td>
						<td><img src='<?php echo $PhotoPath;?>' alt='No Picture' style='width:100px;height:100px;'></td>
						</tr>
						<tr>
							<td>MemberID</td>
							<td><input type="text" class="form-control" id="memberid" name="memberid" placeholder="Your MemberID" data-toggle="tooltip" data-placement="top" title="Enter your Full Name" autocomplete="off" value='<?php echo $Data['MemberID'];?>' readonly/></td>
						</tr>
						<tr>
							<td>Name</td>
							<td><input type="text" class="form-control" id="name" name="name" placeholder="Your Full Name" data-toggle="tooltip" data-placement="top" title="Enter your Full Name" autocomplete="off" value='<?php echo $Data['Name'];?>' autofocus readonly/></td>
						</tr>
						<tr>
							<td>Mobile No.</td>
							<td><input type="text" class="form-control" id="mobile" name="mobile" placeholder="Your Mobile No." data-toggle="tooltip" data-placement="top" title="Enter your 10 digit Mobile No."  value='<?php echo $Data['Mobile'];?>' autocomplete="off" minlength="10" maxlength="10" required/></td>
						</tr>
						<tr>
							<td>Alternate Mobile</td>
							<td> <input type="text" class="form-control" id="mobile2" name="mobile2" placeholder="Your Mobile No."  value='<?php echo $Data['Mobile2'];?>' data-toggle="tooltip" data-placement="top" title="Enter your 10 digit Mobile No." autocomplete="off" minlength="10" maxlength="10"/></td>
						</tr>
						<tr>
							<td>EmailID</td>
							<td> <input type="email" class="form-control" id="emailid" name="emailid" placeholder="Your GIT EmailID"  value='<?php echo $Data['EmailID'];?>' data-toggle="tooltip" data-placement="top" title="Enter your GIT EmailID" autocomplete="off" maxlength="50" required/></td>
						</tr>
						<tr>
							<td>Temporary Address</td>
							<td> <textarea class="form-control" id="address1" name="address1" placeholder="Your Address" data-toggle="tooltip" rows="3" data-placement="top" title="Enter your address" maxlength="255" required><?php echo $Data['Address1'];?></textarea></td>
						</tr>
						<tr>
							<td>Permanent Address</td>
							<td><textarea class="form-control" id="address2" name="address2" placeholder="Your Permanent Address" rows="3" data-toggle="tooltip" data-placement="top" title="Enter your permanent address" maxlength="255" required><?php echo $Data['Address2'];?></textarea></td>
						</tr>
						<tr>
							<td>Department</td>
							<td> <Select class="form-control" id="deptid" name="deptid" required>
		                    	<?php echo $DepartmentList; ?>
		                	</Select></td>
						</tr>
						<tr>
							<td>Designation</td>
							<td> <Select class="form-control" id="desigid" name="desigid" required>
		                    	<?php echo $DesignationList; ?>
		                	</Select></td>
						</tr>
						<tr>
							<td>Aadhar </td>
							<td><input type="text" class="form-control" id="aadhar" name="aadhar" data-toggle="tooltip"  value='<?php echo $Data['Aadhar'];?>' data-placement="top" title="Enter Aadhar No" placeholder="Aadhar Card No" minlength='12' maxlength="12" required /></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="text" class="form-control" id="pan" name="pan" data-toggle="tooltip" data-placement="top"  value='<?php echo $Data['PAN'];?>' title="PAN" placeholder="Enter PAN" maxlength="12" required /></td>
						</tr>
						<tr>
							<td>Bank IFSC</td>
							<td><input type="text" class="form-control" id="bankifsc" name="bankifsc" data-toggle="tooltip"  value='<?php echo $Data['BankIFSC'];?>'  data-placement="top" title="Bank IFSC" placeholder="Enter Bank IFSC Code" maxlength="15" required /></td>
						</tr>
						<tr>
							<td>Bank AccNo</td>
							<td><input type="text" class="form-control" id="bankacno" name="bankacno" data-toggle="tooltip"  value='<?php echo $Data['BankAcNo'];?>' data-placement="top" title="BankAcno" placeholder="Enter Bank Account No" maxlength="15" required /></td>
						</tr>
						<!--
						<tr>
							<td>Nominee</td>
							<td><input type="text" class="form-control" id="nominee" name="nominee" //data-toggle="tooltip"  value='<?php //echo $Data['Nominee'];?>' data-placement="top" title="Nominee" placeholder="Enter Name of Nominee" maxlength="50" required /></td>
						</tr>
						<tr>
							<td>Nominee Address</td>
							<td><textarea class="form-control" id="nomineeaddr" name="nomineeaddr" data-toggle="tooltip" data-placement="top" title="BankAcno" placeholder="Enter Nominee Address" maxlength="255" required ><?php //echo $Data['NomineeAddr'];?></textarea></td>
						</tr>
						<tr>
							<td>Nominee Relation</td>
							<td><input type="text" class="form-control" id="nomineerel" name="nomineerel" data-toggle="tooltip"  value='<?php //echo $Data['NomineeRel'];?>' data-placement="top" title="BankAcno" placeholder="Enter Relation with Nominee" maxlength="20" required /></td>
						</tr>
						-->
						<tr>
							<td>Date of Birth</td>
							<td><input type="date" class="form-control" id="dob" name="dob" data-toggle="tooltip" data-placement="top"  value='<?php echo $Data['DOB'];?>'  title="Date of Birth" maxlength="10" required /></td>
						</tr>
						<tr>
							<td>Joining Date</td>
							<td><input type="date" class="form-control" id="opendate" name="opendate" data-toggle="tooltip"  value='<?php echo $Data['OpenDate'];?>' data-placement="top" title="Date of Joining" maxlength="10" required /></td>
						</tr>
						<tr>
							<td>Retirement</td>
							<td><input type="date" class="form-control" id="dor" name="dor" data-toggle="tooltip"  value='<?php echo $Data['DOR'];?>' data-placement="top" title="Date of Retirement" maxlength="10" required /></td>
						</tr>
						<tr>
							<td>Monthly Contribution</td>
							<td><input type="number" class="form-control" id="mthcontr" name="mthcontr" data-toggle="tooltip"  value='<?php echo $Data['MthContr'];?>' data-placement="top" step="100" title="Monthly Contribution" maxlength="4" required min="100" max="5000" /></td>
						</tr>
						<tr>
							<td>Opening Balance</td>
							<td><input type="number" class="form-control" id="openbal" name="openbal" data-toggle="tooltip"  value='<?php echo $Data['OpenBal'];?>' data-placement="top" title="Opening Balance" maxlength="15" readonly/></td>
						</tr>
						<tr id="phototr" style="height:10px">
							<td>Upload Passport size Photo</td>
							<td> <input type="file" class="button" id="photo" name="photo" accept="image/*" action="camera" onchange="preview_image(event)" required/>
							<img id="output_image" style="height:10px" />                                    
							<div id="showfileerror"></div></td>
						</tr>
						<tr>
				
							<td><input type="submit" value="Save" name="submit" data-toggle="tooltip" data-placement="top" title="Click to Save" >
							</td>
							<td><input type="button" class="button" onclick="location.href ='membershome.php';" value="Cancel">
							
							</td>
						</tr>
						
					</table>
					</form>
						
		
</center>
</body>
</html>
<script>
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

	$("#photo").change(function(){
		checkUploadPhoto();
        $("#output_image").css('height','200px');
	});
	function checkUploadPhoto() {
		$("#showfileerror").html("");
        $("#phototr").css('height','200px');
        
		var x = document.getElementById("photo");
		var txt = "";
		if ('files' in x) {
	    	if (x.files.length == 0) {
	        	alert("Select file to upload");
	    	} else {
	        	for (var i = 0; i < x.files.length; i++) {
	            	txt += "<br><strong>" + (i+1) + ". file</strong><br>";
	            	var file = x.files[i];
	            	if ('name' in file) {
	                	txt += "name: " + file.name + "<br>";
	            	}
	            	if ('size' in file) {
	                	txt += "size: " + file.size + " bytes <br>";
	            	}
	            	
	            	if (file.size > 250000) {
	            		$("#showfileerror").html("<p style='color:red;'>File size should not exceed 250KB</p>");
	            		$("#photo").val("");
	            		event.preventDefault();
	            	}
	        	}
	    	}
	    }
	}
	$("#emailid").change(function(){
		$("#showerror").html("");
		var emailid = $("#emailid").val();
		if( emailid.indexOf("@git.edu") <0){
			$("#showerror").html("<p style='color:red';>Enter your KLS GIT EmailID</p>");
			$("#emailid").val("");
			$("#emailid").focus();
		}
		$.ajax({
	        type: "POST",
	        url: "checkdata.php",
	        data: "FileName=shareholders&FieldName=EmailID&FieldVal="+emailid,
	        success : function(text){
				var ret = JSON.parse(text);
				var ret_val = ret['FieldVal'];
				if (ret_val =='Found') {
					$("#showerror").html("<p style='color:red';>You have already SignedUp</p>");
					$("#emailid").val("");
				}
	        }
		});
	});
</script>
<script>
	$("#deptid").val( <?php echo $Data['DeptID'];  ?>);
	$("#desigid").val(<?php echo $Data['DesignID'];?>);
</script>
