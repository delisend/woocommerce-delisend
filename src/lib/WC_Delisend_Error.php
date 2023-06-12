<?php

namespace Delisend\WC\Lib;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WC_Delisend_Error')) :
    final class WC_Delisend_Error
    {
        const UNKNOWN = 'unknown';
        const BAD_REQUEST = 'bad_request';
        const UNAUTHORIZED = 'unauthorized';
        const FORBIDDEN = 'forbidden';
        const NOT_FOUND = 'not_found';

        private function __construct()
        {

        }
    }

endif;