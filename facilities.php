<?php 
include_once("header.html");
include_once("navbar.html");
?>
<style>
    
.fac{
      margin-top: 20px;
      padding-top: 8px;
    background-color: white;
    max-width: 80%;
    padding-left:20%;
    align-items: center;
    align-content: center;
}
/* Style tab links */
.tablink {
    align-content:center;
    text-align:center;
  background-color: #555;
  color: white;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 8px 8px;
  font-size: 17px;
  width: 29%;
}

.tablink:hover {
  background-color: #777;
}

/* Style the tab content (and add height:100% for full page content) */
.tabcontent {
  display: none;
  padding-top:50px;
  padding-left: 28%;
  height: 100%;
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
  // Remove the background color of all tablinks/buttons
  tablinks = document.getElementsByClassName("tablink");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].style.backgroundColor = "";
  }

  // Show the specific tab content
  document.getElementById(pageName).style.display = "block";

  // Add the specific color to the button used to open the tab content
  elmnt.style.backgroundColor = color;
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();
        </script>
<div class="fac">
<button class="tablink" onclick="openPage('member', this, 'black')">New Membership</button>
<button class="tablink" onclick="openPage('loans', this, 'black')" id="defaultOpen">Loans</button>
<button class="tablink" onclick="openPage('contribution', this, 'black')">Share Contribution Upgradation</button>
</div>
<div id="member" class="tabcontent">
  <h2>New Membership</h2>
  <p><a href="#">Download Form</a></p>
</div>

<div id="loans" class="tabcontent">

<div id="contribution" class="tabcontent">
  <h2>Contribution Upgrading</h2>
  <p><a href="#">Upgradation form</a></p>
</div>
