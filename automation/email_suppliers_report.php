<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('Secure_Controller.php');

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
