
<?php
session_start();
require_once("../includes/functions.php");
require_once("../includes/pdofunctions_v1.php");
require_once("../includes/loans.php");
$db = connectPDO();
if(isset($_POST['LoanID'])){
    $LoanID=$_POST['LoanID'];
  
    $result=getSingleField($db,"delete from loanappl where LoanID='$LoanID'");
    if($result==""){
        echo "Loan Application Deleted Successfully";
    }
}
?>