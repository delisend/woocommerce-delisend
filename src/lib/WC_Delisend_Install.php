<?php

namespace Delisend\WC\Lib;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WC_Delisend_Install')) :

    /**
     * Class WC_Delisend_Install
     *
     * @package Delisend\WC\Lib
     */
    class WC_Delisend_Install
    {

        /** @var WC_Delisend_Install singleton instance */
        protected static $instance;


        /**
         * WC_Delisend_Install constructor.
         */
        public function __construct()
        {
            $this->install();
        }


        /**
         * Gets the plugin singleton instance.
         *
         * @return WC_Delisend_Install the plugin singleton instance
         */
        public static function instance()
        {

            if (null === self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }


        /**
         * Create the queue tables in the DB so we can use it for syncing.
         *
         * @return bool
         */
        private function create_queue_tables()
        {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            global $wpdb;

            $wpdb->hide_errors();

            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}delisend_rating_history (
				id bigint(20) unsigned NOT NULL auto_increment,
				customer_id int (11) DEFAULT NULL,
                order_id varchar (191) NOT NULL,
                type varchar (16) NOT NULL,
                rating_id varchar(191) NOT NULL,
                rating_data mediumtext NOT NULL,
                created_at datetime NOT NULL,
				PRIMARY KEY  (`id`),
				UNIQUE KEY `idx_rating_id` (`rating_id`),
                INDEX `idx_order_id` (`order_id`)
                INDEX `idx_type` (`type`)
				) $charset_collate;";

            dbDelta($sql);

            return true;
        }


        /**
         * Create database table and base plugin options
         *
         * @return void
         */
        private function install(): void
        {

            $this->create_queue_tables();

            // set the Delisend WooCommerce version at the time of install
            update_site_option('wc_delisend_version', WC_Delisend_Definitions::PLUGIN_VERSION);

            // update the settings so we have them for use.
            $check_options = get_option('wc_delisend_params_store_id', false);

            // if we haven't saved options previously, we will need to create the site id and update base options
            if (empty($check_options)) {
                WC_Delisend_Utils::clean_database();
                update_option('wc_delisend_params', array());

                // Base plugin options
                $json_data = '{"wc_delisend_enable":"1","wc_delisend_connect":"1","wc_delisend_enable_debug_mode":"","wc_delisend_order_status":"wc-processing","wc_delisend_info_box":{"highlight_color":"#000000","text_color":"#000000","background_color":"#ffffff","border_radius":"3","show_close_icon":"1","image_redirect":"1","message_display_effect":"fade-in","message_hidden_effect":"fade-out"},"message_purchased":["Someone in {city} purchased a {product_with_link} {time_ago}","{product_with_link} {custom}"]}';

                if (!get_option('delisend_params', '')) {
                    update_option('wc_delisend_params_store_id', WC_Delisend_Utils::get_domain_name(get_site_url()));
                    update_option('wc_delisend_params', json_decode($json_data, true));
                    update_option('wc_delisend_access_token', json_decode("", true));
                }

                // only do this if the option has never been set before.
                if (!is_multisite()) {
                    add_option('wc_delisend_woocommerce_plugin_do_activation_redirect', true);
                }
            }
        }

    }

endif;