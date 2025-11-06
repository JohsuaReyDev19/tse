<?php ob_start(); ?>
<?php require_once('../connections/pdoconnect.php'); ?>

<?php
$phu=new php_util();
$menu_id=$phu->get_menu_id(basename($_SERVER['PHP_SELF']));

$db=new DatabaseConnect();

if ((isset($_POST["POSTcheck"])) && ($_POST["POSTcheck"] == "form1")) {

    $SQLcrud = "DELETE FROM category WHERE `category_id` = ?";
    $db->query($SQLcrud);
    $db->bind(1,$_POST['id']);
    $db->execute();
  
    $GoTo = "category_list.php?recordID=" . $_POST['events_id'];
    header(sprintf("Location: %s", $GoTo));
}

$query_rs = "SELECT * FROM `category` WHERE `category_id` = ?";
$db->query($query_rs);
$db->bind(1,$_GET['recordID']);
$row_rs = $db->rowsingle();


$total_percent=$row_rs['percent'];

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
        <div class="card-header"><h5 class="card-title"><strong><?php echo htmlentities($_SESSION['title']); ?></strong></h5></div>
            <div class="card-body">
<!--------------------------------------------------------------------------------->
<form id="form1" name="form1" method="post" enctype="multipart/form-data">
        <div class="form-horizontal">
        
        <fieldset>

           <div class="row">
              <div class="form-group col-md-8 col-sm-12">
                <label for="description">Category</label>
                <input readonly type="text" class="form-control" name="description" id="description" placeholder=" " value="<?php echo $row_rs['description'];?>">
              </div>
              <div class="form-group col-md-4 col-sm-12">
                <label for="description">Percent</label>
                <input readonly type="number" class="form-control" name="percent" id="percent" min="0" max="<?php echo $total_percent; ?>" placeholder=" " value="<?php echo $total_percent; ?>">
              </div>
            </div>
            <br>
            <div class="form-group">
        <div class="col-md-2"></div>
        <div class="col-md-10">
          Are you sure you want to Remove this Record? &nbsp;<button type="submit" class="btn btn-outline-danger" form="form1"><span class="bi-trash"></span> Yes</button>
          <a href="category_list.php?recordID=<?php echo $row_rs['events_id']?>" class="btn btn-outline-primary hidelink"><span class="bi-x-octagon"></span> No</a>
          </div>
        </div>
            
        </fieldset>
    </div>
    <input type="hidden" name="POSTcheck" value="form1">
  <input type="hidden" name="events_id" value="<?php echo $row_rs['events_id']; ?>">
  <input type="hidden" name="id" value="<?php echo $row_rs['category_id']; ?>">
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
