<?php

namespace Delisend\WC\Lib;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WC_Delisend_Endpoint')) :
    abstract class WC_Delisend_Endpoint
    {

    }
endif;