<?php ob_start(); ?>
<?php require_once('../connections/pdoconnect.php'); ?>

<?php
$phu = new php_util();
$menu_id = $phu->get_menu_id(basename($_SERVER['PHP_SELF']));

$db = new DatabaseConnect();
$message = ''; // For error messages

if ((isset($_POST["POSTcheck"])) && ($_POST["POSTcheck"] == "form1")) {

    $events_id = $_POST['events_id'];
    $description = trim($_POST['description']);
    $percent = (float)$_POST['percent'];

    // Get remaining percent again (server-side)
    $query_rs = "SELECT 100 - SUM(percent) AS total FROM `category` WHERE events_id=?";
    $db->query($query_rs);
    $db->bind(1, $events_id);
    $rs_category = $db->rowsingle();
    $total_percent = $rs_category['total'] ?? 100;

    // Check percent > 0
    if ($percent <= 0) {
        $message = "<div class='alert alert-warning'>Percent must be greater than 0. Record not saved.</div>";
    }
    // Check if input exceeds remaining percent
    else if ($percent > $total_percent) {
        $message = "<div class='alert alert-warning'>
                        Percent exceeds the remaining allowed ($total_percent%). Record not saved.
                    </div>";
    }
    else {
        // Check if description already exists for this event
        $checkSQL = "SELECT COUNT(*) as count FROM category WHERE events_id=? AND description=?";
        $db->query($checkSQL);
        $db->bind(1, $events_id);
        $db->bind(2, $description);
        $checkResult = $db->rowsingle();

        if ($checkResult['count'] > 0) {
            $message = "<div class='alert alert-warning'>Category description already exists for this event. Record not saved.</div>";
        } else {
            // Insert new category
            $SQLcrud = "INSERT INTO category (events_id, `description`, percent) VALUES (?, ?, ?)";
            $db->query($SQLcrud);
            $db->bind(1, $events_id);
            $db->bind(2, $description);
            $db->bind(3, $percent);
            $db->execute();

            // Redirect
            $GoTo = "category_list.php?recordID=" . $events_id;
            header("Location: $GoTo");
            exit;
        }
    }
}


// Get total remaining percent
$query_rs = "SELECT 100 - SUM(percent) AS total FROM `category` WHERE events_id=?";
$db->query($query_rs);
$db->bind(1,$_GET['recordID']);
$rs_category=$db->rowsingle();
$rs_category_total=$db->rowcount();

$total_percent="0";
if ($rs_category_total>0){
    $total_percent=$rs_category['total'];
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
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

        <!-- Show validation messages -->
        <?php
        if ($message != '') {
            echo $message;
        }
        ?>

        <form method="post" name="form1" id="form1" enctype="multipart/form-data">
            <div class="form-horizontal">
                <fieldset>
                    <div class="row">
                        <div class="form-group col-md-8 col-sm-12">
                            <label for="description">Category</label>
                            <input required type="text" class="form-control" name="description" id="description" placeholder=" " value="">
                        </div>
                        <div class="form-group col-md-4 col-sm-12">
                            <label for="percent">Percent</label>
                            <input required type="number" class="form-control" name="percent" id="percent" min="0" max="<?php echo $total_percent; ?>" placeholder=" " value="<?php echo $total_percent; ?>">
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <div class="col-md-2"></div>
                        <div class="col-md-10">
                            <button type="submit" class="btn btn-outline-primary" form="form1"><span class="bi-save"></span> Save</button>
                            <a href="category_list.php?recordID=<?php echo $_GET['recordID']; ?>" class="btn btn-outline-danger hidelink"><span class="bi-x-octagon"></span> Cancel</a>
                        </div>
                    </div>
                </fieldset>
            </div>
            <input type="hidden" name="POSTcheck" value="form1">
            <input type="hidden" name="events_id" value="<?php echo $_GET['recordID']; ?>">
        </form>

    </div>
    <div class="card-footer"></div>
</div>
<?php require_once('../template/footer.php'); ?>

</body>
</html>
<?php
$db->close();
?>
