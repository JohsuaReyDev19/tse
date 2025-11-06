<?php ob_start(); ?>
<?php require_once('../connections/pdoconnect.php'); ?>

<?php

if (!isset($_SESSION)) {
    session_start();
  }
  
$phu=new php_util();
$menu_id=$phu->get_menu_id(basename($_SERVER['PHP_SELF']));

$db=new DatabaseConnect();

if ((isset($_POST["POSTcheck"])) && ($_POST["POSTcheck"] == "form1")) {

    $SQLcrud = "DELETE FROM `user_menu` WHERE id = ?";
    $db->query($SQLcrud);
    $db->bind(1,$_POST['id']);
    $db->execute();

     $SQLcrud = "DELETE FROM `user_restriction` WHERE `menu_id`=?";
  
    $db->query($SQLcrud);
    $db->bind(1,$_POST['id']);
    $db->execute();

    $GoTo = "user_menu_list.php";
    header(sprintf("Location: %s", $GoTo));
}

$query_rs = "SELECT * FROM `user_menu` WHERE id = ?";
$db->query($query_rs);
$db->bind(1,$_GET['recordID']);
$row_rs = $db->rowsingle();

$query_rscombo = "SELECT * FROM user_menu WHERE `id`=?";
$db->query($query_rscombo);
$db->bind(1,$row_rs['parent_id']);
$rsmenu = $db->rowsingle();

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
            <div class="card-body alert alert-info">
<!--------------------------------------------------------------------------------->
<form id="form1" name="form1" method="post">
        <div class="form-horizontal">
        
        <fieldset>
           

            <div class="form-group">
              <label for=""><strong>Name</strong></label>
              <input name="name" type="text" placeholder="" class="form-control input-md" readonly value="<?php echo htmlentities($row_rs['name']); ?>">
            </div>
            <br>
            <div class="form-group">
              <label for=""><strong>Link</strong></label>
              <input name="href" type="text" placeholder="" class="form-control input-md" readonly value="<?php echo htmlentities($row_rs['href']); ?>">
            </div>

            <br>
             <div class="form-group">
             <label ><strong>Parent Menu</strong></label>
                <input name="href" type="text" placeholder="" class="form-control input-md" readonly value="<?php echo htmlentities($rsmenu['name']);?>">
            </div>
            <br>
            <div class="form-group">       
              <label for="icon"><strong>Icon</strong></label>   
              <div class="input-group"> <span class="input-group-text" id="inputGroupPrepend"><i class="<?php echo htmlentities($row_rs['icon']); ?>"> </i></span>
              <input name="icon" id="icon" type="text" placeholder="Icon" class="form-control"  readonly  value="<?php echo htmlentities($row_rs['icon']); ?>">
            </div>

            <br>
            <div class="form-group">
            <label for=""><strong>Type</strong></label>
                <input name="type" type="text" placeholder="" class="form-control input-md" readonly value="<?php if (!strcmp(htmlentities($row_rs['type']),'m')) {echo 'Main Menu';}?>">
            </div>

            <br>
            <div class="form-group">
            <label for=""><strong>Order</strong></label>
                <input name="type" type="text" placeholder="" class="form-control input-md" readonly value="<?php echo htmlentities($row_rs['order']); ?>">
            </div>

        <br>
        <div class="form-group">
        <div class="col-md-2"></div>
        <div class="col-md-10">
          Are you sure you want to Remove this Record? &nbsp;<button type="submit" class="btn btn-outline-danger" form="form1"><span class="bi-trash"></span> Yes</button>
          <a href="user_menu_list.php" class="btn btn-outline-primary hidelink"><span class="bi-x-octagon"></span> No</a>
          </div>
        </div>
            
        </fieldset>
    </div>
    <input type="hidden" name="POSTcheck" value="form1">
    <input type="hidden" name="id" id="id" value="<?php echo htmlentities($row_rs['id']);?>">    
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
