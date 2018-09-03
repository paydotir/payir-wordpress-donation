<?php
defined('ABSPATH') or die('No script kiddies please!');
?>
<style type="text/css">
	/*Hide Add new button for pay*/
	a.page-title-action{display: none;}
	.wc_donaite_header {clear: both;}
	.wc_donaite_header .statistics{margin:auto;width: 100%;}
	.wc_donaite_header .td{
		width: 18%;
		min-width: 200px;
		min-height: 25px;
		background: #fff;
		float: right;
		margin:5px;
		padding: 25px;
		border: 1px solid #adadad;
	}
	.wc_donaite_header .counter{display: block;}
	.wc_donaite_header .counter.total {
		font-size: 2em;
		color: green;
	}

</style>
<?php
/**
 * get total payments
 */
function cupri_get_total_payments() {

	global $wpdb;

	$order_totals =  $wpdb->get_row("

		SELECT 
		meta.meta_id,
		SUM(meta.meta_value) AS total_sales,
		COUNT(posts.ID) AS total_orders 
		FROM {$wpdb->posts} AS posts
		LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
		WHERE 
			meta.meta_key = '_cupri_fprice'

		AND posts.post_type = 'cupri_pay'
		AND posts.post_status = 'cupri_paid'

		");

	return absint($order_totals->total_sales);

}

?>


<div class="wc_donaite_header">
	<div class="statistics">
		<div class="td">
			<span class="counter total"><?php echo cupri_get_total_payments(); ?></span>
			<span class="counter_desc"><?php _e('Total Payed', 'cupri');?></span>
		</div>
		<?php
		/*
		<div class="td">
			<span class="counter"><?php echo cupri_get_total_items(); ?></span>
			<span class="counter_desc"><?php _e('Total Items', 'cupri');?></span>
		</div>
		<div class="td">
			<span class="counter"><?php echo cupri_get_total_completed_items(); ?></span>
			<span class="counter_desc"><?php _e('Total Sucess Items', 'cupri');?></span>
		</div>
		<div class="td">
			<span class="counter"><?php echo cupri_get_last_payed_item(); ?></span>
			<span class="counter_desc"><?php _e('Last Paid price', 'cupri');?></span>
		</div>
		*/
		?>
	</div>
	<div style="clear:both;width: 100%;height: 1px;display: block;"></div>
</div>