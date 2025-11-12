<?php ob_start(); ?>
<?php require_once('../connections/pdoconnect.php'); ?>

<?php
if (!isset($_SESSION)) {
    session_start();
}
$phu = new php_util();
$menu_id = $phu->get_menu_id(basename($_SERVER['PHP_SELF']));
$db = new DatabaseConnect();

$totalRows_rscheck = 0;

if ((isset($_POST["POSTcheck"])) && ($_POST["POSTcheck"] == "form1")) {

if ($_POST['group']=="Administrator"){
    $SQLcrud = "INSERT INTO user (fullname, username, password, designation, `group`, `status`,`contact_no`, `address`) VALUES (?,?,?,?,?,?,?,?)";
    $db->query($SQLcrud);
    $db->bind(1, htmlentities($_POST['fullname']));
    $db->bind(2, htmlentities($_POST['username']));
    $db->bind(3, htmlentities($phu->encryptMessage($_POST['password'])));
    $db->bind(4, htmlentities($_POST['designation']));
    $db->bind(5, htmlentities($_POST['group']));
    $db->bind(6, htmlentities($_POST['status']));
    $db->bind(7, htmlentities($_POST['contact_no']));
    $db->bind(8, htmlentities($_POST['address']));
    $db->execute();

    $id_pict=$db->lastinsertid();
    //mhash(MHASH_MD5, $data, "MySecret"); 

    
}else if ($_POST['group']=="Judge"){
    $SQLcrud = "INSERT INTO judge (`name`, events_id) VALUES (?, ?)";
    $db->query($SQLcrud);
    $db->bind(1,$_POST['fullname']);
    $db->bind(2,$_POST['events_id']);
    $db->execute();
    $id=$db->lastinsertid();

    $SQLcrud = "INSERT INTO user (fullname, username, password, designation, `group`, `status`,`contact_no`, `address`, judge_id) VALUES (?,?,?,?,?,?,?,?, ?)";
    $db->query($SQLcrud);
    $db->bind(1, htmlentities($_POST['fullname']));
    $db->bind(2, htmlentities($_POST['username']));
    $db->bind(3, htmlentities($phu->encryptMessage($_POST['password'])));
    $db->bind(4, htmlentities($_POST['designation']));
    $db->bind(5, htmlentities($_POST['group']));
    $db->bind(6, htmlentities($_POST['status']));
    $db->bind(7, htmlentities($_POST['contact_no']));
    $db->bind(8, htmlentities($_POST['address']));
    $db->bind(9,$id );
    $db->execute();
    $id_pict=$db->lastinsertid();
   
}

    $allowed = array('jpg', 'png', 'jpeg');
    $filenames = "../images/user/" . $id_pict . ".jpg";

    if (isset($_FILES['image-upload1']) && $_FILES['image-upload1']['error'] == 0) {
        $extension = pathinfo($_FILES['image-upload1']['name'], PATHINFO_EXTENSION);
        if (move_uploaded_file($_FILES['image-upload1']['tmp_name'], $filenames)) {
        }
    }

    $insertGoTo = "user_list.php";
    header(sprintf("Location: %s", $insertGoTo));
}

$query_rsdesignation = "SELECT * FROM designation ORDER BY `designation` ASC";
$db->query($query_rsdesignation);
$rsdesignation = $db->rowset();
$totalRows_rsdesignation = $db->rowcount();


$query_rsgroup = "SELECT * FROM user_group ORDER BY `group` ASC";
$db->query($query_rsgroup);
$rsgroup = $db->rowset();
$totalRows_rsgroup = $db->rowcount();

$query_rs = "select * FROM `campus` order by `campus`";
$db->query($query_rs);
$rscampus = $db->rowset();

$query_rs = "select * FROM `events` WHERE events_date>=CURRENT_DATE() order by events_date ASC, `events_description` ASC";
$db->query($query_rs);
$rsevents=$db->rowset();
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


    function create_fullname() {
        form1.fullname.value = form1.title.value + ' ' + form1.firstname.value + ' ' + form1.middlename.value + ' ' + form1
            .lastname.value + ' ' + form1.extname.value;
    }

    function calculateAge() { // birthday is a date

        var dateString = form1.dob.value;
        var now = new Date();
        var today = new Date(now.getYear(), now.getMonth(), now.getDate());

        var yearNow = now.getYear();
        var monthNow = now.getMonth();
        var dateNow = now.getDate();

        var dob = new Date(dateString.substring(0, 4),
            dateString.substring(5, 7),
            dateString.substring(8, 10)
        );

        var yearDob = dob.getYear();
        var monthDob = dob.getMonth();
        var dateDob = dob.getDate();
        var age = {};
        var ageString = "";
        var yearString = "";
        var monthString = "";
        var dayString = "";


        yearAge = yearNow - yearDob;

        if (monthNow >= monthDob)
            var monthAge = monthNow - monthDob;
        else {
            yearAge--;
            var monthAge = 12 + monthNow - monthDob;
        }

        if (dateNow >= dateDob)
            var dateAge = dateNow - dateDob;
        else {
            monthAge--;
            var dateAge = 31 + dateNow - dateDob;

            if (monthAge < 0) {
                monthAge = 11;
                yearAge--;
            }
        }

        age = {
            years: yearAge,
            months: monthAge,
            days: dateAge
        };

        if (age.years > 1) yearString = " years";
        else yearString = " year";
        if (age.months > 1) monthString = " months";
        else monthString = " month";
        if (age.days > 1) dayString = " days";
        else dayString = " day";


        if ((age.years > 0) && (age.months > 0) && (age.days > 0))
            ageString = age.years + yearString + ", " + age.months + monthString + ", and " + age.days + dayString +
            " old.";
        else if ((age.years == 0) && (age.months == 0) && (age.days > 0))
            ageString = "Only " + age.days + dayString + " old!";
        else if ((age.years > 0) && (age.months == 0) && (age.days == 0))
            ageString = age.years + yearString + " old. Happy Birthday!!";
        else if ((age.years > 0) && (age.months > 0) && (age.days == 0))
            ageString = age.years + yearString + " and " + age.months + monthString + " old.";
        else if ((age.years == 0) && (age.months > 0) && (age.days > 0))
            ageString = age.months + monthString + " and " + age.days + dayString + " old.";
        else if ((age.years > 0) && (age.months == 0) && (age.days > 0))
            ageString = age.years + yearString + " and " + age.days + dayString + " old.";
        else if ((age.years == 0) && (age.months > 0) && (age.days == 0))
            ageString = age.months + monthString + " old.";
        else ageString = "Oops! Could not calculate age!";

        form1.age.value = ageString;

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
                    <fieldset>


                        <div class="form-group" align="center">
                            <img id="image_preview1" src="" class="img-fluid" width="400px" height="400px" />
                            <div class="mb-4">
                                <label for="image-upload1" class="form-label"></label>
                                <input class="form-control" type="file" id="image-upload1" name="image-upload1"
                                    onchange="preview1()" accept="image/png, image/jpeg, image/jpg">

                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-8 col-sm-12">
                                <label><strong>FullName</strong></label><input required type="text" class="form-control"
                                    name="fullname" id="fullname" placeholder=" " value="">

                            </div>

                            <div class="form-group  col-md-4 col-sm-12">
                                <label for="designation"><strong>Designation</strong></label>
                                <select name="designation" id="designation" class="form-select" placeholder=" ">
                                    <?php
                                    foreach ($rsdesignation as $row_rsdesignation) {
                                    ?>
                                        <option value="<?php echo $row_rsdesignation['designation'] ?>">
                                            <?php echo htmlentities($row_rsdesignation['designation']); ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>


                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group col-md-8 col-sm-12">
                                <label><strong>Address</strong></label>
                                <input type="text" class="form-control col-form-label" name="address" id="address"
                                    placeholder=" ">

                            </div>

                            <div class="form-group col-md-4 col-sm-12">
                                <label><strong>Contact No*</strong></label>
                                <input required type="text" class="form-control col-form-label" name="contact_no" id="contact_no"
                                    placeholder=" ">

                            </div>
                        </div>
                        <br>
                        <div class="row">

                            <div class="form-group  col-md-4 col-sm-12">
                                <label for="username"><strong>Email/UserName*</strong></label>

                                <input required type="email" class="form-control col-form-label" name="username"
                                    id="username" placeholder=" "
                                    OnKeyUp="showAjax('user_duplicate.php','txtString',this.value, 'results_here');"
                                    Onblur="showAjax('user_duplicate.php','txtString',this.value, 'results_here');">
                                <span id="results_here" class="text-danger"></span> <input type="hidden"
                                    id="results_here1">
                            </div>

                            <div class="form-group  col-md-4 col-sm-12">
                                <label><strong>Password*</strong></label>
                                <input required type="password" class="form-control col-form-label" name="password"
                                    id="password" placeholder="" minlength="8" maxlength="20" onkeyup="clear_retype(); validateForm();" onclick="clear_retype(); validateForm();">
                                <input class="form-check-input" type="checkbox" id="gridCheck1"
                                    onclick="showhidepassword()"> <label class="form-check-label"
                                    for="gridCheck1">Show
                                    Password</label><br>
                                <span id="pw_error" name="pw_error" class="text-danger text-badge"></span>

                                <br>

                            </div>
                            <div class="form-group  col-md-4 col-sm-12">
                                <label><strong>Re-Type Password*</strong></label>
                                <input required type="password" class="form-control col-form-label" name="ReType"
                                    id="ReType" placeholder=" " onkeyup="password_not_match()" onclick="password_not_match()" minlength="8" maxlength="20">

                                <span id="rpw_error" name="rpw_error" class="text-danger"></span>
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="form-group col-md-4 col-sm-12">
                                <!-- Text input-->

                                <label><strong>User Group</strong></label>
                                <select id="group" name="group" class="form-select" placeholder=" ">
                                    <?php
                                    foreach ($rsgroup as $row_rsgroup) {
                                    ?>
                                        <option value="<?php echo $row_rsgroup['group'] ?>"
                                            <?php if (isset($_POST['group']) && !(strcmp($row_rsgroup['group'], $_POST['group']))) {
                                                echo "selected=\"selected\"";
                                            } ?>>
                                            <?php echo htmlentities($row_rsgroup['group']); ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>


                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                                <!-- Text input-->

                                <label><strong>Status</strong></label>
                                <select name="status" id="status" class="form-select" placeholder="">

                                    <option value="active">active</option>
                                    <option value="disabled">disabled</option>

                                </select>

                            </div>

                            <div id="events" class="form-group col-md-4 col-sm-12" style="display:none;">
                                <!-- Text input-->

                                <label><strong>Events</strong></label>
                                <select id="events_id" name="events_id" class="form-select" placeholder=" ">
                                    <!-- Default placeholder option -->
                                    <option value="none">-- Select Event --</option>
                                    
                                    <?php
                                    foreach ($rsevents as $row_rsevents) {
                                    ?>
                                        <option value="<?php echo $row_rsevents['events_id']; ?>"
                                            <?php 
                                            if (isset($_POST['events_id']) && $row_rsevents['events_id'] == $_POST['events_id']) {
                                                echo "selected";
                                            } 
                                            ?>>
                                            <?php echo htmlentities($row_rsevents['events_description']); ?>
                                        </option>
                                    <?php
                                    }
                                    ?>
                                </select>


                            </div>
                        </div>
                        <br>
                        
                        <div class="form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-10">
                                <button type="submit" class="btn btn-outline-primary"><span class="bi-save"></span>
                                    Save</button>
                                <a href="user_list.php" class="btn btn-outline-danger hidelink"><span
                                        class="bi-x-octagon"></span> Cancel</a>
                            </div>
                        </div>

                    </fieldset>
                </div>

                <input type="hidden" name="POSTcheck" value="form1">
            </form>
            <script>
                function preview1() {
                    image_preview1.src = URL.createObjectURL(event.target.files[0]);
                }

                function clearImage1() {
                    document.getElementById('image-upload1').value = null;
                    image_preview1.src = "";
                }
            </script>

            <script>
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
                            clearImage1();
                            this.value = '';
                        } else if (fileSize > 10485760) {
                            Swal.fire($app_title,
                                'File size exceeds 10MB. Please select a JPEG or PNG file NOT MORE THAN 10MB.',
                                'info');
                            clearImage1();
                            this.value = '';
                        }
                    });
                });
            </script>

            <!--------------------------------------------------------------------------------->
        </div>
        <div class="card-footer"></div>
    </div>

    <?php require_once('../template/footer.php'); ?>


</body>
<!-- <script>
  $('#group').on('change', function() {
    const selectedGroup = $(this).val();

    if (selectedGroup === 'Judge') {
      $('#events').show();
      $('#events_id').prop('required', true);
    } else {
      $('#events').hide();
      $('#events_id').prop('required', false);
    }

  });
</script> -->

</html>
<?php ob_flush();
$db->close();
?>