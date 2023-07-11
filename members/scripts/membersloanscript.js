function preview_image(event) 
	{
	 var reader = new FileReader();
	 reader.onload = function()
	 {
	  var output = document.getElementById('output_image');
	  output.src = reader.result;
	 }
	 reader.readAsDataURL(event.target.files[0]);
	}

    debugger;
  	//debugger;
	$("#loanfmid").change(function(){
		$("#showerror").html("");
		var loanfmid = $("#loanfmid").val();
		var message  = "";
		$.ajax({
	        type: "POST",
	        url: "ajax_getloansettings.php",
	        data: "LoanFMID="+loanfmid,
	        success : function(text){
	        	$("#intrate").val(text);
				$("#showerror").html(message);
	        }
		});
	});
	$("#loanamt").change(function(){
		
		$("#showerror").html("");
		var loanamt = $("#loanamt").val();
		var intrate = $("#intrate").val();
		var months  = $("#months").val();
		var years   = months / 12;
		var message  = "";
		//alert(intrate);
		$.ajax({
	        type: "POST",
	        url: "ajax_getemi.php",
	        data: "LoanAmt="+loanamt+"&IntRate="+intrate+"&Years="+years,
	        success : function(text){
				//alert(text);
	        	$("#mthemi").val(text);
				$("#showerror").html(message);
	        }
		});
	});
    $("form").submit(function(event){
        // Stop form from submitting normally
        //check all the element values 
        var errors = 0;
        if($("#g1memberid").val() == $("#g2memberid").val()) {
        	alert("Select two different Guarantors");
        	errors++;
        }
        if($("#loanamt").val()<=0){
        	alert("Please enter valid Loan Amt");
        	errors++;
        }
        if($("#intrate").val()<=0){
        	alert("Please Select Loan Account");
        	errors++;
        }
        if($("#months").val()<=0){
        	alert("Please enter Number of Months");
        	errors++;
        }
        if($("#mthemi").val()<=0){
        	alert("Please enter all details correctly");
        	errors++;
        }

        if(errors>0){
        	alert("Please solve errors in entry ");
        	event.preventDefault();
        	return false;
        }
        // Get action URL
        var actionFile = $(this).attr("action");
        var formValues = $(this).serialize();
        // Send the form data using post
        $.post(actionFile, formValues, function(data){
            // Display the returned data in browser
            // $("#result").html(data);
        });
    });
