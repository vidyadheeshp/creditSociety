<?php
   $MemberRow = $_SESSION['UserRow'];
   foreach($MemberRow as $Row){
      $MemberID = $Row['MemberID'];
      $MemberName = $Row['Name'];
      //echo $MemberName;
   }
   $PhotoPath="login.png";
   if(file_exists("../uploads/$MemberID".".jpg"))
      $PhotoPath    = "../uploads/$MemberID".".jpg";
   else
      if(file_exists("../uploads/$MemberID".".png"))	
         $PhotoPath = "../uploads/$MemberID".".png";
      else if(file_exists("../uploads/$MemberID".".jpeg"))
         $PhotoPath="../uploads/$MemberID".".jpeg";
?>
<?php include_once 'header.html';?>

<body>
   <div class='header'>
      <img class="logo" src="git.png" alt="GIT"/> <h1 class="title">GIT Employees Cooperative Credit Society</h1>
      <h4 class="estd">Estd:1993</h4>
   </div>
   <nav>
      <a class="links" href="membershome.php">HOME</a>
      <div class="dropdown-menu">
         <button class="menu-btn">FACILITIES</button>
         <div class="menu-content">
            <a class="links-hidden" href="loans.php">Loans</a>
            <a class="links-hidden" href="updateshare.php">Update Share Contribution</a>
            <a class="links-hidden" href="updateemi.php">Update EMI</a>
            <a class="links-hidden" href="closemembership.php">Membership Closure</a>
         </div>
      </div>
      <a class="links" href="emicalculator.php">EMI CALCULATOR</a>
      <div class="dropdown-menu">
         <button class="menu-btn">
            <img src='<?php echo $PhotoPath;?>' alt='No Picture' style='width:25px;height:20px;'/>&nbsp;<?php echo $MemberName ?>
         </button>
         <div class="menu-content">
            <a class="links-hidden" href="editprofile.php">Edit Profile</a>
            <a class="links-hidden" href="resetpassword.php">Reset Password</a>
            <a class="links-hidden" href="logout.php">Logout</a>
         </div>
      </div>
   </nav>