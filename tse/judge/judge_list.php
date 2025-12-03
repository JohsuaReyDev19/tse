<?php ob_start(); ?>
<?php require_once('../connections/pdoconnect.php'); ?>

<?php
$phu=new php_util();
$menu_id=$phu->get_menu_id(basename($_SERVER['PHP_SELF']));

$db=new DatabaseConnect();

$query_rs = "select * FROM `judge_events` WHERE events_id=? order by `name`";
$db->query($query_rs);
$db->bind(1,$_GET['recordID']);
$rs=$db->rowset();

$query_rs = "select * FROM `events` WHERE events_id=? ";
$db->query($query_rs);
$db->bind(1,$_GET['recordID']);
$rs_event=$db->rowsingle();

?>
<!DOCTYPE html>
<html lang="en">
<head>

<title><?php echo $app_title; ?>  </title>
</head>
<?php require_once('../template/phplink.php'); ?>
<body >
<?php require_once('../template/header.php'); ?>

<div class="card">
<div class="card-header"><h5 class="card-title"><strong><?php echo 'Judges of '.htmlentities($rs_event['events_description']); ?></strong></h5>
<span class="small-text"><?php echo htmlentities($rs_event['events_date']); ?></span></div>
            <div class="card-body">
<!--------------------------------------------------------------------------------->

<a href='../events/events_list.php' class="btn btn-outline-secondary" data-toogle="tooltip" data-placement="bottom" title=""><span class=" bi bi-trophy-fill"></span> Events List</a>


<table id="tablelist" class="table table-striped table-hover table-responsive table-bordered" >
    <thead>
        <tr class="alert-info">
            <th>Name</th>
            
               <th data-sortable="false"  width="110px">
                <div class="btn-group" role="group" align="center">
                     <?php if (!(strcmp($phu-> set_button_group($_SESSION['AIT_MM_UserGroup'],"judge_new.php"),1))){ ?>
                    <a href="judge_new.php?recordID=<?php echo $_GET['recordID'];?>" class="btn btn-outline-secondary" data-toogle="tooltip" data-placement="bottom" title="New"><span class="bi-plus-square"></span> </a>
                    <?php } ?>
                    <!--<a type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalDialogPrint"><span class="bi-printer-fill"  data-toogle="tooltip" data-placement="bottom" title="Create Printable Form"></span></a>-->
                </div>
            </th>
        </tr>
    </thead>
    <tbody>

<?php foreach ($rs as $rs_data){ ?>
    <tr >
    <td class="align-middle"><?php echo htmlentities($rs_data['name']); ?></td>
   
    <td  class="align-middle" width="110px">
        <div class="btn-group" role="group" align="center">
             <?php if (!(strcmp($phu-> set_button_group($_SESSION['AIT_MM_UserGroup'],"judge_update.php"),1))){ ?>
            <a href="judge_update.php?recordID=<?php echo $rs_data['judge_id'];?>" class="btn btn-outline-success" data-toogle="tooltip" data-placement="bottom" title="Update"><span class="bi-pencil-square"></span></a>
            <?php } ?>
             
             <?php if (!(strcmp($phu-> set_button_group($_SESSION['AIT_MM_UserGroup'],"judge_remove.php"),1))){ ?>
            <a href='judge_remove.php?recordID=<?php echo $rs_data["judge_id"];?>' class="btn btn-outline-danger" data-toogle="tooltip" data-placement="bottom" title="Remove"><span class="bi-trash"></span></a>
            <?php }?>
        </div>
    </td>
    </tr>
<?php } ?>
</tbody>
</table>
<!--------------------------------------------------------------------------------->
</div>
    <div class="card-footer"></div>
</div>
<?php require_once('../template/footer.php'); ?>

<script>
        const labelData = {
                placeholder: "Hanapin...",
                noRows: "No record to display",
                info: "Showing {start} to {end} of {rows} record (Page {page} of {pages} pages)"
            }


    const dataTable = new simpleDatatables.DataTable("#tablelist", {
        searchable: true,
        fixedHeight: true,
        //perPage: 25,
        //labels: labelData,
    });

    //dataTable.columns().hide([1, 2])
    //dataTable.columns().remove([0, 2, 3, 6]);
    //dataTable.columns().order([0, 2, 1]);
    
</script>



</body>

</html>
<?php ob_flush(); 
$db->close();
?>
