<?php ob_start(); ?>
<?php require_once('../connections/pdoconnect.php'); ?>

<?php
$phu = new php_util();
$menu_id = $phu->get_menu_id(basename($_SERVER['PHP_SELF']));

$db = new DatabaseConnect();

if ((isset($_POST["POSTcheck"])) && ($_POST["POSTcheck"] == "form1")) {



    $GoTo = "../admin/index.php";
    header(sprintf("Location: %s", $GoTo));
}

$query_rs = "SELECT distinct(judge_id) FROM score WHERE events_id=?";
$db->query($query_rs);
$db->bind(1, $_GET['events_id']);
$rs_judge = $db->rowset();
$rs_judge_count = $db->rowcount();

$query_rs = "select * FROM `events` WHERE events_id=? ";
$db->query($query_rs);
$db->bind(1, $_GET['events_id']);
$rs_event = $db->rowsingle();

$query_rs = "select * FROM `category` WHERE events_id=? ";
$db->query($query_rs);
$db->bind(1, $_GET['events_id']);
$rs_category = $db->rowset();
$rs_category_total = $db->rowcount();

$query_rs = "select p.* FROM participant p WHERE p.events_id=?";
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
    window.onpopstate = function() {
        location.href = "../admin/log-in.php"; // or use window.location.replace()
    };
</script>

<style>
    @media print {
        body {
            font-size: 12pt;
        }
    }
</style>

<body>
    <div align="center" class="alert" style="color: #012970;
    background-color: #f6f9ff;"><img src="../images/logo.png?start_time=<?php echo time(); ?>" class="img-responsive" width="150px">
        <h3><strong>Tabulation System for Events and Competetion for PRMSU Candelaria Campus</strong></h3><br>
        <h5 class="fw-bold text-success"><?php echo htmlentities($rs_event['events_description']); ?></h5>
        <span class="small-text text-success">Date : <?php echo htmlentities($rs_event['events_date']); ?><br><br>
        <strong>Final Result</strong></span>
    </div>

    <div class="card">

        <div class="card-body">
            <!--------------------------------------------------------------------------------->
            <form method="post" name="form1" id="form1">
                <table id="tablelist" class="table-striped table-hover table-responsive table-bordered" id="do_not_print">
                    <thead>
                        <tr class="alert-info">
                            <!--<th class="fw-bold">#</th>-->
                            <th class="fw-bold" data-sortable="false">Constestant</th>
                            <?php
                            foreach ($rs_category as $row_rs_category) {

                                echo '<th class="fw-bold text-center" data-sortable="false">' . $row_rs_category['description'] . '<br>';
                                echo $row_rs_category['percent'] . '%</th>';
                            }  ?>
                            <th class="fw-bold  text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $ctr = 0;
                        foreach ($rs_participant as $row_rs_participant) {
                            $ctr++;
                            $i = '<input type="hidden" id="participant_id_' . $ctr . '" name="participant_id_' . $ctr . '" value="' . $row_rs_participant['participant_id'] . '">';
                            echo '<tr>';
                            //echo '<td width="5%">' . $ctr . '.</td>';
                            echo '<td>' . $i . $row_rs_participant['name'] .' <br><span class="badge bg-secondary">'.$row_rs_participant['description'] . '</span></td>';

                            $total_value = "0";
                            $query_rs = "SELECT SUM(category_score)/? 'category_score' FROM  score  WHERE events_id=? AND participant_id=?";
                            $db->query($query_rs);
                            $db->bind(1, $rs_judge_count);
                            $db->bind(2, $_GET['events_id']);
                            $db->bind(3, $row_rs_participant['participant_id']);
                            $rs_total_value = $db->rowsingle();
                            $rs_total_value_count = $db->rowsingle();
                            if ($rs_total_value_count > 0) {
                                $total_value = $rs_total_value['category_score'];
                            }

                            $cat = 0;
                            foreach ($rs_category as $row_rs_category) {
                                $cat++;
                                $value = "0";
                                $query_rs = "SELECT SUM(category_score)/? 'category_score' FROM  score  WHERE events_id=? AND category_id=? AND participant_id=?";
                                $db->query($query_rs);
                                $db->bind(1, $rs_judge_count);
                                $db->bind(2, $_GET['events_id']);
                                $db->bind(3, $row_rs_category['category_id']);
                                $db->bind(4, $row_rs_participant['participant_id']);
                                $rs_value = $db->rowsingle();
                                $rs_value_count = $db->rowsingle();
                                if ($rs_value_count > 0) {
                                    $value = $rs_value['category_score'];
                                }



                                echo '<td  width="10%" class="text-center">' . $value . '</td>';
                            }
                            echo '<td width="10%"  class="text-center">' . $total_value . '</td>';
                            echo '</tr>';
                        }  ?>
                        <br>

                    </tbody>
                </table>

                <div class="form-group">
                    <div class="col-md-2"></div>
                    <div class="col-md-10" id="do_not_print">
                        <div id="response"></div>
                        <button type="submit" class="btn btn-outline-primary" form="form1"><span
                                class="bi-x-octagon"> Close</button>
                        <button id="print_button" class="btn btn-outline-secondary"> <span class="bi-printer-fill" data-toogle="tooltip" data-placement="bottom" title=""></span> Print Result</button>

                    </div>
                </div>

                <input type="hidden" name="POSTcheck" value="form1">
            </form>
        </div>
        <div class="card-footer"></div>
    </div>
    <div id="do_not_print_footer">
    <?php require_once('../template/footer.php'); ?>
    </div>
    <script>
        const labelData = {
            placeholder: "",
            noRows: "No record to display",
            info: ""
        }


        const dataTable = new simpleDatatables.DataTable("#tablelist", {
            searchable: false,
            fixedHeight: true,
            perPageSelect: false,
            perPage: 10000,
            //labels: labelData,
        });

        //dataTable.columns().hide([1, 2])
        //dataTable.columns().remove([0, 2, 3, 6]);
        //dataTable.columns().order([0, 2, 1]);

        $(document).ready(function() {
            const $table = $('#tablelist');
            const $tbody = $table.find('tbody');
            const rows = $tbody.find('tr').get();

            const lastColIndex = $table.find('thead th').length - 1;

            rows.sort(function(a, b) {
                const valA = $(a).children('td').eq(lastColIndex).text().trim();
                const valB = $(b).children('td').eq(lastColIndex).text().trim();
                return parseFloat(valB) - parseFloat(valA); // descending
            });

            $.each(rows, function(index, row) {
                $tbody.append(row);
            });
        });
    </script>

    <style>
        .dataTable-info {
            display: none !important;
        }
    </style>

    <script>
        $('#print_button').on('click', function() {
            // Temporarily hide the excluded content
            $('#do_not_print').hide();
            $('#do_not_print_footer').hide();

            // Trigger print
            window.print();

            // Show the excluded content again after printing
            setTimeout(function() {
                $('#do_not_print').show();
            }, 1000);
        });
    </script>


</body>








</html>
<?php ob_flush();
$db->close();
?>