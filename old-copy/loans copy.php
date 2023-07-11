<?php include_once("header.html");
include_once("navbar.html");
?>

<style>
  .accordion>input[name="collapse"] {
    display: none;

    /*position: absolute;
  left: -100vw;*/
  }

  .accordion label,
  .accordion .content {
    max-width: 620px;

  }


  .accordion .content {
    background: #fff;
    overflow: hidden;
    height: 0;
    transition: 0.5s;
    box-shadow: 1px 2px 4px rgba(0, 0, 0, 0.3);
  }

  .accordion>input[name="collapse"]:checked~.content {
    height: 380px;
    transition: height 0.5s;
  }

  .accordion label {
    display: block;

  }


  /* For Desktop */
  @media only screen and (min-width: 620px) {


    .accordion>input[name="collapse"]:checked~.content {
      height: 300px;
    }

  }

  .accordion {
    margin-bottom: 1em;
  }

  .accordion>input[name="collapse"]:checked~.content {
    border-top: 0;
    transition: 0.3s;
  }

  .accordion .handle {
    margin: 0;
    font-size: 16px;

  }

  .accordion label {
    color: #fff;
    cursor: pointer;
    font-weight: normal;
    padding: 10px;
    background: #6d615c;
    user-select: none;

  }

  .accordion label:hover,
  .accordion label:focus {
    background: #252525;
  }

  .accordion .handle label:before {
    content: "\f0d7";
    font-family: FontAwesome;
    display: inline-block;
    margin-right: 10px;
    font-size: 1em;
    line-height: 1.556em;
    vertical-align: middle;
    transition: 0.4s;

  }

  .accordion>input[name="collapse"]:checked~.handle label:before {
    transform: rotate(180deg);
    transform-origin: center;
    transition: 0.4s;
  }
</style>
<script>
  function openPage(pageName, elmnt, color) {
    // Hide all elements with class="tabcontent" by default */
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }
  }
</script>
<center>
  <h2> Loans </h2>

  <section class="accordion">
    <input type="checkbox" name="collapse" id="handle4" checked="checked">

    <h2 class="handle">
      <label for="handle4">Long Term Loans</label>
    </h2>
    <div class="content">
      <p><strong>Details of Long Term Loans:</strong>
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
  </section>
  <section class="accordion">
    <input type="checkbox" name="collapse" id="handle5">
    <h2 class="handle">
      <label for="handle5">Short Term Loans</label>
    </h2>
    <div class="content">
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
  </section>
  <section class="accordion">
    <input type="checkbox" name="collapse" id="handle6">
    <h2 class="handle">
      <label for="handle6">Emergency Loans</label>
    </h2>
    <div class="content">
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
  </section>
  </div>
</center>