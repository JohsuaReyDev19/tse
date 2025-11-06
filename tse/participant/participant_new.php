<?php ob_start(); ?>
<?php require_once('../connections/pdoconnect.php'); ?>

<?php
$phu = new php_util();
$menu_id = $phu->get_menu_id(basename($_SERVER['PHP_SELF']));

$db = new DatabaseConnect();


if ((isset($_POST["POSTcheck"])) && ($_POST["POSTcheck"] == "form1")) {

  $SQLcrud = "INSERT INTO participant (`name`, events_id, `description`) VALUES (?, ?, ?)";
  $db->query($SQLcrud);
  $db->bind(1, $_POST['name']);
  $db->bind(2, $_POST['events_id']);
  $db->bind(3, $_POST['description']);
  $db->execute();

  $id_pict = $db->lastinsertid();

  $allowed = array('jpg', 'png', 'jpeg');
  $filenames = "../images/participants/" . $id_pict . ".jpg";

  if (isset($_FILES['image-upload1']) && $_FILES['image-upload1']['error'] == 0) {
    $extension = pathinfo($_FILES['image-upload1']['name'], PATHINFO_EXTENSION);
    if (move_uploaded_file($_FILES['image-upload1']['tmp_name'], $filenames)) {
    }
  }


  $GoTo = "participant_list.php?recordID=" . $_POST['events_id'];
  header(sprintf("Location: %s", $GoTo));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

  <title><?php echo $app_title; ?> </title>

</head>


<?php require_once('../template/phplink.php'); ?>
<!--
<script type="text/javascript">
 $(document).ready(function () {
  $('#Date').datepicker({format: "yyyy-mm-dd",autoclose:true}); /*input ID*/

});
</script>
-->

<body>
  <?php require_once('../template/header.php'); ?>
  <div class="card">
    <div class="card-header">
      <h5 class="card-title"><strong><?php echo htmlentities($_SESSION['title']); ?></strong></h5>
    </div>
    <div class="card-body">
      <!--------------------------------------------------------------------------------->
      <form method="post" name="form1" id="form1" enctype="multipart/form-data">

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

            <div class="form-group">
              <label for="">Name</label>
              <input required type="text" class="form-control" name="name" id="name" placeholder=" " value="<?php if (isset($_POST['name'])) echo $_POST['name']; ?>">
            </div>
            <br>
             <div class="form-group">
              <label for="">Description</label>
              <input required type="text" class="form-control" name="description" id="description" placeholder=" " value="<?php if (isset($_POST['description'])) echo $_POST['description']; ?>">
            </div>

            <br>
            <div class="form-group">
              <div class="col-md-2"></div>
              <div class="col-md-10">
                <button type="submit" class="btn btn-outline-primary" form="form1"><span class="bi-save"></span> Save</button>
                <a href="participant_list.php?recordID=<?php echo $_GET['recordID']; ?>" class="btn btn-outline-danger hidelink"><span class="bi-x-octagon"></span> Cancel</a>
              </div>
            </div>

          </fieldset>
        </div>
        <input type="hidden" name="POSTcheck" value="form1">
        <input type="hidden" name="events_id" value="<?php echo $_GET['recordID']; ?>">
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
</html>
<?php
$db->close();
?>