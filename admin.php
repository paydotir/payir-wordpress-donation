<?php
defined( 'ABSPATH' ) or die(  'No script kiddies please!' );
$table = new __wc_custom_donate();
$table->prepare_items();
echo $table->display();
