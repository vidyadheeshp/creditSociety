<?php
require_once("includes/pdofunctions_v1.php");
require_once("includes/functions.php");
$db = connectPDO();
if (isset($_POST['fname']) && isset($_POST['mname']) && isset($_POST['lname'])) {
    $fname = filter_input(INPUT_POST, 'fname', FILTER_UNSAFE_RAW);
    $mname = filter_input(INPUT_POST, 'mname', FILTER_UNSAFE_RAW);
    $lname = filter_input(INPUT_POST, 'lname', FILTER_UNSAFE_RAW);
    $name = $fname . " " . $mname . " " . $lname;
    $memberappl = getResultSet($db, "Select * from newmemberapplication Where name like '%$name%' ");

    if (count($memberappl) > 0) {
        echo "success";
    } else {
        echo "failed";
    }
}
