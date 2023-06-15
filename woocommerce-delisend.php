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
WC tested up to: 6.2.2
Requires at least: 5.0
Requires PHP: 7.4
*/

if (!defined('ABSPATH')) { exit; }

require_once(__DIR__ . '/includes/functions.php');

// Load Composer autoloader
require_once(__DIR__ . '/vendor/autoload.php');

// Plugins loaded callback
Delisend\WC\WC_Delisend_Loader::instance();
