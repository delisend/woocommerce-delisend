<?php

namespace Delisend\WC\Lib;

if (!defined('ABSPATH')) { exit; }


/**
 * Class WC_Delisend_Helper
 */
class WC_Delisend_Helper {

    /**
     * Safely gets a value from $_POST.
     * If the expected data is a string also trims it.
     *
     * @param string $key posted data key
     * @param int|float|array|bool|null|string $default default data type to return (default empty string)
     * @return int|float|array|bool|null|string posted data value if key found, or default
     */
    public static function get_posted_value( $key, $default = '' ) {

        $value = $default;

        if ( isset( $_POST[ $key ] ) ) {
            $value = is_string( $_POST[ $key ] ) ? trim( $_POST[ $key ] ) : $_POST[ $key ];
        }

        return $value;
    }

    /**
     * Safely gets a value from $_REQUEST.
     * If the expected data is a string also trims it.
     *
     * @param string $key posted data key
     * @param int|float|array|bool|null|string $default default data type to return (default empty string)
     * @return int|float|array|bool|null|string posted data value if key found, or default
     */
    public static function get_requested_value( $key, $default = '' ) {

        $value = $default;

        if ( isset( $_REQUEST[ $key ] ) ) {
            $value = is_string( $_REQUEST[ $key ] ) ? trim( $_REQUEST[ $key ] ) : $_REQUEST[ $key ];
        }

        return $value;
    }

    /**
     * Get the count of notices added, either for all notices (default) or for one
     * particular notice type specified by $notice_type.
     * WC notice functions are not available in the admin
     *
     * @param string $notice_type The name of the notice type - either error, success or notice. [optional]
     * @return int
     */
    public static function wc_notice_count( $notice_type = '' ) {

        if ( function_exists( 'wc_notice_count' ) ) {
            return wc_notice_count( $notice_type );
        }

        return 0;
    }

    /**
     * Add and store a notice.
     * WC notice functions are not available in the admin
     *
     * @param string $message The text to display in the notice.
     * @param string $notice_type The singular name of the notice type - either error, success or notice. [optional]
     */
    public static function wc_add_notice( $message, $notice_type = 'success' ) {

        if ( function_exists( 'wc_add_notice' ) ) {
            wc_add_notice( $message, $notice_type );
        }
    }

    /**
     * Print a single notice immediately
     * WC notice functions are not available in the admin
     *
     * @param string $message The text to display in the notice.
     * @param string $notice_type The singular name of the notice type - either error, success or notice. [optional]
     */
    public static function wc_print_notice( $message, $notice_type = 'success' ) {

        if ( function_exists( 'wc_print_notice' ) ) {
            wc_print_notice( $message, $notice_type );
        }
    }

    /**
     * Determines if the current request is for a WC REST API endpoint.
     *
     * @see \WooCommerce::is_rest_api_request()
     * @return bool
     */
    public static function is_rest_api_request() {

        if ( is_callable( 'WC' ) && is_callable( [ WC(), 'is_rest_api_request' ] ) ) {
            return (bool) WC()->is_rest_api_request();
        }

        if ( empty( $_SERVER['REQUEST_URI'] ) || ! function_exists( 'rest_get_url_prefix' ) ) {
            return false;
        }

        $rest_prefix         = trailingslashit( rest_get_url_prefix() );
        $is_rest_api_request = false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix );

        /* applies WooCommerce core filter */
        return (bool) apply_filters( 'woocommerce_is_rest_api_request', $is_rest_api_request );
    }

    /**
     * Gets the Delisend for WooCommerce plugin instance.
     *
     * @return WC_Delisend_Plugin instance of the plugin
     */
    public static function wc_delisend(): WC_Delisend_Plugin {
        return WC_Delisend_Plugin::instance();
    }
}