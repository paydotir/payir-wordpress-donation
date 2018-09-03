<?php
defined('ABSPATH') or die('No script kiddies please!');
echo '<div id="cupri_form">';
$cupri_gateways_settings = get_option('cupri_gateways_settings');
$cupri_general = get_option('cupri_general_settings', array('admin_sms_format'=>__("New pay:\n {price} \n {mobile}",'cupri'),'form_color'=>'#51cbee'));

if(!isset($cupri_gateways_settings['default']) || empty($cupri_gateways_settings['default']))
{
	if(current_user_can('manage_options' ))
	{
		_e('Please set the default gateway from admin','cupri');
		echo '  ';
		echo '<a href="'.admin_url('edit.php?post_type=cupri_pay&page=cupri-gateways').'">'.__('Settings','cupri').'</a>';
	}else
	{
		_e('No defualt gateway was set','cupri');

	}
	return;
}
?>
<style type="text/css">

	.cupri_ajax_img{
		display: none;
		width: 7px !important;
		outline: none !important;
		border: none !important;
		padding: 0 !important;
		vertical-align: middle;
		background: none !important;
	}
	.cupri_response_placeholder ul li {
		list-style: none;
		color: #000;
		font-style: unset;
		background: yellow;
	}
	.cupri-errors{background: yellow;color: #000;}
	#cupri_submit_form,.cupri-errors{width:200px;margin:auto;text-align:center;padding: 15px;	border-radius: 5px;	box-shadow: 0px 1px 18px #ededed;}
	#cupri_submit_form input[type="number"], 
	#cupri_submit_form input[type="number"] { 
		-webkit-appearance: none !important;
		appearance: none !important;
		-moz-appearance: textfield !important;
	}
	#cupri_submit_form ul{width: 100% !important;padding: 0 !important;margin: 0 !important;}
	#cupri_submit_form input[type=text],#cupri_submit_form input[type=number],#cupri_submit_form input[type=email], #cupri_submit_form textarea {
		width: 100%;
		height: auto !important;
		-moz-transition: all 0.3s ease-in-out;
		-o-transition: all 0.3s ease-in-out;
		-webkit-transition: all 0.3s ease-in-out;
		transition: all 0.3s ease-in-out;
		outline: none;
		padding: 3px !important;
		margin: 0 3px 10px 3px;
		border: 1px solid #DDDDDD;
		-moz-box-shadow: none !important;
		-webkit-box-shadow: none !important;
		box-shadow: none !important;
		line-height: auto !important;
		border-radius: none;
		background: #f5f5f5;
	}
	#cupri_submit_form input[type=number]{width: 96%;padding: 4px !important;}
	#cupri_submit_form input[type=text]:focus,#cupri_submit_form input[type=number]:focus,#cupri_submit_form input[type=email]:focus, #cupri_submit_form textarea:focus {
		-moz-box-shadow: 0 0 5px <?php echo $cupri_general['form_color']; ?>;
		-webkit-box-shadow: 0 0 5px <?php echo $cupri_general['form_color']; ?>;
		box-shadow: 0 0 5px <?php echo $cupri_general['form_color']; ?>;
		/*padding: 3px 0px 3px 3px;*/
		/*margin: 0 3px 10px 3px;*/
		border: 1px solid <?php echo $cupri_general['form_color']; ?> !important;
		outline: none;
		background: #fff;
	}

	#cupri_submit_form .cupri_clear
	{
		clear: both;
		display: block;
	}
	#cupri_submit_form #cupri_submit {
	    outline: 0 !important;
	    border: 1px solid #ededed;
	    padding: 5px 10px;
	    font-size: 1em;
	    background: <?php echo $cupri_general['form_color']; ?>;
	    margin: auto;
	    display: block;
	}
	#cupri_submit_form #cupri_submit:focus{outline: 0 !important;}
	#cupri_submit_form #cupri_submit span.heart{ transition:all ease .3s;}
	#cupri_submit_form #cupri_submit:hover span.heart{color: red;}




</style>
<script type="text/javascript">
/*
	//this is ajax action that disabled and is beta
	jQuery(document).ready(function($) {
		var cupri_has_error = false;
		$('#cupri_submit').on('click' , function(e){
			// e.preventDefault();
			$('#cupri_submit_form input[required]').each(function() {
				$(this).css({borderBottom: '1 solid #eee'})
				if($.trim($(this).val())=='') {
					cupri_has_error = true;
					$(this).css({borderBottom: '0 solid #FF0000'}).animate({
						borderWidth: 1
					}, 100);
				}   
			});


			if(cupri_has_error) return false;

			var ajx_img = $('.cupri_ajax_img'),
			cupri_response_placeholder = $('.cupri_response_placeholder');
			ajx_img.slideDown();
			var _form_data = new FormData($('#cupri_submit_form')[0]);
			_form_data.append('action', 'cupri_action');

			$.ajax({
				url: '<?php echo add_query_arg(array('_nocacheplease'=> time()), admin_url('admin-ajax.php')); /* Prevent Server Cache */ ?>&nocachejs='+new Date().getTime(),
				type: 'POST',
				dataType: 'html',
				cache: false, // Prevent Browser Cache 
				processData: false,
				contentType: false,
				data: _form_data,
			})
			.done(function(data) {
				ajx_img.slideUp();
				cupri_response_placeholder.html(data);

			})
			.fail(function(data) {
				ajx_img.slideUp();
				cupri_response_placeholder.html(data);
			})
			.always(function(data) {
				ajx_img.slideUp();
				cupri_response_placeholder.html(data);
			});

		});
	});*/
</script>
<?php
/**
 * If Javascript is disabled so form works with below code
 */
if (isset($_POST['cupri_fprice']) && !empty($_POST['cupri_fprice']))
{
	define('DOING_AJAX' , FALSE);
	$cupri = cupri::get_instance();
	$cupri->ajax();

}

$_cupri = get_option('_cupri', cupri_get_defaults_fields());

echo '<div class="row">';
echo '<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-12 text-center">';
echo '<form method="post" id="cupri_submit_form" action="#cupri_form" >';

foreach ($_cupri['type'] as $wc_cf_key => $wc_cf) {
	if($_cupri['disable'][$wc_cf_key] == 1){continue;}
	if ($_cupri['type'][$wc_cf_key] == 'text') {}
		$required = '';
	if (isset($_cupri['required'][$wc_cf_key]) && $_cupri['required'][$wc_cf_key] == 1) {
		$required = ' required="required" ';
	}

	echo '<div class="cupri_input_wrapper">';
	if ($_cupri['type'][$wc_cf_key] == 'paragraph') {
		/*Dont show title if the field type is paragraph,just print!*/
		echo '<label class="cupri_f' . $wc_cf_key . '">';
	} else {
		echo '<label class="cupri_tbl cupri_f' . $wc_cf_key . '">';
		echo '<span>' . $_cupri['name'][$wc_cf_key];
		if ($required) {echo '<span style="color:red;font-weigth:bold;">*</span>';}
		echo '</span>';
	}

	switch ($_cupri['type'][$wc_cf_key]) {
	/**
	 * Builtin Fields
	 */
	case 'price':
	echo ' ('.cupri_get_currency().')';
	$value='';
	$has_pre_value_price = false;
	$has_pre_value_price_item = false;
	if (isset($_GET['cupri_f' . $wc_cf_key])) {
		$has_pre_value_price = true;
		$value = $has_pre_value_price_item = (int)htmlspecialchars($_GET['cupri_f' . $wc_cf_key]);
	}
	echo '<input value="'.$value.'" required="required" type="number" name="cupri_f' . $wc_cf_key . '" id="cupri_f' . $wc_cf_key . '">';
	
	break;
	case 'mobile':
	echo '<input ' . $required . ' type="text" name="cupri_f' . $wc_cf_key . '" id="cupri_f' . $wc_cf_key . '">';
	
	break;
	case 'email':
	echo '<input ' . $required . ' type="email" name="cupri_f' . $wc_cf_key . '" id="cupri_f' . $wc_cf_key . '">';

	break;
	/**
	 * Other Fields added in admin
	 */

	case 'text':
	echo '<input ' . $required . ' type="text" name="cupri_f' . $wc_cf_key . '" id="cupri_f' . $wc_cf_key . '">';
	break;
	case 'checkbox':
	echo '<input ' . $required . ' type="checkbox" name="cupri_f' . $wc_cf_key . '" value="1" id="cupri_f' . $wc_cf_key . '">';
	break;
	case 'paragraph':
	echo '<p class="cupri_full_centered cupri_f' . $wc_cf_key . '">' . $_cupri['paragraph_content'][$wc_cf_key] . '</p>';
	break;
	case 'select':
	$has_selected = false;
	$has_selected_item = false;
	if (isset($_GET['cupri_f' . $wc_cf_key]) && in_array($_GET['cupri_f' . $wc_cf_key], $_cupri['combobox_choices'][$wc_cf_key])) {
		$has_selected = true;
		$has_selected_item = htmlspecialchars($_GET['cupri_f' . $wc_cf_key]);
	}
	echo '<select style="width:100%;" ' . $required . ' name="cupri_f' . $wc_cf_key . '" id="cupri_f' . $wc_cf_key . '">';
	echo '<option>---' . __('select one', 'cupri') . '---</option>';
	foreach ($_cupri['combobox_choices'][$wc_cf_key] as $combobox_choice) {
		echo '<option ' . (($has_selected) ? selected($has_selected_item, $combobox_choice, false) : '') . ' value="' . $combobox_choice . '">' . $combobox_choice . '</option>';
	}
	echo '</select>';
	break;
	default:
	echo '<input ' . $required . ' type="text" name="cupri_f' . $wc_cf_key . '" id="cupri_f' . $wc_cf_key . '">';

}

echo '</label>';
echo '<div class="cupri_clear"></div>';
echo '</div>';
}

echo '<p class="cupri_submit_label">';
echo '<button class="cupri_full_centered" name="cupri_submit" id="cupri_submit"><span class="heart">&hearts; </span> پرداخت <img width="7px" style="display:none;" class="cupri_ajax_img" src="'.cupri_url.'/assets/ajax-loader.gif"></button>';
echo '<p class="cupri_response_placeholder alert"></p>';
echo '</p>';
echo '</form>';
echo '</div>';

echo '</div>';

echo '</div>';
