<?php

namespace Delisend\WC\Lib;

if (!defined('ABSPATH')) { exit; }

if ( ! class_exists( 'WC_Delisend_Utils' ) ) :

/**
 * Class WC_Delisend_Utils
 */
class WC_Delisend_Utils {

    /** @var string plugin URL */
	protected static $plugin_url;


    /**
     * Returns true if WooCommerce plugin found.
     *
     * @return bool
     */
    public static function isWoocommerceIntegration() {

        if (class_exists( 'WooCommerce' )) {
            return true;
        }

        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            return true;
        }

        return false;
    }


    /**
     * Determines if the server environment is compatible with this plugin.
     * Override this method to add checks for more than just the PHP version.
     *
     * @return bool
     */
    public static function is_environment_compatible() {
        return version_compare(PHP_VERSION, WC_Delisend_Definitions::MINIMUM_PHP_VERSION, '>=');
    }


    /**
     * Returns integration dependent name.
     *
     * @access public
     * @return string
     */
    public static function getIntegrationName() {
        if ( self::isWoocommerceIntegration() ) {
            return 'WooCommerce';
        }

        return 'WordPress';
    }


    /**
     * Determines whether the enhanced admin is available.
     * This checks both for WooCommerce v4.0+ and the underlying package availability.
     *
     * @return bool
     */
    public static function is_enhanced_admin_available() {
        return self::is_wc_version_gte( '4.0' ) && function_exists( 'wc_admin_url' );
    }


    /**
    * Gets the version of the currently installed WooCommerce.
    *
    * @return string|null Woocommerce version number or null if undetermined
    */
    public static function get_wc_version() {
        return defined( 'WC_VERSION' ) && WC_VERSION ? WC_VERSION : null;
    }


    /**
     * Determines if the installed WooCommerce version matches a specific version.
     *
     * @param string $version semver
     * @return bool
     */
    public static function is_wc_version( $version ) {
        $wc_version = self::get_wc_version();
        return $wc_version === $version || ( $wc_version && version_compare( $wc_version, $version, '=' ) );
    }


    /**
     * Determines if the installed version of WooCommerce is equal or greater than a given version.
     *
     * @param string $version version number to compare
     * @return bool
     */
    public static function is_wc_version_gte( $version ) {
        $wc_version = self::get_wc_version();
        return $wc_version && version_compare( $wc_version, $version, '>=' );
    }


    /**
     * Gets the settings page URL.
     *
     * @param null $plugin_id unused
     * @return string
     */
    public function get_settings_url( $plugin_id = null ) {
        return admin_url( 'admin.php?page=wc-delisend' );
    }


    /**
     * Determines if the installed version of WooCommerce is lower than a given version.
     *
     * @param string $version version number to compare
     * @return bool
     */
    public static function is_wc_version_lt( $version ) {
        $wc_version = self::get_wc_version();
        return $wc_version && version_compare( $wc_version, $version, '<' );
    }


    /**
     * Gets the plugin's documentation URL.
     *
     * @return string
     */
    public static function get_documentation_url() {
        return 'https://delisend.com/docs';
    }


    /**
     * Gets the login URL.
     *
     * @return string
     */
    public static function get_connect_url() {
        return 'https://delisend.com/login';
    }


    /**
     * Gets the plugin's support URL.
     *
     * @return string
     */
    public static function get_support_url() {
        return 'https://delisend.com/support';
    }


    /**
     * Gets the services sales page URL.
     *
     * @return string
     */
    public static function get_services_page_url() {
        return 'https://delisend.com/#pricing';
    }


    /**
     * Gets the plugin's sales page URL.
     *
     * @return string
     */
    public static function get_sales_page_url() {

        $user_locale = get_user_locale();

        if ($user_locale === 'sk_SK') {
            $lang = 'sk';
        } else if ($user_locale === 'cz_CZ') {
            $lang = 'cz';
        } else {
            $lang = 'en';
        }

        return 'https://addons.rosigroup.com/'.$lang.'/wordpress/delisend';
    }


    /**
     * Gets the plugin's sales page URL.
     *
     * @return string
     */
    public static function get_plugin_url() {

        if ( null === self::$plugin_url ) {
            self::$plugin_url = dirname(untrailingslashit( plugins_url( '/', self::get_file() ) ),2);
        }

        return self::$plugin_url;
    }


    /**
     * Gets the plugin file.
     *
     * @return string
     */
    protected static function get_file() {
        return __FILE__;
    }


    /**
     * Gets the woocommerce uploads path, without trailing slash.
     * Oddly WooCommerce core does not provide a way to get this.
     *
     * @return string
     */
    public static function get_woocommerce_uploads_path() {
        $upload_dir = wp_upload_dir();
        return $upload_dir['basedir'] . '/woocommerce_uploads';
    }


    /**
     * To be used when running clean up for uninstalls or store disconnection.
     */
    public static function clean_database() {
        global $wpdb;

        // delete custom tables data
        self::flush_database_tables();

        // delete plugin options
        $plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'wc%delisend%'" );

        foreach( $plugin_options as $option ) {
            delete_option( $option->option_name );
        }
    }


    /**
     *
     */
    public static function flush_database_tables() {
        try {
            global $wpdb;
            $wpdb->query("TRUNCATE `{$wpdb->prefix}delisend_rating_history`");
        } catch (\Exception $e) {}
    }


    /**
     * @param $domain
     * @return array|false|string|string[]
     */
    public static function get_domain_name($domain) {
        $domain = strtolower($domain);
        $domain = str_replace(['http://', 'https://', 'www.'], '', $domain);
        if (str_contains($domain, '/')) {
            $domain = strstr($domain, '/', true);
        }
        if (str_contains($domain, '@')) {
            $domain = strstr($domain, '@');
            $domain = str_replace('@', '', $domain);
        }

        return $domain;
    }

    /**
     * Returns the plugin id with dashes in place of underscores, and
     * appropriate for use in frontend element names, classes and ids
     *
     * @return string plugin id with dashes in place of underscores
     */
    public static function get_id_dasherized() {
        return str_replace( '_', '-', WC_Delisend_Definitions::PLUGIN_ID );
    }
}


if (!function_exists('str_contains')) {
    /**
     * A PHP 7.0+ compatible polyfill
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function str_contains(string $haystack, string $needle): bool {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}

endif;