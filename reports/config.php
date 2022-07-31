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

/* ===== OPTIONAL FIELDS, RECOMMEND LEAVING ERRORS ON ===== */
ini_set('display_errors','1');
error_reporting(E_ALL);
define('MY_CHARSET', 'ISO-8859-1'); //You do not need to change this, some may prefer to use UTF-8 however
define('FILE_PUT_CONTENTS_ATOMIC_TEMP', dirname(__FILE__).'/cache'); 
define('FILE_PUT_CONTENTS_ATOMIC_MODE', 0777); 
/* ======================================================== */

/* ===== CHANGE THESE TO SUIT ===== */

//TEST MODES
define('TEST_MODE', true); //Set to true when configuring, false when you want to go live
define('SMTP_TEST_MODE', false); // set to true to debug your smtp (only works when TEST_MODE is false and USE_SMTP is true)

//DATABASE CONNECTION
define('DB_TYPE', 'mysql'); //database type
define('DB_SERVER', "localhost"); //database server, normally "localhost"
define('DB_USER', "root"); ////database login name, enter the username
define('DB_PASS', "pointofslae"); //database login password, enter the password
define('DB_DATABASE', "ospos"); //enter the name of the database you want to connect to

//EMAIL CONNECTION
define('USE_SMTP', false); //set to true if you want to use manual SMTP details below
define('SMTP_HOST', 'smtp.yourhost.com');
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTPSECURE', ''); //disable by setting to empty '', or enable with 'tsl' or 'ssl'
define('SMTP_PORT', 25);
define('SMTP_AUTH', true); //set to false only if you have no need for SMTP username and password

//EMAIL PREFERENCES
define('FROM_EMAIL', "muinamuson@gmail.com"); //Define your standard from address for emails, can be no-reply@yourdomain.com for example
define('FROM_NAME', 'Daily Sales Generator'); //Define the 'from name' in the email
define('SEND_EMAIL_IF_NO_RESULTS', true); //If true, will repress email if query returns no results
define('NO_RESULTS_MESSAGE', 'There were no sales today'); //If your report generates no results, what should your email say?

//ATTACHMENT PREFERENCES
define('ATTACHMENT_TYPE', 'CSV'); //Set to NONE, CSV, PDF, BOTH
define('CSV_FILENAME', 'report.csv'); //Set filename of CSV
define('PDF_FILENAME', 'report.pdf'); //Set filename of CSV

//PDF PREFERENCES
define('SET_PDF_AUTHOR', 'Jellyhound');
define('SET_PDF_TITLE', 'My Report Name');
define('SET_PDF_ORIENTATION', 'P'); //P FOR PORTRAIT, L FOR LANDSCAPE

/* =============================== */

?>