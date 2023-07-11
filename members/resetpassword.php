<?php
session_start();
include('../includes/pdofunctions_v1.php');
   $db = connectPDO();
   include('../includes/functions.php'); 
   include('../includes/shares.php'); 
if(!isset($_SESSION['UserType'])) {
    MsgBox("Direct script access prohibited","../index.php",True);
    exit();
}

   $Date = date("d-m-Y H:i");
   //var_dump($_SESSION['UserRow']);
   $MemberRow = $_SESSION['UserRow'];
   $MemberName="";
   $MemberID="";
   foreach($MemberRow as $Row){
       $MemberID = $Row['MemberID'];
       $MemberName = $Row['Name'];
   }
   
if(isset($_POST['submit'])){
    $old=filter_input(INPUT_POST,'old',FILTER_SANITIZE_STRING);
    $pass=filter_input(INPUT_POST,'pass',FILTER_SANITIZE_STRING);
    $Count = getSingleField($db,"Select count(*) from `shareholders` where MemberID='$MemberID' and (password='$old' or DOB='$old')");
    if($Count==0){
        CreateLog(" $MemberID $MemberName resetting password failed due to wrong entry of current password ");
		MsgBox("You have entered invalid current password","resetpassword.php",true);
		exit();
    }
    else{
        $query="update `shareholders` set password='$pass' where MemberID='$MemberID' ";
        $Count=getSingleField($db,$query);
        echo "<script type=''javascript'>alert('Your Password is Reset. Kindly re-login');
        window.location.href='$DisplayPage';
        </script>";
        header("location:logout.php");
    }
}   
?>
<?php include_once('../header.html');
?>
<style>
    th, td {
  border-style: none;
  width: 40%;
}
.inpt{
    width: 60%;
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
  -webkit-border-radius: 5px;
  font-size: 16px;
}
input[type=submit] {
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
     <script type="text/javascript">
    window.onload = function () {
        var txtPassword = document.getElementById("txtPassword");
        var txtConfirmPassword = document.getElementById("txtConfirmPassword");
        txtPassword.onchange = ConfirmPassword;
        txtConfirmPassword.onkeyup = ConfirmPassword;
        function ConfirmPassword() {
            txtConfirmPassword.setCustomValidity("");
            if (txtPassword.value != txtConfirmPassword.value) {
                txtConfirmPassword.setCustomValidity("Passwords do not match.");
            }
        }
    }
</script>

<body>
   <?php include_once('navbar.php');
   ?>
   <center>
        <h2>Reset Password</h2>
        <form action="#" class="myform" method="post">
        <table>
            <tr>
                <td>Enter old password:</td>
                <td class="inpt"><input type="password" required name='old'></td>
            </tr>
            <tr>
                <td>Enter new password:</td>
                <td class="inpt"><input type="password" id="txtPassword" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" title="minimum 8 characters and has at-least one uppercase, one lowercase, one special character(@$!%*?&) and one Number" name="pass"></td>
            </tr>
            <tr>
                <td>Confirm new password:</td>
                <td class="inpt"><input type="password" id="txtConfirmPassword" name="cpass"></td>
            </tr>
            <tr >
                <td colspan=2 style="text-align:center;"><input type="submit" name="submit" value="Submit"></td>
            </tr>
        </table>
</form>
</center>
