<?php
defined('ABSPATH') or die('No script kiddies please!');
function cupri_normalize_mobile($mobile)
{
    $mobile = _wpm_persian_digit_to_eng($mobile);
    $mobile = trim($mobile);
    $mobile = str_replace(array('+'), '', $mobile);
    $mobile = ltrim($mobile, '0');
    if (substr($mobile, 0, 2) == '98') {
        $mobile = ltrim($mobile, '9');
        $mobile = ltrim($mobile, '8');
    }
    if (strlen($mobile) != 10) {
        return false;
    }

    return apply_filters( 'cupri_normalize_mobile', '0' . $mobile);
}

/**
 * Posts columns
 */
add_filter('manage_cupri_pay_posts_columns', 'add_cupri_pay_columns',99999999);
function add_cupri_pay_columns($columns) {
    $new_columns = array(
        'cb' => '<input type="checkbox" />',
        // 'title' => __('Title' ),
        // 'post_id' => __('ID' ),
        'date' => __('Date' ),
        // 'price' => __('Price' , 'cupri'),
        'status' => __('Status' , 'cupri'),
        );

    $_cupri = get_option('_cupri', cupri_get_defaults_fields());
    foreach ($_cupri['type'] as $wc_cf_key => $wc_cf) {
        $key = 'cupri_f' . $wc_cf_key;
        $key = '_'.$key;
        $new_columns[$key ] = $_cupri['name'][$wc_cf_key ];
    }

    unset($new_columns['_cupri_femail']);

    return $new_columns;
}

add_action('manage_cupri_pay_posts_custom_column', 'custom_cupri_pay_column', 99999999, 2);
function custom_cupri_pay_column($column, $post_id) {
    if(strpos($column,'cupri_f'))
    {
        $value = get_post_meta($post_id, $column, true);
        if($column=='_cupri_fmobile')
        {
            $email_value = get_post_meta($post_id, '_cupri_femail', true);
            $email_value = (empty($email_value)?'-':$email_value);
            $value .= '<br>'.$email_value;
        }
        echo (empty( $value)?'-': $value);
    }

    if($column == 'post_id')
    {
        echo $post_id;
    }
    // if($column == 'title2')
    // {
    //     echo '<a href="'.admin_url('post.php?post='.$post_id.'&action=edit' ).'">'.__('More Details','cupri').'</a>';
    // }
    if($column == 'status')
    {
        $get_post_status_object = get_post_status_object( get_post_status($post_id) );
        if(is_object($get_post_status_object) && !is_wp_error($get_post_status_object )){
            echo $get_post_status_object->label;
        }
        
    }

}

/**
 * Register Post status
 */
register_post_status('cupri_waiting', array(
    'label' => 'منتظر پرداخت',
    'public' => true,
    'exclude_from_search' => true,
    'show_in_admin_all_list' => true,
    'show_in_admin_status_list' => true,
    'label_count' => _n_noop('منتظر پرداخت <span class="count">(%s)</span>', 'منتظر پرداخت <span class="count">(%s)</span>'),
    )
);
register_post_status('cupri_paid', array(
    'label' => 'پرداخت شده',
    'public' => true,
    'exclude_from_search' => true,
    'show_in_admin_all_list' => true,
    'show_in_admin_status_list' => true,
    'label_count' => _n_noop('پرداخت شده <span class="count">(%s)</span>', 'پرداخت شده <span class="count">(%s)</span>'),
    )
);
register_post_status('cupri_failed', array(
    'label' => 'ناموفق',
    'public' => true,
    'exclude_from_search' => true,
    'show_in_admin_all_list' => true,
    'show_in_admin_status_list' => true,
    'label_count' => _n_noop('ناموفق <span class="count">(%s)</span>', 'ناموفق <span class="count">(%s)</span>'),
    )
);

/**
 * Fns
 */

function cupri_msg($msg,$order_id=false,$type=false) {
    $cupri_general = get_option('cupri_general_settings', array('admin_sms_format'=>__("New pay:\n {price} \n {mobile}",'cupri'),'form_color'=>'#51cbee'));

    $_msg = '<!doctype html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>'.get_bloginfo('name' ).' | '.__('Payment' , 'cupri').'</title></head><body dir="'.(is_rtl()?'rtl':'ltr').'">';
    $_msg .= '
    <style>
        .cupri-msg{
            direction:rtl;
            font-family:tahoma arial;
            padding:20px ;
            background:#f9f9f9;
        }
        .cupri-msg img{
            display: inline-block;
            vertical-align: middle;
            margin: 3px;
        }
        .cupri-home-link {
            background: '.$cupri_general['form_color'].';
            padding: 5px;
            display: block;
            margin: auto;
            text-align: center;
            width: 80px;
            color: #000;
            text-decoration:none;
        }

    .tfooter,.tfooter td{background:#f3f3f3;text-align:center !important;border:1px solid #eee;}
    .cupri_order_details_wrapper{
        display: block;
        max-width: 500px;
        width:100%;
        margin: auto;
        padding: 15px;
    }
    .cupri_order_details tr td{width:50%;}
    .cupri_order_details tbody,.cupri_order_details tr{width:100%;}
    .cupri_order_details_wrapper table caption{ background:#eee;padding:5px;font-size:14px;color:#000;text-shadow:none; }
    .cupri_order_details_wrapper table {
        font-family:Tahoma,Arial, Helvetica, sans-serif;
        color:#666;
        font-size:12px;
        text-shadow: 1px 1px 0px #fff;
        background:#f9f9f9;
        border:#ccc 1px solid;

        -moz-border-radius:3px;
        -webkit-border-radius:3px;
        border-radius:3px;

        -moz-box-shadow: 0 1px 2px #d1d1d1;
        -webkit-box-shadow: 0 1px 2px #d1d1d1;
        box-shadow: 0 1px 2px #d1d1d1;
        width:100%;
    }
    .cupri_order_details_wrapper table th {
        padding:21px 25px 22px 25px;
        border-top:1px solid #fafafa;
        border-bottom:1px solid #e0e0e0;

        background: #ededed;
        background: -webkit-gradient(linear, left top, left bottom, from(#ededed), to(#ebebeb));
        background: -moz-linear-gradient(top,  #ededed,  #ebebeb);
    }
    .cupri_order_details_wrapper table th:first-child {
        text-align: right;
        padding-right:20px;
    }
    .cupri_order_details_wrapper table tr:first-child th:first-child {
        -moz-border-radius-topleft:3px;
        -webkit-border-top-right-radius:3px;
        border-top-right-radius:3px;
    }
    .cupri_order_details_wrapper table tr:first-child th:last-child {
        -moz-border-radius-topright:3px;
        -webkit-border-top-left-radius:3px;
        border-top-left-radius:3px;
    }
   .cupri_order_details_wrapper table tr {
        text-align: center;
        padding-right:20px;
    }
    .cupri_order_details_wrapper table td:first-child {
        text-align: right;
        padding-right:20px;
        border-right: 0;
    }
    .cupri_order_details_wrapper table td {
        padding:18px;
        border-top: 1px solid #ffffff;
        border-bottom:1px solid #e0e0e0;
        border-right: 1px solid #e0e0e0;

        background: #fafafa;
        background: -webkit-gradient(linear, right top, right bottom, from(#fbfbfb), to(#fafafa));
        background: -moz-linear-gradient(top,  #fbfbfb,  #fafafa);
    }
    .cupri_order_details_wrapper table tr.even td {
        background: #f6f6f6;
        background: -webkit-gradient(linear, right top, right bottom, from(#f8f8f8), to(#f6f6f6));
        background: -moz-linear-gradient(top,  #f8f8f8,  #f6f6f6);
    }
    .cupri_order_details_wrapper table tr:last-child td {
        border-bottom:0;
    }
    .cupri_order_details_wrapper table tr:last-child td:first-child {
        -moz-border-radius-bottomright:3px;
        -webkit-border-bottom-right-radius:3px;
        border-bottom-right-radius:3px;
    }
    .cupri_order_details_wrapper table tr:last-child td:last-child {
        -moz-border-radius-bottomright:3px;
        -webkit-border-bottom-left-radius:3px;
        border-bottom-left-radius:3px;
    }
    .cupri_order_details_wrapper table tr:hover td {
        background: #f2f2f2;
        background: -webkit-gradient(linear, right top, right bottom, from(#f2f2f2), to(#f0f0f0));
        background: -moz-linear-gradient(top,  #f2f2f2,  #f0f0f0);  
    }
    </style>';
    $home_link = __('Home');
    $order_details = '';
    if($order_id)
    {
        $price  = get_post_meta( $order_id, '_cupri_fprice', true );
        $status = '-';
        $get_post_status_object = get_post_status_object( get_post_status($order_id) );
        if(is_object($get_post_status_object) && !is_wp_error($get_post_status_object )){
            $status =  $get_post_status_object->label;
        }

        $order_details .= '
        <div class="cupri_order_details_wrapper">
        <table class="cupri_order_details">
               <caption> جزيیات پرداخت </caption>
            <tbody>
            <tr>
                <td><strong>'.__('Price','cupri').':</strong> </td>
                <td>'.$price.' ('.cupri_get_currency().')</td>
            </tr>
            <tr>
                <td><strong>'.__('Payment Status','cupri').':</strong> </td>
                <td>'.$status.' </td>
            </tr>';

            if($type == 'success')
            {
                $new_columns = array();
                $_cupri = get_option('_cupri', cupri_get_defaults_fields());
                foreach ($_cupri['type'] as $wc_cf_key => $wc_cf) {
                    $key = 'cupri_f' . $wc_cf_key;
                    $key = '_'.$key;
                    if($key =='_cupri_fprice' || $_cupri['disable'][$wc_cf_key ] ==1) continue;
                    $order_details .= '<tr>
                        <td><strong>'.$_cupri['name'][$wc_cf_key ].':</strong> </td>
                        <td>'.get_post_meta($order_id,$key,true).' </td>
                    </tr>';
                }

            }

            $order_details .='
            </tbody>
              <tfoot>
                <tr class="tfooter">
                  <td colspan=2>'.get_the_date('Y/m/d - g:i A',$order_id).'</td>
                </tr>
              </tfoot>

            ';


        $order_details .='
        </table>
        </div>
        ';
    }
    $_msg .= '<div class="cupri_msg_wrapper"> ' . $msg .$order_details. '<a class="cupri-home-link" href="'.get_bloginfo('url').'"> ℹ '.$home_link.'</a></div>';
    return $_msg;
}
function cupri_success_msg($msg,$order_id=false) {
    $msg = '<p style="color:green;text-align:center;border:1px solid #ededed;" class="cupri-msg cupri-success"><img src="'.cupri_url.'/assets/checked.png" width="50" height="50" >' . $msg . '</p>';
    return cupri_msg($msg,$order_id,$type='success');
}
function cupri_failed_msg($msg,$order_id=false) {
    $msg = '<p style="color:red;text-align:center;border:1px solid #ededed;" class="cupri-msg cupri-error"><img src="'.cupri_url.'/assets/cancel.png" width="50" height="50" >' . $msg . '</p>';
    return cupri_msg($msg,$order_id,$type='failed');
}




function cupri_add_tbl_head(){
  $screen = get_current_screen();
  if( $screen->id =='edit-cupri_pay' )
  {
    require_once cupri_dir.'admin-table-header.php';
}


}
add_action('admin_notices','cupri_add_tbl_head');


function cupri_get_defaults_fields()
{
    $def = array();
    /**
     * Price Field
     */
    $def['type']['price'] = 'price';
    $def['name']['price'] = __('Price' , 'cupri');
    $def['min']['price'] = '';
    $def['default']['price'] = '';
    $def['text_placeholder']['price'] = __('Please enter a price' , 'cupri');

    /**
     * Name
     */
    $def['type'][] = 'text';
    $def['name'][] = __('Name' , 'cupri');
    $def['disable'][] = '';
    $def['required'][] = 1;
    $def['text_placeholder'][] = __('Please enter your name' , 'cupri');


    /**
     * Mobile Field
     */
    $def['type']['mobile'] = 'mobile';
    $def['name']['mobile'] = __('Mobile' , 'cupri');
    $def['disable']['mobile'] = '';
    $def['required']['mobile'] = 1;
    $def['text_placeholder']['mobile'] = __('Please enter your mobile' , 'cupri');

    /**
     * Email Field
     */
    $def['type']['email'] = 'email';
    $def['name']['email'] = __('Email' , 'cupri');
    $def['disable']['email'] = '';
    $def['text_placeholder']['email'] = __('Please enter your email' , 'cupri');




    return $def;
}

/**
 * Extend payments list search to seek in the post_meta table also
 * @thanksTo http://wordpress.stackexchange.com/a/12356
 */

add_filter('posts_join', 'cupri_pay_search_join' );
function cupri_pay_search_join ($join){
    global $pagenow, $wpdb;
    // I want the filter only when performing a search on edit page of Custom Post Type named "cupri_pay"
    if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='cupri_pay' && $_GET['s'] != '') {    
        $join .='LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }
    return $join;
}

add_filter( 'posts_where', 'cupri_pay_search_where' );
function cupri_pay_search_where( $where ){
    global $pagenow, $wpdb;
    // I want the filter only when performing a search on edit page of Custom Post Type named "cupri_pay"
    if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='cupri_pay' && $_GET['s'] != '') {
        $where = preg_replace(
         "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
         "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
    }
    return $where;
}


/**
 * cupri currency
 */
function cupri_get_currency()
{
    return apply_filters( 'cupri_get_currency', __('Toman','cupri') );
}


/**
 * تبدیل اعداد فارسی به انگلیسی
 */
function _wpm_persian_digit_to_eng($digits) {
    return str_replace(
        array(
            '۱',
            '۲',
            '۳',
            '۴',
            '۵',
            '۶',
            '۷',
            '۸',
            '۹',
            '۰',
            ),
        array(
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            '0',

            ),
        $digits
        );
}

/**
 * Prevent From woocommerce to redirect to my-account page
 */
add_action('wp_loaded' , 'cupri_check_wc_redirection');
function cupri_check_wc_redirection(){
    if(current_user_can('manage_cupri_pays' ) && !current_user_can('manage_options'))
    {
        add_filter( 'woocommerce_prevent_admin_access', '__return_false' );
        add_filter( 'woocommerce_disable_admin_bar', '__return_false' );
    }
}

