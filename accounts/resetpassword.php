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
$UserName="";
$loginid="";
$oldpwd="";
   $Date = date("d-m-Y H:i");
   //var_dump($_SESSION['UserRow']);
   $MemberRow = $_SESSION['UserRow'];
   $MemberName="";
   $MemberID="";
  
   foreach($MemberRow as $Row){
    $userid= $Row['UserID'];
    $loginid=$Row['LoginID'];
    $oldpwd=$Row['Password'];
   }
   
if(isset($_POST['submit'])){
    $old=MD5(filter_input(INPUT_POST,'old',FILTER_SANITIZE_STRING));
    $pass=MD5(filter_input(INPUT_POST,'pass',FILTER_SANITIZE_STRING));
    if(strcmp($old,$oldpwd)==0)
    {
        $query="update `users` set password='$pass' where LoginID='$loginid' ";
        $Count=getSingleField($db,$query);
        echo "<script type=''javascript'>alert('Your Password is Reset. Kindly re-login');
        window.location.href='$DisplayPage';
        </script>";
        header("location:logout.php");
    }
    else
    {
        CreateLog(" $MemberID $MemberName resetting password failed due to wrong entry of current password ");
		MsgBox("You have entered invalid current password","resetpassword.php",true);
		exit();
    }
}   

?>
 <?php include_once('navbar.php');
   ?>
     <script>
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

  
   <div class="main">
   <center>
        <h3><b>Reset Password</b></h3>
        <form action="#" class="myform" method="post">
        <table class="mytable">
            <tr>
                <td><label class="mylabel">Enter old password:</label></td>
                <td class="inpt"><input class="myinput" type="password" required name='old'></td>
            </tr>
            <tr>
                <td><label class="mylabel">Enter new password:</label></td>
                <td class="inpt"><input class="myinput" type="password" id="txtPassword" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" title="minimum 8 characters and has at-least one uppercase, one lowercase, one special character(@$!%*?&) and one Number" name="pass"></td>
            </tr>
            <tr>
                <td><label class="mylabel">Confirm new password:</label></td>
                <td class="inpt"><input class="myinput" type="password" id="txtConfirmPassword" name="cpass"></td>
            </tr>
            <tr >
                <td colspan=2 style="text-align:center;"><input type="submit" name="submit" value="Submit"></td>
            </tr>
        </table>
</form>
</center>
</div>
</body>
</html>