<?php
/*
Plugin Name: Delisend for WooCommerce
Plugin URI: https://addons.rosigroup.com/woocommerce/delisend/
Description: Delisend WooCommerce integration
Author: Rosi Group s.r.o.
Author URI: https://rosigroup.com
Version: 1.0.0
Text Domain: wc-delisend
Domain Path: /languages
WC requires at least: 3.5
WC tested up to: 5.1
Requires at least: 5.0
Requires PHP: 7.0
*/

if (!defined('ABSPATH')) { exit; }

// Load Composer autoloader
require_once(__DIR__ . '/vendor/autoload.php');

// plugins loaded callback
Delisend\WC\WC_Delisend_Loader::instance();
