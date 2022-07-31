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
 

/*******************************************************************
	generateReport($sQuery, $header, $footer, $color)
	================================================================
	@PARAMETERS
	$reportName - text - name of report
	$sQuery - text - the SQL query
	$header - html - header message displayed above the report
	$footer - html - footer message displayed under the report
	$color - text - The colour of the report ("grey", "green" or "blue")
	********************************************************************/

function generateReport($sQuery, $header, $footer, $color) {
	
	$color = strtolower($color);
	
	if ($color != 'red' && $color != 'compatibility' && $color != 'green' && $color != 'blue' && $color != 'grey')	{
		$color = 'grey';	
	}

	//Display configuration information if in test mode
	if (TEST_MODE) {
		echo '<h1>MySQL PHP Reporter - Test Mode</h1>';	
		echo '<span style="color:#f00;"><b>Report running in test mode.</b></span><br />Disable test mode in the config.php when you are ready for the report to go live.<br /><br />';	
		echo '<table style="width:100%">';
		echo '<tr><td style="background:#EEE;padding:20px;"><h3>Database Connection</h3>';
		echo '<b>Target MySQL Server: </b>'.DB_SERVER.'</br >';
		if (DB_DATABASE == '') {
			$databaseMsg = '<span style="color: #f00;">Setup in your config.php</span>';
		} else {
			$databaseMsg = DB_DATABASE;
		}
		echo '<b>Target Database: </b>'.$databaseMsg.'</br >';	
		if ($sQuery == '' || $sQuery == 'SELECT firstName AS "First Name", lastName AS "Last Name" FROM userinfo ORDER BY lastName DESC' ) {
			$queryMsg = '<span style="color: #f00;">Setup in your report.php</span>';
		} else {
			$queryMsg = $sQuery;
		}		
		echo '<b>SQL query for report: </b>'.$queryMsg.'</br >';	
		echo '</td></tr>';
		
		echo '<tr><td style="background:#EEE;padding:20px;"><h3>Report Preferences</h3>';
		echo '<b>Report Color: </b><span style="color:'.$color.'">'.$color.'</span></br >';
		if ($header != '') {
			echo '<b>Use report header: </b>YES</br >';
		} else {
			echo '<b>Use report header: </b>NONE SET</br >';
		}
		if ($footer != '') {
			echo '<b>Use report footer: </b>YES</br >';
		} else {
			echo '<b>Use report footer: </b>NONE SET</br >';
		}	
		if (USE_SMTP) {
			echo '<b>Use SMTP: </b>TRUE</br >';
		} else {
			echo '<b>Use SMTP: </b>FALSE</br >';
		}
		if (SMTP_TEST_MODE && USE_SMTP) {
			echo '<b style="color:#ff0000">Warning: </b>SMTP test mode is set to true, to see the SMTP test results you need to set TEST_MODE to false in your config.php and re-run the report</br >';
		}
		if (strtoupper(ATTACHMENT_TYPE)=='PDF' || strtoupper(ATTACHMENT_TYPE)=='BOTH') {
			echo '<b>Attach PDF to email: </b>YES</br >';
		} else {
			echo '<b>Attach PDF to email: </b>NO</br >';
		}			
		
		if (strtoupper(ATTACHMENT_TYPE)=='CSV' || strtoupper(ATTACHMENT_TYPE)=='BOTH') {
			echo '<b>Attach CSV to email: </b>YES</br >';
		} else {
			echo '<b>Attach CSV to email: </b>NO</br >';
		}			
		
		if (SEND_EMAIL_IF_NO_RESULTS) {
			echo '<b>Send Email if report returns no results : </b>YES</br >';
		} else {
			echo '<b>Send Email if report returns no results : </b>NO</br >';
		}			
		echo '</td></tr></table>';
	
		
	}
	
	//setup variable function pointers based on choice of color
	switch ($color)
		{
		case 'red':
			$h1Class 		 = 'h1Red';
			$tableClass  	 = 'tableRed';
			$thClass 	 	 = 'thRed';
			$trClass0	 	 = 'tr0Red';
			$trClass1 	 	 = 'tr1Red';
			$tdClass0		 = 'td0Red';
			$tdClass1		 = 'td1Red';
			break;			
		case 'green':
			$h1Class 		 = 'h1Green';
			$tableClass  	 = 'tableGreen';
			$thClass 	 	 = 'thGreen';
			$trClass0	 	 = 'tr0Green';
			$trClass1 	 	 = 'tr1Green';
			$tdClass0		 = 'td0Green';
			$tdClass1		 = 'td1Green';
			break;
		case 'blue':
			$h1Class 		 = 'h1Blue';		
			$tableClass 	 = 'tableBlue';
			$thClass		 = 'thBlue';
			$trClass0		 = 'tr0Blue';
			$trClass1		 = 'tr1Blue';
			$tdClass0		 = 'td0Blue';
			$tdClass1		 = 'td1Blue';
			break;
		case 'grey':
			$h1Class 		 = 'h1Grey';
			$tableClass 	 = 'tableGrey';
			$thClass		 = 'thGrey';
			$trClass0		 = 'tr0Grey';
			$trClass1		 = 'tr1Grey';
			$tdClass0		 = 'td0Grey';
			$tdClass1		 = 'td1Grey';
			break;
		case 'compatibility':
			$h1Class 		 = 'h1Compatibility';
			$tableClass 	 = 'tableCompatibility';
			$thClass		 = 'thCompatibility';
			$trClass0		 = 'tr0Compatibility';
			$trClass1		 = 'tr1Compatibility';
			$tdClass0		 = 'td0Compatibility';
			$tdClass1		 = 'td1Compatibility';
			break;				
		default:
			$tableClass 	 = 'tableGrey';
			$thClass		 = 'thGrey';
			$trClass0		 = 'tr0Grey';
			$trClass1		 = 'tr1Grey';
			$tdClass0		 = 'td0Grey';
			$tdClass1		 = 'td1Grey';
		}
	
	//Initialise our report
	$report = '';
	
	//initialise csv export
	$fileContents = "";
	$data = "";
	
	//Add Header Message
	$report .= '<p>'.$header.'</p>';	

	//Connect to SQL Server and Database
	try {
		$link = new PDO(DB_TYPE.":host=".DB_SERVER.";dbname=".DB_DATABASE.";charset=".MY_CHARSET, DB_USER, DB_PASS);
	}
		catch(PDOException $e)
	{
		$report .= '<p>An error occured connecting to the SQL Server</p>';
		$report .= '<p>'.$e->getMessage().'</p>';
		if (TEST_MODE) {
			echo '<table style="width:100%">';
			echo '<tr><td style="color:#fff;background:#FF0000;padding:20px;"><h3>SQL Error</h3>';						
			echo '<p>An error occured connecting to the SQL Server</p>';
			echo '<p>'.$e->getMessage().'</p>';
			echo '</td></tr></table>';			
		}
		return $report;	
	}
	
	$link->setAttribute(PDO::ATTR_EMULATE_PREPARES,false); 	
	$result = $link->prepare($sQuery);	

	if (!$result) {
		$report .= '<p>An error occured with your SQL statement, activate test mode in your config.php to debug</p>';
		if (TEST_MODE) {	
			echo '<table style="width:100%">';
			echo '<tr><td style="color:#fff;background:#FF0000;padding:20px;"><h3>SQL Error</h3>';			
			echo '<p>An error occured connecting to the SQL Server</p>';
			echo "\nPDO::errorInfo():\n";
			print_r($link->errorInfo());
			echo '</td></tr></table>';
		}
		return $report;	
	}	
	else {
		//Execute User Query
		$result->execute();
	}

	//Check no of rows
	$countRows = $result->rowCount();
	
	//process results and generate report		
	if ($countRows == 0) {
		if (!SEND_EMAIL_IF_NO_RESULTS)
		{
			$report = false;
			if (TEST_MODE) {	
				echo '<table style="width:100%">';
				echo '<tr><td style="color:#fff;background:#FF0000;padding:20px;"><h3>No Results</h3>';								
				echo '<p>Your query returned no results so no email will be sent.</p><p>To generate an email even if there are no results, set SEND_EMAIL_IF_NO_RESULTS to true in your config.php</p>';
				echo '</td></tr></table>';
			}
			return $report;
		}
		else
		{
			$report .= '<p>'.NO_RESULTS_MESSAGE.'</p>';
		}
	} else {
		//Get Field Names (Used as Table Headers)
		$rows = array();
		while($records = $result->fetch(PDO::FETCH_ASSOC))
		{
				$keys = array_keys($records);
				$fieldArray = $keys;			
				$rows[] = $records;													
		}		
		
		
		// ***************************************** CSV ATTACHMENT CODE
		// GENERATE CSV
		
		//create and attach CSV file
		if (strtoupper(ATTACHMENT_TYPE)=='CSV' || strtoupper(ATTACHMENT_TYPE)=='BOTH')
		{	
			
			// Generate CSV File
			$CSVHeader = '';

			// Create CSV Header
			for ($i=0;$i<count($fieldArray);$i++)
			{
				$CSVHeader .= $fieldArray[$i] . ",";
			}
			
			//Clean header
			$CSVHeader = trim( $CSVHeader ) . "\r\n";
			
			//Create CSV Data
			$rowNo = 0;
			//For each row... (stepping through rows)
			foreach ($rows as $row)
			{
				//step through all fields for this row
				for ($i=0;$i<count($fieldArray);$i++)
				{
					$line = '';
					foreach( $row as $value )
					{                                            
						if ( ( !isset( $value ) ) || ( $value == "" ) )
						{
							$value = ",";
						}
						else
						{
							$value = str_replace( '"' , '""' , $value );
							$value = '"' . $value . '"' . ",";
						}
						$line .= $value;
					}
				}	
				$data .= trim( $line ) . "\r\n";					
			}
				
			if ( $data == "" )
			{
			    $data = "\n(0) Records Found!\n";                        
			}	
			
			//prepend header
			$data = $CSVHeader.$data;

			if (TEST_MODE)
			{
				$GLOBALS['csv_debug'] = $data;
			}
			
			
			//Save csv export to file			
			$attachSuccess = file_put_contents_atomic('report.csv', $data);		
		
		}
		
		// ***************************************** END OF CSV ATTACHMENT CODE

	
		// ***************************************** GENERATE HTML 
		// GENERATE HTML REPORT	
		
		
		//Build Table Headers
		if ($color == 'compatibility')
		{
				$report.= '<table border="1" '.$tableClass().'><tr>';
		} else {
				$report.= '<table border="1" width="100%" frame="box" rules="cols" cellspacing="0" cellpadding="10" '.$tableClass().'><tr>';
		}
		
		for ($i=0;$i<count($fieldArray);$i++) {
			$report.= '<th '.$thClass().'><strong>'.$fieldArray[$i].'</strong></th>';
		}
		$report.= '</tr>';		


		// Create Data
		$rowNo = 0;
		foreach ($rows as $row) {
					
			$rowNo = 1 - $rowNo;
			if ($rowNo == 0) {
				$rowStyle = $trClass0();
				$cellStyle = $tdClass0();
			} else {
				$rowStyle = $trClass1();
				$cellStyle = $tdClass1();
			}
			
			$report .= '<tr '.$rowStyle.'>';
			
		
			//step through fields
			for ($i=0;$i<count($fieldArray);$i++) {
				
				$report .= '<td '.$cellStyle.'>'.$row[$fieldArray[$i]].'</td>';
			
			}	
			
			$report .= '</tr>';
			
		}
		
		$report.= '</table>';	
	}
	
	//Add Footer
	$report .= '<p>'.$footer.'</p>';
	
	
	//CSV ERROR OUTPUT
	if (TEST_MODE)
	{			
		if (strtoupper(ATTACHMENT_TYPE)=='CSV' || strtoupper(ATTACHMENT_TYPE)=='BOTH')
		{
			echo '<div style="padding:20px;"><b>Attempting to write CSV file to current directory:</b> ';

			if ($countRows == 0)
			{
				echo '<span style="color:red">No records found so no CSV report generated</span>';
				
			}
			else
			{
				if (isset($attachSuccess) && $attachSuccess == true) {
					echo '<span style="color:green">Success</span>';
				} else {
					echo '<span style="color:red">Failed - ensure the scripts directory is writable</span>';
				}	
			}

			echo '</div>';
		}
	}

	
	
	// ***************************************** PDF ATTACHMENT CODE
	// GENERATE PDF - INSERT PREVIOUSLY GENERATED HTML
	
	//create and attach PDF file
	if (strtoupper(ATTACHMENT_TYPE)=='PDF' || strtoupper(ATTACHMENT_TYPE)=='BOTH')
	{	
		
		require_once('tcpdf/tcpdf.php');
		ob_start();		
		
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(SET_PDF_AUTHOR);
		$pdf->SetTitle(SET_PDF_TITLE);
		$pdf->SetSubject('Report');
		$pdf->setPrintHeader(false);
		//$pdf-setPageMark();
		$pdf->setPageOrientation( SET_PDF_ORIENTATION,'','');	//PDF_ORIENTATION
				
		// add a page
		$pdf->AddPage();
		
		$pdf->writeHTML($report, true, false, true, false, '');
		
		// ---------------------------------------------------------
		
		//Close and output PDF document
		$pdf->Output($_SERVER['DOCUMENT_ROOT'].'/'.dirname($_SERVER['PHP_SELF']).'/'.PDF_FILENAME, 'F');	
				
		//Save PDF to file			
		$attachSuccess = file_exists($_SERVER['DOCUMENT_ROOT'].'/'.dirname($_SERVER['PHP_SELF']).'/'.PDF_FILENAME);
	
		if (TEST_MODE)
		{			
			if (strtoupper(ATTACHMENT_TYPE)=='PDF' || strtoupper(ATTACHMENT_TYPE)=='BOTH')
			{
			echo '<div style="padding-left:20px;"><b>Attempting to write PDF file to current directory:</b> ';
			if ($attachSuccess) {
				echo '<span style="color:green">Success</span>';
			} else {
				echo '<span style="color:red">Failed - ensure the scripts directory is writable</span>';
			}	
			echo '</div>';
			}
		}	
	
	}
	// ***************************************** END OF PDF ATTACHMENT CODE			
	
	
	$link = null;	
	return $report;
}



//	Generate HTML Email
function html_email($to,$subject,$msg) {
	
	if (!TEST_MODE) 
	{
		$mail = new PHPMailer();
		$body = str_replace("<","\r\n<", $msg);
		
		if (USE_SMTP)
		{
			 $mail->IsSMTP();
		}
		 
		//SMTP DEBUGGING - Output connection log files
		if (SMTP_TEST_MODE && USE_SMTP)
		{
			echo '<h2>PHP Mailer SMTP Debug Enabled</h2><p>Disable in config.php when you are happy your emails are sending correctly</p><pre>';
			$mail->SMTPDebug = 2; //if SMTP Test Mode active then enable debugging
		}		 

		if (USE_SMTP)
		{
			$mail->SMTPAuth   = SMTP_AUTH;
			$mail->Host       = SMTP_HOST;
			$mail->Port       = SMTP_PORT;
			$mail->Username   = SMTP_USERNAME;
			$mail->Password   = SMTP_PASSWORD;
		}
		
		$mail->SetFrom(FROM_EMAIL, FROM_NAME);
		 
		if (strtoupper(ATTACHMENT_TYPE)=='CSV' || strtoupper(ATTACHMENT_TYPE)=='BOTH')
		{
			$mail->AddAttachment('report.csv');
		}
		
		if (strtoupper(ATTACHMENT_TYPE)=='PDF' || strtoupper(ATTACHMENT_TYPE)=='BOTH')
		{
			$mail->AddAttachment('report.pdf');
		}				
	 
		 $mail->AddReplyTo(FROM_EMAIL,FROM_NAME);
		 $mail->Subject    = $subject;
		 $mail->AltBody    = "To view the message, please use an HTML compatible email viewer";
		 $mail->MsgHTML($body);
		 $mail->AddAddress($to, "");
		 
		if (SMTP_TEST_MODE && USE_SMTP) //close pre (formatting) if in test mode
		{
			echo '</pre>';
		}		 
		 
		
		//CHECK IF WE SHOULD SEND EMAIL
		if (!SEND_EMAIL_IF_NO_RESULTS && !$msg)
		{
			echo 'SEND_MAIL_IF_NO_RESULTS is set to false and there were no results! Supressing email';
		}
		else
		{
			if(!$mail->Send()) {
				echo 'The mail to '.$to.' failed to send. Check your SMTP settings in config.php<br />';
			} else {
				echo 'The mail was sent successfully to '.$to.'.<br />';
			}	
		}
			 
	} else {
		if (SEND_EMAIL_IF_NO_RESULTS || $msg)
		{
			echo '<table style="margin-top:20px;width:100%">';
			echo '<tr><td style="background:#e4e5e0;padding:20px;">';
			echo '<h3>Email Preview</h3>';
			echo '<i>Configure your email, report and other details in <strong>report.php</strong>.</i><br /><br />';
			echo '<div style="background-color:#efefef;padding:20px;display:inline-block;border:3px solid #c1b2c0;">';
			echo '<span style="color:#AAA"><strong>Email To:</strong></span> '.$to.'<br />';
			echo '<span style="color:#AAA"><strong>Subject:</strong></span> '.$subject.'</br>';
			echo '<span style="color:#AAA"><strong>Date:</strong></span> '.date('l jS \of F Y h:i:s A').'</br></br>';
			echo '<div style="padding:20px;background:#fff;border:3px solid #414290;">';
			echo $msg.'</div></div></div>';
			if (strtoupper(ATTACHMENT_TYPE)=='CSV' || strtoupper(ATTACHMENT_TYPE)=='BOTH')
			{
				if (isset($GLOBALS['csv_debug'])) {
					echo '<h3>CSV Output</h3>';
					echo '<pre>'.$GLOBALS['csv_debug'].'</pre>';
					echo '<a href="http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/'.CSV_FILENAME.'">Download CSV</a>';
				}
			}
			if (strtoupper(ATTACHMENT_TYPE)=='PDF' || strtoupper(ATTACHMENT_TYPE)=='BOTH')
			{
				echo '<h3>PDF Output</h3>';
				echo '<a href="http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/'.PDF_FILENAME.'">Preview PDF</a>';
			}	
			echo '</td></tr></table>';
		}
	}
}	
	

	
	
function file_put_contents_atomic($filename, $content)
{ 
   
    $temp = tempnam(FILE_PUT_CONTENTS_ATOMIC_TEMP, 'temp'); 
    if (!($f = @fopen($temp, 'wb')))
    { 
        $temp = FILE_PUT_CONTENTS_ATOMIC_TEMP . DIRECTORY_SEPARATOR . uniqid('temp'); 
        if (!($f = @fopen($temp, 'wb')))
        { 
            trigger_error("file_put_contents_atomic() : error writing temporary file '$temp'", E_USER_WARNING); 
            return false; 
        } 
    } 
   
    fwrite($f, $content); 
    fclose($f); 
   
    if (!@rename($temp, $filename)) { 
        @unlink($filename); 
        @rename($temp, $filename); 
    } 
   
    @chmod($filename, FILE_PUT_CONTENTS_ATOMIC_MODE); 
   
    return true; 
   
} 

	
	
/* INLINE BLUE STYLE SHEET SCHEME FOR HTML EMAIL */
function h1Blue(){
	$style = '';
	return $style;
}
function tableBlue(){
	$style = 'style="font-family: Arial, Helvetica, sans-serif;border-collapse: collapse; white-space: nowrap;"';
	return $style;
}
function thBlue(){
	$style = 'bgcolor="#37475f" style="white-space: nowrap; border:1px solid #000; font-weight: bold; font-size: 100%; background-color: #37475f; color: #fff; text-align: left;"';
	return $style;
}
function tr0Blue() {
	$style = 'bgcolor="#f2f5f8" style="background-color: #f2f5f8; color: #000; font-weight: normal;"';
	return $style;
}
function tr1Blue() {
	$style = 'bgcolor="#dfe4e9" style="background-color: #dfe4e9; color: #000; font-weight: normal;"';
	return $style;
}
function td0Blue() {
	$style = 'style="white-space: nowrap; text-align: left;"';
	return $style;
}
function td1Blue() {
	$style = 'style="white-space: nowrap; text-align: left;"';
	return $style;
}
function headerBlue() {
	$style = '';
	return $style;
}
function footerBlue() {
	$style = '';
	return $style;
}
/* END BLUE COLOR SCHEME */




/* INLINE GREEN STYLE SHEET SCHEME FOR HTML EMAIL */
function h1Green(){
	$style = '';
	return $style;
}
function tableGreen(){
	$style = 'style="border-collapse: collapse; white-space: nowrap;"';
	return $style;
}
function thGreen(){
	$style = 'bgcolor="#4caf50" style="white-space: nowrap; border:1px solid #ddd; font-weight: bold; font-size: 100%; background-color: #4caf50; color: #fff; text-align: left;"';
	return $style;
}
function tr0Green() {
	$style = 'bgcolor="#ffffff" style="background-color: #ffffff; color: #000; font-weight: normal;"';
	return $style;
}
function tr1Green() {
	$style = 'bgcolor="#f2f2f2" style="background-color: #f2f2f2; color: #000; font-weight: normal;"';
	return $style;
}
function td0Green() {
	$style = 'style="border:1px solid #ddd;white-space: nowrap; text-align: left;"';
	return $style;
}
function td1Green() {
	$style = 'style="border:1px solid #ddd;white-space: nowrap; text-align: left;"';
	return $style;
}
function headerGreen() {
	$style = '';
	return $style;
}
function footerGreen() {
	$style = '';
	return $style;
}
/* END Green COLOR SCHEME */






/* INLINE Red STYLE SHEET SCHEME FOR HTML EMAIL */
function h1Red(){
	$style = '';
	return $style;
}
function tableRed(){
	$style = 'style="font-family: Arial, Helvetica, sans-serif;border-collapse: collapse; white-space: nowrap;"';
	return $style;
}
function thRed(){
	$style = 'bgcolor="#b92832" style="white-space: nowrap; border: 1px solid #ddd;; font-weight: bold; font-size: 100%; background-color: #b92832; color: #fff; text-align: left;"';
	return $style;
}
function tr0Red() {
	$style = 'bgcolor="#ffffff" style="background-color: #ffffff; color: #000; font-weight: normal;"';
	return $style;
}
function tr1Red() {
	$style = 'bgcolor="#f1f1f1" style="background-color: #f1f1f1; color: #000; font-weight: normal;"';
	return $style;
}
function td0Red() {
	$style = 'style="border: 1px solid #ddd;white-space: nowrap; text-align: left;"';
	return $style;
}
function td1Red() {
	$style = 'style="border: 1px solid #ddd;white-space: nowrap; text-align: left;"';
	return $style;
}
function headerRed() {
	$style = '';
	return $style;
}
function footerRed() {
	$style = '';
	return $style;
}
/* END Red COLOR SCHEME */






/* INLINE GREY STYLE SHEET SCHEME FOR HTML EMAIL */
function h1Grey(){
	$style = '';
	return $style;
}
function tableGrey(){
	$style = 'style="border-collapse: collapse; white-space: nowrap;"';
	return $style;
}
function thGrey(){
	$style = 'bgcolor="#939393" style="white-space: nowrap; border:1px solid #DDD; font-weight: bold; font-size: 100%; background-color: #939393; color: #fff; text-align: left;"';
	return $style;
}
function tr0Grey() {
	$style = 'bgcolor="#ffffff" style="background-color: #ffffff; color: #555; font-weight: normal;"';
	return $style;
}
function tr1Grey() {
	$style = 'bgcolor="#f2f2f2" style="background-color: #f2f2f2; color: #777; font-weight: normal;"';
	return $style;
}
function td0Grey() {
	$style = 'style="border:1px solid #ddd;white-space: nowrap; text-align: left;"';
	return $style;
}
function td1Grey() {
	$style = 'style="border:1px solid #ddd;white-space: nowrap; text-align: left;"';
	return $style;
}
function headerGrey() {
	$style = '';
	return $style;
}
function footerGrey() {
	$style = '';
	return $style;
}
/* END Grey COLOR SCHEME */




/* INLINE Compatibility STYLE SHEET SCHEME FOR HTML EMAIL */
function h1Compatibility(){
	$style = '';
	return $style;
}
function tableCompatibility(){
	$style = 'style="border: 1px solid black;"';
	return $style;
}
function thCompatibility(){
	$style = '';
	return $style;
}
function tr0Compatibility() {
	$style = '';
	return $style;
}
function tr1Compatibility() {
	$style = '';
	return $style;
}
function td0Compatibility() {
	$style = '';
	return $style;
}
function td1Compatibility() {
	$style = '';
	return $style;
}
function headerCompatibility() {
	$style = '';
	return $style;
}
function footerCompatibility() {
	$style = '';
	return $style;
}
/* END Compatibility COLOR SCHEME */

?>