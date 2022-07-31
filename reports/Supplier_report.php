<?php 

require_once('./application/Secure_Controller.php');

class Reports extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('reports');

		$method_name = $this->uri->segment(2);
		$exploder = explode('_', $method_name);

		if(sizeof($exploder) > 1)
		{
			preg_match('/(?:inventory)|([^_.]*)(?:_graph|_row)?$/', $method_name, $matches);
			preg_match('/^(.*?)([sy])?$/', array_pop($matches), $matches);
			$submodule_id = $matches[1] . ((count($matches) > 2) ? $matches[2] : 's');

			// check access to report submodule
			if(!$this->Employee->has_grant('reports_' . $submodule_id, $this->Employee->get_logged_in_employee_info()->person_id))
			{
				redirect('no_access/reports/reports_' . $submodule_id);
			}
		}

		$this->load->helper('report');
	}

	

	//Summary Suppliers report
	public function summary_suppliers($start_date, $end_date, $sale_type, $location_id = 'all')
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id);

		$this->load->model('reports/Summary_suppliers');
		$model = $this->Summary_suppliers;

		$report_data = $model->getData($inputs);
		$summary = $this->xss_clean($model->getSummaryData($inputs));

		$tabular_data = array();
		foreach($report_data as $row)
		{
			$tabular_data[] = $this->xss_clean(array(
				'supplier_name' => $row['supplier'],
				'quantity' => to_quantity_decimals($row['quantity_purchased']),
				'subtotal' => to_currency($row['subtotal']),
				'tax' => to_currency_tax($row['tax']),
				'total' => to_currency($row['total']),
				'cost' => to_currency($row['cost']),
				'profit' => to_currency($row['profit'])
				
			));
		}

		$data = array(
			'title' => $this->lang->line('reports_suppliers_summary_report'),
			'subtitle' => $this->_get_subtitle_report(array('start_date' => $start_date, 'end_date' => $end_date)),
			'headers' => $this->xss_clean($model->getDataColumns()),
			'data' => $tabular_data,
			'summary_data' => $summary
		);

		$this->load->view('reports/tabular', $data);
	}


	public function specific_supplier($start_date, $end_date, $supplier_id, $sale_type)
	{
		$inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'supplier_id' => $supplier_id, 'sale_type' => $sale_type);

		$this->load->model('reports/Specific_supplier');
		$model = $this->Specific_supplier;

		$model->create($inputs);

		$report_data = $model->getData($inputs);

		$tabular_data = array();
		foreach($report_data as $row)
		{
			$tabular_data[] = $this->xss_clean(array(
				'id' => $row['sale_id'],
				'sale_time' => to_datetime(strtotime($row['sale_time'])),
				'name' => $row['name'],
				'category' => $row['category'],
				'item_number' => $row['item_number'],
				'quantity' => to_quantity_decimals($row['items_purchased']),
				'subtotal' => to_currency($row['subtotal']),
				'tax' => to_currency_tax($row['tax']),
				'total' => to_currency($row['total']),
				'cost' => to_currency($row['cost']),
				'profit' => to_currency($row['profit' / 2])
								
			));
		}

		$supplier_info = $this->Supplier->get_info($supplier_id);
		$data = array(
			'title' => $this->xss_clean($supplier_info->company_name . ' (' . $supplier_info->first_name . ' ' . $supplier_info->last_name . ') ' . $this->lang->line('reports_report')),
			'subtitle' => $this->_get_subtitle_report(array('start_date' => $start_date, 'end_date' => $end_date)),
			'headers' => $this->xss_clean($model->getDataColumns()),
			'data' => $tabular_data,
			'summary_data' => $this->xss_clean($model->getSummaryData($inputs))
		);

		$this->load->view('reports/tabular', $data);
	}


	public function send_pdf($sale_id, $type = 'invoice')
	{
		$sale_data = $this->_load_sale_data($sale_id);

		$result = FALSE;
		$message = $this->lang->line('sales_invoice_no_email');

		if(!empty($sale_data['customer_email']))
		{
			$to = $sale_data['customer_email'];
			$number = $sale_data[$type."_number"];
			$subject = $this->lang->line("sales_" . $type) . ' ' . $number;

			$text = $this->config->item('invoice_email_message');
			$tokens = array(new Token_invoice_sequence($sale_data['invoice_number']),
				new Token_invoice_count('POS ' . $sale_data['sale_id']),
				new Token_customer((object)$sale_data));
			$text = $this->token_lib->render($text, $tokens);
			$sale_data['mimetype'] = get_mime_by_extension('uploads/' . $this->config->item('company_logo'));

			// generate email attachment: invoice in pdf format
			$html = $this->load->view("sales/" . $type . "_email", $sale_data, TRUE);

			// load pdf helper
			$this->load->helper(array('dompdf', 'file'));
			$filename = sys_get_temp_dir() . '/' . $this->lang->line("sales_" . $type) . '-' . str_replace('/', '-', $number) . '.pdf';
			if(file_put_contents($filename, create_pdf($html)) !== FALSE)
			{
				$result = $this->email_lib->sendEmail($to, $subject, $text, $filename);
			}

			$message = $this->lang->line($result ? "sales_" . $type . "_sent" : "sales_" . $type . "_unsent") . ' ' . $to;
		}

		echo json_encode(array('success' => $result, 'message' => $message, 'id' => $sale_id));

		$this->sale_lib->clear_all();

		return $result;
	}





}