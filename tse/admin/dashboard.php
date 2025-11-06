<?php

//current events
$query_rs = "select * FROM `events` WHERE events_date=CURRENT_DATE() order by events_date ASC, `events_description` ASC";
$db->query($query_rs);
$rs_current = $db->rowset();
$rs_current_count = $db->rowset();

//upcoming events
$query_rs = "select * FROM `events` WHERE events_date>CURRENT_DATE() order by events_date ASC, `events_description` ASC";
$db->query($query_rs);
$rs_upcoming = $db->rowset();
$rs_upcoming_count = $db->rowset();

//previous events
$query_rs = "select * FROM `events` WHERE events_date<CURRENT_DATE() order by events_date ASC, `events_description` ASC";
$db->query($query_rs);
$rs_previous = $db->rowset();
$rs_previous_count = $db->rowset();



?>
<br>
<style>
    .card:hover {
        background-color: #9ec2f1ff;
        /* Light blue on hover */
        transition: background-color 0.3s ease;
    }
</style>

<?php if ($rs_current_count>0) { ?>
     <h5 class="card-title"><strong>CURRENT EVENTS</strong></h5>
    <div class="row">
        <?php foreach ($rs_current as $rs_current_data) {
        ?>
            <div class="col-xxl-4 col-xl-12">
                <?php if ($_SESSION['AIT_MM_UserGroup']=="Judge") { ?>
                <a href="../scorecard/scoresheet.php?events_id=<?php echo htmlentities($rs_current_data['events_id']); ?>">
                <?php } ?>
                <?php if ($_SESSION['AIT_MM_UserGroup']=="Administrator") { ?>
                <a href="../scorecard/scoresheet_result.php?events_id=<?php echo htmlentities($rs_current_data['events_id']); ?>">
                <?php } ?>
                    <div class="card">

                        <div class="card info-card green-card">
                            <div style="text-align:center">
                                <h5 class="card-title"><?php echo htmlentities($rs_current_data['events_description']); ?></h5>
                            </div>
                            <div class="card-body" style="text-align:center">
                                
                                <div class="align-items-center"></span></h5>
                                    <div> <img class="rounded" src="../images/events/<?php echo $rs_current_data['events_id']; ?>.jpg?t=<?php echo time(); ?>" class="img-fluid" width="300px" height="300px" /></div>
                                    <div class="row"><span class="text-danger small pt-1 fw-bold"></span> <span class="text-muted small pt-2 ps-1"><?php echo htmlentities($rs_current_data['events_date']); ?></span></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </a>
            </div>

        <?php } ?>
    </div>
<?php } ?>
<br>

<?php if ($rs_upcoming_count>0) { ?>
     <h5 class="card-title"><strong>UPCOMING EVENTS</strong></h5>
    <div class="row">
        <?php foreach ($rs_upcoming as $rs_upcoming_data) {
        ?>
            <div class="col-xxl-4 col-xl-12">
                
                    <div class="card">

                        <div class="card info-card green-card">
                            <div style="text-align:center">
                                <h5 class="card-title"><?php echo htmlentities($rs_upcoming_data['events_description']); ?></h5>
                            </div>
                            <div class="card-body" style="text-align:center">
                                
                                <div class="align-items-center"></span></h5>
                                    <div> <img class="rounded" src="../images/events/<?php echo $rs_upcoming_data['events_id']; ?>.jpg?t=<?php echo time(); ?>" class="img-fluid" width="300px" height="300px" /></div>
                                    <div class="row"><span class="text-danger small pt-1 fw-bold"></span> <span class="text-muted small pt-2 ps-1"><?php echo htmlentities($rs_upcoming_data['events_date']); ?></span></div>
                                </div>
                            </div>
                        </div>

                    </div>
                
            </div>

        <?php } ?>
    </div>
<?php } ?>
<br>

<?php if ($rs_previous_count>0) { ?>
<h5 class="card-title"><strong>PREVIOUS EVENTS</strong></h5>
    <div class="row">
        <?php foreach ($rs_previous as $rs_previous_data) {
        ?>
            <div class="col-xxl-4 col-xl-12">
                
                <a href="../scorecard/scoresheet_result.php?events_id=<?php echo htmlentities($rs_previous_data['events_id']); ?>">

                    <div class="card">

                        <div class="card info-card green-card">
                            <div style="text-align:center">
                                <h5 class="card-title"><?php echo htmlentities($rs_previous_data['events_description']); ?></h5>
                            </div>
                            <div class="card-body" style="text-align:center">
                                
                                <div class="align-items-center"></span></h5>
                                    <div> <img class="rounded" src="../images/events/<?php echo $rs_previous_data['events_id']; ?>.jpg?t=<?php echo time(); ?>" class="img-fluid" width="300px" height="300px" /></div>
                                    <div class="row"><span class="text-danger small pt-1 fw-bold"></span> <span class="text-muted small pt-2 ps-1"><?php echo htmlentities($rs_previous_data['events_date']); ?></span></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </a>
            </div>

        <?php } ?>
    </div>
<?php } ?>