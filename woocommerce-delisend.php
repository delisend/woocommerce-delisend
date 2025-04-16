<?php
/*
Plugin Name: Delisend for WooCommerce
Plugin URI: https://rosigroup.com/woocommerce/delisend/
Description: Delisend WooCommerce integration
Author: Rosi Group s.r.o.
Author URI: https://rosigroup.com
Version: 1.2.0
Text Domain: woocommerce-delisend
Domain Path: /languages
WC requires at least: 9.7
WC tested up to: 9.7.1
Requires PHP: 8.0
*/

use Automattic\WooCommerce\Utilities\FeaturesUtil;

if (!defined('ABSPATH')) { exit; }

require_once(__DIR__ . '/includes/functions.php');

require_once(__DIR__ . '/vendor/autoload.php');

// HPOS compatibility declaration.
add_action('before_woocommerce_init', function () {
    if (class_exists(FeaturesUtil::class)) {
        FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__);
        FeaturesUtil::declare_compatibility('product_block_editor', __FILE__);
        FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__);
    }
});

Delisend\WC\WC_Delisend_Loader::instance();
