<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Function include all files in folder
 *
 * @param $path   Directory address
 * @param $ext    array file extension what will include
 * @param $prefix string Class prefix
 */
if (!function_exists('fn_delisend_get_zones')) {

    /**
     * Get shipping zones from the database.
     *
     * @param string $context Getting shipping methods for what context. Valid values, admin, json.
     * @return array Array of arrays.
     * @throws Exception
     */
    function fn_delisend_get_zones(string $context = 'admin'): array
    {
        return WC_Shipping_Zones::get_zones();
    }
}

if (!function_exists('fn_delisend_get_zone_by')) {

    /**
     * Get shipping zone by an ID.
     *
     * @param string $by Get by 'zone_id' or 'instance_id'.
     * @param int $id ID.
     * @return WC_Shipping_Zone|bool
     * @throws Exception
     */
    function fn_delisend_get_zone_by(string $by = 'zone_id', int $id = 0): int|null
    {
        return WC_Shipping_Zones::get_zone_by($by, $id);
    }
}

if (!function_exists('fn_delisend_get_ip_address')) {

    /**
     * Get user IP address
     *
     * @return string|null
     * @throws Exception
     */
    function fn_delisend_get_ip_address(): ?string
    {
        $ip_address = WC_Geolocation::get_ip_address();
        return (!empty($ip_address) ? $ip_address : null);
    }
}

if (!function_exists('fn_delisend_customize_hazard_rate_range')) {
    /**
     * Input attributes for hazard rate opacity option.
     *
     * @return array Array containing attribute names and their values.
     */
    function fn_delisend_customize_hazard_rate_range(): array
    {
        /**
         * Filters the input attributes for hazard rate.
         *
         * @param array $attrs {
         *     The attributes.
         *
         * @type int $min Minimum value.
         * @type int $max Maximum value.
         * @type int $step Interval between numbers.
         * }
         */
        return apply_filters('delisend_customize_hazard_rate_range', [
            'min' => 0,
            'max' => 90,
            'step' => 5,
        ]);
    }
}