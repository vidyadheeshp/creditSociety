
<?php //include_once 'navbar.php' 
    
    //includes the html tag with head section of the web page. (Starts with Body Tag)
    include_once 'includes/Layout/header.php';
    
    //includes the navbar consisting of Nav menus for home page.
    include_once 'includes/Layout/navbar.php';
    ?>

<div class="content-wrapper">
    <div class="container">

    <section class="content">
        <div class="callout callout-default">
        <h2 class="text-default">About Us</h2>

          <p>Lorem ipsum dolor sit amet,
         consectetur adipiscing elit, sed do eiusmod tempor
         incididunt ut labore et dolore magna aliqua.
         Ut enim ad minim veniam, quis nostrud exercitation
         ullamco laboris nisi ut aliquip ex ea commodo consequat.
         Duis aute irure dolor in reprehenderit in voluptate velit
         esse cillum dolore eu fugiat nulla pariatur.</p>
        </div>
        <div class="callout callout-default">
        <h2 class="fw-bold">Chairman's Message</h2>

          <p>The construction of this layout differs from the normal one. In other words, the HTML markup of the navbar
            and the content will slightly differ than that of the normal layout.</p>
            <p class="pull-right"> <strong>Dr. V.G.Mutalik Desai</strong></p>
        </div>
     
 </div>
 </div>
 <?php
      //includes the footer section with closure of body tag, the javascript and the html tag
    include_once 'includes/Layout/footer.php';
    ?>