<?php

namespace Delisend\WC\Lib;

if (!defined('ABSPATH')) { exit; }


if ( ! class_exists(\Delisend\WC\Lib\WC_Delisend_Definitions::class) ) :

/**
 * Class WC_Delisend_Definitions
 * @package Delisend\WC\Lib
 */
class WC_Delisend_Definitions {

    /** @var string the plugin ID */
    const PLUGIN_ID = 'woocommerce_delisend';

    /** @var string The plugin name */
    const PLUGIN_VERSION = '1.0.0';

    /** @var string The slug used to check for updates. */
    const PLUGIN_SLUG_FOR_UPDATES = 'woocommerce-delisend';

    /** @var string The menu slug for plugin's settings page. */
    const MENU_SLUG = 'wc_delisend';

    /** @var string The plugin slug */
    const PLUGIN_SLUG = 'wc-delisend';

    /** @var string The plugin name */
    const PLUGIN_NAME = 'Delisend customer assistant for WooCommerce';

    /** @var string Minimum WordPress version required by this plugin */
    const MINIMUM_WP_VERSION = '5.6';

    /** @var string Minimum PHP version required by this plugin */
    const MINIMUM_PHP_VERSION = '7.2.0';

    /** @var string Minimum WooCommerce version required by this plugin. */
    const MINIMUM_WC_VERSION = '3.5.0';

    /** @var string The plugin text domain */
    const TEXT_DOMAIN = 'woocommerce-delisend';

    /** @var string The action used to route ajax calls to this plugin. */
    const AJAX_ACTION = 'wc-delisend-plugin';

    // Delisend API connection.
    const ARG_API_URL = 'https://delisend.com/api/';
    const ARG_API_VERSION = 'v1';
    const ARG_API_TIMEOUT = 15;

    // GET/POST Arguments
    const ARG_ORDER_ID = 'order_id';
    const ARG_FIRST_NAME = 'first_name';
    const ARG_LAST_NAME = 'last_name';
    const ARG_ADDRESS = 'address';
    const ARG_ZIP = 'zip';
    const ARG_PHONE = 'phone';
    const ARG_EMAIL = 'email';
    const ARG_COUNTRY = 'country';

    // Get/Post Arguments
    const ARG_INSTALL_GEOIP_DB = 'delisend_install_geoip_db';
    const ARG_MESSAGE_ID = 'delisend_msg_id';
    const ARG_AJAX_COMMAND = 'exec';

    // Error codes
    const RES_OK = 0;
    const ERR_INVALID_TEMPLATE = 1001;
    const ERR_INVALID_SOURCE_CURRENCY = 1103;
    const ERR_INVALID_DESTINATION_CURRENCY = 1104;
    const ERR_INVALID_EU_VAT_NUMBER = 5001;
    const ERR_COULD_NOT_VALIDATE_VAT_NUMBER = 5002;

    // URLs
    const URL_CONTACT_FORM = 'https://delisend.com/en/contact';
    const URL_GITLAB_REPOSITORY = 'https://github.com/delisend/woocomerce-delisend/';
    const URL_PUBLIC_SUPPORT_FORUM = ''; //https://wordpress.org/support/plugin/woocomerce-delisend/';

    // Plugin options

    /** @var string the action callback for the connection */
    const OPTION_ACTION_CONNECT = 'wc_delisend_connect';

    /** @var string enable delisend service option name */
    const OPTION_STATUS = 'wc_delisend_status';

    /** @var string  */
    const OPTION_ENVIRONMENT = 'wc_delisend_environment';

    /** @var string the system user access token option name */
    const OPTION_TRACKING_ID = 'wc_delisend_tracking_id';

    /** @var string the system user access token option name */
    const OPTION_ACCESS_TOKEN = 'wc_delisend_access_token';

    /** @var string automatic check when opening an order option name */
    const OPTION_ENABLE_AUTOMATIC_CHECK = 'wc_delisend_enable_automatic_check';

    /** @var string automatic check when opening an order option name */
    const OPTION_HAZARD_RATE = 'wc_delisend_hazard_rate';

    /** @var string automatic check on checkout page */
    const OPTION_ENABLE_ON_CHECKOUT_PAGE = 'wc_delisend_enable_on_checkout_page';

    /** @var string the system user access token option name */
    const OPTION_ENABLE_DEBUG_MODE = 'wc_delisend_enable_debug_mode';

    /** @var string list of prohibited shipping method */
    const OPTION_SHIPPING_FILTER = 'wc_delisend_shipping_filter';

    /** @var string check after for purchase status */
    const OPTION_DELISEND_ORDER_STATUS = 'wc-processing';
}

endif;
