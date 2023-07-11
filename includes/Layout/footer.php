 <!-- /.content-wrapper -->
 <footer class="main-footer">
    <div class="container">
      <div class="pull-right hidden-xs">
        <b>Version</b> 2.0
      </div>
      <strong>Copyright &copy; 2023 <a href="http://git.edu">GIT Employees Cooperative Credit Society</a>.</strong> All rights
      reserved.
    </div>
    <!-- /.container -->
  </footer>
</div>
<!-- ./wrapper -->
</body>
<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>


<!-- Script for Governance Page-->
<script>
    $(document).ready(function() {
        var result = get_query();
        switch (result) {
            case 'directors':
                $('#directors').show();
                break;
            case 'patrons':
                $('#patrons').show();
                break;
            case 'auditors':
                $('#auditors').show();
                break;
            case 'legaladvisor':
                $("#advisors").show();
                break;
            case 'officestaff':
                $('#officestaff').show();
                break;
            default:
                $('#error').innerHTML('invalid URL');

        }

    });
    
    $("#check :input").attr("disabled", true);
    $('#lname').focusout(function() {

      let fname = $('#fname').val();
      let lname = $('#lname').val();
      let mname = $('#mname').val();
      $.post("verifymemberappl.php", {
        fname: fname,
        mname: mname,
        lname: lname
      }, function(data) {
        if (data == 'success') {
          $.post("NewMembershipPDF.php", {
            name: fname + " " + mname + " " + lname
          }, function(data) {
            var link = document.createElement('a');
            link.href = "NewMembershipPDF.php";
            link.download = "fname New Membership Application" + ".pdf";
            link.click();
            link.remove();
          })
        } else if (data == 'failed') {
          $("#check :input").attr("disabled", false);
        }
      });
    });

    function get_query() {
        var url = document.location.href;
        var qs = url.substring(url.indexOf('?') + 1).split('&');

        qs[0] = qs[0].split('=');
        return qs[0][1]
    }

    // Script for new membership page.
  
   
</script>

</html>