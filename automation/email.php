<?php
  
 
$dbh = new PDO('mysql:host=localhost;
user=admin;
password=pointofsale;
dbname=ospos');

$sql = 'SELECT person_id FROM ospos_suppliers ORDER BY company_name';
foreach ($conn->query($sql) as $row) {
    print $row['person_id'] . "\t";
    
}

  
// Closing connection

?>