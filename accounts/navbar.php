<?php
 $MemberName="Admin";
 $PhotoPath="../login.png";
?>
<!DOCTYPE html>
<html>
    <head>
        <title>GIT Employees Cooperative Credit Society</title>
        
        <link rel="stylesheet" href="./adminstyles.css"/>
        <link rel="icon" href="../favicon.ico?" type= "image/x-icon"/>
        <link rel="stylesheet" href="../bootstrap/dist/css/bootstrap.css"/>
        <link rel="stylesheet" href="../assets/js/dt/datatables.bootstrap.css"/>
       	<script src="../bootstrap/dist/js/jquery-1.10.2.js"></script>
	    <script src="../bootstrap/dist/js/bootstrap.min.js"></script> 
        
    </head>
    <body>
<div class="top">
   <div class='header'>
      <img class="logo" src="../git.png" alt="GIT"> <h1 class="title">GIT Employees Cooperative Credit Society</h1>
      <h4 class="estd">Estd:1993</h4>
   </div>
   <nav>
   
      <div class="drpdown-menu" style="padding:0px 4px 0px ;">
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
</div>
<div class="left">
   <div class="sidenav">
  
   <div class="drpdown-menu">
      <button class="menu-btn">Transactions
         <i>&#9660;</i>
      </button>
      <div class="menu-content">
         <a class="links-hidden" href="uploadmthsharecontr.php">Monthly Share Contributions</a>
         <a class="links-hidden" href="uploadloanmthemi.php">Monthly LoanEMI Collections</a>
         <a class="links-hidden" href="bankentries_list.php">Bank Transactions List</a>
         <a class="links-hidden" href="ftentries_list.php">GL Transaction List</a>
         <a class="links-hidden" href="sharevariations_list.php">Monthly Share Variations List</a>
         <a class="links-hidden" href="loanappl_list.php">Loan Application List</a>
      </div>
   </div>
   <br/>
   <div class="drpdown-menu">
      <button class="menu-btn">Reports
      <i>&#9660;</i>
      </button>
      <div class="menu-content">
      
         <a class="links-hidden" href="sharesdeptwisesummary.php">Shares-Deptwise-Summary</a>
         <a class="links-hidden" href="sharesmonthwise.php">Shares-Monthwise-Summary</a>
         <a class="links-hidden" href="emicalculator.php">EMI Calculator</a>
         <a class="links-hidden" href="loanmonthwisesummary.php">Loans-Monthwise-Summary</a>
         <a class="links-hidden" href="repsharemthcontr.php">share Mth contr List</a>
         <a class="links-hidden" href="reploanmthemi.php">Loan Mth EMI List</a>
         <a class="links-hidden" href="sharetrans_list.php">Share Collection List</a>
         <a class="links-hidden" href="loantrans_list.php">Loans Recovery List</a>
         <a class="links-hidden" href="repforloancollection.php">Loans Statement for Collection</a>
         <a class="links-hidden" href="rep_loan_coll_summary.php">Loan Collection Summary</a>
         <a class="links-hidden" href="reploangiven.php">Loan Given Statement</a>
         <a class="links-hidden" href="rep_bankbook.php">Bank Book</a>
         <a class="links-hidden" href="rep_banksummary.php">Bank Summary</a>
         <a class="links-hidden" href="rep_tb1.php">Trial Balance</a>
         <a class="links-hidden" href="rep_tb2.php">Trial Balance-Detailed</a>
         <a class="links-hidden" href="rep_recpay1.php">Receipt and Payment</a>
         <a class="links-hidden" href="rep_tb3.php">Trial Balance-ShareHolders</a>
         <a class="links-hidden" href="rep_tb4.php">Trial Balance-ShareHolders-Detailed</a>
         <a class="links-hidden" href="rep_tb5.php">Trial Balance-Loans</a>
         <a class="links-hidden" href="rep_tb6.php">Trial Balance-Loans-Detailed</a>
         <a class="links-hidden" href="rep_incexp1.php">Income & Expenditure</a>
         <a class="links-hidden" href="rep_genledgers1.php">General Ledgers</a>
         <a class="links-hidden" href="exceptionalreport.php">Exceptional Report</a>
    
      </div>
   </div>
   <br/>
   <div class="drpdown-menu">
         <button class="menu-btn">Masters
         <i>&#9660;</i>
         </button>
         <div class="menu-content">
            <a class="links-hidden" href="">Share Holders loantrans_list</a>
            <a class="links-hidden" href="">Loan Accounts List</a>
            <a class="links-hidden" href="">Loan Accounts List Edit</a>
            <a class="links-hidden" href="">General Ledger List</a>
            <a class="links-hidden" href="">Calculate Divident(1)</a>
            <a class="links-hidden" href="">Calculate Divident(2)</a>
            <a class="links-hidden" href="">ReCalculate Balances</a>
         </div>
   </div>
   <div class="drpdown-menu">
      <button class="menu-btn" onclick="location.href='viewlogfile.php';"  >View LogFile</button>
   </div>
  
   </div>
</div>