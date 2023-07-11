<?php
    session_start();
    require_once('../includes/functions.php');
    if(!isset($_SESSION['UserType'])) {
       MsgBox("Direct script access prohibited","../index.php",True);
       exit();
    }	
    $UserType 	= $_SESSION['UserType'];
	
    if($UserType == 'Member'){
    } else{
		//MsgBox($UserType,'',false);
       MsgBox("Access for Members only","../login.php",True);
       exit();
    }	
    include('../includes/pdofunctions_v1.php');
    $db = connectPDO();
	$Guarantors1	 	= genShareHoldersSelect3($db," Where ClosBal>0 ","g1memberid");   
	//$Guarantors2 		= genShareHoldersSelect3($db," Where ClosBal>0 ","g2memberid"); 
    include('../includes/shares.php'); 
    $Date = date("d-m-Y H:i");
   //var_dump($_SESSION['UserRow']);
    $MemberRow = $_SESSION['UserRow'];
    $MemberName="";
    $MemberID="";
    foreach($MemberRow as $Row){
        $MemberID = $Row['MemberID'];
        $MemberName = $Row['Name'];
       //echo $MemberName;
   
        $ClosBal  = $Row['ClosBal'];
   
        $MemberID = trim($Row['MemberID']);
        $MthContr = $Row['MthContr'];
    }
    $Report       = genDashboardMember($db);

	if(isset($_POST['approve'])){
		echo "approved";
	}
	if(isset($_POST['reject'])){
		echo "reject";
	}
?>

<?php //include_once 'navbar.php' 
    
    //includes the html tag with head section of the web page. (Starts with Body Tag)
    include_once 'Layout/header.php';
    
    //includes the navbar consisting of Nav menus for home page.
    include_once 'Layout/sidebar.php';
    ?>


<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Hello and Welcome <strong><?php echo $MemberName;?></strong></h3>

          <!--div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
              <i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
              <i class="fa fa-times"></i></button>
          </div-->
        </div>
        <div class="box-body">
            <input type='hidden' id='memberid' value='<?php echo $MemberID;?>'/>
            <center>
                <?php echo $Report ?>
                <div id='shareledger' class='table-responsive'>
                </div>
                <div id='loanledgerheader' class='table-responsive'>
                </div>
                <div id='loanledger' class='table-responsive'>
                </div>
            </center>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
          
        </div>
        <!-- /.box-footer-->
      </div>
      <!-- /.box -->

    </section>

    </div>
      <?php
      //includes the footer section with closure of body tag, the javascript and the html tag
    include_once 'Layout/footer.php';
    ?>
   