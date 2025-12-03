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
            <!--------------------------------------------------------------->
<!-- CATEGORY RANKING TABLE (Option A)                         -->
<!--------------------------------------------------------------->

<h4 class="text-center mt-4 mb-3 text-primary fw-bold">
    Category Winners (Ranking per Category)
</h4>

<?php
// GET NUMBER OF JUDGES
$query_jcount = "SELECT COUNT(DISTINCT judge_id) AS total_judges 
                 FROM score WHERE events_id=?";
$db->query($query_jcount);
$db->bind(1, $_GET['events_id']);
$jcount = $db->rowsingle()['total_judges'];

if ($jcount == 0) { $jcount = 1; }

// GET CATEGORIES
$query_cat = "SELECT * FROM category WHERE events_id=?";
$db->query($query_cat);
$db->bind(1, $_GET['events_id']);
$categories = $db->rowset();

// LOOP THROUGH EACH CATEGORY
foreach ($categories as $cat) {

    echo "<h5 class='mt-4 mb-2 text-success fw-bold'>
            ⭐ " . htmlentities($cat['description']) . "
          </h5>";

    echo "<table class='table table-bordered table-striped'>
            <thead class='table-dark'>
                <tr>
                    <th width='10%'>Rank</th>
                    <th width='40%'>Contestant</th>
                    <th width='30%'>Description</th>
                    <th width='20%' class='text-center'>Average Score</th>
                </tr>
            </thead>
            <tbody>";
    
    // FETCH PARTICIPANTS WITH SCORES FOR THIS CATEGORY
    $query_scores = "
        SELECT 
            p.participant_id,
            p.name,
            p.description,
            (SUM(s.category_score)/$jcount) AS avg_score
        FROM score s
        JOIN participant p ON s.participant_id = p.participant_id
        WHERE s.events_id = ?
        AND s.category_id = ?
        GROUP BY p.participant_id
        ORDER BY avg_score DESC
    ";
    
    $db->query($query_scores);
    $db->bind(1, $_GET['events_id']);
    $db->bind(2, $cat['category_id']);
    $rows = $db->rowset();

    $rank = 1;
    foreach ($rows as $r) {
        echo "<tr>
                <td class='fw-bold'>$rank</td>
                <td>" . htmlentities($r['name']) . "</td>
                <td><span class='badge bg-secondary'>" . htmlentities($r['description']) . "</span></td>
                <td class='text-center fw-bold'>" . number_format($r['avg_score'], 2) . "</td>
              </tr>";
        $rank++;
    }

    echo "</tbody></table>";
}
?>

<br>
            <form method="post" name="form1" id="form1">
                <h4 class="text-center mt-4 mb-3 text-primary fw-bold">
    Overall Final Result
</h4>
                <table id="tablelist" class="table-striped table-hover table-responsive table-bordered" id="do_not_print">
    <thead>
        <tr class="alert-info">
            <th class="fw-bold" data-sortable="false">Constestant</th>
            <?php
            foreach ($rs_category as $row_rs_category) {
                echo '<th class="fw-bold text-center" data-sortable="false">' . $row_rs_category['description'] . '<br>';
                echo $row_rs_category['percent'] . '%</th>';
            }
            ?>
            <th class="fw-bold text-center">Total</th>
        </tr>
    </thead>

    <tbody>
        <?php
        $ctr = 0;
        foreach ($rs_participant as $row_rs_participant) {
            $ctr++;
            $i = '<input type="hidden" id="participant_id_' . $ctr . '" name="participant_id_' . $ctr . '" value="' . $row_rs_participant['participant_id'] . '">';

            echo '<tr>';
            echo '<td>' . $i . $row_rs_participant['name'] . ' <br><span class="badge bg-secondary">' . $row_rs_participant['description'] . '</span></td>';

            // ===========================
            // FIX TOTAL SCORE
            // ===========================
            $query_rs = "SELECT SUM(category_score)/? AS category_score FROM score WHERE events_id=? AND participant_id=?";
            $db->query($query_rs);
            $db->bind(1, $rs_judge_count);
            $db->bind(2, $_GET['events_id']);
            $db->bind(3, $row_rs_participant['participant_id']);

            $rs_total_value = $db->rowsingle();

            // Convert NULL → 0
            $total_raw = $rs_total_value['category_score'] ?? 0;
            $total_value = number_format((float)$total_raw, 2);

            // ===========================
            // FIX CATEGORY SCORES
            // ===========================
            foreach ($rs_category as $row_rs_category) {

                $query_rs = "SELECT SUM(category_score)/? AS category_score FROM score 
                             WHERE events_id=? AND category_id=? AND participant_id=?";

                $db->query($query_rs);
                $db->bind(1, $rs_judge_count);
                $db->bind(2, $_GET['events_id']);
                $db->bind(3, $row_rs_category['category_id']);
                $db->bind(4, $row_rs_participant['participant_id']);

                $rs_value = $db->rowsingle();

                // Convert NULL → 0
                $value_raw = $rs_value['category_score'] ?? 0;
                $value = number_format((float)$value_raw, 2);

                echo '<td width="10%" class="text-center">' . $value . '</td>';
            }

            echo '<td width="10%" class="text-center">' . $total_value . '</td>';
            echo '</tr>';
        }
        ?>
        <br>
    </tbody>
</table>

<br><hr>

<h4 class="text-center mt-4 mb-3 text-primary fw-bold">Judge Score Breakdown</h4>

<table class="table table-bordered table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th width="20%">Judge</th>
            <th width="30%">Contestant</th>
            <th width="30%">Category</th>
            <th width="10%" class="text-center">Score</th>
        </tr>
    </thead>

    <tbody>
        <?php
        // ------------------------------------------------------------
        // FETCH JUDGE LIST  (judge.judge_id = user.judge_id)
        // ------------------------------------------------------------
        $query_judge = "SELECT j.judge_id, u.fullname 
                        FROM judge j
                        LEFT JOIN user u ON j.judge_id = u.judge_id";

        $db->query($query_judge);
        $judgeList = $db->rowset();

        // ------------------------------------------------------------
        // FETCH RAW SCORES
        // ------------------------------------------------------------
        $query_scores = "SELECT 
                            s.judge_id,
                            s.participant_id,
                            s.category_id,
                            s.category_score,
                            p.name AS participant_name,
                            p.description AS participant_desc,
                            c.description AS category_name
                        FROM score s
                        JOIN participant p ON s.participant_id = p.participant_id
                        JOIN category c ON s.category_id = c.category_id
                        WHERE s.events_id = ?
                        ORDER BY s.judge_id, s.participant_id, s.category_id";

        $db->query($query_scores);
        $db->bind(1, $_GET['events_id']);
        $scoreRows = $db->rowset();

        // ------------------------------------------------------------
        // ORGANIZE: JUDGE → CONTESTANT → CATEGORY
        // ------------------------------------------------------------
        $organized = [];

        foreach ($scoreRows as $row) {

            // MATCH JUDGE NAME
            $judgeName = '';
            foreach ($judgeList as $j) {
                if ($j['judge_id'] == $row['judge_id']) {
                    $judgeName = $j['fullname'];
                    break;
                }
            }

            if ($judgeName == "") { $judgeName = "Unknown Judge"; }

            // Contestant label w/ badge
            $contestantKey = $row['participant_name'] . 
                             " <span class='badge bg-secondary'>" . 
                             $row['participant_desc'] . "</span>";

            $organized[$judgeName][$contestantKey][] = [
                'category' => $row['category_name'],
                'score'    => $row['category_score']
            ];
        }

        // ------------------------------------------------------------
        // RENDER GROUPED TABLE
        // ------------------------------------------------------------
        foreach ($organized as $judge => $contestants) {

            // JUDGE HEADER
            echo "<tr class='table-primary'>
                    <td colspan='1' class='fw-bold text-center fs-5'>
                        Judge: " . htmlentities($judge) . "
                    </td>
                  </tr>";

            foreach ($contestants as $contestant => $categories) {

                // CONTESTANT HEADER
                echo "<tr class='table-secondary'>
                        <td></td>
                        <td colspan='3' class='fw-bold fs-6'>$contestant</td>
                      </tr>";

                // CATEGORY ROWS
                foreach ($categories as $c) {
                    echo "<tr>";
                    echo "<td></td>"; // indent
                    echo "<td></td>"; // indent
                    echo "<td>" . htmlentities($c['category']) . "</td>";
                    echo "<td class='text-center fw-bold'>" . htmlentities($c['score']) . "</td>";
                    echo "</tr>";
                }
            }
        }
        ?>
    </tbody>
</table>



                <div class="form-group d-flex justify-content-between align-items-center mt-3">
                    <div id="do_not_print" class="d-flex gap-2">
                        <div id="response"></div>
                        <button type="submit" class="btn btn-outline-primary" form="form1">
                        <i class="bi-x-octagon"></i> Close
                        </button>
                        <button id="print_button" type="button" class="btn btn-outline-secondary">
                        <i class="bi-printer-fill" data-toggle="tooltip" data-placement="bottom" title="Print Result"></i> Print Result
                        </button>
                    </div>
                    <!-- <div id="EndEvent" class="btn btn-outline-danger">
                        <i class="bi-x-octagon"></i> End Event
                    </div> -->
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
        $(document).ready(function(){
            $("#EndEvent").click(function(){
                if (confirm("Are you sure you want to end this event?")) {
                    $.ajax({
                        url: "./updated_event.php",
                        type: "POST",
                        data: { event_id: <?php echo $rs_event['events_id']; ?> },
                        success: function(response){
                            alert(response);
                            location.reload();
                        },
                        error: function(){
                            alert("Error updating event date.");
                        }
                    });
                }
            });
        });
    </script>

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