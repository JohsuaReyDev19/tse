<?php ob_start(); ?><?php require_once('../connections/pdoconnect.php'); ?>
<?php

if (!isset($_SESSION)) {
    session_start();
}


$phu = new php_util();
$menu_id = $phu->get_menu_id(basename($_SERVER['PHP_SELF']));
$db = new DatabaseConnect();

$totalRows_rscheck = 0;

if ((isset($_POST["POSTcheck"])) && ($_POST["POSTcheck"] == "form1")) {

    $SQLcrud = "UPDATE user SET username=?, `password`=?, designation=?, `contact_no`=?, `address`=? WHERE id=?";

    $db->query($SQLcrud);
    $db->bind(1, $_POST['username']);
    $db->bind(2, $phu->encryptMessage($_POST['password']));
    $db->bind(3, $_POST['designation']);
    $db->bind(4, $_POST['contact_no']);
    $db->bind(5, $_POST['address']);
    $db->bind(6, $_POST['id']);
    $db->execute();

    $allowed = array('jpg', 'png', 'jpeg');
    $filenames = "../images/user/" . $_POST['id'] . ".jpg";

    if (isset($_FILES['image-upload1']) && $_FILES['image-upload1']['error'] == 0) {

        $extension = pathinfo($_FILES['image-upload1']['name'], PATHINFO_EXTENSION);


        if (move_uploaded_file($_FILES['image-upload1']['tmp_name'], $filenames)) {
        }
    }


    $updateGoTo = "../admin/index.php";
    header(sprintf("Location: %s", $updateGoTo));
}

$query_rsdesignation = "SELECT * FROM designation ORDER BY `designation` ASC";
$db->query($query_rsdesignation);
$rsdesignation = $db->rowset();
$totalRows_rsdesignation = $db->rowcount();



$query_rsposition = "SELECT * FROM user WHERE id = ?";
$db->query($query_rsposition);
$db->bind(1, $_SESSION['AIT_MM_ID']);
$row_rs = $db->rowsingle();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="robots" content="noindex, nofollow">
    <meta content="" name="description">
    <meta content="" name="keywords">
    <title><?php echo $app_title; ?> </title>
</head>
<script type="text/javascript" language="javascript">
    function generatePassword() {
        const lowercase = 'abcdefghijklmnopqrstuvwxyz';
        const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const numbers = '0123456789';
        const symbols = '!@#$%^&*()_+-={}[]|\\:;"<>,.?/~`';

        const allChars = lowercase + uppercase + numbers + symbols;

        let password = '';
        password += getRandom(lowercase);
        password += getRandom(uppercase);
        password += getRandom(numbers);
        password += getRandom(symbols);

        for (let i = 0; i < 4; i++) {
            password += getRandom(allChars);
        }

        return password;
    }

    function getRandom(str) {
        return str[Math.floor(Math.random() * str.length)];
    }


    function validateForm() {

        var decimal = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,20}$/;

        var spw = document.forms["form1"]["password"].value;
        var srepw = document.forms["form1"]["ReType"].value;
        document.getElementById("pw_error").innerHTML = "";

        $("#ReType").attr('disabled', 'disabled');

        if (spw.length < 8) {
            document.getElementById("pw_error").innerHTML =
                "<i class='bi bi-exclamation-circle-fill text-danger'></i> Password should be 8 to 20 characters long!";
            return false;
        } else if (!spw.match(decimal)) {
            document.getElementById("pw_error").innerHTML =
                "<i class='bi bi-exclamation-circle-fill text-danger'></i> Please choose a stronger password. Password must contain at least one lowercase letter, one uppercase letter, one numeric digit, and one special character. Password must contain 8 to 20 characters.";
            return false;
        } else {
            document.getElementById("pw_error").innerHTML = "";

            $("#ReType").removeAttr('disabled');

            return true;
        }

        var x = document.forms["form1"]["results_here1"].value;
        if (x == null || x == "") {
            return true;
        } else {
            document.getElementById("results_here").innerHTML =
                "<i class='bi bi-exclamation-circle-fill text-danger'></i> Record NOT Save.";
            document.getElementById("username").focus();
            return false;
        }

    }

    function password_not_match() {
        var spw = document.forms["form1"]["password"].value;
        var srepw = document.forms["form1"]["ReType"].value;
        document.getElementById("rpw_error").innerHTML = "";

        if (spw != srepw) {
            document.getElementById("rpw_error").innerHTML =
                "<i class='bi bi-exclamation-circle-fill text-danger'></i> Password did NOT match!";
            return false;
        }
    }

    function clear_retype() {
        document.forms["form1"]["ReType"].value = "";
    }

    function disable_retype() {
        $("#ReType").attr('disabled', 'disabled');
    }
</script>
<?php require_once('../template/phplink.php'); ?>


<body onload="disable_retype();">
    <?php require_once('../template/header.php'); ?>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><strong><?php echo htmlentities($_SESSION['title']); ?></strong></h5>
        </div>
        <div class="card-body alert alert-info">
            <!--------------------------------------------------------------------------------->
            <form method="POST" name="form1" id="form1" onsubmit="return validateForm();" enctype="multipart/form-data">

                <div class="form-horizontal">

                    <div class="form-group" align="center">

                        <img id="image_preview1" src="../images/user/<?php echo $row_rs['id']; ?>.jpg?t=
                            <?php echo time(); ?>" class="img-fluid" width="400px" height="400px" />
                        <div class="mb-4">

                            <label for="image-upload1" class="form-label"></label>
                            <input class="form-control" type="file" id="image-upload1" name="image-upload1"
                                onchange="preview1()" accept="image/png, image/jpeg, image/jpg">

                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-8 col-sm-12">

                            <div class="form-floating">
                                <input required class="form-control" type="text" name="fullname"
                                    value="<?php echo $row_rs['fullname']; ?>" size="32" placeholder=" ">
                                <label>FullName</label>
                            </div>
                        </div>
                        <div class="form-group col-md-4 col-sm-12">

                            <div class="form-floating">
                                <select disabled name="designation" id="designation" class="form-select "
                                    placeholder=" ">
                                    <?php
                                    foreach ($rsdesignation as $row_rsdesignation) {
                                    ?>
                                        <option value="<?php echo $row_rsdesignation['designation'] ?>"
                                            <?php if (!(strcmp($row_rsdesignation['designation'], $row_rs['designation']))) {
                                                echo "selected=\"selected\"";
                                            } ?>>
                                            <?php echo htmlentities($row_rsdesignation['designation']); ?></option>
                                    <?php } ?>
                                </select>
                                <label>Designation</label>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group col-md-8 col-sm-12">
                            <div class="form-floating ">

                                <input required class="form-control" type="email" name="username" id="username"
                                    placeholder="Enter Email" value="<?php echo $row_rs['username']; ?>" size="32"
                                    placeholder=" "
                                    OnKeyUp="showAjax('user_duplicate.php','txtString',this.value + '&prev_id=<?php if (isset($_POST['prev_id'])) echo $_POST['prev_id'];
                                                                                                                else echo $row_rs['username']; ?>' , 'results_here');">
                                <label>Email/UserName*</label>
                            </div>
                            <span id="results_here" class="alert-danger"></span> <input type="hidden"
                                id="results_here1">
                        </div>

                        <div class="form-group col-md-4 col-sm-12">
                            <div class="form-floating ">
                                <input required type="text" class="form-control col-form-label" name="contact_no"
                                    id="contact_no" placeholder=" " value="<?php echo $row_rs['contact_no']; ?>">
                                <label>Contact No*</label>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group col-md-8 col-sm-12">
                            <div class="form-floating ">
                                <input type="text" class="form-control col-form-label" name="address" id="address"
                                    placeholder=" " value="<?php echo $row_rs['address']; ?>">
                                <label>Address</label>
                            </div>
                        </div>

                        <div class="form-floating col-md-4 col-sm-12">
                            <input readonly class="form-control" type="text" name="group" id="group"
                                value="<?php echo $row_rs['group']; ?>" size="32" placeholder=" ">
                            <label>&nbsp;&nbsp;&nbsp;User Group</label>
                        </div>


                    </div>

                    <br>
                    <div class="row">

                        <div class="form-group form-floating col-md-4 col-sm-12">


                            <input required name="password" type="password" class="form-control" id="password"
                                value="<?php echo $phu->decryptMessage($row_rs['password']); ?>" placeholder=" "
                                minlength="8" maxlength="20" onkeyup="clear_retype(); validateForm();"
                                onclick="clear_retype(); validateForm();">
                            <span id="pw_error" name="pw_error" class="text-danger"></span>


                            <label>&nbsp;&nbsp;&nbsp;Password*</label>
                            <div>
                                <input class="form-check-input" type="checkbox" id="gridCheck1"
                                    onclick="showhidepassword()"> <label class="form-check-label" for="gridCheck1">Show
                                    Password</label>
                            </div>
                        </div>



                        <div class="form-floating col-md-4 col-sm-12">

                            <input required class="form-control" type="password" name="ReType" id="ReType" value="<?php echo $phu->decryptMessage($row_rs['password']); ?>"
                                size="32" placeholder=" " onkeyup="password_not_match()"
                                onclick="password_not_match()" minlength="8" maxlength="20">
                            <label>&nbsp;&nbsp;&nbsp;Re-Type Password</label>
                            <span id="rpw_error" name="rpw_error" class="text-danger"></span>

                        </div>
                        
                    </div>


                    <br>

                  
                    <div class="form-group">
                        <label class="col-md-2 control-label" align="right"></label>
                        <div class="form-floating input-group">
                            <button type="submit" class="btn btn-outline-primary"><span class="bi-save"></span>
                                Save</button>
                            <a href="../admin/index.php" class="btn btn-outline-danger "><span
                                    class="bi-x-octagon"></span>
                                Cancel</a>
                        </div>
                    </div>



                    <input type="hidden" name="POSTcheck" value="form1">
                    <input type="hidden" name="id" value="<?php echo $row_rs['id']; ?>">
                    <input type="hidden" name="prev_id" id="prev_id"
                        value="<?php if (isset($_POST['prev_id'])) echo $_POST['prev_id'];
                                else echo $row_rs['username']; ?>">
            </form>
        </div>

        <!--------------------------------------------------------------------------------->
    </div>
    <div class="card-footer"></div>
    </div>

    <?php require_once('../template/footer.php'); ?>

    <script>
        function preview1() {
            image_preview1.src = URL.createObjectURL(event.target.files[0]);
        }

        function clearImage1() {
            document.getElementById('image-upload1').value = null;
            image_preview1.src = "";
        }

        function showhidepassword() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        $(document).ready(function() {
            $('#image-upload1').change(function() {
                var file = this.files[0];
                var fileType = file['type'];
                var fileSize = file['size'];
                var validImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if ($.inArray(fileType, validImageTypes) < 0) {
                    Swal.fire($app_title, 'Please select a JPEG or PNG file.', 'info');
                    clearImage1()
                    this.value = '';
                } else if (fileSize > 10485760) {
                    Swal.fire($app_title, 'File size exceeds 10MB. Please select a JPEG or PNG file NOT MORE THAN 10MB.',
                        'info');
                    clearImage1();
                    this.value = '';
                }
            });
        });
    </script>
</body>

</html>
<?php ob_flush();
$db->close();
?>