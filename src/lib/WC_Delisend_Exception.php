<?php

namespace Delisend\WC\Lib;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WC_Delisend_Plugin')) :
    /**
     * Class WC_Delisend_Exception
     */
    class WC_Delisend_Exception extends \Exception
    {
    }

endif;