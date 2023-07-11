<?php include_once("header.html");
include_once("navbar.html");

session_start();
session_destroy();
session_start();

date_default_timezone_set("Asia/Kolkata");	
require_once("includes/pdofunctions_v1.php");
require_once("includes/functions.php");
$userErr = "";
define('BASEPATH','UsedIndexPage');
$_SESSION['UserName']  = "";
$_SESSION['UserType'] =  "";
$_SESSION['mainpath'] = getcwd();

if (isset($_POST['username'])) {
  $db = ConnectPDO();
  if(!$db) {
    MsgBox("Couldnot Connect Database","",true);
    exit();
  }
  //build_table($db,"Select * from shareholders","");		
  $PasswordMD5 = MD5(filter_input(INPUT_POST,'password',FILTER_SANITIZE_STRING));
  $Password    = filter_input(INPUT_POST,'password',FILTER_SANITIZE_STRING);
  $UserName    = filter_input(INPUT_POST,'username',FILTER_SANITIZE_STRING);
  $Sql 	     = "Select count(*) from users Where Password='$PasswordMD5' AND LoginID='$UserName'";
  $SysUsers    = getSingleField($db,$Sql);
  if($SysUsers>0) {
    $_SESSION['VALIDUSER'] = "Yes";
    $_SESSION['UserRow']  = getResultSet($db,"Select * from users Where Password='$PasswordMD5' AND LoginID='$UserName' and Status='Active'");
    $Sql = "Select Name from users Where Password='$PasswordMD5' AND LoginID='$UserName' and Status='Active'";
    MsgBox($Sql,"",true);
    $_SESSION['UserName']  = getSingleField($db,$Sql);
    $Sql = "Select UserType from users Where Password='$PasswordMD5' AND LoginID='$UserName' and Status='Active'";
    $_SESSION['UserType'] =  getSingleField($db,$Sql);
    $Sql = "Select UserID from users Where Password='$PasswordMD5' AND LoginID='$UserName' and Status='Active'";
    $_SESSION['UserID']   = getSingleField($db,$Sql);
    $_SESSION['MemberID'] = "";
    $_SESSION['mainpath'] = getcwd();
  } else {
    //$Sql 	  = "Select count(*) from shareholders Where MemberID='$Password' AND 
    //	( EmailID='$UserName' or Mobile='$UserName')";
    $Sql 	  = "Select count(*) from shareholders Where (DOB='$Password' OR Password='$Password') AND 
      ( MemberID ='$UserName')";
      $Members  = getSingleField($db,$Sql);
    if($Members>0) {
      define('VALIDUSER','Yes');
      $_SESSION['UserRow']  = getResultSet($db,"Select * from shareholders Where (DOB='$Password' OR Password='$Password') AND 
      ( MemberID ='$UserName')");
      $_SESSION['UserName']  = getSingleField($db,"Select Name from shareholders Where (DOB='$Password' OR Password='$Password') AND 
      ( MemberID ='$UserName')");
      $_SESSION['UserType'] =  "Member";
      $_SESSION['UserID'] =  0;
      $_SESSION['MemberID'] = getSingleField($db,"Select MemberID from shareholders Where (DOB='$Password' OR Password='$Password') AND 
      ( MemberID ='$UserName')");
      $_SESSION['mainpath'] = getcwd();
      MsgBox('','',false);
    }else {
      session_destroy();
      session_start();
      $_SESSION['UserName'] = "Invalid";
      $_SESSION['UserType'] = "";
      MsgBox('Error!!! Invalid Member Credentials','login.php',true);
    }
  }
  MsgBox($_SESSION['UserName'],'',false);
  if($_SESSION['UserType'] == "Member") {
    header("location: members/membershome.php");
    exit();
  }
  if (strstr("Admin,Chairman,Accounts,Shares,Loans",$_SESSION['UserType'])) {
    header("location: accounts/accountsmenu.php");
    exit();
  }
}
else {
}
?>
<style>
    .login-page {
  width: 30%;
  padding: 8% 0 0;
  margin: auto;
}
.login-page .form .login{
  margin-top: -31px;
margin-bottom: 26px;
}
.form {
  position: relative;
  z-index: 1;
  background: #f5deec;
  max-width: 80%;
  margin: 0 auto 100px;
  padding: 10px;
  padding-top: 50px;
  text-align: center;
  -webkit-border-radius: 10px;
  box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
}
.form input {
  font-family: "Roboto", sans-serif;
  outline: 0;
  color: rgb(105, 74, 74);
  width: 100%;
  border: 0;
  margin: 0 0 10px;
  padding: 5px;
  box-sizing: border-box;
  -webkit-border-radius: 5px;
  font-size: 16px;
}
#login {
  font-family: "Roboto", sans-serif;
  text-transform: uppercase;
  outline: 0;
  background-color:  #454545;
  font-size: 18px;
    font-weight: bold;
  width: 100%;
  border: 0;
  padding: 15px;
  color: #FFFFFF;
  font-size: 14px;
  -webkit-transition: all 0.3 ease;
  transition: all 0.3 ease;
  cursor: pointer;
}
.form .message {
  margin: 15px 0 0;
  color: #b3b3b3;
  font-size: 12px;
}
.form .message a {
  color: #4CAF50;
  text-decoration: none;
}


td {
  border-style: none;
}
    </style>
<center>
<div class="login-page">
      <div class="form">
        <div class="login">
          <div class="login-header">
            <h3>LOGIN</h3>
            <p>Please enter your credentials to login.</p>
          </div>
        </div>
        <form class="login-form" action="#" method="post">
          <table>
            <tr>
              <td id="lbl"><label>User Name</label></td>
              <td><input type="text" name="username" placeholder="username"/></td>
            </tr>
            <tr>
              <td id="lbl"><label>Password</label></td>
              <td><input type="password" name="password" placeholder="password"/></td>
            </tr>
            <tr>
              <td colspan=2><input type="submit" id="login" value="Login" name="login"></td>
            </tr>
</table>
          <a class="message" href="">Forgot Password</a>
          <div id="error"></div>
        </form>
      </div>
    </div>