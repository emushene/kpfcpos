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

/****** EMAIL CONFIGURATION ******/

//Setup Email Subject
$subject = 'Subject of email';

//Color is the color report table, it can be set to be 'red', 'grey', 'green', 'blue' or 'compatibility'
$color = 'blue';

//Add a message to the header of the report, use plain text, or better still HTML and link to images
$header = '<img src="http://www.julian-young.com/mysqlreporter/gfx/report-header-image.png"/><h3>REPORT NAME</h3>';

//Add a message to the footer of the report, use plain text, or better still HTML and link to images
$footer = '<br /><br /><img src="http://www.julian-young.com/mysqlreporter/gfx/report-footer-image.png"/>';

/****** DATABASE QUERY CONFIGURATION ******/

//The Database Query to Run - Recommend Testing in SQL / PHPMYADMIN / Other tool beforehand to ensure syntax is correct
$query = 'SELECT firstName AS "First Name", lastName AS "Surname", zipCode AS "ZIP", telephone AS "Telephone", numberofpurchases AS "Number of Purchases" FROM test_data WHERE firstname = "test" ORDER BY lastName';
//$query = 'SELECT firstName AS "First Name", lastName AS "Last Name" FROM userinfo ORDER BY lastName DESC';
//$query = 'CALL GetAllProducts'; //or use a stored procedure, you can create a stored procedure within PHPMyAdmin / MySQL

/****** RUN REPORT ******/

//Generate the report (Do not change)
$report = generateReport($query, $header, $footer, $color);

//Setup Email Recipient (Who are we sending the report to)
$recipient1 = 'someone@somewhere.com';
//$recipient2 = 'person2@somedomain.com';
//$recipient3 = 'person3@somedomain.com';

//Send the report (if you uncomment out recipients above then ensure you uncomment the corresponding html_email below
html_email($recipient1,$subject,$report);

?>