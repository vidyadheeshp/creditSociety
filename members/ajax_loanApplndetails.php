<?php
	// Author: Anand V Deshpande
	// Date written : 09.11.2019
	//ajax_loanaccountledger.php
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
    
	$LoanRecSet = getResultSet($db,"Select * from loanappl Where LoanID='$LoanID'");
	foreach ($LoanRecSet as $LoanRow) {
		$MemberID 	= $LoanRow['MemberID'];
		$MemberName = getSingleField($db,"Select Name from shareholders Where MemberID='$MemberID'");
		$LoanAmt    = $LoanRow['LoanAmt'];
		$IntRate    = $LoanRow['IntRate'];
		$applnDate   = date("d-m-Y",strtotime($LoanRow['ApplOn']));
        $G1MemberID = $LoanRow['G1MemberID'];
        if($LoanRow['G1Approved']==''){
            $G1Approved="Pending";
        }
        else{
            if($LoanRow['G1Approved']=="No"){
               
            }
            else{
                $G1Approved = $LoanRow['G1Approved'];
            }
        }
		$G1Name     = getSingleField($db,"Select Name from shareholders Where MemberID='$G1MemberID'");
		$G2MemberID = $LoanRow['G2MemberID'];
        if($LoanRow['G2Approved']=='')
        {
            $G2Approved="pending";
        }
        else
        {
            if($LoanRow['G1Approved']=="No"){
                
            }
            else{
                $G2Approved=$LoanRow['G2Approved'];
            }
        }
		$G2Name     = getSingleField($db,"Select Name from shareholders Where MemberID='$G2MemberID'");
		$MthEMI 	= $LoanRow['MthEMI'];
	
    }
    $Header = "\n<h4>Loan Application Details:$LoanID</h4>\n";
    $Html=" <strong> ".$MemberName. " MemberID:".$MemberID." </strong>";
    $Html.="<table>
                <tr>
                    <td>LoanAmt(in Rs.)</td><td> Rs.".number_format($LoanAmt).".00/-
                </tr>
                <tr>
                    <td>LoanAmt (in Words)</td><td>".getIndianCurrency($LoanAmt)."</td>
                </tr>
                <tr>
                    <td>Application Date</td><td> ".$applnDate. " </td>
                </tr>
                <tr>
                    <td>IntRate </td><td>".$IntRate."%</td>
                </tr>
                <tr>
                    <td> EMI </td><td> Rs.".number_format($MthEMI). ".00/- </td>
                </tr>
	            <tr>
                    <td>Date Of Joining</td><td>" . getSingleField($db,"Select DOB from shareholders Where MemberID='$MemberID'")."</td>
                </tr>
                <tr>
                    <td>Date of Retirement </td><td> ". getSingleField($db,"Select DOR from shareholders Where MemberID='$MemberID'")."</td>
                </tr>
                <tr>
                    <td> Guarantor1</td><td> ".$G1Name."</td>
                </tr>
                <tr>
                    <td>Guarantor1 Status</td><td>$G1Approved</td>
                </tr>
                <tr>
                    <td>Guarantor2</td><td> ".$G2Name."</td>
                </tr>
                <tr>
                    <td>Guarantor2 Status</td><td>$G2Approved</td>
                </tr>";
                if($G1Approved=="Yes" && $G2Approved=="Yes"){
                    $Html.="<tr>
                        <td colspan='2'>
                        <center>
                        <b style='color:blue;'>Download the application and<br/>
                         submit the duly signed application to the<br/> GIT Employee's Co-Op credit society's office</b>
                         <br/>
                         <br/>
                         <form action='LoanPDF.php' method='post'>
                         <input type='hidden' name='loanid' value='$LoanID'>
                            <input type='submit' id='dapp'
                            style='padding:10px 10px 10px 10px; float:none!important;display:inline;
                                    background: #454545; font-weight: bold; color:white;' 
                                    name='dapp' value='Download Application'/>
                                    </form>
                       
                        </center></td>
                    </tr>";
                }
                else{
                    $Html.="<tr>
                        <td><input type='submit' name='edit' onclick=javascript:editLoanAppl('$LoanID') value='Edit'/></td>
                        <td><input type='submit' name='delete' onclick=javascript:deleteLoanAppl('$LoanID') value='Delete'/></td>
                        </tr>";
                }   

            $Html.="</table>";
            
           
           
            
 
	
	echo json_encode(array('Header'=>$Header,'Body'=> $Html), JSON_FORCE_OBJECT);	

            }
?>

