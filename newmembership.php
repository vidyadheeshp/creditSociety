<?php
date_default_timezone_set("Asia/Kolkata");
require_once("includes/pdofunctions_v1.php");
require_once("includes/functions.php");
$error = array();
$fname = "";
$lname = "";
$mname = "";
$deptid = "";
$startdate = "";
$amt = "";
$title = "";

$db = connectPDO();
$DepartmentList = "";
$sql = "Select * from departments Order By DeptName";
$result = getResultSet($db, $sql);

if (isset($_POST['submit'])) {
  if (isset($_POST['fname'])) {
    $fname = filter_input(INPUT_POST, 'fname', FILTER_UNSAFE_RAW);

    if (!ctype_alpha($fname)) {
      $error['fname'] = "Enter valid first name";
    }
  }
  if (isset($_POST['mname'])) {
    $mname = filter_input(INPUT_POST, 'mname', FILTER_UNSAFE_RAW);

    if (!ctype_alpha($fname)) {
      $error['mname'] = "Enter valid Middle name";
    }
  }
  if (isset($_POST['lname'])) {
    $lname = filter_input(INPUT_POST, 'lname', FILTER_UNSAFE_RAW);
    if (!ctype_alpha($lname)) {
      $error['lname'] = "Enter valid last name";
    }
  }
  if (isset($_POST['title'])) {
    $title = filter_input(INPUT_POST, 'title', FILTER_UNSAFE_RAW);
    echo $title;
  }
  $flag = 0;
  if (isset($_POST['deptid'])) {
    foreach ($result as $row) {
      if ($row['DeptID'] == $_POST['deptid']) {
        $flag = 1;
        break;
      }
    }
    if ($flag == 0) {
      $error['dept'] = "Select a valid department";
    } else {
      $deptid = $_POST['deptid'];
    }
  }
  if (isset($_POST['startdate'])) {
    $flag = 0;
    for ($i =  1; $i <  4; $i++) {
      $time = strtotime(sprintf('%d months', $i));
      $month = date("F", $time);
      $year = date("Y", $time);
      $label = $month . "-" . $year;
      if ($label == $_POST['startdate']) {
        $flag = 1;
      }
    }
    if ($flag == 0) {

      $error['startdate'] = "Invalid start month";
    } else {
      $startdate = $_POST['startdate'];
    }
  }
  if (isset($_POST['amt'])) {
    $amt = filter_input(INPUT_POST, 'amt', FILTER_UNSAFE_RAW);
    if ((!ctype_digit($_POST['amt'])) || $_POST['amt'] < 499) {

      $error['amt'] = "Enter valid amount";
    }
  }
  if (empty($error)) {
    $fields = array();
    $fields['name'] = strtoupper($title . " " . $fname . " " . $mname . " " . $lname);
    $fields['deptid'] = $deptid;
    $fields['startmonth'] = $startdate;
    $fields['amt'] = $amt;
    $fields['status'] = 0;
    var_dump($fields);
    insert($db, 'newmemberapplication', $fields);
    session_start();

    $_SESSION['name'] = $title . " " . $fname . " " . $mname . " " . $lname;

    header("location: http://localhost/Society/NewMembershipPDF.php");
    ?> <script>window.location.reload()</script>
    <?php
  }
}



$DepartmentList .= "<option value=''>Select Department</option>";
$selected = "";
foreach ($result as $row) {
  if (isset($_POST['deptid']) && $row['DeptID'] == $_POST['deptid']) {
    $selected = "selected";
  }
  $DepartmentList .= "<option $selected value=" . $row['DeptID'] . ">" . $row['DeptName'] . "</option>";
  $selected = "";
}
    
//includes the html tag with head section of the web page. (Starts with Body Tag)
include_once 'includes/Layout/header.php';

//includes the navbar consisting of Nav menus for home page.
include_once 'includes/Layout/navbar.php';

?>
<div class="hold-transition register-page">
<section class="content">
        <div class="register-box" >
          <div class="register-logo">  
            <a href="index.php"><b>GIT</b> Employees Cooperative Credit Society</a>
          </div>

          <div class="register-box-body">
            <p class="login-box-msg">Register a new membership</p>
            <p class="login-box-msg" style="text-align:justify; color:navy">Fill the following details required for generating the new membership application.
                Take a print of the application document and <b>submit the duly signed form to the GIT
                  Employees Co-Op. Credit Society Office </b> for further processing. </p>

            <form method="post" action="#">
              <div class="form-group has-feedback">
              <label for="firstname" class="form-label fw-bold">First Name :</label>
                <div class="col-md-12">
                  
                    <div class="row">
                     <div class="form-group col-md-3">
                          <select class="form-control" name="title" id="title" >
                            <option value="Mr.">Mr.</option>
                            <option value="Ms.">Ms.</option>
                            <option value="Mrs.">Mrs.</option>
                          </select>
                        </div>
                        <div class="form-group col-md-9">
                          <div class=" has-validation">
                            <input type=" text" name="fname" id="fname" value="<?php echo $fname; ?>" pattern="[A-Za-z\s.'-]{1,}" title="Only Alphabets and spaces allowed. Enter your first name." style="text-transform: uppercase" class="form-control  
                          <?php if (isset($error['fname'])) echo 'is-invalid';
                          ?>" id="firstname" placeholder="First Name" required>


                            <div id="fnameFeedback" class="<?php if (isset($error['fname'])) echo 'invalid-feedback';
                                                            ?> ">
                              <?php if (isset($error['fname'])) echo $error['fname']; ?>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
              </div>

              <div class="form-group has-feedback">
                <label for="mname" class="form-label fw-bold">Middle Name :</label>
                <div class=" has-validation">
                  <input type="text" class="form-control" id="mname" name="mname" value="<?php echo $mname; ?>" style=" text-transform: uppercase" class="form-control <?php if (isset($error['mname'])) echo 'is-invalid'; ?>" pattern="[A-z\s.'-]{1,}" title="Only Alphabets and spaces allowed. Enter your middle name." placeholder="Middle Name" required>
                  <div id="mnameFeedback" class="<?php if (isset($error['mname'])) echo 'invalid-feedback'; ?> ">
                    <?php if (isset($error['mname'])) echo $error['mname']; ?>
                  </div>
                </div>
              </div>
              <div class="form-group has-feedback">
                <label for="lname" class="form-label fw-bold">Last Name :</label>
                <div class="has-validation">
                  <input type="text" id="lname" name="lname" value="<?php echo $lname; ?>" style=" text-transform: uppercase" class="form-control <?php if (isset($error['lname'])) echo 'is-invalid'; ?>" pattern="[A-z\s.'-]{1,}" title="Only Alphabets and spaces allowed. Enter your last name." placeholder="Last Name" id="lname" required>
                  <div id="lnameFeedback" class="<?php if (isset($error['lname'])) echo 'invalid-feedback'; ?> ">
                    <?php if (isset($error['lname'])) echo $error['lname']; ?>
                  </div>
                </div>
              </div>
            <div id="check">
              <div class="form-group has-feedback">
              <label for="Department" class="form-label fw-bold">Department</label>
                  <div class=" has-validation">
                    <Select class="form-control <?php if (isset($error['dept'])) echo 'is-invalid'; ?>" id="deptid" required name="deptid" required>
                      <?php echo $DepartmentList; ?>
                    </Select>
                    <div class="<?php if (isset($error['dept'])) echo 'invalid-feedback'; ?>">
                      <?php if (isset($error['dept'])) echo $error['dept']; ?>
                    </div>
                  </div>
              </div>
              <div class="form-group has-feedback">
                <label for="startmonth" class="form-label fw-bold">Start Month</label>
                    <div class="has-validation">
                      <select class="form-control <?php if (isset($error['startdate'])) echo 'is-invalid'; ?>" name="startdate" size='1'>
                        <?php
                        $selected = "";
                        for ($i =  1; $i <  4; $i++) {
                          $time = strtotime(sprintf('%d months', $i));
                          $month = date("F", $time);
                          $year = date("Y", $time);
                          $label = $month . "-" . $year;
                          if (isset($_POST['startdate']) && $_POST['startdate'] == $label) {
                            $selected = 'selected';
                          }
                          echo "<option $selected value='$label'>$label</option>";
                          $selected = "";
                        }
                        ?>
                      </select>
                      <div id="startmonthFeedback" class="invalid-feedback">
                        <?php if (isset($error['startdate'])) echo $error['startdate']; ?>
                      </div>
                    </div>
              </div>
              <div class="form-group has-feedback">
                    <label for="amount" class="form-label fw-bold">Amount</label>
                    <div class="has-validation">
                      <input type="number" value="<?php echo $amt; ?>" class="form-control <?php if (isset($error['amt'])) echo 'is-invalid'; ?>" required name="amt" min=500>
                      <div id="amtFeedback" class="invalid-feedback">
                        <?php if (isset($error['amt'])) echo $error['amt']; ?>
                      </div>
                    </div>
              </div>
              <div class="row">
              <button class="btn btn-lg btn-primary col-md-12" id="submit" name="submit" value="submit" type="submit">Generate Application</button>
                  
              </div>
            </form>
          </div>
            <a href="#" class="text-center">I already have a membership</a>
          </div>
          <!-- /.form-box -->
        </div>
        <!-- /.register-box -->
      </section>
</div>
<?php
      //includes the footer section with closure of body tag, the javascript and the html tag
    include_once 'includes/Layout/footer.php';
    ?>
