<?php

namespace Delisend\WC\Lib;

if (!defined('ABSPATH')) { exit; }


if ( ! class_exists( 'WC_Delisend_Order' ) ) :

/**
 * Class WC_Delisend_Order
 * @package Delisend\WC\Lib
 */
class WC_Delisend_Order {

    /**
     * Get the order if ID is passed, otherwise the order is new and empty.
     *
     * @see WC_Order::__construct()
     */
    public function __construct() {

    }
}

endif;
