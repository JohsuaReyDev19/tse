<?php ob_start(); ?>
<?php require_once('../connections/pdoconnect.php'); ?>

<?php
$phu=new php_util();

$db=new DatabaseConnect();
$check_submit="0";

if ((isset($_POST["POSTcheck"])) && ($_POST["POSTcheck"] == "form1")) {
  
    $SQLcrud = "INSERT INTO respondent (`date`, name,gender,age,region, services_id, campus_id, client_type_id, email, suggestion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $db->query($SQLcrud);
    $db->bind(1,$_POST['date']);
    $db->bind(2,$_POST['name']);
    $db->bind(3,$_POST['gender']);
    $db->bind(4,$_POST['age']);
    $db->bind(5,$_POST['region']);
    $db->bind(6,$_POST['services_id']);
    $db->bind(7,$_POST['campus_id']);
    $db->bind(8,$_POST['client_type_id']);
    $db->bind(9,$_POST['email']);
    $db->bind(10,$_POST['suggestion']);
    $db->execute();
  
    $lastid=$db->lastinsertid();

    for ($x=1;$x<=3;$x++){
        $SQLcrud = "INSERT INTO respondent_answer (survey_question_selection_order,survey_code,respondent_id) VALUES (?, ?, ?)";
        $db->query($SQLcrud);
        $db->bind(1,$_POST['CC'.$x]);
        $db->bind(2,'CC'.$x);
        $db->bind(3,$lastid);
        $db->execute();
    }

    for ($x=0;$x<=8;$x++){
        $SQLcrud = "INSERT INTO respondent_answer (survey_question_selection_order,survey_code,respondent_id) VALUES (?, ?, ?)";
        $db->query($SQLcrud);
        $db->bind(1,$_POST['SQD'.$x]);
        $db->bind(2,'SQD'.$x);
        $db->bind(3,$lastid);
        $db->execute();
    }
    $check_submit="1";

    //$GoTo = "index.php";
    //header(sprintf("Location: %s", $GoTo));
}

$query_rs = "select * FROM `survey_question` WHERE question_type='CC' order by `survey_order`";
$db->query($query_rs);
$rs_cc=$db->rowset();

$query_rs = "select * FROM `survey_question` WHERE question_type='SQD' order by `survey_order`";
$db->query($query_rs);
$rs_sqd=$db->rowset();

$query_rs = "select * FROM `office` order by `office`";
$db->query($query_rs);
$rs_office=$db->rowset();

$query_rs = "select * FROM `campus` order by `campus`";
$db->query($query_rs);
$rs_campus=$db->rowset();

$query_rs = "select * FROM `service` order by `service`";
$db->query($query_rs);
$rs_service=$db->rowset();

$query_rs = "select * FROM `client_type` order by `client_type`";
$db->query($query_rs);
$rs_client_type=$db->rowset();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo $tagline; ?> </title>
</head>


<?php require_once('../template/phplink.php'); ?>
<script type="text/javascript" language="javascript">
function get_services() {

    //var office_id = document.getElementById('office_id').value; 

    office_id = $('#office_id').val();


    $.ajax({
        url: "update_services.php",
        method: "POST",
        dataType: "json",
        data: {
            office_id: office_id
        },
        success: function(data) {
            $("#services_id").empty(); // remove old options

            if (Number(data.return_value) > 0) {

                //$('#services_list').html(data.service_details);

                //('#services_list_id').html(data.service_details_option);

                //var arr= JSON.parse(data.service_option);
                var arr_value = JSON.parse(data.service_option_value);
                var arr_text = JSON.parse(data.service_option_text);

                //Swal.fire('get_services',arr_value[2],'info');

                for (var i = 0; i < arr_value.length; i++) {
                    var o = new Option(arr_text[i], arr_value[i]);
                    $('#services_id').append(o);
                }
            }

            //Swal.fire('get_services',data.service_details,'info');
            //$('#mm_item_count').html(data.return_value); 
            //$('#item_card_details').html(data.service_details); 


            //alert(data.return_value);  
            //location.reload(true); 

        }
    });
}
</script>

<body onload="get_services();">
    <div align="center" class="alert bg-gradient">

        <strong><?php echo $hd1; ?></strong>
        <h6><small> <?php echo $hd2; ?></small></h6>
        <h6><small> <?php echo $hd3; ?></small></h6>
        <h6><small> <?php echo $hd4; ?></small></h6><br>
        <strong>HELP US SERVE YOU BETTER!</strong><br><br>
        <strong><?php echo $tagline; ?></strong>
    </div>
    <div class="card">
        <div class="card-header">
            <h6 class="card-title">This Client Satisfaction Measurement (CSM) tracks the customer experience of
                government offices. Your feedback on your recently conclude transaction will help this office provide a
                better service. Personal information shared will be kept confidential and you always have the option to
                not answer this form</h6>
                
                <?php if (!(strcmp($check_submit,"1"))) {?>
                <br>
                <div class="alert alert-danger alert-dismissible fade show" role="alert"> <i class="bi bi-check-circle me-1"></i> Survey has been submitted. Thank You! <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
                <br>
            <?php } ?>
        </div>
        <div class="card-body alert alert-info">
            <!--------------------------------------------------------------------------------->
            
            <form method="post" name="form1" id="form1">

                <div class="form-horizontal">
                    <fieldset>
                        <br>

                        <div class="row">
                            <div class="form-group col-md-8 col-sm-12">
                                <label for="client_type">Client Type*</label>
                                <select required name="client_type_id" id="client_type_id" class="form-select"
                                    placeholder=" ">
                                    <?php
                    foreach($rs_client_type as $row_client_type) {  
                    ?>
                                    <option value="<?php echo $row_client_type['client_type_id']?>">
                                        <?php echo htmlentities($row_client_type['client_type']);?></option>
                                    <?php
                      }
                    ?>
                                </select>
                            </div>

                            <div class="form-group col-md-4 col-sm-12">
                                <label for="date">Date*</label>
                                <input required type="date" class="form-control" name="date" id="date" placeholder=" "
                                    value="<?php echo date('Y-m-d');?>">

                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group col-md-2 col-sm-12">
                                <label for="gender">Sex*</label>
                                <select required name="gender" id="gender" class="form-select" placeholder="">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>

                            <div class="form-group col-md-2 col-sm-12">
                                <label for="age">Age*</label>
                                <input required type="text" class="form-control" name="age" id="age" placeholder=" "
                                    value="">

                            </div>

                            <div class="form-group col-md-4 col-sm-12">
                                <label for="region">Region of Origin*</label>
                                <input required type="text" class="form-control" name="region" id="region"
                                    placeholder=" " value="">

                            </div>

                            <div class="form-group col-md-4 col-sm-12">
                                <label for="campus_id">Campus*</label>
                                <select required name="campus_id" id="campus_id" class="form-select" placeholder=" ">
                                    <?php
                    foreach($rs_campus as $row_campus) {  
                    ?>
                                    <option value="<?php echo $row_campus['campus_id']?>">
                                        <?php echo htmlentities($row_campus['campus']);?></option>
                                    <?php
                      }
                    ?>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">

                            <div class="form-group col-md-4 col-sm-12">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder=" " value="">

                            </div>

                            <div class="form-group  col-md-4 col-sm-12">
                                <label for="office_id">Office*</label>
                                <select required name="office_id" id="office_id" class="form-select" placeholder=" "
                                    onchange="get_services();">
                                    <?php
                                        foreach($rs_office as $row_rsoffice) {  
                                        ?>
                                    <option value="<?php echo $row_rsoffice['office_id']?>">
                                        <?php echo htmlentities($row_rsoffice['office']);?></option>
                                    <?php
                                        }
                                        ?>
                                </select>
                            </div>

                            <div id="services_list" class="form-group  col-md-4 col-sm-12">
                                <label for="services_id">Service*</label>
                                <select required name="services_id" id="services_id" class="form-select"
                                    placeholder=" ">

                                </select>
                            </div>
                        </div>

                        <br>
                        <div class="form-group col-md-12 col-sm-12">
                            <label for="email">Suggestion</label>
                            <textarea class="form-control" name="suggestion" id="suggestion"></textarea>

                        </div>

                        <div class="form-group col-md-12 col-sm-12">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" id="email" placeholder=" ">

                        </div>
                        <br>
                        <p>
                        <strong>INSTRUCTIONS: Select your answer to the Citizen’s Charter (CC) questions. The
                            Citizen’s Charter is an official document that reflects the services of a government
                            agency/office including its requirements, fees and processing times among others.</strong>
                        </p>
                        <table id="tablelist" class="table table-striped table-hover table-responsive table-bordered">
                            <thead>
                                <tr class="alert-info">
                                    <th data-sortable="false"></th>
                                    <th data-sortable="false"></th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($rs_cc as $rs_ccdata){ 
                                    $query_rs = "select * FROM `survey_question_selection` sqs INNER JOIN survey_question sq ON sq.survey_question_id=sqs.survey_question_id WHERE sq.survey_question_id=? ORDER BY sqs.survey_question_selection_order ASC";
                                    $db->query($query_rs);
                                    $db->bind(1,htmlentities($rs_ccdata['survey_question_id']));
                                    $rs_ccq=$db->rowset();
                                    
                                    ?>
                                <tr>
                                    <td class="align-middle"><strong><?php echo htmlentities($rs_ccdata['survey_code']); ?></strong></td>
                                    <td class="align-middle"><strong><?php echo htmlentities($rs_ccdata['survey_question']); ?></strong><br><br>
                                    <?php foreach ($rs_ccq as $rs_ccqdata){ ?>
                                        <input class="form-check-input inline" type="radio" name="<?php echo $rs_ccdata['survey_code'] ?>" id="<?php echo $rs_ccdata['survey_code'] ?>" value="<?php echo htmlentities($rs_ccqdata['survey_question_selection_order']); ?>">&nbsp;<label class="form-check-label" for=""><?php echo htmlentities($rs_ccqdata['details']); ?></label><br>
                                    <?php } ?>

                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <br>
                        <p>
                        <strong>INSTRUCTIONS: For SQD 0-8, please put a check mark (/) on the column that best corresponds to your answer.</strong> 

                        </p>
                        <table id="tablelist" class="table table-striped table-hover table-responsive table-bordered">
                            <thead>
                                <tr class="alert-info">
                                    <th data-sortable="false"></th>
                                    <th data-sortable="false"></th>
                                    <th data-sortable="false">Strongly Disagree</th>
                                    <th data-sortable="false">Disagree</th>
                                    <th data-sortable="false">Neither Agree nor Disagree</th>
                                    <th data-sortable="false">Agree</th>
                                    <th data-sortable="false">Strongly Agree</th>
                                    <th data-sortable="false">Not Applicable</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($rs_sqd as $rs_sqddata){ ?>
                                <tr>
                                    <td class="align-middle"><strong><?php echo htmlentities($rs_sqddata['survey_code']); ?></strong></td>
                                    <td class="align-middle"><strong><?php echo htmlentities($rs_sqddata['survey_question']); ?></strong></td>
                                    <td class="align-middle" style="text-align:center"><input class="form-check-input" type="radio" name="<?php echo $rs_sqddata['survey_code'] ?>" id="<?php echo $rs_sqddata['survey_code'] ?>" value="1"></td>
                                    <td class="align-middle" style="text-align:center"><input class="form-check-input" type="radio" name="<?php echo $rs_sqddata['survey_code'] ?>" id="<?php echo $rs_sqddata['survey_code'] ?>" value="2"></td>
                                    <td class="align-middle" style="text-align:center"><input class="form-check-input" type="radio" name="<?php echo $rs_sqddata['survey_code'] ?>" id="<?php echo $rs_sqddata['survey_code'] ?>" value="3"></td>
                                    <td class="align-middle" style="text-align:center"><input class="form-check-input" type="radio" name="<?php echo $rs_sqddata['survey_code'] ?>" id="<?php echo $rs_sqddata['survey_code'] ?>" value="4"></td>
                                    <td class="align-middle" style="text-align:center"><input class="form-check-input" type="radio" name="<?php echo $rs_sqddata['survey_code'] ?>" id="<?php echo $rs_sqddata['survey_code'] ?>" value="5"></td>
                                    <td class="align-middle" style="text-align:center"><input class="form-check-input" type="radio" name="<?php echo $rs_sqddata['survey_code'] ?>" id="<?php echo $rs_sqddata['survey_code'] ?>" value="6"></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <br>
                        <div class="form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-10">
                                <button type="submit" class="btn btn-outline-primary" form="form1"><span
                                        class="bi-save"></span> Save</button>
                                <a href="index.php" class="btn btn-outline-danger hidelink"><span
                                        class="bi-x-octagon"></span> Clear</a>
                            </div>
                        </div>

                    </fieldset>
                </div>


                <input type="hidden" name="POSTcheck" value="form1">
            </form>

            <!--------------------------------------------------------------------------------->
        </div>
        <div class="card-footer"></div>
    </div>
    </div>
    </section>

    </main>

    <footer id="footer" class="footer">
        <div class="copyright">

            &copy; Copyright <strong><span><?php echo $app_copyright;?></span></strong>. All Rights Reserved. </div>
        <div class="credits"> <?php echo $tagline.' * '.$app_footer;?> </div>


    </footer>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up"></i></a>

    <script src="../lib/js/main.js"></script>

</body>

</html>
<?php
$db->close();
?>