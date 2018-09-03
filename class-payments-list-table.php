<?php
defined( 'ABSPATH' ) or die(  'No script kiddies please!' );
if (!class_exists('WP_List_Table')) {
	require_once ABSPATH . 'wp-admin/includes/screen.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
class cupri_payments_table extends WP_List_Table {
	public $_tbl_name = '';
	public $_tbl_key = '';
	public $_tbl_title = '';
	public $_addable = false;

	public function __construct() {
		global $wpdb;
		$this->_tbl_name = $wpdb->prefix . 'posts';
		$this->_tbl_key = 'id';
		$this->_tbl_title = 'Payments';
		$this->_addable = false;

		parent::__construct(array(
			'singular' => __('Pay', 'cupri'), //singular name of the listed records
			'plural' => __('Payments', 'cupri'), //plural name of the listed records
			'ajax' => false, //does this table support ajax?
			));

	}
	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'id' => __('id', 'cupri'),
			'status' => __('status', 'cupri'),
			'post_date' => __('date', 'cupri'),
			'price' => __('price', 'cupri'),

			);
		$_cupri = get_option('_cupri',array() );
		foreach ($_cupri['type'] as $wc_cf_key => $wc_cf) 
		{
			if($_cupri['type'][$wc_cf_key]=='paragraph'){continue;}
			$_submitted_name = '_wc_donation_f'.$wc_cf_key;
			$columns[$_submitted_name] = $_cupri['name'][$wc_cf_key];
		}

		return $columns;
	}

	/**
	 * Get count of records
	 * @return int
	 */
	public function record_count() {
		$args = array(
			'post_type' => 'shop_order',
			'post_status' => 'any',
			'meta_query'=>
			array(
				'key'=>'_shahabnet_order_type',
				'value'=>array(''),
				'compare'=>'NOT IN'
				)
			);
		// if (!empty($_REQUEST['orderby'])) {
		// 	$sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
		// 	$sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
		// }
		$query = new WP_Query($args);
		return $query->found_posts;
	}

	public function no_items() {
		_e('No Items Found', 'cupri');
	}

	public function get_items($per_page = 15, $page_number = 1) {

		$result = array();

		$args = array(
			'post_type' => 'shop_order',
			'post_status' => 'any',
			'posts_per_page' => $per_page, // return this many
			'offset' => ($page_number - 1) * $per_page,
			'meta_query'=>
			array(
				'key'=>'_shahabnet_order_type',
				'value'=>array(''),
				'compare'=>'NOT IN'
				)
			);
		// if (!empty($_REQUEST['orderby'])) {
		// 	$sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
		// 	$sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
		// }
		//
		
		$_cupri = get_option('_cupri',array() );
		$query = new WP_Query($args);
		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$_order = new wc_order(get_the_id());

				$query->the_post();
				$to_add = array(
					'id' => get_the_ID(),
					'post_title' => get_the_title(),
					'post_date' => get_the_date('F j, Y g:i a'),
					'price' =>$_order->get_total() . ' ' . get_woocommerce_currency_symbol() ,
					'status' => __(get_post_status( get_the_ID() ),'woocommerce'),
					);
				// 		custom fields
				$custom_to_add = array();
				foreach ($_cupri['type'] as $wc_cf_key => $wc_cf) 
				{
					if($_cupri['type'][$wc_cf_key]=='paragraph'){continue;}
					$_submitted_name = '_wc_donation_f'.$wc_cf_key;
					$custom_to_add[$_submitted_name] = get_post_meta( get_the_ID(), $_submitted_name, true );
				}
				$result[] = array_merge($to_add,$custom_to_add);
			}
		}



		return $result;
	}

	public function prepare_items() {

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page = $this->get_items_per_page('cwc_per_page', 15);
		$current_page = $this->get_pagenum();
		$total_items = $this->record_count();

		$this->set_pagination_args(array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page' => $per_page, //WE have to determine how many items to show on a page
			));

		$items = $this->get_items($per_page, $current_page);
		$this->items = $this->get_items($per_page, $current_page);
	}

	public function column_default($item, $column_name) {
		return @$item[$column_name];
	}
}

