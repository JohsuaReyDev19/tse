<?php ob_start(); ?><?php require_once('../connections/pdoconnect.php'); ?>
<?php
$phu = new php_util();
$menu_id = $phu->get_menu_id(basename($_SERVER['PHP_SELF']));
$db = new DatabaseConnect();

if ((isset($_POST["POSTcheck"])) && ($_POST["POSTcheck"] == "form1")) {

  $SQLcrud = "UPDATE `judge` SET `name`=? WHERE `judge_id`=?";

  $db->query($SQLcrud);
  $db->bind(1, $_POST['name']);
  $db->bind(2, $_POST['id']);
  $db->execute();

  $SQLcrud = "UPDATE `user` SET `fullname`=? WHERE `judge_id`=?";

  $db->query($SQLcrud);
  $db->bind(1, $_POST['name']);
  $db->bind(2, $_POST['id']);
  $db->execute();

  $GoTo = "judge_list.php?recordID=" . $_POST['events_id'];
  header(sprintf("Location: %s", $GoTo));
}

$query_rs = "SELECT * FROM `judge` WHERE `judge_id` = ?";
$db->query($query_rs);
$db->bind(1, $_GET['recordID']);
$row_rs = $db->rowsingle();

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
      <!--------------------------------------------------------------------------------->
      <form method="POST" id="form1" name="form1" enctype="multipart/form-data">

        <div class="form-horizontal">
          <fieldset>

            <div class="form-group">

              <label>Name</label>
              <input required class="form-control" type="text" name="name" id="name"
                value="<?php echo $row_rs['name']; ?>" size="32"
                placeholder=" ">
            </div>
            <br>
            <div class="form-group">
              <div class="col-md-2"></div>
              <div class="col-md-10">
                <button type="submit" class="btn btn-outline-primary" form="form1"><span class="bi-save"></span> Save</button>
                <a href="judge_list.php?recordID=<?php echo $row_rs['events_id']; ?>" class="btn btn-outline-danger"><span class="bi-x-octagon"></span> Cancel</a>
              </div>
            </div>

          </fieldset>
        </div>

        <input type="hidden" name="POSTcheck" value="form1">
        <input type="hidden" name="events_id" value="<?php echo $row_rs['events_id']; ?>">
        <input type="hidden" name="id" value="<?php echo $row_rs['judge_id']; ?>">

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