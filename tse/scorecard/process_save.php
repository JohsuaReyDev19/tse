<?php require_once('../connections/pdoconnect.php'); 


$db = new DatabaseConnect();

$score          =$_POST['score'];
$participant_id =$_POST['participant_id'];
$category_id    =$_POST['category_id'];
$events_id      =$_POST['events_id'];
$judge_id       =$_POST['judge_id'];

//$return_msg="";


$query_rs = "SELECT * FROM  score  WHERE events_id=? AND judge_id=? AND category_id=? AND participant_id=?";
$db->query($query_rs);
$db->bind(1,$events_id);
$db->bind(2,$judge_id);
$db->bind(3,$category_id);
$db->bind(4,$participant_id);
$rstemp=$db->rowsingle();
$rstemp_count=$db->rowcount();

if ($rstemp_count>0){
    $SQLcrud = "UPDATE score SET category_score=? WHERE score_id=?";
    $db->query($SQLcrud);
    $db->bind(1,$score);
    $db->bind(2,$rstemp['score_id']);
    $db->execute();
    //$return_msg="Score has been Saved!";
}else{
    $SQLcrud = "INSERT INTO score (events_id,judge_id,category_id,participant_id,category_score) VALUES (?,?,?,?,?)";
    $db->query($SQLcrud);
    $db->bind(1,$events_id);
    $db->bind(2,$judge_id);
    $db->bind(3,$category_id);
    $db->bind(4,$participant_id);
    $db->bind(5,$score);
    $db->execute();
    //$return_msg="Score has been Updated!";
}



//$output = array(  
//    'return_msg' => $return_msg
//);             
//echo json_encode($return_msg);
?>
