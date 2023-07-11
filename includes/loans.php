<?php
	// Author: Anand V Deshpande,Belagavi
	// Date Written : 01.12.2019
	// loans.php
	// modified on 20.11.2020 for Location
	function emi_calculator($p, $r, $t) 
	{ 
    	$emi; 
      	// one month interest 
  	  	$r = $r / (12 * 100); 
    	// one month period 
	    $t = $t * 12;  
      
    	$emi = ($p * $r * pow(1 + $r, $t)) /  
                  (pow(1 + $r, $t) - 1); 
  
    	//echo ($emi);
		return $emi; 
	} 
	function genMthEMIContrTable($db,$Month,$Year,$IntUptoDate,$FileName,$LoanFMID,$Location) {
	  	// create entries from shareholders table in tempmthsharecontr
		// first delete temp entries
		$Exceptions = "<br>";
		$Sql = "Delete from tempmthloanemicoll Where Location='$Location' and Mth='$Month' AND Yr='$Year' AND FMID=$LoanFMID";
		try {
	      	$stmt = $db->prepare($Sql);
	      	$stmt->execute();
	   	} catch (Exception $ex) {
	      	return $ex->getMessage();
	  	}
	  	$ContrHtml = "";
	  	
	  	// new accounts list
	  	$result = getResultSet($db,"Select B.MemberID,B.FMID,LoanID,A.Name,B.ClosBal,B.LoanAmt,B.IntRate,B.LastRecDate,B.MthEMI,B.LoanAmt,B.LoanDate,B.Status from customers B,shareholders A Where 
	  		B.MemberID = A.MemberID and (B.Status = 'New' or B.Status='ToBeSettled') AND B.FMID=$LoanFMID Order By B.Status,B.LoanID");

		$ContrHtml .= "<strong>Loan Accounts Non-Active:  Just for Information</strong>";
		$ContrHtml .= "<table id='gridtable1' class='display table table-responsive table-striped table-bordered table-condensed'>";
		$ContrHtml .= "<thead>";
		$ContrHtml .= "<tr style='color:red'>";
		$ContrHtml .= "<th>SNo</th>";
		$ContrHtml .= "<th>GenAcc</th>";
		$ContrHtml .= "<th>Name</th>";
		$ContrHtml .= "<th>MemberID</th>";
		$ContrHtml .= "<th>LoanID</th>";
		$ContrHtml .= "<th>LoanAmt</th>";
		$ContrHtml .= "<th>LoanDate</th>";
		$ContrHtml .= "<th>Balance</th>";
		$ContrHtml .= "<th>MthEMI</th>";
		$ContrHtml .= "<th>Status</th>";
		$ContrHtml .= "<th>IntUpto</th>";
		$ContrHtml .= "</tr>";
		$SerialNo=1;
	  	foreach($result as $row) {
			$ContrHtml .= "<tr style='color:red'>";
			$ContrHtml .= "<td>".$SerialNo."</td>";
			$ContrHtml .= "<td>".$row['FMID']."</td>";
			$ContrHtml .= "<td nowrap>".$row['Name']."</td>";
			$ContrHtml .= "<td nowrap>".$row['MemberID']."</td>";
			$ContrHtml .= "<td>".$row['LoanID']."</td>";
			$ContrHtml .= "<td nowrap>".$row['LoanAmt']."</td>";
			$ContrHtml .= "<td>".date("d-m-Y",strtotime($row['LoanDate']))."</td>";
			$ContrHtml .= "<td align='right'>".ConvBalance($row['ClosBal'])."</td>";
			$ContrHtml .= "<td nowrap>".$row['MthEMI']."</td>";
			$ContrHtml .= "<td nowrap>".$row['Status']."</td>";
			$ContrHtml .= "<td nowrap>".date("d-m-Y",strtotime($row['LastRecDate']))."</td>";
			$ContrHtml .= "</tr>";
			$SerialNo++;
	  	}
		$ContrHtml .= "</table>";
	  	//////////////////////////////////////////
		


	  	// Only if customer has balance 
	  	// modified on 06.02.2020

		// removed AND B.ClosBal<0 from query to enable to accept more amount than credit balance
		// modified on 20.11.2020 for Location
	  	$result = getResultSet($db,"Select B.MemberID,B.FMID,LoanID,A.Name,B.ClosBal,B.LoanAmt,B.IntRate,B.LastRecDate,B.MthEMI,A.DeptID from customers B,shareholders A Where 
	  		B.MemberID = A.MemberID and B.Status='Active' AND B.MthEMI >0  AND B.FMID=$LoanFMID 
	  			  Order By B.LoanID");
	  	foreach($result as $row) {
	  		$DeptID     = $row['DeptID'];
	  		$Loc1  		= trim(getSingleField($db,"Select Location from departments Where DeptID='$DeptID'"));
	  		//echo "Location in func $Location $Loc1 ";
	  		if($Location == $Loc1){
		  		$MemberID 	= $row['MemberID'];
		  		$MemberName = $row['Name'];
		  		$LoanID 	= $row['LoanID'];
		  		$FMID  		= $row['FMID'];
		  		$MthEMI 	= $row['MthEMI'];
		  		$IntRate 	= $row['IntRate'];
		  		$Array = array("FMID"=>$FMID,"LoanID"=>$LoanID,"MemberID"=>$MemberID,"Name"=>$MemberName,
		  				"Mth"=>$Month,"Yr"=>$Year,"MthEMI"=>$MthEMI,"Prev"=>$MthEMI,"IntRate"=>$IntRate,"Location"=>$Loc1);
		  		$RetValue = insert($db,"tempmthloanemicoll",$Array);
		  	}
	  	}
	  	// now check entries in file uploaded : make corrosponding changes in tempmthsharecontr
	  	//echo "FileName : ".$FileName;
	  	if($FileName=='Nil'){
			$file = fopen($FileName,"r");
			while(! feof($file)) {
				$row = fgetcsv($file);
				$LoanID 	= $row[0];
				$MemberName = $row[1];
				$MthEMI 	= $row[2];
				$Array = array("MthEMI"=>$MthEMI);
				$Where = "LoanID='$LoanID'";
				//print_r($Array);
				$RetValue = update($db,"tempmthloanemicoll",$values = $Array,$Where);
				if($RetValue==false) {

				} 
			}
	  	}
		$result = getResultSet($db,"Select * from tempmthloanemicoll Where Location='$Location' AND Mth='$Month' AND Yr='$Year' AND FMID=$LoanFMID Order By LoanID");
		$ContrHtml .= "<strong>Loan Accounts Monthly EMI Collection includes Uploaded Variation Data</strong>";
		$ContrHtml .= "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed'>";
		$ContrHtml .= "<thead>";
		$ContrHtml .= "<tr>";
		$ContrHtml .= "<th>SNo</th>";
		$ContrHtml .= "<th>GenAcc</th>";
		$ContrHtml .= "<th>Name</th>";
		$ContrHtml .= "<th>MemberID</th>";
		$ContrHtml .= "<th>LoanID</th>";
		$ContrHtml .= "<th>Name</th>";
		$ContrHtml .= "<th align='right'>Balance</th>";
		$ContrHtml .= "<th align='right'>Prev</th>";
		$ContrHtml .= "<th align='right'>MthEMI</th>";
		$ContrHtml .= "<th align='right'>Days</th>";
		$ContrHtml .= "<th align='right'>Prin</th>";
		$ContrHtml .= "<th align='right'>Int</th>";
		$ContrHtml .= "<th align='right'>IntUpto</th>";
		$ContrHtml .= "</tr>";
		$ContrHtml .= "</thead>";
		$ContrHtml .= "<tbody>";
		$SerialNo = 1;
		$TotalMthEMI = 0;
		$TotalClosBal= 0;
		$TotalPrev   = 0;
		$TotalCount  = 0;
		$TotalPrin   = 0;
		$TotalInt 	 = 0;
		foreach($result as $row) {
			$Principal  = 0;
			$Interest   = 0;
			$Days 		= 0;
			$LoanFMID  	= $row['FMID'];
			$IntFMID    = getSingleField($db,"Select LoanIntFMID from loansettings Where LoanFMID='$LoanFMID'");
			$LoanID   	= $row['LoanID'];
			$MemberID 	= $row['MemberID'];
			$MthEMI   	= $row['MthEMI'];
			$Prev       = $row['Prev'];
			$LoanFMID   = $row['FMID'];
			$IntRate    = $row['IntRate'];
			$LoanDate   = getSingleField($db,"Select LoanDate from customers Where LoanID='$LoanID'");
			$LoanDate   = date("Y-m-d",strtotime($LoanDate));
			$LastRecDate= getSingleField($db,"Select LastRecDate from customers Where LoanID='$LoanID'");
			$GenAcc   	= getSingleField($db,"Select Name from fm Where FMID='$LoanFMID'"); 
			$ClosBal    = getSingleField($db,"Select ClosBal from customers Where LoanID='$LoanID'");
			$CalcIntUpto= $IntUptoDate;
			if(is_null($LastRecDate)) {
				$Days = getDaysDiff($LoanDate,$IntUptoDate);
			} else{
				$LastRecDate = date("Y-m-d",strtotime($LastRecDate));
				$Days = getDaysDiff($LastRecDate,$IntUptoDate);
			}
			$RowColor = "";
			if($ClosBal>0) {
				$Interest  = 0;
				$Principal = $MthEMI;
				$Days      = 0;
				$RowColor  = "style='color:violet'";
				$Exceptions .= $LoanID. " Name: ".$row['Name']." EMI ".$MthEMI. " Credit Bal ".($ClosBal)."<br>";
			} else{
				if($Days<0){
					$RowColor = "style='color:red'";	
					$Exceptions .= $LoanID. " Name: ".$row['Name']." EMI ".$MthEMI. " Bal ".($ClosBal*-1)." Days negative "."<br>";

				}
				$Interest = round(  ($ClosBal*-1) * $IntRate * $Days / 36500,0);
				if($MthEMI >= $Interest){
					$Principal = $MthEMI - $Interest;
					if($ClosBal+$Principal>0){
						// FOLLOWING LINE commented on 24.06.2020 2.13pm
						//$Principal = ($ClosBal*-1);
						$Exceptions .= $LoanID. " Name: ".$row['Name']." EMI ".$MthEMI. " Bal ".($ClosBal*-1)." Int: ".$Interest. " Taken: ".($Principal+ $Interest)."<br>";
						$RowColor = "style='color:red'";
						// removed following line on 23.06.2020
						//$MthEMI = $Principal + $Interest;
					}
				}
				if($Interest > $MthEMI){
					// modify this procedure: calc int upto date 
					// 08.09.2020
					$Exceptions .= $LoanID. " Name: ".$row['Name']." EMI ".$MthEMI. " Bal ".($ClosBal*-1)." Int: ".$Interest. " is more than EMI "."<br>";
					$RowColor = "style='color:red'";
					
					$Principal = 0;
					if(is_null($LastRecDate)) {
						$IntCalFrom = $LoanDate;  //date("Y-m-d",strtotime($LoanDate));
					} else{
						$IntCalFrom = $LastRecDate; //date("Y-m-d",strtotime($LastRecDate));
					}
					// $Interest calculated for $Days so for $MthEMI how many days?
					$Days1 = round($MthEMI * $Days / $Interest,0);
					$Days1Str = " + ".$Days1. " days";
					$CalcIntUpto = date("Y-m-d",strtotime($IntCalFrom. $Days1Str));
					// new days set to calc.days, Interest set to MthEMI 
					$Days     = $Days1;
					$Interest = $MthEMI;
					CreateLog($LoanID.":emi".$MthEMI.":int".$Interest.":days".$Days." and days:".$Days1.":from".$IntCalFrom.":to".$CalcIntUpto. " Datestr:".$Days1Str);								
				}
			}
			//if($MthEMI <> $Prev) {
			//	$ContrHtml .= "<tr $RowColor >";
			//} else{
			//	$ContrHtml .= "<tr>";
			//}
			$ContrHtml .= "<tr $RowColor >";
			$ContrHtml .= "<td>$SerialNo</td>";
			$ContrHtml .= "<td>".$LoanFMID."</td>";
			$ContrHtml .= "<td nowrap>".$GenAcc."</td>";
			$ContrHtml .= "<td nowrap>".$MemberID."</td>";
			$ContrHtml .= "<td><input id='loanid[]' name='loanid[]' size='10' value='$LoanID' readonly /></td>";
			$Error = "";
			if($MthEMI != $Principal + $Interest){
				$Error = "* Diff: ".($MthEMI-$Principal-$Interest);
			}
			$ContrHtml .= "<td nowrap>".$row['Name'].$Error."</td>";
			$ContrHtml .= "<td align='right'>".ConvBalance($ClosBal)."
						<input type='hidden' id='intfmid[]' name='intfmid[]' value='$IntFMID' readonly />
						<input type='hidden' id='closbal[]' name='closbal[]' size='12' value='$ClosBal' readonly /></td>";
			$ContrHtml .= "<td>".$Prev."</td>";
			$ContrHtml .= "<td align='right'><input id='mthemi[]' name='mthemi[]' size='5' value='$MthEMI' /></td>";
			$ContrHtml .= "<td align='right'><input id='days[]' name='days[]' size='5' value='$Days' /></td>";
			$ContrHtml .= "<td align='right'><input id='principal[]' name='principal[]' size='5' value='$Principal' /></td>";
			$ContrHtml .= "<td align='right'><input id='interest[]' name='interest[]' size='5' value='$Interest' /></td>";
			$ContrHtml .= "<td align='right'><input type='date' id='calcintupto[]' name='calcintupto[]' size='10' value='$CalcIntUpto' /></td>";
			$ContrHtml .= "</tr>";
			$SerialNo++;
			$TotalClosBal  	+= $ClosBal;
			$TotalPrev      += $Prev;
			$TotalMthEMI 	+= $MthEMI;

			$TotalPrin      += $Principal;
			$TotalInt 		+= $Interest;
		}
		$ContrHtml .= "</tbody>";
		$ContrHtml .= "<tfoot>";
		$RowColor   = "";
		$TotalDiff  = "";
		if($TotalMthEMI != $TotalPrin + $TotalInt) {
			$RowColor  = "style='color:red'";
			$TotalDiff = $TotalMthEMI - $TotalPrin - $TotalInt;
		}

		$ContrHtml .= "<tr $RowColor>";
		$ContrHtml .= "<td></td>";
		$ContrHtml .= "<td></td>";
		$ContrHtml .= "<td></td>";
		$ContrHtml .= "<td></td>";
		$ContrHtml .= "<td></td>";
		$ContrHtml .= "<td>".$TotalDiff."</td>";
		$ContrHtml .= "<td align='right'>".ConvBalance($TotalClosBal)."</td>";
		$ContrHtml .= "<td align='right'>".$TotalPrev."</td>";
		$ContrHtml .= "<td align='right'>".$TotalMthEMI."</td>";
		$ContrHtml .= "<td></td>";
		$ContrHtml .= "<td align='right'>".$TotalPrin."</td>";
		$ContrHtml .= "<td align='right'>".$TotalInt."</td>";
		$ContrHtml .= "<td></td>";
		$ContrHtml .= "<td></td>";
		$ContrHtml .= "</tr>";
		$ContrHtml .= "</tfoot>";
		$ContrHtml .= "</table>";
		$ContrHtml .= "Exceptions: ".$Exceptions."<br>";
		$ContrHtml .= "Total to be collected ". $TotalMthEMI. "<br>";
		$ContrHtml .= genSelectFM($db,"bankacc" ,"AcType='Bank'", " required ");
		$ContrHtml .= "<input type='submit' class='btn btn-success' id='Submit' 
			name='Submit' value='Submit'></input>";
		CreateLog("MthEMI Loan Collection Exceptions: ".$Exceptions);
		return $ContrHtml;
	}
	function genLoansMonthwise($db) {

		$Html = "";
		$Sql1 = "Select FMID,Name from fm Where AcType='Cust' Order By FMID ";
		$LoanAccs = getResultSet($db,$Sql1);
		foreach($LoanAccs as $IndLoan){
			$FMID = $IndLoan['FMID'];
			$Sql  = "Select SUM(OpenBal) as S_OpenBal from customers Where FMID='$FMID' AND OpenBal<0";
			$Balance = getSingleField($db,$Sql);
			$Html .= "<b>".$IndLoan['Name']. "</b> AccID: <b>".$IndLoan['FMID']."</b><br>";
			$Html .= "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed'>";

			$Html .= "<thead>";
			$Html .= "<tr style='background-color:yellow;'>";
			$Html .= "<th align='center'>Year-MM</th>";
			$Html .= "<th align='right'>Debit</th>";
			$Html .= "<th align='right'>Credit</th>";
			$Html .= "<th align='right'>Balance</th>";
			$Html .= "<th align='right'>Interest</th>";
			$Html .= "<th align='right'>TotReceipt</th>";
			$Html .= "<th align='right'>DebCount</th>";
			$Html .= "<th align='right'>CreCount</th>";
			$Html .= "</tr>";
			$Html .= "</thead>";

			$Html .= "<tbody>";
			$Html .= "<tr>";
			$Html .= "<td align='center'>01.04.2019</th>";
			$Html .= "<td align='right'></td>";
			$Html .= "<td align='right'></td>";
			$Html .= "<td align='right'>".ConvBalanceFormat($Balance)."</td>";
			$Html .= "<td align='right'></td>";
			$Html .= "<td align='right'></td>";
			$Html .= "<td align='right'></td>";
			$Html .= "<td align='right'></td>";
			$Html .= "</tr>";

			$SerialNo 		= 1;
			$TotalDebits	= 0;
			$TotalCredits	= 0;
			$TotalInterest	= 0;
			$TotalReceipt   = 0;
			$TotalDbCount   = 0;
			$TotalCrCount   = 0;
			$Sql = "Select DATE_FORMAT(TrnDate,'%Y-%m') as YM,SUM(Credit) as S_Credit,SUM(Debit) as S_Debit,SUM(Interest) as S_Interest from ft Where FMID='$FMID' Group By DATE_FORMAT(TrnDate,'%Y-%m')";
			$result = getResultSet($db,$Sql);
			foreach($result as $row) {
				$YM = $row['YM'];
				$DbCount = getSingleField($db,"Select count(*) from ft Where FMID='$FMID' AND DATE_FORMAT(TrnDate,'%Y-%m')='$YM' AND Debit>0");
				$CrCount = getSingleField($db,"Select count(*) from ft Where FMID='$FMID' AND DATE_FORMAT(TrnDate,'%Y-%m')='$YM' AND Credit>0");
				$Balance = $Balance + $row['S_Credit'] - $row['S_Debit'];
				$Receipt = $row['S_Credit'] + $row['S_Interest'];
				$Html .= "<tr>";
				$Html .= "<td align='center'>".$row['YM']."</td>";
				$Html .= "<td align='right'>".moneyFormatIndia($row['S_Debit'])."</td>";
				$Html .= "<td align='right'>".moneyFormatIndia($row['S_Credit'])."</td>";
				$Html .= "<td align='right'>".ConvBalanceFormat($Balance)."</td>";
				$Html .= "<td align='right'>".moneyFormatIndia($row['S_Interest'])."</td>";
				$Html .= "<td align='right'>".moneyFormatIndia($Receipt)."</td>";
				$Html .= "<td align='right'>".$DbCount."</td>";
				$Html .= "<td align='right'>".$CrCount."</td>";
				$Html .= "</tr>";

				$SerialNo++;
				$TotalDebits   	+= $row['S_Debit'];
				$TotalCredits 	+= $row['S_Credit'];
				$TotalInterest  += $row['S_Interest'];
				$TotalReceipt   += $Receipt;
				$TotalDbCount   += $DbCount;
				$TotalCrCount   += $CrCount;
			}
			$Html .= "</tbody>";
			$Html .= "<tfoot>";
			$Html .= "<tr style='background-color:lightblue;'>";
			$Html .= "<td></td>";
			$Html .= "<td align='right'>".moneyFormatIndia($TotalDebits)."</td>";
			$Html .= "<td align='right'>".moneyFormatIndia($TotalCredits)."</td>";
			$Html .= "<td align='right'></td>";
			$Html .= "<td align='right'>".moneyFormatIndia($TotalInterest)."</td>";
			$Html .= "<td align='right'>".moneyFormatIndia($TotalReceipt)."</td>";
			$Html .= "<td align='right'>".$TotalDbCount."</td>";
			$Html .= "<td align='right'>".$TotalCrCount."</td>";
			$Html .= "</tr>";
			$Html .= "</tfoot>";
			$Html .= "</table>";
		}
		return $Html;
	}

	function SendEmailLoanEMI($db,$Month,$Year){
		$Subject = "Loans Monthly EMI Collection For $Month/$Year ";
		require_once('../assets/PHPMailer-master/class.phpmailer.php');
		$mail = new PHPMailer(true);
		$mail->IsSMTP(); // telling the class to use SMTP
		
		try
		{
			$mail->Host       = "smtp.gmail.com";   // SMTP server
			$mail->SMTPAuth   = true;              // enable SMTP authentication
			$mail->SMTPSecure = "ssl";            // sets the prefix to the servier  
			$mail->Port       = 465;             // set the SMTP port for the GMAIL
	  
			$mail->Username = "empsociety@git.edu";  // GMAIL username
			$mail->Password = "git12345";
			$mail->From     = "KLS GIT Emp.Co-Op Credit Society";     // GMAIL password
			$mail->FromName = "empsociety@git.edu";
			$mail->AddReplyTo("noreply@git.edu","No-Reply");

			$Sql  = "Select * from ft Where ForMonth='$Month' and ForYear='$Year' AND TrnType='MthEMI' AND TrnCode='REC' Order By DateStmp ";
			$ResultSet = getResultSet($db,$Sql);
			foreach($ResultSet as $row){
				$MemberName	= "";
				$EmailID   	= "";
				$Mobile 	= "";
				$ClosBal 	= "";
				$MemberID 	= "";
				$IntRate 	= "";

				$LoanID     = $row['LoanID'];
				$MthEMI 	= $row['Principal'] + $row['Interest'];
				$Credit 	= $row['Credit'];
				$IntColl 	= $row['Interest'];
				$Total 		= $Credit + $Interest;
				$Days 		= $row['Days'];
				$IntUpto    = $row['IntUptoDt'];

				$RowCust    = getResultSet($db,
					"Select B.LoanID,B.MemberID,B.ClosBal,A.Name,A.Mobile,A.EmailID from customers B, shareholders A Where B.MemberID = A.MemberID ANd 
						LoanID='$LoanID'");
				foreach($RowCust as $loanrow){
					$MemberID 	= $row['MemberID'];
					$ClosBal    = ConvBalance($loanrow['ClosBal']);
					$MemberName = $Member['Name'];
					$EmailID 	= $Member['EmailID'];
					$Mobile 	= $Member['Mobile'];
					$IntRate 	= $loanrow['IntRate'];
				}
				$TrnDate  = date("d-m-Y",strtotime($row['TrnDate']));
				$Credit   = $row['Credit'];
				$Details  = "Dear <strong>$MemberName<strong>,"."<br>";
				$Details .= "Received an amount of Rs.".$Total." Principal: ".$Credit. " Interest: ".$IntColl."  Days: ".$Days."<br>";
				$Details .= "from Monthly EMI Collection for $Month/$Year "."<br>";
				$Details .= "Interest is calculated upto ".$IntUpto."<br>";
				$Details .= "Your Loan Account ($LoanID) is credited with Rs.$Credit on ".$TrnDate."<br>";
				$Details .= "Your Account Balance is ".$ClosBal."<br>";
				
				$mail->addAddress($EmailID);
				$mail->Subject = $Subject;
				//$mail->addStringAttachment($pdf, 'doc.pdf');
				$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // 
				$mail->IsHTML(true);
				$mail->Body = $Details;
				if($mail->Send()) {} else {}
			}
		}
		catch (phpmailerException $e) 
		{
			//echo $e->errorMessage();
			return $e->errorMessage(); 
		} 
		catch (Exception $e) 
		{
			return $e->getMessage(); 
		} 

	}

	function getOpenBalLoanAcc($db,$LoanID,$BalUptoDate){
		$Sql = "Select OpenBal,
			(Select SUM(Debit)  from ft Where ft.LoanID =  '$LoanID' AND ft.TrnDate < '$BalUptoDate' ) as Debit1,
			(Select SUM(Credit) from ft Where ft.LoanID =  '$LoanID' AND ft.TrnDate < '$BalUptoDate' ) as Credit1
		From customers 
		Where customers.LoanID = '$LoanID'";
		$ResultRow = getResultSet($db,$Sql);
		foreach($ResultRow as $Row){		
			$OpenBal = $Row['OpenBal'];
			$Debit 	= !is_null($Row['Debit1']) ? $Row['Debit1'] : 0; 
			$Credit = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0; 
			$ClosBal= $OpenBal - $Debit + $Credit;
			return $ClosBal;
		}
	}
	function getLoanDets($db,$LoanID){
		$IntUptoDt  = date("Y-m-d");   // todays

		CreateLog("Cust: $LoanID $TrnCode $IntUptoDt $TrnAmount ");
		$Interest  	= 0;
		$Principal  = 0;
		
		$ResultCust = getResultSet($db,"Select * from customers Where LoanID='$LoanID' LIMIT 1");
		foreach($ResultCust as $Cust){
			$IntRate 	 = $Cust['IntRate'];
			$LastRecDate = $Cust['LastRecDate'];
			$LoanDate    = $Cust['LoanDate'];
			$ClosBal     = $Cust['ClosBal'];
			$MthEMI 	 = $Cust['MthEMI'];
		}
		CreateLog("Cust: $IntRate $LastRecDate $LoanDate $ClosBal ");
		$ConvAmt 	= ConvBalance($ClosBal);
		
		if($ClosBal<=0){
			if(is_null($LastRecDate)) {
				$Days = getDaysDiff($LoanDate,$IntUptoDt) ;
				CreateLog("Cust: int calc TrnDate $TrnDate LoanDate $LoanDate IntUptoDt $IntUptoDt $Interest days: $Days");
			} else{
				$Days = getDaysDiff($LastRecDate,$IntUptoDt);
			CreateLog("Cust: int calc  Trndate $TrnDate LastRecDate $LastRecDate IntUptoDt $IntUptoDt $Interest days: $Days");
			}
			$Interest = round( ($ClosBal*-1) * $IntRate * $Days / 36500,0);
		}
		/*
		CreateLog("Cust: Int-> $Principal  $Interest ");
		$LastRecDate = date("d-m-Y",strtotime($LastRecDate)) ;
		echo json_encode(array('ConvAmt'=> $ConvAmt,
				'ClosBal'  => $ClosBal,
				'Interest' => $Interest,
				'Days'     => $Days,
				'LastRecDate' => $LastRecDate,
				'MthEMI'   => $MthEMI), JSON_FORCE_OBJECT);	
		*/
		return array($ConvAmt,$ClosBal,$Interest,$Days,$LastRecDate,$MthEMI);
	}
?>
