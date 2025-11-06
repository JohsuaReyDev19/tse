<?php ob_start(); ?>
<?php require_once('../connections/pdoconnect.php'); ?>

<?php

if (!isset($_SESSION)) {
    session_start();
}

$phu = new php_util();
$menu_id = $phu->get_menu_id(basename($_SERVER['PHP_SELF']));
$db = new DatabaseConnect();

if ((isset($_POST["POSTcheck"])) && ($_POST["POSTcheck"] == "form1")) {

    $SQLcrud = "DELETE FROM user WHERE id = ?";
    $db->query($SQLcrud);
    $db->bind(1, $_POST['id']);
    $db->execute();

    unlink("../images/user/" . $_POST['id'] . ".jpg");

    $GoTo = "user_list.php";
    header(sprintf("Location: %s", $GoTo));
}

$recordID = "-1";
if (isset($_GET['recordID'])) {
    $recordID = $_GET['recordID'];
}
$query_rs = "SELECT * FROM `user` u WHERE u.id = ?";
$db->query($query_rs);
$db->bind(1, $recordID);
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
<?php require_once('../template/phplink.php'); ?>


<body>
    <?php require_once('../template/header.php'); ?>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><strong><?php echo htmlentities($_SESSION['title']); ?></strong></h5>
        </div>
        <div class="card-body alert alert-info">
            <!--------------------------------------------------------------------------------->
            <form method="post" name="form1" id="form1">
                <div class="form-horizontal">
                    <fieldset>

                        <div class="form-group" align="center">
                            <img id="image_preview1" src="../images/user/<?php echo $row_rs['id']; ?>.jpg?t=<?php echo time(); ?>"
                                class="img-fluid" width="400px" height="400px" />
                            <div class="mb-4">
                                <label for="image-upload1" class="form-label"></label>
                                <input class="form-control" type="file" id="image-upload1" name="image-upload1"
                                    onchange="preview1()" accept="image/png, image/jpeg, image/jpg">

                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-8 col-sm-12">

                                <label for="fullname"><strong>FullName</strong></label>
                                <input readonly class="form-control" type="text" name="fullname"
                                    value="<?php echo $row_rs['fullname']; ?>" size="32" placeholder=" ">


                            </div>

                            <div class="form-group col-md-4 col-sm-12">

                                <label><strong>Designation</strong></label>
                                <input readonly name="designation" type="designation" class="form-control" id="password"
                                    value="<?php echo $row_rs['designation']; ?>" placeholder=" ">


                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group  col-md-8 col-sm-12">

                                <label><strong>Email/UserName</strong></label>
                                <input readonly class="form-control" type="email" name="username" id="username"
                                    value="<?php echo $row_rs['username']; ?>" size="32" placeholder=" ">


                            </div>
                            <div class="form-group  col-md-4 col-sm-12">
                                <label><strong>Contact No</strong></label>
                                <input readonly type="text" class="form-control col-form-label" name="contact_no"
                                    id="contact_no" placeholder=" " value="<?php echo $row_rs['contact_no']; ?>">


                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group col-md-6 col-sm-12">
                                <label><strong>Address</strong></label>
                                <input readonly type="text" class="form-control col-form-label" name="address"
                                    id="address" placeholder=" " value="<?php echo $row_rs['address']; ?>">


                            </div>


                            <div class="form-group  col-md-3 col-sm-12">

                                <label><strong>Group</strong></label>
                                <input readonly name="group" type="group" class="form-control" id="password"
                                    value="<?php echo $row_rs['group'] ?>" placeholder=" ">


                            </div>

                            <div class="form-group  col-md-3 col-sm-12">

                                <label><strong>Status</strong></label>
                                <input readonly name="status" type="status" class="form-control" id="password"
                                    value="<?php echo $row_rs['status'] ?>" placeholder=" ">


                            </div>
                           
                        </div>
                        <br>
                        
                        <div class="form-group">
                            <label class="col-md-2 control-label" align="right"></label>
                            <div class="form-floating input-group">

                                Are you sure you want to Remove this Record? &nbsp;<button type="submit"
                                    class="btn btn-outline-danger" form="form1"><span class="bi-trash"></span>
                                    Yes</button>
                                <a href="user_list.php" class="btn btn-outline-primary hidelink"><span
                                        class="bi-x-octagon"></span> No</a>
                            </div>
                        </div>
                        <input type="hidden" name="POSTcheck" value="form1">
                        <input type="hidden" name="id" id="id" value="<?php echo htmlentities($row_rs['id']); ?>">
                    </fieldset>
                </div>
            </form>

            <!--------------------------------------------------------------------------------->
        </div>
        <div class="card-footer"></div>
    </div>
    <?php require_once('../template/footer.php'); ?>

</body>

</html>
<?php ob_flush();
$db->close();
?>