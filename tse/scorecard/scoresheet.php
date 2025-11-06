<?php ob_start(); ?>
<?php require_once('../connections/pdoconnect.php'); ?>

<?php
$phu = new php_util();
$menu_id = $phu->get_menu_id(basename($_SERVER['PHP_SELF']));

$db = new DatabaseConnect();

if ((isset($_POST["POSTcheck"])) && ($_POST["POSTcheck"] == "form1")) {


    
  $GoTo = $_SERVER['PHP_SELF']."?doLogout=true";
  header(sprintf("Location: %s", $GoTo));
}

$query_rs = "select * FROM `judge` WHERE judge_id=? order by `name`";
$db->query($query_rs);
$db->bind(1, $_SESSION['judge_id']);
$rs_judge = $db->rowsingle();

$query_rs = "select * FROM `events` WHERE events_id=? ";
$db->query($query_rs);
$db->bind(1, $_GET['events_id']);
$rs_event = $db->rowsingle();

$query_rs = "select * FROM `category` WHERE events_id=?";
$db->query($query_rs);
$db->bind(1, $_GET['events_id']);
$rs_category = $db->rowset();
$rs_category_total = $db->rowcount();

$query_rs = "select * FROM `participant` WHERE events_id=? ORDER BY name";
$db->query($query_rs);
$db->bind(1, $_GET['events_id']);
$rs_participant = $db->rowset();


if (!(strcmp($phu->found_group($_SESSION['AIT_MM_UserGroup'], $menu_id), 1))) {
    $MM_authorizedUsers = $_SESSION['AIT_MM_UserGroup'];
}

require_once('../admin/grant_checker.php');



?>
<!DOCTYPE html>
<html lang="en">

<head>

    <title><?php echo $app_title; ?> </title>
</head>
<?php require_once('../template/phplink.php'); ?>

<script>
  window.onpopstate = function () {
    location.href = "../admin/log-in.php"; // or use window.location.replace()
  };

</script>


<body>
    <div align="center" class="alert" style="color: #012970;
    background-color: #f6f9ff;"><img src="../images/logo.png?start_time=<?php echo time(); ?>" class="img-responsive" width="150px">
        <h3><strong>Tabulation System for Events and Competetion for PRMSU Candelaria Campus</strong></h3><br>
        <h5 class="fw-bold text-success"><?php echo htmlentities($rs_event['events_description']); ?></h5>
        <span class="small-text text-success"><?php echo htmlentities($rs_event['events_date']); ?></span>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><strong><?php echo 'Welcome ' . $rs_judge['name']; ?></strong></h5>
            
        </div>
        <div class="card-body">
            <!--------------------------------------------------------------------------------->
            <form method="post" name="form1" id="form1">
                <table id="tablelist" class="table table-striped table-hover table-responsive table-bordered">
                    <thead>

                        <tr class="alert-info">
                            <td class="fw-bold" data-sortable="false">#</td>
                            <td class="fw-bold" data-sortable="false">Constestant</td>
                            <?php
                            foreach ($rs_category as $row_rs_category) {

                                echo '<td class="fw-bold text-center" data-sortable="false">' . $row_rs_category['description'] . '<br>';
                                echo $row_rs_category['percent'] . '%</td>';
                            }  ?>
                            <td class="fw-bold  text-center" data-sortable="false">Total</td>
                        </tr>
                    </thead>
                    <tbody>


                        <?php $ctr = 0;
                        foreach ($rs_participant as $row_rs_participant) {
                            $ctr++;
                            $i = '<input type="hidden" id="participant_id_' . $ctr . '" name="participant_id_' . $ctr . '" value="' . $row_rs_participant['participant_id'] . '">';
                            echo '<tr>';
                            echo '<td width="5%">' . $ctr . '.</td>';
                            echo '<td>' . $i . $row_rs_participant['name'] .' <br><span class="badge bg-secondary">'.$row_rs_participant['description'] .  '</span></td>';

                            $total_value="0";
                            $query_rs = "SELECT SUM(category_score) 'category_score' FROM  score  WHERE events_id=? AND judge_id=? AND participant_id=?";
                            $db->query($query_rs);
                            $db->bind(1, $_GET['events_id']);
                            $db->bind(2,$rs_judge['judge_id']);
                            $db->bind(3,$row_rs_participant['participant_id']);    
                            $rs_total_value = $db->rowsingle();
                            $rs_total_value_count = $db->rowsingle();
                            if ($rs_total_value_count>0){
                                $total_value=$rs_total_value['category_score'];
                            }

                            $cat=0;
                            foreach ($rs_category as $row_rs_category) {$cat++;
                                $value="0";
                                $query_rs = "SELECT * FROM  score  WHERE events_id=? AND judge_id=? AND category_id=? AND participant_id=?";
                                $db->query($query_rs);
                                $db->bind(1, $_GET['events_id']);
                                $db->bind(2,$rs_judge['judge_id']);
                                $db->bind(3,$row_rs_category['category_id']);
                                $db->bind(4,$row_rs_participant['participant_id']);    
                                $rs_value = $db->rowsingle();
                                $rs_value_count = $db->rowsingle();
                                if ($rs_value_count>0){
                                    $value=$rs_value['category_score'];
                                }

                               

                                echo '<td  width="10%"><input class="form-control" type="number" min="0" max="' . $row_rs_category['percent'] . '"  id="cat_'.$ctr.'_'. $cat . '" name="cat_'.$ctr.'_'. $cat . '" value="'.$value.'" 
                                onkeyup="compute_total('.$ctr.','.$rs_category_total.','.$row_rs_participant['participant_id'] .','.$row_rs_category['category_id'].','.$_GET['events_id'].','.$rs_judge['judge_id'].','.$cat.');"
                                onchange="compute_total('.$ctr.','.$rs_category_total.','.$row_rs_participant['participant_id'] .','.$row_rs_category['category_id'].','.$_GET['events_id'].','.$rs_judge['judge_id'].','.$cat.');"></td>';
                                

                            }
                            echo '<td width="10%"><input readonly class="form-control" type="number" min="0" max=""  id="total_'.$ctr.'_'. $rs_category_total . '" name="total_'.$ctr.'_'. $rs_category_total . '" value="'. $total_value.'"></td>';
                            echo '</tr>';
                           
                        }  ?>
                        <br>

                    </tbody>
                </table>

                <div class="form-group">
                    <div class="col-md-2"></div>
                    <div class="col-md-10">
                        <div id="response"></div>
                        <button type="submit" class="btn btn-outline-primary" form="form1"><span
                                        class="bi-x-octagon"> Close</button>

                    </div>
                </div>

                <input type="hidden" name="POSTcheck" value="form1">
            </form>
        </div>
        <div class="card-footer"></div>
    </div>
    <?php require_once('../template/footer.php'); ?>

    <script>
        const labelData = {
            placeholder: "",
            noRows: "No record to display",
            info: "Showing {start} to {end} of {rows} record (Page {page} of {pages} pages)"
        }


        const dataTable = new simpleDatatables.DataTable("#tablelist", {
            searchable: false,
            fixedHeight: true,
            perPage: 25,
            //labels: labelData,
        });

        //dataTable.columns().hide([1, 2])
        //dataTable.columns().remove([0, 2, 3, 6]);
        //dataTable.columns().order([0, 2, 1]);
    </script>



</body>

<script>

      document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('input', () => {
      const max = parseFloat(input.max);
      const value = parseFloat(input.value);
      if (!isNaN(max) && value > max) {
        input.value = max;
      }
    });
  });

  
function compute_total(ctr, cat_total, participant_id, category_id, events_id, judge_id,cat){
    var el=String("#cat_" + ctr + "_" + cat);
    var score=$(el).val();
    //Swal.fire('Value', participant_id + " " + category_id + " " + events_id + " " + judge_id + " " + score, 'info');
    
    $.ajax({
                    url: 'process_save.php',
                    type: 'POST',
                    data: { 
                        
                        score: score,
                        participant_id: participant_id,
                        category_id: category_id,
                        events_id: events_id,
                        judge_id: judge_id


                    },
                    success: function(data) {
                    //var msg= data;
                    //Swal.fire('Value',msg, 'info');

                    //$('#response').html(msg); // Show response from PHP
                    },
                    error: function(xhr, status, error) {
                    //$('#response').html('Error: ' + error);
                    }
                });


var t=0;
    for (let i = 1; i <= cat_total; i++) {
        var value= parseFloat($("#cat_" + ctr + "_" + i).val()); 
        //Swal.fire('Compute',  $("#cat_" + ctr + "_" + i).val(), 'info');
        if (!isNaN(value)) {
        t=t+value;    
        console.log(value);
        }
    }
    
    $("#total_" +ctr + "_" + cat_total).val(t);
}

</script>


</html>
<?php ob_flush();
$db->close();
?>