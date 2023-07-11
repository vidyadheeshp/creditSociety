<?php 
    session_start();
    $_SESSION['UserRow']=null;
    $_SESSION['UserType']="";

    session_commit();
    session_abort();
    session_cache_expire();
    session_destroy();
    header("location:../login.php");
    exit();
?>