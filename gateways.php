<?php
defined( 'ABSPATH' ) or die(  'No script kiddies please!' );
?>
<div class="wrap">
	<h2></h2>
	<style type="text/css">
		.cupri_gateways {border: 1px solid #f9f9f9;padding: 10px;margin:5px;border-radius:5px;}
		.cupri_gateways .fields{}
	</style>
	<?php 
	if(isset($_POST['cupri_gateways']) && !empty($_POST['cupri_gateways']) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'cupri_gateways_form' ))
	{
		foreach ($_POST['cupri_gateways'] as $key => $value) {
			if(is_array($value))
			{
				foreach ($value as $key_v => $value_v) {
					$_POST['cupri_gateways'][$key][$value_v] = sanitize_text_field($_POST['cupri_gateways'][$key][$value_v]);			
					$_POST['cupri_gateways'][$key][$value_v] = esc_html($_POST['cupri_gateways'][$key][$value_v]);			
					$_POST['cupri_gateways'][$key][$value_v] = esc_sql($_POST['cupri_gateways'][$key][$value_v]);			
				}

			}else
			{
				$_POST['cupri_gateways'][$key] = sanitize_text_field($_POST['cupri_gateways'][$key]);			
				$_POST['cupri_gateways'][$key] = esc_html($_POST['cupri_gateways'][$key]);			
				$_POST['cupri_gateways'][$key] = esc_sql($_POST['cupri_gateways'][$key]);			
			}
		}
		update_option( 'cupri_gateways_settings', $_POST['cupri_gateways'] );
	}
	$cupri_gateways_settings = get_option('cupri_gateways_settings');
	$gateways = apply_filters( 'cupri_gateways', array() );
	 ?>
	 <h1><?php _e('Gateway Settings','cupri'); ?></h1>
	 <hr>
	<form method="post">
	<?php 
	wp_nonce_field( 'cupri_gateways_form' );
	 ?>
	<h2> :: <?php _e('Default Gateway','cupri'); ?></h2>
	<code><?php _e('currency' , 'cupri'); echo ' : '; echo cupri_get_currency()?></code>
	<p class="">
		<strong></strong><br>
		<select name="cupri_gateways[default]">
			<?php 
				foreach ($gateways as $g_id => $g_name) {
					echo '<option '.selected( $cupri_gateways_settings['default'], $g_id, false ).' value="'.$g_id.'">'.$g_name.'</option>';
				}
			 ?>
		</select>
	</p>
	<?php 
	foreach ($gateways as $id => $name)
	{
		echo '<div class="cupri_gateways">';
			echo '<h2>  :: '.$name.'</h2><hr>';
				$settings = apply_filters( 'cupri_gateways_'.$id.'_settings', array() );
				foreach ($settings as $s_id => $s_name) {
					$value = '';
					if(isset($cupri_gateways_settings[$id][$s_id]))
					{
						$value = $cupri_gateways_settings[$id][$s_id];
					}
					echo '<p class="fields"><label>';
						echo '<strong>';
							echo $s_name;
						echo '</strong><br>';
						echo '<input type="text" value="'.$value.'" name="cupri_gateways['.$id.']['.$s_id.']">';
					echo '</label></p>';
				}
		echo '</div>';
	}
	?>
	<button class="button-primary"><?php _e('Save'); ?></button>
 	</form>

</div>