<?php
   $MemberRow = $_SESSION['UserRow'];
   foreach($MemberRow as $Row){
      $MemberID = $Row['MemberID'];
      $MemberName = $Row['Name'];
      //echo $MemberName;
   }
?>
<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="../images/avatar.png" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo $MemberName; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <li>
          <a href="membershome.php">
            <i class="fa fa-dashboard"></i> <span>Home</span>
            <!--span class="pull-right-container">
              <small class="label pull-right bg-red">3</small>
              <small class="label pull-right bg-blue">17</small>
            </span-->
          </a>
        </li>
        <li>
          <a href="loans.php">
            <i class="fa fa-money"></i> <span>Loans</span>
            <span class="pull-right-container">
              <small class="label pull-right bg-red">3</small>
              <small class="label pull-right bg-blue">17</small>
            </span>
          </a>
        </li>
       
        <li>
          <a href="updateshare.php">
            <i class="fa fa-rupee"></i> <span>Update Share Contribution</span>
          </a>
        </li>
        <li>
          <a href="updateemi.php">
            <i class="fa fa-bank"></i> <span>Update EMI</span>
          </a>
        </li>
        <li>
          <a href="closemembership.php">
            <i class="fa fa-user-times"></i> <span>Membership Closure</span>
          </a>
        </li>
        
        
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- =============================================== -->