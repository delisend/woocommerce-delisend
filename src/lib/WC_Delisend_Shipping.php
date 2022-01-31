<?php

namespace Delisend\WC\Lib;

use Exception;
use WC_Data_Store;

if (!defined('ABSPATH')) { exit; }

if ( ! class_exists( 'WC_Delisend_Shipping' ) ) :

    class WC_Delisend_Shipping {

        /**
         * Get the order if ID is passed, otherwise the order is new and empty.
         *
         * @see WC_Delisend_Order::__construct()
         */
        public function __construct() {

        }


        /**
         * Get a list of all active shipping methods.
         *
         * @return array
         * @throws Exception
         */
        public static function get_shipping_methods()
        {
            $data = array();
            $zones = fn_delisend_get_zones();

            foreach ( $zones as $zone) {
                foreach ($zone['shipping_methods'] as $key => $shipping_method) {
                    $shipping_list[$key]['instance_id'] = $shipping_method->instance_id;
                    $shipping_list[$key]['title'] = $shipping_method->title;
                }
            }

            foreach ($shipping_list as $shipping) {
                $data[$shipping['instance_id']]  =  $shipping['title'];
            }
            
            return $data;
        }
    }

endif;