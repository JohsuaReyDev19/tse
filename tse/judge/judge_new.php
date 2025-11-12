<?php ob_start(); ?>
<?php require_once('../connections/pdoconnect.php'); ?>

<?php
$phu = new php_util();
$menu_id = $phu->get_menu_id(basename($_SERVER['PHP_SELF']));

$db = new DatabaseConnect();

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
  
    // ✅ Insert only into judge table
    $SQLcrud = "INSERT INTO judge (`name`, events_id) VALUES (?, ?)";
    $db->query($SQLcrud);
    $db->bind(1, $_POST['name']);
    $db->bind(2, $_POST['events_id']);
    $db->execute();

    // Redirect to judge list page
    $GoTo = "judge_list.php?recordID=" . $_POST['events_id'];
    header(sprintf("Location: %s", $GoTo));
    exit;
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
    <label for="judge">Select Judges</label>
    <select required name="name" id="name" class="form-control">
        <option value="">-- Select Judge --</option>
        <?php
        // Database connection
        $conn = new mysqli("localhost", "root", "", "dbtse");

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $events_id = $_GET['recordID']; // current event

        // Fetch all users
        $sql = "SELECT fullname FROM user";
        $result = $conn->query($sql);

        // Fetch judges already assigned to this event
        $existingJudges = [];
        $sql_judges = "SELECT name FROM judge WHERE events_id = ?";
        $stmt_j = $conn->prepare($sql_judges);
        $stmt_j->bind_param("i", $events_id);
        $stmt_j->execute();
        $res_judges = $stmt_j->get_result();
        while ($row_j = $res_judges->fetch_assoc()) {
            $existingJudges[] = $row_j['name'];
        }
        $stmt_j->close();

        // Store previously selected judge (if form was submitted)
        $selectedJudge = isset($_POST['name']) ? $_POST['name'] : '';

        // Loop through users
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $fullname = $row['fullname'];

                // Skip if fullname contains "Administrator"
                if (stripos($fullname, 'Administrator') !== false) {
                    continue;
                }

                // Disable if already assigned to this event
                $disabled = in_array($fullname, $existingJudges) ? 'disabled' : '';
                $selected = ($selectedJudge == $fullname) ? 'selected' : '';

                echo '<option value="' . htmlspecialchars($fullname) . '" ' . $disabled . ' ' . $selected . '>' 
                     . htmlspecialchars($fullname) 
                     . ($disabled ? ' (Already assigned)' : '') 
                     . '</option>';
            }
        } else {
            echo '<option value="">No judges found</option>';
        }

        $conn->close();
        ?>
    </select>
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
