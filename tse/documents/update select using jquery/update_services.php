<?php require_once('../connections/pdoconnect.php'); 

$phu=new php_util();
if (!isset($_SESSION)) {
    session_start();
  }
  
$menu_id=$phu->get_menu_id(basename($_SERVER['PHP_SELF']));
$db=new DatabaseConnect();

$office_id = $_POST['office_id'];

$return_value='';
$service_details='';
$service_details_option='';


$query_rs = "SELECT service_id,`service` FROM `service` s INNER JOIN office o ON o.office_id=s.office_id WHERE s.office_id=?";
$db->query($query_rs);
$db->bind(1,$office_id);
$rsservice=$db->rowset();
$rscount=$db->rowcount();

$return_value .=$rscount;

$service_details.='<label for="service_id">Service*</label>';
$service_details.='<select required name="service_id" id="service_id" class="form-select" placeholder=" ">';
foreach($rsservice as $row_rsservice) {  
    $service_option_value[] = $row_rsservice['service_id'];
    $service_option_text[] = $row_rsservice['service'];

    $service_details.='<option value="'.$row_rsservice['service_id'].'">';
    $service_details.=htmlentities($row_rsservice['service']).'</option>';

   

}
$service_details.='</select>';

$output = array(  
    'return_value'     =>     $return_value,
    'service_details'     =>     $service_details,  
    'service_option_value' =>json_encode($service_option_value),
    'service_option_text' =>json_encode($service_option_text)
);             
echo json_encode($output); 
?>