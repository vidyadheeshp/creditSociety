
<?php //include_once 'navbar.php' 
    
    //includes the html tag with head section of the web page. (Starts with Body Tag)
    include_once 'includes/Layout/header.php';
    
    //includes the navbar consisting of Nav menus for home page.
    include_once 'includes/Layout/navbar.php';
    ?>

<div class="hold-transition register-page">
<section class="content-wrapper">
      
<div class="clearfix"></div>
  <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
              <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab">Long Term Loans</a></li>
                <li><a href="#tab_2" data-toggle="tab">Short Term Loans</a></li>
                <li><a href="#tab_3" data-toggle="tab">Emergency Loans</a></li>
                
                <!--li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li-->
              </ul>
              <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                  <ul style=" text-align: left;">
                    <li> Rate of Interest: 10%</li>
                    <li>Loan tenure: 5 years</li>
                    <li>Maximum amount sanctioned: Rs. 4,00,000/-(Rs. Four lakh only) </li>
                  </ul>
                  <b>Criteria to get the loan</b>
                  <ul style=" text-align: left;padding-bottom:2px">
                    <li>He or She is eligible to get the loan of 50% of the gross salary.</li>
                    <li> He or She should be confirmed employee.</li>
                    <li> Dividend will be paid every year at the time of Deepavali depend up on the profit earned during the financial year.
                    </li>
                  </ul>
                  </p>
                  <p><a href="./members/membershome.php">Long Term Loan Form</a> </p>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="tab_2">
                    <p><strong>Short Term Loan Details</strong>.
                    <ul style=" text-align: left;">
                      <li> Rate of Interest: 12%</li>
                      <li>Loan tenure: 3 years</li>
                      <li>Maximum amount sanctioned: Rs. 2,00,000/-(Rs. Two lakh only) </li>
                    </ul>
                    <b>Criteria to get the loan</b>
                    <ul style=" text-align: left;padding-bottom:2px">
                      <li>He or She is eligible to get the loan of 50% of the gross salary.</li>
                      <li> He or She should be confirmed employee.</li>
                      <li> Dividend will be paid every year at the time of Deepavali depend up on the profit earned during the financial year.
                      </li>
                    </ul>
                    </p>
                    <p><a href="#">Short Term Loan Form</a></p>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="tab_3">
                  <p><strong>Emergency Loan Details:</strong>
                  <ul style=" text-align: left;">
                    <li> Rate of Interest: 15%</li>
                    <li>Loan tenure: 1 years</li>
                    <li>Maximum amount sanctioned: Rs. 1,00,000/-(Rs. One lakh only) </li>
                  </ul>
                  <b>Criteria to get the loan</b>
                  <ul style=" text-align: left;padding-bottom:2px">
                    <li>He or She is eligible to get the loan of 50% of the gross salary.</li>
                    <li> He or She should be confirmed employee.</li>
                    <li> Dividend will be paid every year at the time of Deepavali depend up on the profit earned during the financial year.
                    </li>
                  </ul>
                  </p>
                  <p><a href="#">Emergency Loan Form</a></p>
                </div>
                <!-- /.tab-pane -->
              </div>
              <!-- /.tab-content -->
            </div>
            <!-- nav-tabs-custom -->
          </div>
          <!-- /.col -->
          <div class="clearfix"></div>

</section>  
</div
      <?php
      //includes the footer section with closure of body tag, the javascript and the html tag
    include_once 'includes/Layout/footer.php';
    ?>
