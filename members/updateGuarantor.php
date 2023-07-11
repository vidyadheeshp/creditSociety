<?php
	session_start();
	require_once("../includes/functions.php");
	require_once("../includes/pdofunctions_v1.php");
	require_once("../includes/loans.php");
	$db = connectPDO();
   
	$LoanRecSet="";
	$LoanID="";

if(isset($_POST['LoanID'])){
        
    $LoanID   	= $_POST['LoanID'];
        if(isset($_POST['flag'])){
         
            $set=array();
            $set[$_POST['gnumber']]=$_POST['g1memberid'];
            $where="LoanID=$LoanID";
            $res=update($db,'loanappl',$set,$where); 
            if($res==true){
                
                echo '<script>alert("Welcome to Geeks for Geeks")
                window.location.href="memberhome.php";
                </script>';
            }
        }
    }