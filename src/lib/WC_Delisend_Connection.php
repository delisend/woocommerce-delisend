<?php

namespace Delisend\WC\Lib;

if (!defined('ABSPATH')) { exit; }


if ( ! class_exists(\Delisend\WC\Lib\WC_Delisend_Connection::class) ) :

    /**
 * Class WC_Delisend_Connection
 * @package Delisend\WC\Lib
 */
class WC_Delisend_Connection {

    /** @var string Delisend client identifier */
    const CLIENT_ID = '';

    /** @var string Delisend tracking identifier */
    const TRACKING_ID = '';

    /** @var string Delisemd OAuth external URL and authentication */
    const OAUTH_URL = 'https://delisend.com/api/v1/login/';

    /** @var string Delisend OAuth 2.0 Token */
    const BEARER_TOKEN = '';


    /**
     * @return void
     */
    public static function get_connect_url() {

    }


    /**
     * Determines whether the site is connected.
     * A site is connected if there is an access token stored.
     *
     * @return bool
     */
    public function is_connected() {

        return (bool) $this->get_access_token();
    }


    /**
     * Gets the API access token.
     *
     * @return string
     */
    public function get_access_token() {

        $access_token = get_option( WC_Delisend_Definitions::OPTION_ACCESS_TOKEN, '' );

        /**
         * Filters the API access token.
         *
         * @param string $access_token access token
         * @param WC_Delisend_Connection $connection connection handler instance
         */
        return apply_filters( 'wc_delisend_connection_access_token', $access_token, $this );
    }

}

endif;
