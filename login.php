<?php
//session_start();
//session_destroy();
//session_start();

date_default_timezone_set("Asia/Kolkata");
require_once("includes/pdofunctions_v1.php");
require_once("includes/functions.php");
$userErr = "";
define('BASEPATH', 'UsedIndexPage');
$_SESSION['UserName']  = "";
$_SESSION['UserType'] =  "";
$_SESSION['mainpath'] = getcwd();

if (isset($_POST['username'])) {
  $db = ConnectPDO();
  if (!$db) {
    MsgBox("Couldnot Connect Database", "", true);
    exit();
  }
  //build_table($db,"Select * from shareholders","");		
  $PasswordMD5 = MD5(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW));
  $Password    = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
  $UserName    = filter_input(INPUT_POST, 'username', FILTER_UNSAFE_RAW);
  
  $Sql        = "Select count(*) from users Where Password='$PasswordMD5' AND LoginID='$UserName'";
  $SysUsers    = getSingleField($db, $Sql);
  if ($SysUsers > 0) {
    session_start();
    $_SESSION['VALIDUSER'] = "Yes";
    $_SESSION['UserRow']  = getResultSet($db, "Select * from users Where Password='$PasswordMD5' AND LoginID='$UserName' and Status='Active'");
    $Sql = "Select Name from users Where Password='$PasswordMD5' AND LoginID='$UserName' and Status='Active'";
    //MsgBox($Sql,"",true);
    $_SESSION['UserName']  = getSingleField($db, $Sql);
    $Sql = "Select UserType from users Where Password='$PasswordMD5' AND LoginID='$UserName' and Status='Active'";
    $_SESSION['UserType'] =  getSingleField($db, $Sql);
    $Sql = "Select UserID from users Where Password='$PasswordMD5' AND LoginID='$UserName' and Status='Active'";
    $_SESSION['UserID']   = getSingleField($db, $Sql);
    $_SESSION['MemberID'] = "";
    $_SESSION['mainpath'] = getcwd();
    

  } else {
    //$Sql 	  = "Select count(*) from shareholders Where MemberID='$Password' AND 
    //	( EmailID='$UserName' or Mobile='$UserName')";
    $Sql     = "Select count(*) from shareholders Where (DOB='$Password' OR Password='$Password') AND 
      ( MemberID ='$UserName')";
    $Members  = getSingleField($db, $Sql);
    if ($Members > 0) {
      session_start();
      define('VALIDUSER', 'Yes');
      $_SESSION['UserRow']  = getResultSet($db, "Select * from shareholders Where (DOB='$Password' OR Password='$Password') AND 
      ( MemberID ='$UserName')");
      $_SESSION['UserName']  = getSingleField($db, "Select Name from shareholders Where (DOB='$Password' OR Password='$Password') AND 
      ( MemberID ='$UserName')");
      $_SESSION['UserType'] =  "Member";
      $_SESSION['UserID'] =  0;
      $_SESSION['MemberID'] = getSingleField($db, "Select MemberID from shareholders Where (DOB='$Password' OR Password='$Password') AND 
      ( MemberID ='$UserName')");
      $_SESSION['mainpath'] = getcwd();
      MsgBox('', '', false);


      
    } else {
      session_destroy();
      session_start();
      $_SESSION['UserName'] = "Invalid";
      $_SESSION['UserType'] = "";

      MsgBox('Error!!! Invalid Member Credentials', 'login.php', true);
    }
  }
  MsgBox($_SESSION['UserName'], '', false);
  if ($_SESSION['UserType'] == "Member") {
    header("location: members/membershome.php");

      exit();
    
  }
  if (strstr("Admin,Chairman,Accounts,Shares,Loans", $_SESSION['UserType'])) {
   header("location: accounts/accountsmenu.php");
    exit();
  }
} else {
}
?>

<?php //include_once 'navbar.php' 
    
    //includes the html tag with head section of the web page. (Starts with Body Tag)
    include_once 'includes/Layout/header.php';
    
    //includes the navbar consisting of Nav menus for home page.
    include_once 'includes/Layout/navbar.php';
    ?>
    
<div class=" hold-transition login-page">
  <section class="content">
  <div class="login-box">
  <div class="login-logo">
    <a href="index.php"><b>GIT </b>Employees Cooperative Credit Society</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Sign in to start your session</p>

    <form action="#" method="post">
      <div class="form-group has-feedback">
      <input type="text" name="username" placeholder="Your Username" class="form-control"/>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
      <input type="password" name="password" placeholder="Your Password" class="form-control"/>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <!--div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox"> Remember Me
            </label>
          </div>
        </div-->
        <!-- /.col -->
        <div class="col-xs-4 pull-right">
        <input type="submit" class="btn btn-primary  btn-block btn-flat" id="login" value="Sign In" name="login"/ >
        
        </div>
        <!-- /.col -->
      </div>
    </form>

    <a href="#">I forgot my password</a><br>
    <!--a href="register.html" class="text-center">Register a new membership</a-->

  </div>
  <!-- /.login-box-body -->
</div>
</div>
<!-- /.login-box -->
</section>
</div>     
  <?php
      //includes the footer section with closure of body tag, the javascript and the html tag
    include_once 'includes/Layout/footer.php';
    ?>