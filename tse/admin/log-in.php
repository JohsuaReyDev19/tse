<?php ob_start(); ?>
<?php require_once('../connections/pdoconnect.php'); ?>

<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
    session_start();
}

$phu = new php_util();
$db = new DatabaseConnect();

$loginFormAction = $_SERVER['PHP_SELF'];


if (isset($_GET['accesscheck'])) {
    $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

$msg = "";

if (isset($_POST['username'])) {

    $check_field_query = "SHOW COLUMNS FROM participant LIKE 'description'";
    $db->query($check_field_query);
    $check_field_queryRS = $db->rowsingle();
    $check_field_query_count = $db->rowcount();
    
    if ($check_field_query_count==0) {
        $SQLcrud = "ALTER TABLE participant ADD description MEDIUMTEXT";
        $db->query($SQLcrud);
        $db->execute();
    } 

    $loginUsername = $_POST['username'];
    $password = $_POST['password'];
    $MM_fldUserAuthorization = 'group';
    $MM_redirectLoginSuccess = "index.php";
    $MM_redirectLoginFailed = "log-in.php";
    $MM_redirecttoReferrer = true;

    $LoginRS_query = "select * from user where `username`=? and `password`=?";
    $db->query($LoginRS_query);
    $db->bind(1, $_POST['username']);
    $db->bind(2, $phu->encryptMessage($_POST['password']));
    $LoginRS = $db->rowsingle();
    $loginFoundUser = $db->rowcount();

    if ($loginFoundUser > 0 && $LoginRS['status'] === "active") {

       

            if (PHP_VERSION >= 5.1) {
                session_regenerate_id(true);
            } else {
                session_regenerate_id();
            }
            //declare two session variables and assign them

            $_SESSION['AIT_MM_UserName']  = NULL;
            $_SESSION['AIT_MM_UserGroup'] = NULL;
            $_SESSION['AIT_MM_FullName']  = NULL;
            $_SESSION['AIT_MM_ID']        = NULL;
            $_SESSION['AIT_MM_Designation'] = NULL;
            $_SESSION['judge_id'] = NULL;

            unset($_SESSION['AIT_MM_UserName']);
            unset($_SESSION['AIT_MM_UserGroup']);
            unset($_SESSION['AIT_MM_FullName']);
            unset($_SESSION['AIT_MM_ID']);
            unset($_SESSION['AIT_MM_Designation']);
            unset($_SESSION['judge_id']);



            $_SESSION['AIT_MM_FullName'] = $LoginRS['fullname'];
            $_SESSION['AIT_MM_UserName'] = $LoginRS['username'];
            $_SESSION['AIT_MM_UserGroup'] = $LoginRS['group'];
            $_SESSION['AIT_MM_Designation'] = $LoginRS['designation'];
            $_SESSION['AIT_MM_ID'] = $LoginRS['id'];
            $_SESSION['unique_id'] = $LoginRS['id'];
            $_SESSION['judge_id'] = $LoginRS['judge_id'];
            $phu->add_log_in_out($_SESSION['AIT_MM_ID'], 'log-in');

            if (isset($_SESSION['PrevUrl']) && true) {
                $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];
            }



            header("Location: " . $MM_redirectLoginSuccess);

        
    } 
    else {
        $msg="User Name or Password is Incorrect!";
    }


}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="robots" content="noindex, nofollow">
    <meta content="" name="description">
    <meta content="" name="keywords">
    <title><?php echo $app_title; ?></title>
</head>


<?php require_once('../template/phplink.php'); ?>

<script>
    //const Swal = require('sweetalert2');

    function ShowError() {
        Swal.fire('Log-In Error', '<?php echo $msg; ?>', 'info');
    }
</script>

<style>
    .carousel-caption {
        position: relative;
        left: auto;
        right: auto;
        bottom: 0;
        width: 100%;
        color: white;
        background-color: rgba(0, 0, 0, 0.4);
        font-size: 15px;
        padding-top: 7px;
        padding-bottom: 0px;


    }
</style>

<body >
    <!--<body> style="background-color:<?php //echo $app_background_scheme;
                                        ?>"-->

    <div align="center" class="alert" style="color: #012970;
    background-color: #f6f9ff;"><img src="../images/logo.png?start_time=<?php echo time(); ?>" class="img-responsive" width="150px">
        <h3><strong>Tabulation System for Events and Competetion for PRMSU Candelaria Campus</strong></h3><br>
    </div>
    <br>
    <?php if (isset($_POST['username'])) {
        echo "<script>ShowError();</script>";
    } ?>

    <div class="row">
        <div class="col-md-1 col-sm-12"></div>
        <div class="col-md-6 col-sm-12">
        <img src="../images/front.png?start_time=<?php echo time(); ?>" class="img-responsive" width="100%" >

        </div>


        <div class="card col-md-4">
            <div class="card-body">
                <div>
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class=" text-center small">
                    </p>
                </div>
                <div class="col-md-12 col-sm-12">
                    <form method="POST" name="form1" id="form1" class="row g-3">
                        <div class="input-group"> <span class="input-group-text" id="inputGroupPrepend"><i
                                    class="bi bi-person-badge"> </i></span><input type="text" class="form-control"
                                id="username" name="username" placeholder="User Name" value=""> </div>
                        <br>
                        <div class="input-group"><span class="input-group-text" id="inputGroupPrepend"><i
                                    class="bi bi-key">
                                </i></span> <input type="password" class="form-control" id="password" name="password"
                                placeholder="Password" value=""></div>
                        <div><input class="form-check-input" type="checkbox" id="gridCheck1"
                                onclick="showhidepassword()">
                            <label class="form-check-label" for="gridCheck1">Show
                                Password</label>
                        </div>
                        <br>
   
                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-success "> <i class="bi bi-unlock-fill">
                                </i><span>&nbsp;Log-In</span></button>
                            
                        </div>

                            <div class="d-grid gap-2">

                            </div>
                            <br>

                    </form>
                </div>
                <!-- End card mb-4-->
            </div>
            <!-- End card mb-4-->

        </div>
        <div class="col-md-1 col-sm-12">

        </div>
    </div>




    <div class="col-md-12 footer" align="center">
        <div class="copyright"> &copy; Copyright <strong><span><?php echo $app_copyright; ?></span></strong>. All
            Rights Reserved. </div>
        <div class="credits"> <?php echo $tagline . ' * ' . $app_footer; ?></div>
    </div>
    </div>



</body>
<script>
    function showhidepassword() {
        var x = document.getElementById("password");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>

</html>
<?php ob_flush();
$db->close();
?>