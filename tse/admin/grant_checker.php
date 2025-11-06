<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

$phu=new php_util();

    //$MM_authorizedUsers=$MM_authorizedUsers;
    $MM_restrictGoTo = "../admin/log-in.php";
    require("../admin/grant.php");

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session variables
  $phu=new php_util();
  $db=new DatabaseConnect();
  $phu->add_log_in_out($_SESSION['AIT_MM_ID'],'log-out'); 


  $_SESSION['AIT_MM_FullName'] = $LoginRS['fullname'];
  $_SESSION['AIT_MM_UserName'] = $LoginRS['username'];
  $_SESSION['AIT_MM_UserGroup'] = $LoginRS['group'];
  $_SESSION['AIT_MM_Designation'] = $LoginRS['designation'];
  $_SESSION['AIT_MM_ID']=$LoginRS['id'];
  $_SESSION['unique_id'] = $LoginRS['id'];
  

  $_SESSION['AIT_MM_UserName']  = NULL;
  $_SESSION['AIT_MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl']      = NULL;
  $_SESSION['AIT_MM_FullName']  = NULL;
  $_SESSION['AIT_MM_ID']        = NULL;
  $_SESSION['AIT_MM_Designation'] = NULL;
  $_SESSION['unique_id'] = NULL;
  
  unset($_SESSION['AIT_MM_UserName']);
  unset($_SESSION['AIT_MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
  unset($_SESSION['AIT_MM_FullName']);
  unset($_SESSION['AIT_MM_ID']);
  unset($_SESSION['AIT_MM_Designation']);
  unset($_SESSION['unique_id']); 


  $logoutGoTo = "../admin/log-in.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>