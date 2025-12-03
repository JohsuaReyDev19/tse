<?php

// ===============================
// CURRENT EVENTS
// ===============================
$query_rs = "select * FROM `events` WHERE events_date=CURRENT_DATE() order by events_date ASC, `events_description` ASC";
$db->query($query_rs);
$rs_current = $db->rowset();
$rs_current_count = $db->rowcount();

// ===============================
// UPCOMING EVENTS
// ===============================
$query_rs = "select * FROM `events` WHERE events_date > CURRENT_DATE() order by events_date ASC, `events_description` ASC";
$db->query($query_rs);
$rs_upcoming = $db->rowset();
$rs_upcoming_count = $db->rowcount();

// ===============================
// PREVIOUS EVENTS
// ===============================
$query_rs = "select * FROM `events` WHERE events_date < CURRENT_DATE() order by events_date ASC, `events_description` ASC";
$db->query($query_rs);
$rs_previous = $db->rowset();
$rs_previous_count = $db->rowcount();


// ===============================
// GET JUDGE ASSIGNED EVENTS
// ===============================
$judgeName = $_SESSION['AIT_MM_FullName'];

$query = "SELECT * FROM judge_events WHERE name = ?";
$db->query($query);
$db->bind(1, $judgeName);

$judgeData = $db->rowset();
$judgeCount = $db->rowcount();

// Store ALL assigned events for this judge
$eventIds = [];
if ($judgeCount > 0) {
    foreach ($judgeData as $row) {
        $eventIds[] = $row['events_id'];   // push multiple
    }
}

?>
<br>

<style>
    .card:hover {
        background-color: #9ec2f1ff;
        transition: background-color 0.3s ease;
    }
</style>

<!-- =============================== -->
<!-- CURRENT EVENTS -->
<!-- =============================== -->

<?php if ($rs_current_count > 0) { ?>
    <h5 class="card-title">
        <strong>CURRENT EVENTS
        </strong>
    </h5>

    <div class="row">

        <?php 
        $foundEvent = false;

        foreach ($rs_current as $rs_current_data) {

            // ============================
            // ADMIN → Show all events
            // ============================
            if ($_SESSION['AIT_MM_UserGroup'] == "Administrator") {
        ?>

                <div class="col-xxl-4 col-xl-12">
                    <a href="../scorecard/scoresheet_result.php?events_id=<?php echo htmlentities($rs_current_data['events_id']); ?>">
                        <div class="card info-card green-card">
                            <div style="text-align:center">
                                <h5 class="card-title"><?php echo htmlentities($rs_current_data['events_description']); ?></h5>
                            </div>
                            <div class="card-body" style="text-align:center">
                                <img class="rounded" 
                                    src="../images/events/<?php echo $rs_current_data['events_id']; ?>.jpg?t=<?php echo time(); ?>" 
                                    width="300" height="300" />
                                <div class="row">
                                    <span class="text-muted small pt-2 ps-1">
                                        <?php echo htmlentities($rs_current_data['events_date']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

        <?php
            } // END ADMIN


            // ================================
            // JUDGE → Show ONLY assigned events
            // ================================
            if ($_SESSION['AIT_MM_UserGroup'] == "Judge") {

                // Check if this event matches ANY assigned event
                if (in_array($rs_current_data['events_id'], $eventIds)) {
                    $foundEvent = true;
        ?>

                    <div class="col-xxl-4 col-xl-12">
                        <a href="../scorecard/scoresheet.php?events_id=<?php echo htmlentities($rs_current_data['events_id']); ?>">
                            <div class="card info-card green-card">
                                <div style="text-align:center">
                                    <h5 class="card-title"><?php echo htmlentities($rs_current_data['events_description']); ?></h5>
                                </div>
                                <div class="card-body" style="text-align:center">
                                    <img class="rounded" 
                                        src="../images/events/<?php echo $rs_current_data['events_id']; ?>.jpg?t=<?php echo time(); ?>" 
                                        width="300" height="300" />
                                    <div class="row">
                                        <span class="text-muted small pt-2 ps-1">
                                            <?php echo htmlentities($rs_current_data['events_date']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

        <?php
                } // END assigned event
            } // END Judge
        } // END foreach current events
        ?>

        <?php 
        // Judge has NO assigned events in CURRENT
        if ($_SESSION['AIT_MM_UserGroup']=="Judge" && !$foundEvent) { ?>
            <div class="col-12">
                <div class="alert alert-warning text-center mt-3">
                    No current event assigned to this judge.
                </div>
            </div>
        <?php } ?>

    </div>
<?php } ?>


<br>

<!-- =============================== -->
<!-- UPCOMING EVENTS -->
<!-- =============================== -->

<?php if ($rs_upcoming_count > 0) { ?>
    <h5 class="card-title"><strong>UPCOMING EVENTS</strong></h5>
    <div class="row">

        <?php foreach ($rs_upcoming as $rs_upcoming_data) { ?>
            <div class="col-xxl-4 col-xl-12">
                <div class="card">
                    <div class="card info-card green-card">
                        <div style="text-align:center">
                            <h5 class="card-title"><?php echo htmlentities($rs_upcoming_data['events_description']); ?></h5>
                        </div>
                        <div class="card-body" style="text-align:center">
                            <img class="rounded" 
                                src="../images/events/<?php echo $rs_upcoming_data['events_id']; ?>.jpg?t=<?php echo time(); ?>" 
                                width="300" height="300" />
                            <div class="row">
                                <span class="text-muted small pt-2 ps-1">
                                    <?php echo htmlentities($rs_upcoming_data['events_date']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

    </div>
<?php } ?>

<br>

<!-- =============================== -->
<!-- PREVIOUS EVENTS -->
<!-- =============================== -->

<?php if ($rs_previous_count > 0) { ?>
<h5 class="card-title"><strong>PREVIOUS EVENTS</strong></h5>

    <div class="row">
        <?php foreach ($rs_previous as $rs_previous_data) { ?>
            <div class="col-xxl-4 col-xl-12">
                <a href="../scorecard/scoresheet_result.php?events_id=<?php echo htmlentities($rs_previous_data['events_id']); ?>">
                    <div class="card">
                        <div class="card info-card green-card">
                            <div style="text-align:center">
                                <h5 class="card-title"><?php echo htmlentities($rs_previous_data['events_description']); ?></h5>
                            </div>
                            <div class="card-body" style="text-align:center">
                                <img class="rounded" 
                                    src="../images/events/<?php echo $rs_previous_data['events_id']; ?>.jpg?t=<?php echo time(); ?>" 
                                    width="300" height="300" />
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php } ?>
    </div>
<?php } ?>
