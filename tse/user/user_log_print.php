
<?php require_once('../template/header_print.php');?>
<div align="center">
  <strong>User List</strong><br>
  <?php echo 'Date Printed : '.date('F d, Y');?>

<table id="tablelist_print" class="table table-bordered" width="100%">
  <thead >
    <tr class="alert-info">
      <th>#</th>
      <th>Full Name</th>
      <th>User Group</th>
      <th>Time</th>
      <th>Remarks</th>
    </tr>
  </thead>
  <tbody>
  <?php $ctr=0; foreach ($rs as $rs_user) { $ctr++; ?>
    <tr>
        <td class="align-middle"><?php echo $ctr; ?>.</td>
        <td class="align-middle"><?php echo htmlentities($rs_user['fullname']); ?></td>
        <td class="align-middle"><?php echo htmlentities($rs_user['designation']); ?></td>
        <td data-sortable="false" class="align-middle"><?php echo date_format(date_create($rs_user['log_time']),'l, F d, Y h:i:s A'); ?></td>
        <td class="align-middle"><?php echo htmlentities($rs_user['remarks']); ?></td>
    </tr>
  <?php } ?>
  </tbody>
</table>
</div>
<?php require_once('../template/footer_print.php');?>
