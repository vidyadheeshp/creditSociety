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
	if(isset($_POST['LoanID']))
		$LoanID   	= $_POST['LoanID'];
	$LoanRecSet = getResultSet($db,"Select * from loanappl Where LoanID='$LoanID'");
	foreach ($LoanRecSet as $LoanRow) {
		$MemberID 	= $LoanRow['MemberID'];
		$MemberName = getSingleField($db,"Select Name from shareholders Where MemberID='$MemberID'");
		$LoanAmt    = $LoanRow['LoanAmt'];
		$IntRate    = $LoanRow['IntRate'];
		$applnDate   = date("d-m-Y",strtotime($LoanRow['ApplOn']));
        
		$MthEMI 	= $LoanRow['MthEMI'];
	}

    
    $Header = "\n<h4 style='text-decoration: underline;'>Loan Application Details</h4>\n";
    $Html=" <strong> ".$MemberName. "</strong>";
    
    $Html.="<table style='border: 1px solid;'>
                <tr style='border: 1px solid;'>
                    <td style='border: 1px solid;'>LoanAmt(in Rs.)</td><td style='border: 1px solid;' style='border: 1px solid;'> Rs.".number_format($LoanAmt).".00/-
                </tr>
                <tr style='border: 1px solid;'>
                    <td style='border: 1px solid;'>LoanAmt (in Words)</td><td style='border: 1px solid;'>".getIndianCurrency($LoanAmt)."</td>
                </tr>
                <tr style='border: 1px solid;'>
                    <td style='border: 1px solid;'>Application Date</td><td style='border: 1px solid;'> ".$applnDate. " </td>
                </tr>
                <tr style='border: 1px solid;'>
                    <td style='border: 1px solid;'>IntRate </td><td style='border: 1px solid;'>".$IntRate."%</td>
                </tr>
                <tr style='border: 1px solid;'>
                    <td style='border: 1px solid;'> EMI </td><td style='border: 1px solid;'> Rs.".number_format($MthEMI). ".00/- </td>
                </tr>
	            <tr style='border: 1px solid;'>
                    <td style='border: 1px solid;'>Date Of Joining</td><td style='border: 1px solid;'>" . getSingleField($db,"Select DOB from shareholders Where MemberID='$MemberID'")."</td>
                </tr>
                <tr style='border: 1px solid;'>
                    <td style='border: 1px solid;'>Date of Retirement </td><td style='border: 1px solid;'> ". getSingleField($db,"Select DOR from shareholders Where MemberID='$MemberID'")."</td>
                </tr>
                <tr style='border: 1px solid;'>
                <td align='center'>
                <input type='button' onclick=approve('$LoanID') style='float:none!important;display:inline;background: #454545; font-weight: bold; color:white;' name='approve' value='Approve' />
               </td>
               
               <td align='center'>
               <input type='button' onclick=reject('$LoanID')  style='float:none!important;display:inline;background: #454545; font-weight: bold; color:white;' name='reject' value='Reject' />
              </td>
                </tr>
                
                
            </table>";
 
	
	echo json_encode(array('Header'=>$Header,'Body'=> $Html), JSON_FORCE_OBJECT);	
/*
    function getIndianCurrency(float $number)
    {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(0 => '', 1 => 'one', 2 => 'two',
            3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
            7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve',
            13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
            16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
            19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
            40 => 'forty', 50 => 'fifty', 60 => 'sixty',
            70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
        $digits = array('', 'hundred','thousand','lakh', 'crore');
        while( $i < $digits_length ) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
            } else $str[] = null;
        }
        $Rupees = implode('', array_reverse($str));
        $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
        return ($Rupees ? $Rupees  : '') . $paise." only";
    }   */
?>

