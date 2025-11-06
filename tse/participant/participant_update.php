<?php ob_start(); ?><?php require_once('../connections/pdoconnect.php'); ?>
<?php
$phu = new php_util();
$menu_id = $phu->get_menu_id(basename($_SERVER['PHP_SELF']));
$db = new DatabaseConnect();

if ((isset($_POST["POSTcheck"])) && ($_POST["POSTcheck"] == "form1")) {

  $SQLcrud = "UPDATE `participant` SET `name`=?, `description`=? WHERE `participant_id`=?";

  $db->query($SQLcrud);
  $db->bind(1, $_POST['name']);
  $db->bind(2, $_POST['description']);
  $db->bind(3, $_POST['id']);
  $db->execute();

  $allowed = array('jpg', 'png', 'jpeg');
  $filenames = "../images/participants/" . $_POST['id'] . ".jpg";

  if (isset($_FILES['image-upload1']) && $_FILES['image-upload1']['error'] == 0) {

    $extension = pathinfo($_FILES['image-upload1']['name'], PATHINFO_EXTENSION);

    if (move_uploaded_file($_FILES['image-upload1']['tmp_name'], $filenames)) {
    }
  }

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

            <div class="form-group" align="center">
              <img id="image_preview1" src="../images/participants/<?php echo $row_rs['participant_id']; ?>.jpg?t=
                                <?php echo time(); ?>" class="img-fluid" width="400px" height="400px" />
              <div class="mb-4">
                <label for="image-upload1" class="form-label"></label>
                <input class="form-control" type="file" id="image-upload1" name="image-upload1"
                  onchange="preview1()" accept="image/png, image/jpeg, image/jpg">

              </div>
            </div>

            <div class="form-group">

              <label>Name</label>
              <input required class="form-control" type="text" name="name" id="name"
                value="<?php echo $row_rs['name']; ?>" size="32"
                placeholder=" ">
            </div>
            <br>
            <div class="form-group">

              <label>Description</label>
              <input required class="form-control" type="text" name="description" id="description"
                value="<?php echo $row_rs['description']; ?>" size="32"
                placeholder=" ">
            </div>
            <br>
            <div class="form-group">
              <div class="col-md-2"></div>
              <div class="col-md-10">
                <button type="submit" class="btn btn-outline-primary" form="form1"><span class="bi-save"></span> Save</button>
                <a href="participant_list.php?recordID=<?php echo $row_rs['events_id']; ?>" class="btn btn-outline-danger"><span class="bi-x-octagon"></span> Cancel</a>
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
<script>
  function preview1() {
    image_preview1.src = URL.createObjectURL(event.target.files[0]);
  }

  function clearImage1() {
    document.getElementById('image-upload1').value = null;
    image_preview1.src = "";
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
        Swal.fire($app_title,
          'File size exceeds 10MB. Please select a JPEG or PNG file NOT MORE THAN 10MB.',
          'info');
        clearImage1();
        this.value = '';
      }
    });
  });
</script>

</html>
<?php ob_flush();
$db->close();
?>