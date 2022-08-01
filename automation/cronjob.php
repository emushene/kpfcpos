session_start();  
$s = $_POST['$yesterday'];  
$e = $_POST['$today'];  
$i = $_SESSION['user'];  
$m = "VACANCY APPLICATIONS REPORT FROM $s TO $e \r\n\r\n";  
$conn = mysqli_connect("");

$em = "email address";  
$vrquery = "SELECT jobid,role,jobtype,vacancyref FROM job ORDER BY jobid";    

$vrresult = mysqli_query($conn, $vrquery);  
while($vrrow=mysqli_fetch_array($vrresult))  
{
$m = $m . $vrrow['vacancyref'] . " " . $vrrow['role'] . " " . $vrrow['jobtype'] .    "\r\n\r\n";  
$vquery = "SELECT cname,ctel,cemail FROM candidatejob,cv,candidate WHERE           jobid=".$vrrow['jobid']." AND candidatejob.cvid=cv.cvid AND cv.cid=candidate.cid AND   cvdate>='$s' AND cvdate<='$e' ORDER BY cname";  
$vresult = mysqli_query($conn, $vquery);  
while($vrow=mysqli_fetch_array($vresult))  
{  
    $m = $m . $vrow['cname'] . " " . $vrow['ctel'] . " " . $vrow['cemail'] .   "\r\n";  
}  
$m = $m . "\r\n\r\n";  
}
$m = $m . "\r\n\r\nALL CANDIDATES SUBMITTING CVs WITHIN DATES\r\n\r\n";
$cvquery = "SELECT cname,ctel,cemail FROM cv,candidate WHERE cv.cid=candidate.cid AND     cvdate>='$s' AND cvdate<='$e' ORDER BY cname";
$cvresult = mysqli_query($conn, $cvquery);
while($cvrow=mysqli_fetch_array($cvresult))
{
    $m = $m . $cvrow['cname'] . " " . $cvrow['ctel'] . " " . $cvrow['cemail'] . "\r\n";
}

$conn->close();  


$headers = "From: no-reply@nortech.org.uk\r\nX-Mailer: PHP/" . phpversion();  
mail($em, " Daily Job Applications Report", $m, $headers);  

?>  