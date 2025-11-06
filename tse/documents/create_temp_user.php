<?php ob_start(); ?>
<?php require_once('../connections/pdoconnect.php'); ?>

<?php
$phu=new php_util();

$db=new DatabaseConnect();

require_once('../lib/phplib/phpdotenv/autoload.php');
//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv = Dotenv\Dotenv::createImmutable('../../motolog_env');
$dotenv->safeLoad();

$totalRows_rscheck =0;
$key = substr(base64_encode(sodium_crypto_secretbox_keygen()),0,32);
$nonce = substr(base64_encode(random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES)),0,24);

$blockSize = 64;

echo 'nonce= '.$nonce.'<br>';
echo 'key= '.$key.'<br>';


  $paddedMessage = sodium_pad('tempadmin@tempadmin.tempadmin', $blockSize);
  $encryptedMessage = sodium_crypto_secretbox($paddedMessage, $nonce, $key);
  $encryptedMessage=base64_encode($encryptedMessage);

 	$SQLcrud = "INSERT INTO user (fullname, username, password, designation, `group`, `status`) VALUES (?,?,?,?,?,?)";
	$db->query($SQLcrud);
    $db->bind(1,'tempadmin@tempadmin.tempadmin');
	$db->bind(2,'tempadmin@tempadmin.tempadmin');
	$db->bind(3,$encryptedMessage);
	$db->bind(4,'Administrator');
    $db->bind(5,'Administrator');
    $db->bind(6,'active');
    
	$db->execute();
    
    ?>