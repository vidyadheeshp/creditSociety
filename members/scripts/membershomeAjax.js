
	$(document).ready(function() {
        $("#myModal").modal("hide");

	
		function showshareledger(memberid){
			
			$.ajax({
		        type: "POST",
		        url: "../accounts/ajax_shareholderledger.php",
		        data: "MemberID="+memberid,
		        success : function(text){
		        	//alert(text);
					var ret = JSON.parse(text);
					$("#shareledger").html(ret['Body']);
					$("html, body").animate({ scrollTop: $(document).height()-$(window).height() });
		        }
			});
		}
		function showloanledger(loanid){
			//alert(loanid);
			$.ajax({
		        type: "POST",
		        url: "../accounts/ajax_loanaccountledger.php",
		        data: "LoanID="+loanid,
		        success : function(text){
		        alert(text);
					var ret = JSON.parse(text);
					$("#loanledgerheader").html(ret['Header']);
					$("#loanledger").html(ret['Body']);
					$("html, body").animate({ scrollTop: $(document).height()-$(window).height() });
		        }
			});			
		}		
		function showLoanAppln(loanid){
			$.ajax({
		        type: "POST",
		        url: "./ajax_loanApplndetails.php",
		        data: "LoanID="+loanid,
		        success : function(text){
		
					var ret = JSON.parse(text);
					$("#loanledgerheader").html(ret['Header']);
					$("#loanledger").html(ret['Body']);
					$("html, body").animate({ scrollTop: $(document).height()-$(window).height() });
		        }
			});	
		}
		function aproveGarn(loanid){
			
			$.ajax({
				type:"POST",
				url:"./ajax_loanDetailsGuarantor.php",
				data:"LoanID="+loanid,
				success: function(text){
					
					var ret = JSON.parse(text);
					$("#loanledgerheader").html(ret['Header']);
					$("#loanledger").html(ret['Body']);
					$("html, body").animate({ scrollTop: $(document).height()-$(window).height() });
				}
			});
		}
		function approve(loanid){
			$.ajax({
				type:"POST",
				url:"./ajax_guarantorsApproval.php",
				data:"LoanID="+loanid,
				success: function(text){
					alert(text);
					location.reload(); 
				}
			});
		}
		function reject(loanid){
			$.ajax({
				type:"POST",
				url:"./ajax_rejectGuarantor.php",
				data:"LoanID="+loanid,
				success: function(text){
					alert(text);
					location.reload(); 
				}
			});
		}
		
		function editprofile(){
	       	$("#hidden_type").val("Edit");
	       	$("#hiddenform").submit();			
		}
		function mthsharevariation() {
			window.location.href='mthsharevariation.php';
		}
		function deleteLoanAppl(loanid){
			res=confirm("Are you sure you want to delete the loan application?");
			if(res){
			$.ajax({
				type:"POST",
				url:"./ajax_deleteLoanAppl.php",
				data:"LoanID="+loanid,
				success: function(text){
					alert(text);
					location.reload(); 
				}
			});
		}
		}
		function editLoanAppl(loanid){
			
			window.location.replace("./ajax_editLoanAppl.php?LoanID="+loanid);
			
		}

    });