<?php
defined( 'ABSPATH' ) or die(  'No script kiddies please!' );
if(!class_exists('nusoap_client'))
{
	require_once 'nusoap.php';
}
require_once 'class-cupri-abstract-gateway.php';
require_once 'class-cupri-payir-gateway.php';

// cupri_start_payment