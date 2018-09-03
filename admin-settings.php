<?php
defined( 'ABSPATH' ) or die(  'No script kiddies please!' );
?>
<?php 
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
 ?>
<div class="wrap">
	<style type="text/css">
		.admin_fields{margin: 40px 0;}
		.cupri_gateways {border: 1px solid #f9f9f9;padding: 10px;margin:5px;border-radius:5px;}
		.cupri_gateways .fields{}
	</style>
	<h2></h2>
	<?php 
	if(isset($_POST['cupri_general']) && !empty($_POST['cupri_general']) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'cupri_general_settings_form' ))
	{	
		foreach ($_POST['cupri_general'] as $key => $value) {
			switch ($key) {
				case 'mobiles':{
					$_POST['cupri_general']['mobile'] = sanitize_text_field($_POST['cupri_general']['mobile']);
					$_POST['cupri_general']['mobile'] = esc_sql($_POST['cupri_general']['mobile']);
					$_POST['cupri_general']['mobile'] = esc_html($_POST['cupri_general']['mobile']);
				}break;
				case 'active_sms_notification':
				{
					$_POST['cupri_general']['active_sms_notification'] = (int) ($_POST['cupri_general']['active_sms_notification']);
					if($_POST['cupri_general']['active_sms_notification'] > 1)
					{
						$_POST['cupri_general']['active_sms_notification'] = 1;
					}

				}break;
				case 'admin_sms_format':
				{
					$_POST['cupri_general']['admin_sms_format'] = sanitize_text_field($_POST['cupri_general']['admin_sms_format']);
					$_POST['cupri_general']['admin_sms_format'] = esc_sql($_POST['cupri_general']['admin_sms_format']);
					$_POST['cupri_general']['admin_sms_format'] = esc_html($_POST['cupri_general']['admin_sms_format']);

				}break;
				case 'form_color':
				{
					$_POST['cupri_general']['form_color'] = sanitize_text_field($_POST['cupri_general']['form_color']);
					$_POST['cupri_general']['form_color'] = esc_sql($_POST['cupri_general']['form_color']);
					$_POST['cupri_general']['form_color'] = esc_html($_POST['cupri_general']['form_color']);

				}break;
			}
		}
		// $_POST['cupri_general'] = array_map('sanitize_text_field', $_POST['cupri_general']);
		update_option( 'cupri_general_settings', $_POST['cupri_general'] );
	}
	$cupri_general = get_option('cupri_general_settings', array('admin_sms_format'=>__("New pay:\n {price} \n {mobile}",'cupri'),'form_color'=>'#51cbee'));

	 ?>
	 <h1><?php _e('General Settings','cupri'); ?></h1>
	 <hr>
	<form method="post">
	<?php 
	wp_nonce_field( 'cupri_general_settings_form' );
	 ?>
	<div class="cupri_gateways">
		<h2> :: <?php _e('Notifications','cupri'); ?></h2>
		<p class="admin_fields">
			<strong><?php _e('Admin Mobile(s)','cupri') ?></strong><br>
			<input value="<?php echo $cupri_general['mobiles']; ?>" type="text" name="cupri_general[mobiles]">
			<span class="desc"><?php _e('Seperate more mobiles with ,','cupri') ?></span>
		</p>
		<p class="admin_fields">
			<strong><?php _e('Active notification with sms ?','cupri') ?></strong><br>
			<input <?php checked( $cupri_general['active_sms_notification'], 1, true ); ?> type="checkbox" value="1" name="cupri_general[active_sms_notification]">
			<span class="desc"><?php _e('You need to install and configure this plugin:','cupri'); ?>  <a href="https://wordpress.org/plugins/wp-sms/" target="_blank">wp-sms</a></span>
		</p>
		<p class="admin_fields">
			<strong><?php _e('SMS format','cupri') ?></strong><br>
			<textarea name="cupri_general[admin_sms_format]" rows="3"><?php echo $cupri_general['admin_sms_format']; ?></textarea>
			<span class="desc"><?php _e('Possible formats {price} , {mobile}  : ','cupri'); ?></span>
		</p>
	</div>
	<div class="cupri_gateways">
		<h2> :: <?php _e('Form Style','cupri'); ?></h2>
		<p class="admin_fields">
			<strong><?php _e('change form color','cupri') ?></strong><br>
			<input  type="text" data-default-color="#51cbee" value="<?php echo $cupri_general['form_color']; ?>" name="cupri_general[form_color]" id="cupri_general_form_color">
			<span class="desc"></span>
		</p>
	</div>
	<button class="button-primary"><?php _e('Save'); ?></button>
 	</form>

</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
	    $('#cupri_general_form_color').wpColorPicker({defaultColor:"#51cbee"});
	});
</script>