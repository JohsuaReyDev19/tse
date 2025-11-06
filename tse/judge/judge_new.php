<?php ob_start(); ?>
<?php require_once('../connections/pdoconnect.php'); ?>

<?php
$phu=new php_util();
$menu_id=$phu->get_menu_id(basename($_SERVER['PHP_SELF']));

$db=new DatabaseConnect();

function generateCustomPassword() {
    $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $digits = '0123456789';

    // Pick 3 random digits
    $numPart = '';
    for ($i = 0; $i < 3; $i++) {
        $numPart .= $digits[random_int(0, strlen($digits) - 1)];
    }

    // Pick 5 random letters
    $alphaPart = '';
    for ($i = 0; $i < 5; $i++) {
        $alphaPart .= $letters[random_int(0, strlen($letters) - 1)];
    }

    // Combine parts with @ symbol
    $rawPassword = $alphaPart . '@' . $numPart;

    // Shuffle to randomize position
    return $rawPassword;
}


if ((isset($_POST["POSTcheck"])) && ($_POST["POSTcheck"] == "form1")) {
  
    $SQLcrud = "INSERT INTO judge (`name`, events_id) VALUES (?, ?)";
    $db->query($SQLcrud);
    $db->bind(1,$_POST['name']);
    $db->bind(2,$_POST['events_id']);
    $db->execute();
    $id=$db->lastinsertid();

    $SQLcrud = "INSERT INTO user (fullname, username, password, designation, `group`, `status`,`contact_no`, `address`, campus,judge_id) VALUES (?,?,?,?,?,?,?,?,?,?)";
    $db->query($SQLcrud);
    $db->bind(1, $_POST['name']);
    $db->bind(2, 'judge@'.$id);
    $db->bind(3, htmlentities($phu->encryptMessage(generateCustomPassword())));
    $db->bind(4, 'Judge');
    $db->bind(5, 'Judge');
    $db->bind(6, 'active');
    $db->bind(7, '000-0000-000');
    $db->bind(8, 'Candelaria');
    $db->bind(9,'Candelaria' );
    $db->bind(10,$id );
    $db->execute();

  

    $GoTo = "judge_list.php?recordID=" . $_POST['events_id'];
    header(sprintf("Location: %s", $GoTo));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<title><?php echo $app_title; ?>  </title>

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
        <div class="card-header"><h5 class="card-title"><strong><?php echo htmlentities($_SESSION['title']); ?></strong></h5></div>
            <div class="card-body">
<!--------------------------------------------------------------------------------->
<form method="post" name="form1" id="form1" enctype="multipart/form-data">

     <div class="form-horizontal">
        <fieldset>

        <div class="form-group">
        <label for="">Name</label>
          <input required type="text" class="form-control"  name="name" id="name" placeholder=" " value="<?php if (isset($_POST['name'])) echo $_POST['name'];?>" >
        </div>
   
        <br>
        <div class="form-group">
        <div class="col-md-2"></div>
        <div class="col-md-10">
          <button type="submit" class="btn btn-outline-primary" form="form1"><span class="bi-save"></span> Save</button>
          <a href="judge_list.php?recordID=<?php echo $_GET['recordID']; ?>" class="btn btn-outline-danger hidelink"><span class="bi-x-octagon"></span> Cancel</a>
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
</html>
<?php
$db->close();
?>
