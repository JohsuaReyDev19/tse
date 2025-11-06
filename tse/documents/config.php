<?php
  $hostname = "127.0.0.1";
  $username = "dorm2go_user";
  $password = "dPQ.[Slv((=2";
  $dbname = "dorm2go_db";

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if(!$conn){
    echo "Database connection error".mysqli_connect_error();
  }
?>
