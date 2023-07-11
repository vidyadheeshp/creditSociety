<?php
	// recalculate.php
	// AUthor : Anand V Deshpande
	// Date Written : 26.12.2019
	session_start();
	include('../includes/pdofunctions_v1.php');
	$db = connectPDO();
	include('../includes/functions.php'); 

	ExeQuery($db,"Update fm Set Credits=0,Debits=0");	
	ExeQuery($db,"Update fm Set ClosBal = OpenBal + Credits - Debits");
	ExeQuery($db,"Update fm Set Debits = (Select SUM(Debit) from ft Where fm.FMID=ft.FMID)");
	ExeQuery($db,"update fm Set Credits = (Select SUM(Credit) from ft Where fm.FMID = ft.FMID)");

	// for fix accounts Credits=Credits + (select... does not work)
	$Rs = getResultSet($db,"Select * from fm Where (AcType='Bank' or AcType='Cash')  Order By FMID");
	foreach($Rs as $Row){
		$FMID = $Row['FMID'];
		$IndDebit  = getSingleField($db,"Select SUM(Credit) from ft Where ft.FixFMID='$FMID'");
		$IndCredit = getSingleField($db,"Select SUM(Debit)  from ft Where ft.FixFMID='$FMID'");
		if(is_null($IndDebit)){
			$IndDebit=0;
		}
		if(is_null($IndCredit)){
			$IndCredit = 0;
		}
		update($db,"fm",array("Debits"=>$IndDebit,"Credits"=>$IndCredit),"FMID='$FMID'");
	}
	//ExeQuery($db,"Update fm Set Debits = Debits +  (Select SUM(Credit) from ft Where fm.FMID=ft.FixFMID)");
	//ExeQuery($db,"update fm Set Credits = Credits + (Select SUM(Debit) from ft Where fm.FMID = ft.FixFMID)");
	ExeQuery($db,"update fm Set Debits = 0 Where Debits IS NULL");
	ExeQuery($db,"update fm Set Credits= 0 Where Credits IS NULL");
	ExeQuery($db,"Update fm Set ClosBal = OpenBal + Credits - Debits");

	ExeQuery($db,"Update shareholders Set Debits = (Select SUM(Debit) from ft Where shareholders.MemberID=ft.MemberID)");
	ExeQuery($db,"Update shareholders Set Credits = (Select SUM(Credit) from ft Where shareholders.MemberID=ft.MemberID)");
	ExeQuery($db,"update shareholders Set Debits = 0 Where Debits IS NULL");
	ExeQuery($db,"update shareholders Set Credits= 0 Where Credits IS NULL");
	ExeQuery($db,"Update shareholders Set ClosBal = OpenBal + Credits - Debits");

	ExeQuery($db,"Update customers Set Debits = (Select SUM(Debit) from ft Where customers.LoanID=ft.LoanID)");
	ExeQuery($db,"Update customers Set Credits = (Select SUM(Credit) from ft Where customers.LoanID=ft.LoanID)");
	ExeQuery($db,"update customers Set Debits = 0 Where Debits IS NULL");
	ExeQuery($db,"update customers Set Credits= 0 Where Credits IS NULL");
	ExeQuery($db,"Update customers Set ClosBal = OpenBal + Credits - Debits");
	MsgBox("Recalculation Done successfully!!!","accountsmenu.php",True);
?>
