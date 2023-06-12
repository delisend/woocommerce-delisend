<?php

namespace Delisend\WC\Lib;

use DelisendApi\Configuration;
use DelisendApi\Client;
use DelisendApi\HeaderSelector;
use DelisendApi\DelisendRestAPI;
use Exception;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WC_Delisend_Api')) :

    class WC_Delisend_Api extends WC_Delisend_Endpoint
    {
        /**
         * Core singleton class
         *
         * @var self
         */
        private static $_instance;

        /**
         * @var Configuration
         */
        private Configuration $config;

        /**
         * Initializes this class
         *
         * @see WC_Delisend_Api::__construct()
         */
        public function __construct()
        {
            // Configure HTTP basic Bearer token authentication: BasicAuth
            $token = get_option(WC_Delisend_Definitions::OPTION_ACCESS_TOKEN);
            $tracking = get_option(WC_Delisend_Definitions::OPTION_TRACKING_ID);
            $environment = get_option(WC_Delisend_Definitions::OPTION_ENVIRONMENT);

            if (!empty($tracking) && !empty($token)) {
                $this->config = Configuration::instance()
                    ->setAccessToken($token)
                    ->setTrackingId($tracking)
                    ->setEnvironment($environment);
            }
        }


        /**
         * Gets the instance of this class.
         *
         * @return WC_Delisend_Api
         */
        public static function getInstance(): WC_Delisend_Api
        {
            if (!(self::$_instance instanceof self)) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * Get a list of all active shipping methods.
         *
         * @return array
         * @throws Exception
         */
        public function get_delisend_my_status()
        {
            $result = [];

            if ( empty($this->config)) {
                return $result;
            }

            $delisend = new DelisendRestAPI(new Client, $this->config, new HeaderSelector);

            try {
                $accountApi = $delisend->AccountApi();
                $result = json_decode($accountApi->accountGet());
            } catch (Exception $e) {
                echo 'Exception when calling AccountApi->accountGet: ', $e->getMessage(), PHP_EOL;
            }

            return $result;
        }


        /**
         * Get a list of all active shipping methods.
         *
         * @return array
         * @throws Exception
         */
        public function get_delisend_rating($order_id = 0)
        {
            $result = [];
            $store_id = preg_replace( "#^[^:/.]*[:/]+#i", "", get_option( 'siteurl' ));

            $server_ip_address = $this->getServerIpAddress();

            $order = wc_get_order($order_id);

            if ( empty($order) || empty($this->config)) {
                return $result;
            }

            $delisend = new DelisendRestAPI(new Client, $this->config, new HeaderSelector);

            $request['data']['email'] = $order->get_billing_email();
            $request['data']['phone'] = $order->get_billing_phone();
            $request['data']['firstname'] = $order->get_billing_first_name();
            $request['data']['lastname'] = $order->get_billing_last_name();
            $request['data']['address'] = $order->get_billing_address_1();
            $request['data']['zip'] = $order->get_billing_postcode();
            $request['data']['city'] = $order->get_billing_city();

            $request['data']['country_id'] = $order->get_billing_country();
            $request['data']['order_id'] = $order->get_id();
            $request['data']['client_id'] = $order->get_user_id();
            $request['data']['customer_id'] = $order->get_user_id();
            $request['data']['customer_ip'] = $order->get_customer_ip_address();
            $request['data']['type'] = 'rma';
            $request['data']['user_agent'] = $order->get_customer_user_agent();
            $request['data']['payment_method'] = $order->get_payment_method_title();
            $request['data']['shipping_methods'] = $order->get_shipping_method();
			$request['data']['payment_details'] = array(
                'method_id' => $order->get_payment_method(),
                'method_title' => $order->get_payment_method_title(),
                'paid' => ! is_null( $order->get_date_paid() ),
            );
            $request['domain'] = get_option( 'siteurl' );
            $request['tracking_id'] = $this->config->getTrackingId();
            $request['request_ip'] = $server_ip_address;
            $request['store_id'] = $store_id;

            try {
                $ratingApi = $delisend->RatingApi();
                if (!empty($request)) {
                    $result = json_decode($ratingApi->ratingGet($request));
                }
            } catch (Exception $e) {
                dump(json_decode($e->getResponseBody()));
            }

            return $result;
        }


        /**
         * @param int $order_id
         * @param float $rating
         * @param string|null $comment
         * @param string $type
         *
         * @return array|mixed
         */
        public function create_delisend_customer_rating(int $order_id, float $rating = 2, string $comment = null, string $type = 'rma')
        {
            $store_id = preg_replace("#^[^:/.]*[:/]+#i", "", get_option('siteurl'));
            $order = wc_get_order($order_id);

            $server_ip_address = $this->getServerIpAddress();

            $result = [];
            $request = [];

            if (empty($order) || empty($this->config)) {
                return $result;
            }

            $delisend = new DelisendRestAPI(new Client, $this->config, new HeaderSelector);

            $request['data']['email'] = $order->get_billing_email();
            $request['data']['phone'] = $order->get_billing_phone();

            $request['data']['firstname'] = $order->get_billing_first_name();
            $request['data']['lastname'] = $order->get_billing_last_name();
            $request['data']['address'] = $order->get_billing_address_1();
            $request['data']['city'] = $order->get_billing_city();
            $request['data']['zip'] = $order->get_billing_postcode();
            $request['data']['country_id'] = $order->get_billing_country();
            $request['data']['order_id'] = $order->get_id();
            $request['data']['customer_id'] = $order->get_user_id();
            $request['data']['customer_ip'] = $order->get_customer_ip_address();
            $request['data']['type'] = $type;
            $request['domain'] = get_option( 'siteurl' );
            $request['tracking_id'] = $this->config->getTrackingId();
            $request['request_ip'] = $server_ip_address;
            $request['store_id'] = $store_id;

            try {
                $ratingApi = $delisend->RatingApi();
                if (!empty($request)) {
                    $result = json_decode($ratingApi->createRatingByOrder($request, $order_id, $rating, $comment));
                }
            } catch (Exception $e) {
                dump(json_decode($e->getResponseBody()));
            }

            return $result;
        }


        /**
         * @param array $shipping
         *
         * @return array
         * @throws Exception
         */
        public function get_delisend_rating_by_shipping(array $shipping): array
        {
            $result = [];

            if (empty($shipping) || empty($this->config)) {
                return $result;
            }

            $delisend = new DelisendRestAPI(new Client, $this->config, new HeaderSelector);
            $request = $this->create_delisend_request_data_by_shipping($shipping);

            try {
                $ratingApi = $delisend->RatingApi();
                if (!empty($request)) {
                    $result = json_decode($ratingApi->ratingGet($request));
                }
            } catch (Exception $e) {
                //dump(json_decode($e->getResponseBody()));
            }

            return $result;
        }


        /**
         * @param $shipping
         *
         * @return array
         * @throws Exception
         */
        protected function create_delisend_request_data_by_shipping($shipping): array
        {
            $store_id = preg_replace("#^[^:/.]*[:/]+#i", "", get_option('siteurl'));

            $server_ip_address = $this->getServerIpAddress();

            $billing_email = strtolower(sanitize_email($shipping['billing_email']));
            $user = get_user_by('email', $billing_email);
            $gateway = WC()->payment_gateways->payment_gateways()[$shipping['payment_method']];

            $request['data']['email'] = $shipping['billing_email'];
            $request['data']['phone'] = $shipping['billing_phone'];
            $request['data']['firstname'] = $shipping['billing_first_name'];
            $request['data']['lastname'] = $shipping['billing_last_name'];
            $request['data']['address'] = $shipping['billing_address_1'];
            $request['data']['zip'] = $shipping['billing_postcode'];
            $request['data']['city'] = $shipping['billing_city'];

            $request['data']['country_id'] = $shipping['billing_country'];
            $request['data']['order_id'] = 0;
            $request['data']['client_id'] = $user->ID;
            $request['data']['customer_ip'] = fn_delisend_get_ip_address();
            $request['data']['type'] = 'rma';
            $request['data']['user_agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unavailable';
            $request['data']['payment_method'] = $shipping['payment_method'];
            $request['data']['shipping_methods'] = get_the_title($shipping['shipping_method'][0]);
            $request['data']['payment_details'] = array(
                'method_id' => $gateway->id,
                'method_title' => $gateway->method_title,
                'paid' => null,
            );
            $request['domain'] = get_option('siteurl');
            $request['tracking_id'] = $this->config->getTrackingId();
            $request['request_ip'] = $server_ip_address;
            $request['store_id'] = $store_id;

            return $request;
        }

        /**
         * @return mixed|string
         */
        public function getServerIpAddress()
        {
            $server_ip_address = (!empty($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : "");
            if (empty($server_ip_address)) {
                $server_ip_address = (!empty($_SERVER['LOCAL_ADDR']) ? $_SERVER['LOCAL_ADDR'] : "");
            }
            return $server_ip_address;
        }

        protected function create_delisend_request_data_by_order()
        {

        }
    }

endif;