<?php

require('../fpdf.php');
require_once("../includes/pdofunctions_v1.php");
require_once("../includes/functions.php");

$db = connectPDO();
$error="";
$_SESSION['rdrurl'] = $_SERVER['REQUEST_URI'];
$dt=$_SERVER['dataInicio'] = date('Y-m-d', mktime(0, 0, 0, date('m')+1, 1, date('Y')));
$today=date('d-m-y');
if(isset($_POST['loanid'])){

$LoanID=$_POST['loanid'];

$LoanRecSet = getResultSet($db,"Select * from loanappl Where LoanID='$LoanID'");

foreach ($LoanRecSet as $LoanRow) {
    $MemberID 	= $LoanRow['MemberID'];
    $MemberDetails = getResultSet($db,"Select * from shareholders Where MemberID='$MemberID'");
    $LoanAmt    = $LoanRow['LoanAmt'];
    $IntRate    = $LoanRow['IntRate'];
    $applnDate   = date("d-m-Y",strtotime($LoanRow['ApplOn']));
    $months = $LoanRow['Months'];
    $G1MemberID = $LoanRow['G1MemberID'];
    $G1Details     = getResultSet($db,"Select * from shareholders Where MemberID='$G1MemberID'");
	$G2MemberID = $LoanRow['G2MemberID'];
    $G2Details     = getResultSet($db,"Select * from shareholders Where MemberID='$G2MemberID'");
   $loan_type = strtoupper(getSingleField($db,"select Name from fm where FMID=".$LoanRow['FMID']));

}
foreach($MemberDetails as $Mdetail){
    $designation=getSingleField($db,"select designation from designation where DesignID=".$Mdetail['DesignID']);
    $dob=$Mdetail['DOB'];
    $phone=$Mdetail['Mobile'];
    $MemberName=$Mdetail['Name'];
}
foreach($G1Details as $G1){
    $G1Name=$G1['Name'];
    $G1designation=getSingleField($db,"select designation from designation where DesignID=".$G1['DesignID']);
    $G1dob=$G1['DOB'];
    $diff=date_diff(date_create($dob),date_create($today));
    $G1age=$diff->format('%y');
    $G1phone=$G1['Mobile'];
    $G1Dept=getSingleField($db,"select DeptName from departments where DeptID=".$G1['DeptID']);
}
foreach($G2Details as $G2){
    $G2Name=$G2['Name'];
    $G2designation=getSingleField($db,"select designation from designation where DesignID=".$G2['DesignID']);
    $G2dob=$G2['DOB'];
    $diff=date_diff(date_create($dob),date_create($today));
    $G2age=$diff->format('%y');
    $G2phone=$G2['Mobile'];
    $G2Dept=getSingleField($db,"select DeptName from departments where DeptID=".$G2['DeptID']);
}
$diff = date_diff(date_create($dob), date_create($today));
$age=$diff->format('%y');
$to="To,
    The Chairman,
    GIT Employees Co-Op. Cr. Society,
    Gogte Institute of Technology, Udyambag,
    Belagavi.

Sir,";

$matter="I the undersidgned, a member of the society bearing membership number $MemberID hereby apply for a loan of Rs. $LoanAmt/- (In words) Rupees  ". getIndianCurrency($LoanAmt).".  I wish to repay the same in $months monthly installments.  I request you to kindly sanction the loan.

Name:Shri $MemberName
Designation: $designation                 Date of Birth: $dob            Age: $age.

I offer Sri. $G1Name and Sri. $G2Name as surities for the above loan.

Date:$today
                                                                       Name & Signature of the applicant
__________________________________________________________________
                    AGREEMENT FOR THE $loan_type
                    
I the undersigned, a member of the society bearing membership number $MemberID in consideration of having received this day the sum of Rs. $LoanAmt/- at $IntRate % pa interest, as a loan from GIT Employees Co-Op Credit Soceity, repayable in $months monthly installments, hereby undertake to repay the loan with interest on the scheduled date/s out of my salary paid by Principal, KLS Gogte Institute of Technology, Belagavi.

I authorise the Hon. Secretary to recover the amount from my salary every month.  I further declare that my disbursing officer will be competent to make such recoveries from salary and pay the amount to Hon. Secretary of the society.

I agree to abide by the rules/ regulations and bye-laws of the society.  I also declare that I am not a member of any other credit Soceity.

Date:$today
Contact No.:$phone                                       Name & Signature of the applicant";

        
    $pdf=new FPDF();
        //it helps out to add margin to the document first
        $pdf->setMargins(23, 44, 11.7);
        $pdf->AddPage();
        //this was a special font I used
        
        $pdf->SetFont('Arial','',14);

        $header = "KLS GIT EMPLOYEES CO-OPERATIVE CREDIT SOCIETY LTD.,UDYAMBAG BELAGAVI";
        $title = "APPLICATION FOR $loan_type";
        
    
       // $inwords=getIndianCurrency($amt);
        $pos = 10;
        //adding XY as well helped me, for some reaons without it again it wasn't entirely centered
        $pdf->SetXY(0, 10);

        //with SetX I use numbers instead of lMargin, and I also use half of the size I added as margin for the page when I did SetMargins
        $pdf->SetX(15);
        $pdf->MultiCell(0, 6, $header, $border=0, $align='C',$fill=false, $ishtml=true);
        
    
        $pdf->SetX(11.5);
        $pdf->SetFont('Times','',14);
        $pos = $pos + 10;
        $pdf->Cell(0,5,$title,0,0,'C');

        $pdf->SetX(22);
        $pdf->SetFont('Times',12);
        $pos=$pos+20;        
        $pdf->SetY($pos);
        $pdf->MultiCell(0, 6, $to, $border=0, $align='L', $fill=0, $ishtml=true);
       $pos=$pos+50;
       $pdf->SetFont('Times',12);
       $pdf->SetX(11.5);
       $pdf->SetY($pos);
       $pdf->MultiCell(0, 6, $matter, $border=0, $align='J', $fill=0,
      $ishtml=true);

      $pdf->setMargins(23, 44, 11.7);
        $pdf->AddPage();
        //this was a special font I used
        
        $pdf->SetFont('Arial','',14);

        
        $title = "AGREEMENT OF THE SURITIES $loan_type";
              // $inwords=getIndianCurrency($amt);
              $pos = 10;
              //adding XY as well helped me, for some reaons without it again it wasn't entirely centered
              $pdf->SetXY(0, 10);
      
              //with SetX I use numbers instead of lMargin, and I also use half of the size I added as margin for the page when I did SetMargins
              $pdf->SetX(15);
              $pdf->MultiCell(0, 6, $title, $border=0, $align='C',$fill=false, $ishtml=true);
              
          
          $matterpart1="We the undersigned, a member of GIT Employees Co-Op Credit Society, willingly offer myself as the surely for the loan availed by Sri. $MemberName.  In the event of the borrower becoming defaulter for whatsoever reason or society is not able to recover the loan amount from borrower because of his termination/ resignation/ retirement/ demise, we hereby agree jointly and severally to pay back the loan.  Under such circumstances we authorise the Hon. Secretary to deduct the due installment amount from our salaries every month till the loan is cleared.";
       $Gdata=array();
       $Gdata[0]="1. Name: $G1Name                             2. Name: $G2Name";
       $Gdata[1]="Signature: __________________                   Signature: _________________";                 
        $Gdata[2]="Department: $G1Dept                                 Department: $G2Dept";                           
        $Gdata[3]="Date of Birth: $G1dob  Age: $G1age                Date of Birth: $G2dob    Age: $G2age";        
        $Gdata[4]="Contact No: $G1phone                                Contact No: $G2phone";                         
             


      

             $matterpart2="Place: Belagavi            Date: $today
             ==============================================================
                                        FOR OFFICE USE ONLY
        The application has been considered in the meeting held on_________________, for  ". strtolower($loan_type).".  The loan amount of Rs. $LoanAmt is recommended for sanction.
        
        Date:________________
                                                                                                Hon. Secretary
______________________________________________________________________
    The recommended loan amount has been considered for sanction and the same is approved subject to the usual conditions and bye-laws of the soceity and the resolution passed in the meeting.
    
    Date:______________
                                                                                                 Chairman
==============================================================
                                            ACKNOWLEDGEMENT
    I have received cheque bearing No. _______________dtd.__________for Rs.______________, drawn on _____________________________________ bank, today towards my loan amount from the Hon. Secretary, GIT Employees Co-Op Credit Society, Belagavi.
    
Date:___________________                                  Name & Signature of the Applicant";

    $pos=$pos+10;
    $pdf->SetFont('Times',12);
    $pdf->SetX(11.5);
    $pdf->SetY($pos);
    $pdf->MultiCell(0, 6, $matterpart1, $border=0, $align='J', $fill=0,$ishtml=true);
    
    
    $pos=$pos+40;
    
    for($i=0;$i<count($Gdata);$i++)
    {
        $pos=$pos+7;
        $pdf->SetY($pos);
        $pdf->Cell(40,6,$Gdata[$i]);
        $pdf->Ln();
    }
    $pos=$pos+20;
    $pdf->SetFont('Times',12);
    $pdf->SetX(11.5);
    $pdf->SetY($pos);
    $pdf->MultiCell(0, 6, $matterpart2, $border=0, $align='J', $fill=0,$ishtml=true);
        $pdf->Output();
}