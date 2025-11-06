<?php ob_start(); ?><?php require_once('../connections/pdoconnect.php'); ?>
<?php
$phu = new php_util();
$menu_id = $phu->get_menu_id(basename($_SERVER['PHP_SELF']));
$db = new DatabaseConnect();

if ((isset($_POST["POSTcheck"])) && ($_POST["POSTcheck"] == "form1")) {
  date_default_timezone_set('Asia/Manila');
  $submittedDate = strtotime($_POST['events_date']);
  $currentDate = strtotime(date('Y-m-d'));


  $events_status = "";
  if ($submittedDate >= $currentDate) {
    $events_status = "active";
  } else {
    $events_status = "disabled";
  }
  $SQLcrud = "UPDATE `events` SET `events_description`=?,events_date=?, events_status=? WHERE `events_id`=?";

  $db->query($SQLcrud);
  $db->bind(1, $_POST['events_description']);
  $db->bind(2, $_POST['events_date']);
  $db->bind(3, $events_status);
  $db->bind(4, $_POST['id']);
  $db->execute();

  $allowed = array('jpg');
  $filenames = "../images/events/" . $_POST['id'] . ".jpg";

  if (isset($_FILES['image-upload1']) && $_FILES['image-upload1']['error'] == 0) {
    $extension = pathinfo($_FILES['image-upload1']['name'], PATHINFO_EXTENSION);
    if (move_uploaded_file($_FILES['image-upload1']['tmp_name'], $filenames)) {
    }
  }

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

  <title><?php echo $app_title; ?> </title>
</head>

<script type="text/javascript" language="javascript">
  function validateForm() {
    var x = document.forms["form1"]["results_here1"].value;
    if (x == null || x == "") {
      return true;
    } else {
      document.getElementById("results_here").innerHTML = "Record NOT Save. Duplicate Record Found!";
      document.getElementById("events_description").focus();
      return false;
    }
  }
</script>
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
      <form method="POST" id="form1" name="form1" onsubmit="return validateForm();" enctype="multipart/form-data">

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
                <span id="results_here" class="alert-danger"></span><input type="hidden" id="results_here1">


                <label>Event</label>
                <input required class="form-control" type="text" name="events_description" id="events_description"
                  value="<?php echo $row_rs['events_description']; ?>" size="32"
                  OnKeyUp="showAjax('events_duplicate.php','txtString',this.value + '&prev_id=<?php echo $row_rs['events_description']; ?>' , 'results_here');" placeholder=" ">
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
                <button type="submit" class="btn btn-outline-primary" form="form1"><span class="bi-save"></span> Save</button>
                <a href="events_list.php" class="btn btn-outline-danger"><span class="bi-x-octagon"></span> Cancel</a>
              </div>
            </div>

          </fieldset>
        </div>

        <input type="hidden" name="POSTcheck" value="form1">
        <input type="hidden" name="prev_id" id="prev_id" value="<?php echo $row_rs['events_description']; ?>">
        <input type="hidden" name="id" value="<?php echo $row_rs['events_id']; ?>">

      </form>

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
            var validImageTypes = ['image/jpg'];
            if ($.inArray(fileType, validImageTypes) < 0) {
              Swal.fire(<?php echo $app_title;?>, 'Please select a JPG file.', 'info');
              clearImage1();
              this.value = '';
            } else if (fileSize > 10485760) {
              Swal.fire(<?php echo $app_title;?>,
                'File size exceeds 10MB. Please select a JPG file NOT MORE THAN 10MB.',
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

</html>
<?php ob_flush();
$db->close();
?>