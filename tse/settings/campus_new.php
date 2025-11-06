<?php ob_start(); ?>
<?php require_once('../connections/pdoconnect.php'); ?>

<?php
$phu=new php_util();
$menu_id=$phu->get_menu_id(basename($_SERVER['PHP_SELF']));

$db=new DatabaseConnect();


if ((isset($_POST["POSTcheck"])) && ($_POST["POSTcheck"] == "form1")) {
  
    $SQLcrud = "INSERT INTO campus (`campus`) VALUES (?)";
    $db->query($SQLcrud);
    $db->bind(1,$_POST['campus']);
    $db->execute();
  
    $allowed = array('png');
    $filenames = "../images/campus/" . $db->lastinsertid() . ".png";
  
    if (isset($_FILES['image-upload1']) && $_FILES['image-upload1']['error'] == 0) {
      $extension = pathinfo($_FILES['image-upload1']['name'], PATHINFO_EXTENSION);
      if (move_uploaded_file($_FILES['image-upload1']['tmp_name'], $filenames)) {
      }
    }
  

    $GoTo = "campus_list.php";
    header(sprintf("Location: %s", $GoTo));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<title><?php echo $app_title; ?>  </title>

</head>
    
<script type="text/javascript"  language="javascript">
    function validateForm(){
        var x = document.forms["form1"]["results_here1"].value;
        if (x == null || x == "") { return true; }
        else { document.getElementById("results_here").innerHTML ="Record NOT Save. Duplicate Record Found!";document.getElementById("campus").focus();return false; }
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
        <div class="card-header"><h5 class="card-title"><strong><?php echo htmlentities($_SESSION['title']); ?></strong></h5></div>
            <div class="card-body">
<!--------------------------------------------------------------------------------->
<form method="post" name="form1" id="form1" onsubmit="return validateForm();" enctype="multipart/form-data">

     <div class="form-horizontal">
        <fieldset>

        <div class="form-group" align="center">
              <img id="image_preview1" src="" class="img-fluid" width="400px" height="400px" />
              <div class="mb-4">
                <label for="image-upload1" class="form-label">File format : PNG Only</label>
                <input class="form-control" type="file" id="image-upload1" name="image-upload1"
                  onchange="preview1()" accept="image/png">

              </div>
            </div>

        <span id="results_here" class="alert-danger"></span> <input type="hidden" id="results_here1">

        <div class="form-group">
        <label for="designation">Campus</label>
          <input required type="text" class="form-control"  name="campus" id="campus" placeholder=" " value="<?php if (isset($_POST['campus'])) echo $_POST['campus'];?>" 
                  OnKeyUp="showAjax('campus_duplicate.php','txtString',this.value, 'results_here');" Onblur="showAjax('campus_duplicate.php','txtString',this.value, 'results_here');" >
          
        </div>
   
        <br>
        <div class="form-group">
        <div class="col-md-2"></div>
        <div class="col-md-10">
          <button type="submit" class="btn btn-outline-primary" form="form1"><span class="bi-save"></span> Save</button>
          <a href="campus_list.php" class="btn btn-outline-danger hidelink"><span class="bi-x-octagon"></span> Cancel</a> 
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

        $(document).ready(function() {
          $('#image-upload1').change(function() {
            var file = this.files[0];
            var fileType = file['type'];
            var fileSize = file['size'];
            var validImageTypes = ['image/png'];
            if ($.inArray(fileType, validImageTypes) < 0) {
              Swal.fire($app_title, 'Please select a PNG file.', 'info');
              clearImage1();
              this.value = '';
            } else if (fileSize > 10485760) {
              Swal.fire($app_title,
                'File size exceeds 10MB. Please select a PNG file NOT MORE THAN 10MB.',
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
<?php
$db->close();
?>
