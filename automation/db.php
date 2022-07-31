<?php
    $host='localhost';
    $username='admin';
    $password='pointofsale';
    $dbname = "ospos";
    $conn=mysqli_connect($host,$username,$password,"$dbname");
    if(!$conn)
        {
          die('Could not Connect MySql Server:' .mysql_error());
        }
?>