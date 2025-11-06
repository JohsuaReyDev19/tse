<?php ob_start(); ?>
<?php require_once('../connections/pdoconnect.php'); ?>

<?php
$phu = new php_util();
$menu_id = $phu->get_menu_id(basename($_SERVER['PHP_SELF']));

$db = new DatabaseConnect();

if ((isset($_POST["POSTcheck"])) && ($_POST["POSTcheck"] == "form1")) {

    $SQLcrud = "DELETE FROM participant WHERE `participant_id` = ?";
    $db->query($SQLcrud);
    $db->bind(1, $_POST['id']);
    $db->execute();

    unlink("../images/participants/" . $_POST['id'] . ".jpg");

    $GoTo = "participant_list.php?recordID=" . $_POST['events_id'];
    header(sprintf("Location: %s", $GoTo));
}


$query_rs = "SELECT * FROM `participant` WHERE `participant_id` = ?";
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

                        <div class="form-group">

                            <div class="form-group" align="center">
                                <img id="image_preview1" src="../images/participants/<?php echo $row_rs['participant_id']; ?>.jpg?t=<?php echo time(); ?>"
                                    class="img-fluid" width="400px" height="400px" />
                                <div class="mb-4">
                                    <label for="image-upload1" class="form-label"></label>
                                    <input class="form-control" type="file" id="image-upload1" name="image-upload1"
                                        onchange="preview1()" accept="image/png, image/jpeg, image/jpg">

                                </div>
                                </div>
                                <div class="form-group">
                                    <label>Name</label>
                                    <input readonly class="form-control" type="text" name="name" id="name"
                                        value="<?php echo $row_rs['name']; ?>" size="32"
                                        placeholder=" ">
                                </div>
                                <br>
                                <div class="form-group">

                                    <label>Description</label>
                                    <input readonly class="form-control" type="text" name="description" id="description"
                                        value="<?php echo $row_rs['description']; ?>" size="32"
                                        placeholder=" ">
                                </div>
                                <br>
                                <div class="form-group">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-10">
                                        Are you sure you want to Remove this Record? &nbsp;<button type="submit" class="btn btn-outline-danger" form="form1"><span class="bi-trash"></span> Yes</button>
                                        <a href="participant_list.php?recordID=<?php echo $row_rs['events_id']; ?>" class="btn btn-outline-primary hidelink"><span class="bi-x-octagon"></span> No</a>
                                    </div>
                                </div>

                    </fieldset>
                </div>
                <input type="hidden" name="POSTcheck" value="form1">
                <input type="hidden" name="events_id" value="<?php echo $row_rs['events_id']; ?>">
                <input type="hidden" name="id" value="<?php echo $row_rs['participant_id']; ?>">
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