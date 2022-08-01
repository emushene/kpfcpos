<?php 


		include_once 'db.php';
		$start_date = date('d-m-y h:i:s');
		$end_date = date('d-m-y h:i:s');
		$supplier_id = mysqli_query($conn, "SELECT supplier_id FROM ospos_items");
		 
		
		function runReport () {

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

}
echo $start_date; 
		echo $end_date;
		echo $supplier_id;
?>

<script type="text/javascript">
	$(document).ready(function()
	{
	 	<?php $this->load->view('partial/bootstrap_tables_locale'); ?>

		var details_data = <?php echo json_encode($details_data); ?>;
		<?php
		if($this->config->item('customer_reward_enable') == TRUE && !empty($details_data_rewards))
		{
		?>
			var details_data_rewards = <?php echo json_encode($details_data_rewards); ?>;
		<?php
		}
		?>
		var init_dialog = function() {
			<?php
			if(isset($editable))
			{
			?>
				table_support.submit_handler('<?php echo site_url("reports/get_detailed_" . $editable . "_row")?>');
				dialog_support.init("a.modal-dlg");
			<?php
			}
			?>
		};

		$('#table')
			.addClass("table-striped")
			.addClass("table-bordered")
			.bootstrapTable({
				columns: <?php echo transform_headers($headers['summary'], TRUE); ?>,
				stickyHeader: true,
				stickyHeaderOffsetLeft: $('#table').offset().left + 'px',
				stickyHeaderOffsetRight: $('#table').offset().right + 'px',
				pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
				pagination: true,
				sortable: true,
				showColumns: true,
				uniqueId: 'id',
				showExport: true,
				exportDataType: 'all',
				exportTypes: ['json', 'xml', 'csv', 'txt', 'sql', 'excel', 'pdf'],
				data: <?php echo json_encode($summary_data); ?>,
				iconSize: 'sm',
				paginationVAlign: 'bottom',
				detailView: true,
				escape: false,
				search: true,
				onPageChange: init_dialog,
				onPostBody: function() {
					dialog_support.init("a.modal-dlg");
				},
				onExpandRow: function (index, row, $detail) {
					$detail.html('<table></table>').find("table").bootstrapTable({
						columns: <?php echo transform_headers_readonly($headers['details']); ?>,
						data: details_data[(!isNaN(row.id) && row.id) || $(row[0] || row.id).text().replace(/(POS|RECV)\s*/g, '')]
					});

					<?php
					if($this->config->item('customer_reward_enable') == TRUE && !empty($details_data_rewards))
					{
					?>
						$detail.append('<table></table>').find("table").bootstrapTable({
							columns: <?php echo transform_headers_readonly($headers['details_rewards']); ?>,
							data: details_data_rewards[(!isNaN(row.id) && row.id) || $(row[0] || row.id).text().replace(/(POS|RECV)\s*/g, '')]
						});
					<?php
					}
					?>
				}
		});

		init_dialog();
	});
</script>

<?php $this->load->view("partial/footer"); ?>


}
