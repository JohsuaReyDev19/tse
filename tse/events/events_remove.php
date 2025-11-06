<?php ob_start(); ?>
<?php require_once('../connections/pdoconnect.php'); ?>

<?php
$phu = new php_util();
$menu_id = $phu->get_menu_id(basename($_SERVER['PHP_SELF']));

$db = new DatabaseConnect();

if ((isset($_POST["POSTcheck"])) && ($_POST["POSTcheck"] == "form1")) {

    $SQLcrud = "DELETE FROM events WHERE `events_id` = ?";
    $db->query($SQLcrud);
    $db->bind(1, $_POST['id']);
    $db->execute();

    unlink("../images/events/" . $_POST['id'] . ".jpg");


    $GoTo = "events_list.php";
    header(sprintf("Location: %s", $GoTo));
}

$query_rs = "SELECT * FROM `events` WHERE `events_id` = ?";
$db->query($query_rs);
$db->bind(1, $_GET['recordID']);
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
        <div class="card-body">
            <!--------------------------------------------------------------------------------->
            <form id="form1" name="form1" method="post" enctype="multipart/form-data">
                <div class="form-horizontal">

                    <fieldset>

                        <div class="form-group" align="center">
                            <img id="image_preview1" src="../images/events/<?php echo $row_rs['events_id']; ?>.jpg?t=
            <?php echo time(); ?>" class="img-fluid" width="400px" height="400px" />
                            <div class="mb-4">
                                <label for="image-upload1" class="form-label">File format : JPG Only</label>
                                <input class="form-control" type="file" id="image-upload1" name="image-upload1"
                                    onchange="preview1()" accept="image/jpg">

                            </div>
                        </div>
                        <div class="row">

                            <div class="form-group  col-md-8 col-sm-12">

                                <label>Event</label>
                                <input required class="form-control" type="text" name="events_description" id="events_description"
                                    value="<?php echo $row_rs['events_description']; ?>" size="32"
                                    placeholder=" ">
                            </div>

                            <div class="form-group col-md-4 col-sm-12">
                                <label for="events_date">Date</label>
                                <input required type="date" min="<?php echo date('Y-m-d'); ?>" required class="form-control" name="events_date" id="events_date" placeholder=" " value="<?php echo $row_rs['events_date']; ?>">

                            </div>

                        </div>

                        <br>
                        <div class="form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-10">
                                Are you sure you want to Remove this Record? &nbsp;<button type="submit" class="btn btn-outline-danger" form="form1"><span class="bi-trash"></span> Yes</button>
                                <a href="events_list.php" class="btn btn-outline-primary hidelink"><span class="bi-x-octagon"></span> No</a>
                            </div>
                        </div>

                    </fieldset>
                </div>
                <input type="hidden" name="POSTcheck" value="form1">
                <input type="hidden" name="id" id="id" value="<?php echo htmlentities($row_rs['events_id']); ?>">
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