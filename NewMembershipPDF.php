<?php
require('./fpdf.php');
require_once("includes/pdofunctions_v1.php");
require_once("includes/functions.php");
$db = connectPDO();
$error = "";

//$dt = $_SERVER['dataInicio'] = date('Y-m-d', mktime(0, 0, 0, date('m') + 1, 1, date('Y')));
$today = date('d-m-y');
$time = strtotime(sprintf('%d months', 0));
$month = date('F', $time);


$name = "";
$startDate = "";
$deptid = "";
$amt = "";
$title = "";
session_start();
if (isset($_POST['name']) || isset($_SESSION['name'])) {
    if (isset($_POST['name'])) {
        $name = $_POST['name'];
    } else if (isset($_SESSION['name'])) {
        $name = $_SESSION['name'];
    }
    $memberappl = getResultSet($db, "Select * from `newmemberapplication` Where name like '$name'");
    $deptid = $memberappl[0]['deptid'];
    $amt = $memberappl[0]['amt'];
    $startDate = $memberappl[0]['startmonth'];

    $name = $memberappl[0]['name'];
    $dpt = "";
    $query = "select ShName from departments where DeptID=$deptid";
    $dpt = getSingleField($db, $query);



    $pdf = new FPDF();
    //it helps out to add margin to the document first
    $pdf->setMargins(23, 44, 11.7);
    $pdf->AddPage();
    //this was a special font I used

    $pdf->SetFont('Arial', '', 14);

    $header = "KLS GIT EMPLOYEES CO-OPERATIVE CREDIT SOCIETY LTD.,UDYAMBAG BELAGAVI";
    $title = "APPLICATION FOR MEMBERSHIP";


    $from = "From: ";
    $dept = "Dept: ";

    $to = "
To,
    The Chairman,
    GIT Employees Co-Op. Cr. Society,
    Gogte Institute of Technology, Udyambag,
    Belagavi.
    ";


    $inwords = getIndianCurrency($amt);
    $matter = "
Sir,
        
            I wish to enroll as a member of G.I.T Employees Co-operative Credit Society from $startDate and I am willing to contribute Rs. $amt (Rupees $inwords) every month towards share of the society.  I herewith agree to deduct the amount from my salary.  Further I agree to abide by the conditions and by-laws of the society.

Place: Belagavi
Date: $today                                                                                                    Signature of applicant
        
        _________________________________________________________________________________
                                                                FOR OFFICE USE ONLY
        
        The application of $name for the membership of the society has been considered and approved at the meeting held on _______________.  The membership is subject to the conditions and bye-laws of the society as existing and as amended time to time.


        
        Chairman                                                                                                         Secretary
        ";
    $pos = 10;
    //adding XY as well helped me, for some reaons without it again it wasn't entirely centered
    $pdf->SetXY(0, 10);

    //with SetX I use numbers instead of lMargin, and I also use half of the size I added as margin for the page when I did SetMargins
    $pdf->SetX(15);
    $pdf->MultiCell(0, 6, $header, $border = 0, $align = 'C', $fill = false, $ishtml = true);


    $pdf->SetX(11.5);
    $pdf->SetFont('Times', '', 14);
    $pos = $pos + 10;
    $pdf->Cell(0, 5, $title, 0, 0, 'C');

    $pdf->SetX(22);
    $pdf->SetFont('Times', 12);
    $pos = $pos + 20;

    $pdf->Cell(0, $pos, $from, 0, 0, 'L');

    $pdf->SetX(35);
    $pdf->SetFont('Times', 'U', 12);
    $pdf->Cell(0, $pos, $name, 0, 0, 'L');
    $pos = $pos + 15;
    $pdf->SetX(22);
    $pdf->SetFont('Times', 12);
    $pdf->Cell(0, $pos, $dept, 0, 0, 'L');
    $pdf->SetX(34);
    $pdf->SetFont('Times', 'U', 12);
    $pdf->Cell(0, $pos, $dpt, 0, 0, 'L');
    $pdf->SetFont('Times', 12);

    $pdf->SetY($pos);
    $pdf->MultiCell(
        0,
        6,
        $to,
        $border = 0,
        $align = 'L',
        $fill = 0,
        $ishtml = true
    );
    $pos = $pos + 50;
    $pdf->SetFont('Times', 12);
    $pdf->SetX(11.5);
    $pdf->SetY($pos);
    $pdf->MultiCell(
        0,
        6,
        $matter,
        $border = 0,
        $align = 'J',
        $fill = 0,
        $ishtml = true
    );


    //$pdf->Output('New Membership.pdf', 'I');
    $pdf->Output();
}
