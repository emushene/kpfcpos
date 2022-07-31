<?php
/***
	PHP MYSQL EMAIL REPORTER
	Script Written by Julian Young at Jellyhound
	SQLREPORTER is Copyrighted to Jellyhound 2011-2017 - Not to be resold.
	PHPMailer is Distributed under the Lesser General Public License (LGPL)
	TCPDF is Distributed under the Lesser General Public License (LGPL)
		
	Website: http://www.jellyhound.co.uk
	
	Version: 4.6 - JAN 2017
	
	If you have any queries please do not hesitate to email me at help@jellyhound.co.uk
***/

include ('config.php');
require_once('PHPMailer/class.phpmailer.php');
require_once('PHPMailer/class.smtp.php');
include ('sqlreporter.php');


//Color is the color report table, it can be set to be 'red', 'grey', 'green', 'blue' or 'compatibility'
$color = 'blue';



/***

FOR ADVANCED USERS

THIS EXAMPLE SCRIPT IS DESIGNED TO DEMONSTRATE SENDING MULTIPLE EMAILS TO CLIENTS BY LOOPING THROUGH ONE TABLE AND REPORTING ON ANOTHER

VISIT OUR REPORTER PAGE AT JELLYHOUND.CO.UK TO SEE MORE INFORMATION ON THIS EXAMPLE

***/

//Setup connection for main loop, here we will be looping through customers, note you can use this to connect to a completely seperate database if you wish
$con=mysqli_connect('localhost','username','password','database');

// Check connection is working
if (mysqli_connect_errno())
{
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

//Get list of clients
$client_list = mysqli_query($con,"SELECT firstName, customerNo, email FROM test_data");

//Loop through clients and report on them, we can actually store the email address and send an individual email to each customer
while($row = mysqli_fetch_array($client_list))
{
	//create custom subject for each email
	$subject = 'Hi '.$row['firstName'].', here is what you bought this month';

	//create custom header for each report
	$header= '<h3>Report for '.$row['firstName'].'</h3><p>Customer ID '.$row['customerNo'].'</p>';
	
	//use a standard footer
	$footer = 'Your monthly report from Jellyhound';
	
	//get data for this report
	$query = 'SELECT * FROM test_sales WHERE customerNo="'.$row["customerNo"].'"';

	//generate the report for this customer
	$report = generateReport($query, $header, $footer, $color); //Generate detail report
	//echo $report //when testing the script, you may want to echo your report out and disable the email below!
	
	//When testing your report generation, you may want to hardcode your own email address in for testing purposes
	html_email($row["email"],$subject,$report);
}

mysqli_close($con);

?>