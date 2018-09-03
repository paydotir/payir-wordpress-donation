<?php
defined('ABSPATH') or die('No script kiddies please!');
/**
 * Base Abstract Class for gateways
 */
abstract class cupri_abstract_gateway {
	public $settings;
	public $callback_url;

	// Force Extending class to define this method
	abstract protected function start($payment_data);
	abstract protected function end($payment_data);


	public static function get_instance($id, $name) {
		$class = get_called_class();
		if (!isset($class::$instance)) {
			$class::$instance = new $class($id, $name);
		}

		return $class::$instance;
	}

	function __construct($id, $name) {
		// error_reporting(E_ALL);
		// ini_set('display_errors','on');
		$this->id = $id;
		$this->name = $name;
		/**
		 * Fetch settings
		 * @var array
		 */	
		$cupri_gateways_settings = get_option('cupri_gateways_settings');
		$this->settings = isset($cupri_gateways_settings[$this->id])?$cupri_gateways_settings[$this->id]:array();
		/**
		 * callback url
		 */
		$this->callback_url = get_bloginfo('url' ).'/?cupri_listen=true&cupri_gateway='.$this->id;

		$this->add_gateway();
		$this->_add_settings();
		$this->_start();
		$this->_end();
	}

	function add_gateway() {
		add_filter('cupri_gateways', array($this, 'cupri_gateways'));
	}

	function cupri_gateways($gateways) {
		$gateways[$this->id] = $this->name;
		return $gateways;
	}

	function _add_settings() {
		add_filter('cupri_gateways_' . $this->id . '_settings', array($this, 'add_settings'));
	}
	function _start() {
		add_action('cupri_start_payment_' . $this->id, array($this, 'start'));
	}
	function _end() {
		if(isset($_REQUEST['order_id']))
		{
			if(get_post_status($_REQUEST['order_id'] ) == 'cupri_paid')
			{
				$completed_msg = __('This order is completed already','cupri');
				echo cupri_failed_msg($completed_msg);
				die();
			}else{
				add_action('cupri_end_payment_' . $this->id, array($this, 'end'));					
			}
		}

	}

	function failed($order_id) {
		return $this->update_status($order_id, 'cupri_failed');
	}
	function success($order_id) {
		$this->notification($order_id);
		return $this->update_status($order_id, 'cupri_paid');
	}
	function waiting($order_id) {
		return $this->update_status($order_id, 'cupri_waiting');
	}
	function update_status($order_id, $state) {
		if (!$order_id || empty($state)) {
			return;
		}
		$att = array(
			'ID' => $order_id,
			'post_status' => $state,
			);

		return wp_update_post($att);
	}

	function get_price($order_id)
	{
		return get_post_meta( $order_id, '_cupri_fprice', true );
	}

	function get_mobile($order_id)
	{
		return get_post_meta( $order_id, '_cupri_fmobile', true );
	}
	function get_email($order_id)
	{
		return get_post_meta( $order_id, '_cupri_femail', true );
	}
	function notification($order_id)
	{

		$cupri_general = get_option('cupri_general_settings' , array('admin_sms_format'=>__("New pay:\n {price} \n {mobile}",'cupri')));
		if(isset($cupri_general['active_sms_notification'],$cupri_general['mobiles']) && $cupri_general['active_sms_notification']==1 && !empty($cupri_general['mobiles']))
		{
			if(class_exists('WP_SMS_Plugin'))
			{
				$messgae = $cupri_general['admin_sms_format'];
				$messgae = str_replace(array('{price}','{mobile}'), array($this->get_price($order_id),$this->get_mobile($order_id)), $messgae);

				$mobiles = trim($cupri_general['mobiles']);
				$mobiles = str_replace(array('-','،','+',' '),',',$mobiles);
				$mobiles = explode(',', $mobiles);

				global $sms;
				$sms->to = $mobiles;
				$sms->msg = $messgae;
				$sms->SendSMS();
			}
		}

	}

}

