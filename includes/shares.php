<?php
	// shares.php
	function genSharesDeptwiseSummary($db){
		$Html = "";
		$Sql  = "Select DeptID,Count(*) as mCount,SUM(OpenBal) as S_OpenBal,SUM(Debits) as S_Debits,SUM(Credits) as S_Credits,SUM(ClosBal) as S_ClosBal from shareholders Where ClosBal<>0 Group By DeptID";
		$result = getResultSet($db,$Sql);
		$Html = "<strong>Share Holding Summary</strong>";
		$Html .= "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed'>";
		$Html .= "<thead>";
		$Html .= "<tr class='bg-primary'>";
		$Html .= "<th>SNo</th>";
		$Html .= "<th>DeptID</th>";
		$Html .= "<th>Department</th>";
		$Html .= "<th>Count</th>";
		$Html .= "<th align='right'>OpenBal</th>";
		$Html .= "<th align='right'>Debit</th>";
		$Html .= "<th align='right'>Credit</th>";
		$Html .= "<th align='right'>Balance</th>";
		$Html .= "</tr>";
		$Html .= "</thead>";

		$Html .= "<tbody>";
		$SerialNo = 1;
		$TotalOpenBal=0;
		$TotalDebits=0;
		$TotalCredits=0;
		$TotalClosBal=0;
		$TotalCount  =0;
		foreach($result as $row) {
			$DeptID    	= $row['DeptID'];
			$Dept  		= getSingleField($db,"Select DeptName from departments Where DeptID='$DeptID'");
			$Html .= "<tr>";
			$Html .= "<td>$SerialNo</td>";
			$Html .= "<td>".$row['DeptID']."</td>";
			$Html .= "<td>".$Dept."</td>";
			$Html .= "<td align='center'>".$row['mCount']."</td>";
			$Html .= "<td align='right'>".moneyFormatIndia($row['S_OpenBal'])."</td>";
			$Html .= "<td align='right'>".moneyFormatIndia($row['S_Debits'])."</td>";
			$Html .= "<td align='right'>".moneyFormatIndia($row['S_Credits'])."</td>";
			$Html .= "<td align='right'>".moneyFormatIndia($row['S_ClosBal'])."</td>";
			$Html .= "</tr>";

			$SerialNo++;
			$TotalOpenBal  	+= $row['S_OpenBal'];
			$TotalDebits   	+= $row['S_Debits'];
			$TotalCredits 	+= $row['S_Credits'];
			$TotalClosBal  	+= $row['S_ClosBal'];
			$TotalCount     += $row['mCount'];
		}
		$Html .= "</tbody>";
		$Html .= "<tfoot>";
		$Html .= "<tr class='bg-primary'>";
		$Html .= "<td></td>";
		$Html .= "<td></td>";
		$Html .= "<td></td>";
		$Html .= "<td align='center'>".$TotalCount."</td>";
		$Html .= "<td align='right'>".moneyFormatIndia($TotalOpenBal)."</td>";
		$Html .= "<td align='right'>".moneyFormatIndia($TotalDebits)."</td>";
		$Html .= "<td align='right'>".moneyFormatIndia($TotalCredits)."</td>";
		$Html .= "<td align='right'>".moneyFormatIndia($TotalClosBal)."</td>";
		$Html .= "</tr>";
		$Html .= "</tfoot>";
		$Html .= "</table>";
		return $Html;		
	}
	function genMthShareContrTable($db,$Month,$Year,$PostDate,$FileName,$Location) {
		// first delete temp entries
		$Sql = "Delete from tempmthsharecontr Where Location='$Location' AND Mth='$Month' AND Yr='$Year'";
		try {
	      	$stmt = $db->prepare($Sql);
	      	$stmt->execute();
	   	} catch (Exception $ex) {
	      	return $ex->getMessage();
	  	}	
	  	// create entries from shareholders table in tempmthsharecontr
	  	$result = getResultSet($db,"Select SHID,MemberID,DeptID,Name,ClosBal,MthContr from shareholders Where Status='Active' AND MthContr >0 Order By DeptID,MemberID");
	  	foreach($result as $row) {
	  		$DeptID     = $row['DeptID'];
	  		$Loc1  		= getSingleField($db,"Select Location from departments Where DeptID='$DeptID'");
	  		//echo "Location in func $Location $Loc1 ";
	  		if($Location == $Loc1){	  		
		  		$MemberID 	= $row['MemberID'];
		  		$MemberName = $row['Name'];
		  		$MthContr 	= $row['MthContr'];
		  		$Array = array("MemberID"=>$MemberID,"Name"=>$MemberName,
		  				"Mth"=>$Month,"Yr"=>$Year,"MthContr"=>$MthContr,"Prev"=>$MthContr,"Location"=>$Location);
		  		$RetValue = insert($db,"tempmthsharecontr",$Array);
		  	}
	  	}
	  	// now check entries in file uploaded : make corrosponding changes in tempmthsharecontr
	  	//echo "FileName : ".$FileName;
	  	if($FileName=='Nil'){
			$file = fopen($FileName,"r");
			while(! feof($file)) {
				$row = fgetcsv($file);
				$MemberID 	= $row[0];
				$MemberName = $row[1];
				$MthContr 	= $row[2];
				$Array = array("MthContr"=>$MthContr,"Location" => $Location);
				$Where = "MemberID='$MemberID'";
				//print_r($Array);
				$RetValue = update($db,"tempmthsharecontr",$values = $Array,$Where);
				if($RetValue==false) {

				} 
			}
	  	}
		$result = getResultSet($db,"Select * from tempmthsharecontr Where Location='$Location' AND Mth='$Month' AND Yr='$Year' Order By MemberID");
		$ContrHtml = "<strong>Share Holders Monthly Contribution includes Uploaded Variation Data</strong>";
		$ContrHtml .= "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed'>";
		$ContrHtml .= "<thead>";
		$ContrHtml .= "<tr>";
		$ContrHtml .= "<th>SNo</th>";
		$ContrHtml .= "<th>DeptID</th>";
		$ContrHtml .= "<th>Department</th>";
		$ContrHtml .= "<th>MemberID</th>";
		$ContrHtml .= "<th>Name</th>";
		$ContrHtml .= "<th align='right'>Balance</th>";
		$ContrHtml .= "<th align='right'>Prev</th>";
		$ContrHtml .= "<th align='right'>MthContr</th>";
		$ContrHtml .= "</tr>";
		$ContrHtml .= "</thead>";
		$ContrHtml .= "<tbody>";
		$SerialNo = 1;
		$TotalMthContr = 0;
		$TotalClosBal=0;
		$TotalPrev   =0;
		$TotalCount  =0;
		foreach($result as $row) {
			$MemberID   = $row['MemberID'];
			$MthContr   = $row['MthContr'];
			$Prev       = $row['Prev'];
			$DeptID    	= getSingleField($db,"Select DeptID from shareholders Where MemberID='$MemberID'"); 
			$Dept  		= getSingleField($db,"Select DeptName from departments Where DeptID='$DeptID'");
			$ClosBal    = getSingleField($db,"Select ClosBal from shareholders Where MemberID='$MemberID'");
			if($MthContr <> $Prev) {
				$ContrHtml .= "<tr style='color:red'>";
			} else{
				$ContrHtml .= "<tr>";
			}
			$ContrHtml .= "<td>$SerialNo</td>";
			$ContrHtml .= "<td>".$DeptID."</td>";
			$ContrHtml .= "<td nowrap>".$Dept."</td>";
			$ContrHtml .= "<td><input id='memberid[]' name='memberid[]' size='10' value='$MemberID' readonly /></td>";
			$ContrHtml .= "<td nowrap>".$row['Name']."</td>";
			$ContrHtml .= "<td align='right'><input id='closbal[]' name='closbal[]' size='12' value='$ClosBal' readonly /></td>";
			$ContrHtml .= "<td>".$Prev."</td>";
			$ContrHtml .= "<td align='right'><input id='mthcontr[]' name='mthcontr[]' size='5' value='$MthContr' /></td>";
			$ContrHtml .= "</tr>";

			$SerialNo++;
			$TotalClosBal  	+= $ClosBal;
			$TotalPrev      += $Prev;
			$TotalMthContr 	+= $MthContr;
		}
		$ContrHtml .= "</tbody>";
		$ContrHtml .= "<tfoot>";
		$ContrHtml .= "<td></td>";
		$ContrHtml .= "<td></td>";
		$ContrHtml .= "<td></td>";
		$ContrHtml .= "<td></td>";
		$ContrHtml .= "<td></td>";
		$ContrHtml .= "<td align='right'>".$TotalClosBal."</td>";
		$ContrHtml .= "<td align='right'>".$TotalPrev."</td>";
		$ContrHtml .= "<td align='right'>".$TotalMthContr."</td>";
		$ContrHtml .= "</tr>";
		$ContrHtml .= "</tfoot>";
		$ContrHtml .= "</table>";
		$ContrHtml .= genSelectFM($db,"shareacc","AcType='SC'"," required ");
		$ContrHtml .= genSelectFM($db,"bankacc" ,"AcType='Bank'", " requied ");
		$ContrHtml .= "<input type='submit' class='btn btn-success' id='Submit' 
			name='Submit' value='Submit'></input>";
		return $ContrHtml;
	}

	function genDashboardSummary($db){
		$Html = "";

		//$Sql  = "Select DeptID,Count(*) as mCount,SUM(OpenBal) as S_OpenBal,SUM(Debits) as S_Debits,SUM(Credits) as S_Credits,SUM(ClosBal) as S_ClosBal from shareholders Where ClosBal<>0 Group By DeptID";
		//$result = getResultSet($db,$Sql);

		$Html .= "<table id='gridtable' class='table table-responsive table-bordered table-condensed table-info'>";
		$Html .= "<thead>";
		$Html .= "<tr style='background-color:lightyellow;'>";
		$Html .= "<th>Particulars</th>";
		$Html .= "<th class='text-center'>New</th>";
		$Html .= "<th class='text-center'>Closed</th>";
		$Html .= "<th class='text-center'>Active</th>";
		$Html .= "<th class='text-center'>Mthly Contr</th>";
		$Html .= "<th class='text-right'>Amount</th>";
		$Html .= "</tr>";
		$Html .= "</thead>";

		$Html .= "<tbody>";
		$TotalCount		= 0;
		$TotalClosBal 	= 0;
		$TotalMthContr  = 0;
		$RS = getResultSet($db,"Select Count(*) as s_count,SUM(MthContr) as s_mthcontr,SUM(ClosBal) as s_closbal from shareholders Where ClosBal>0");
		foreach($RS as $Ind){
			$TotalCount 	= $Ind['s_count'];
			$TotalClosBal 	= $Ind['s_closbal'];
			$TotalMthContr 	= $Ind['s_mthcontr'];	
		}
		$TotalSharesClosed  = getSingleField($db,"Select COUNT(*) from shareholders Where Status='Closed'");
		$TotalSharesNew     = getSingleField($db,"Select COUNT(*) from shareholders Where Status='New'");
		//$TotalCount  = getSingleField($db,"Select count(*)     from shareholders Where ClosBal>0");
		//$TotalClosBal= getSingleField($db,"Select SUM(ClosBal) from shareholders Where ClosBal>0");

		$Html .= "<tr><td>ShareHolders</td>";
		$Html .= "<td align='center'>".$TotalSharesNew."</td>";
		$Html .= "<td align='center'>".$TotalSharesClosed."</td>";
		$Html .= "<td align='center'>".$TotalCount."</td>";
		$Html .= "<td align='center'>".moneyFormatIndia($TotalMthContr)."</td>";
		$Html .= "<td align='right'>".moneyFormatIndia($TotalClosBal)."</td></tr>";

		$ResultSet = getResultSet($db,"Select FMID,count(*) as s_count,SUM(MthEMI) as s_mthemi,SUM(ClosBal) as s_closbal from customers Group By FMID");
		foreach($ResultSet as $row){
			$FMID = $row['FMID'];
			$FMName = getSingleField($db,"Select Name from fm Where FMID='$FMID'");
			$TotalCustomersClosed  = getSingleField($db,"Select COUNT(*) from customers Where FMID = '$FMID' and Status='Closed'");
			$TotalCustomersNew = getSingleField($db,"Select COUNT(*) from customers Where FMID = '$FMID' and Status='New'");

			$Html .= "<tr>";
			$Html .= "<td>".$FMName."</td>";
			$Html .= "<td align='center'>".$TotalCustomersNew."</td>";
			$Html .= "<td align='center'>".$TotalCustomersClosed."</td>";
			$Html .= "<td align='center'>".$row['s_count']."</td>";
			$Html .= "<td align='center'>".moneyFormatIndia($row['s_mthemi'])."</td>";
			$Html .= "<td align='right'>".moneyFormatIndia($row['s_closbal']*-1)."</td>";
			$Html .= "</tr>";
		}
		$Html .= "<tr>";
		$Html .= "</tr>";
		$Html .= "</tfoot>";
		$Html .= "</table>";
		return $Html;		
	}

	function genSharesMonthwise($db) {
		$Html = "";
		$Sql  = "Select SUM(OpenBal) as S_OpenBal from shareholders Where OpenBal>0";
		$Balance = getSingleField($db,$Sql);

		$Html = "<strong>Share Holding Summary</strong>";
		$Html .= "<table id='gridtable' class='display table table-responsive table-striped table-bordered table-condensed'>";
		$Html .= "<thead>";
		$Html .= "<tr>";
		$Html .= "<th align='center'>Year-MM</th>";
		$Html .= "<th align='right'>Debit</th>";
		$Html .= "<th align='right'>Credit</th>";
		$Html .= "<th align='right'>Balance</th>";
		$Html .= "</tr>";
		$Html .= "</thead>";

		$Html .= "<tbody>";
		$Html .= "<tr>";
		$Html .= "<td align='center'>1.4.2018</th>";
		$Html .= "<td align='right'></td>";
		$Html .= "<td align='right'></td>";
		$Html .= "<td align='right'>".moneyFormatIndia($Balance)."</td>";
		$Html .= "</tr>";

		$SerialNo = 1;
		$TotalDebits=0;
		$TotalCredits=0;

		$Sql = "Select DATE_FORMAT(TrnDate,'%Y-%m') as YM,SUM(Credit) as S_Credit,SUM(Debit) as S_Debit from ft Where LENGTH(MemberID)=6 Group By DATE_FORMAT(TrnDate,'%Y-%m')";
		$result = getResultSet($db,$Sql);
		foreach($result as $row) {
			$Balance = $Balance + $row['S_Credit'] - $row['S_Debit'];
			$Html .= "<tr>";
			$Html .= "<td align='center'>".$row['YM']."</td>";
			$Html .= "<td align='right'>".moneyFormatIndia($row['S_Debit'])."</td>";
			$Html .= "<td align='right'>".moneyFormatIndia($row['S_Credit'])."</td>";
			$Html .= "<td align='right'>".moneyFormatIndia($Balance)."</td>";
			$Html .= "</tr>";

			$SerialNo++;
			$TotalDebits   	+= $row['S_Debit'];
			$TotalCredits 	+= $row['S_Credit'];
		}
		$Html .= "</tbody>";
		$Html .= "<tfoot>";
		$Html .= "<td></td>";
		$Html .= "<td align='right'>".moneyFormatIndia($TotalDebits)."</td>";
		$Html .= "<td align='right'>".moneyFormatIndia($TotalCredits)."</td>";
		$Html .= "<td align='right'></td>";
		$Html .= "</tr>";
		$Html .= "</tfoot>";
		$Html .= "</table>";
		return $Html;		
	}
	function SendEmailShareMthContr($db,$Month,$Year){
		$Subject = "Shares Monthly Contribution For $Month/$Year ";
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

			$Sql  = "Select * from ft Where ForMonth='$Month' and ForYear='$Year' AND TrnType='MthContr' AND TrnCode='REC' Order By DateStmp ";
			$ResultSet = getResultSet($db,$Sql);
			foreach($ResultSet as $row){
				$MemberID 	= $row['MemberID'];
				$RowMember  = getResultSet($db,"Select * from shareholders Where MemberID='$MemberID'");
				$MemberName	= "";
				$EmailID   	= "";
				$Mobile 	= "";
				$ClosBal 	= "";
				foreach($RowMember as $Member){
					$MemberName = $Member['Name'];
					$ClosBal    = $Member['ClosBal'];
					$EmailID 	= $Member['EmailID'];
					$Mobile 	= $Member['Mobile'];
				}
				$TrnDate = date("d-m-Y",strtotime($row['TrnDate']));
				$Credit  = $row['Credit'];
				$Details = "Dear <strong>$MemberName<strong>,"."<br>";
				$Details .= "Your Share Account ($MemberID) is credited with Rs.$Credit on ".$TrnDate."<br>";
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
	function getOpenBalShareAcc($db,$MemberID,$BalUptoDate){
		$Sql = "Select OpenBal,
			(Select SUM(Debit)  from ft Where ft.MemberID =  '$MemberID' AND ft.TrnDate < '$BalUptoDate' ) as Debit1,
			(Select SUM(Credit) from ft Where ft.MemberID =  '$MemberID' AND ft.TrnDate < '$BalUptoDate' ) as Credit1
		From shareholders 
		Where shareholders.MemberID = '$MemberID'";
		$ResultRow = getResultSet($db,$Sql);
		foreach($ResultRow as $Row){		
			$OpenBal = $Row['OpenBal'];
			$Debit 	= !is_null($Row['Debit1']) ? $Row['Debit1'] : 0; 
			$Credit = !is_null($Row['Credit1']) ? $Row['Credit1'] : 0; 
			$ClosBal= $OpenBal - $Debit + $Credit;
			return $ClosBal;
		}

	}
?>
