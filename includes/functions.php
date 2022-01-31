<?php

if (!defined('ABSPATH')) { exit; }

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
     * @since 1.0.0
     */
    function fn_delisend_get_zones(string $context = 'admin')
    {
        $data_store = WC_Data_Store::load('shipping-zone');
        $raw_zones = $data_store->get_zones();
        $zones = array();

        foreach ($raw_zones as $raw_zone) {
            $zone = new WC_Shipping_Zone($raw_zone);
            $zones[$zone->get_id()] = $zone->get_data();
            $zones[$zone->get_id()]['zone_id'] = $zone->get_id();
            $zones[$zone->get_id()]['formatted_zone_location'] = $zone->get_formatted_location();
            $zones[$zone->get_id()]['shipping_methods'] = $zone->get_shipping_methods(false, $context);
        }

        return $zones;
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
     * @since 1.0.0
     */
    function fn_delisend_get_zone_by(string $by = 'zone_id', int $id = 0)
    {
        $zone_id = false;

        switch ($by) {
            case 'zone_id':
                $zone_id = $id;
                break;
            case 'instance_id':
                $data_store = WC_Data_Store::load('shipping-zone');
                $zone_id = $data_store->get_zone_id_by_instance_id($id);
                break;
        }

        if (false !== $zone_id) {
            try {
                return new WC_Shipping_Zone($zone_id);
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

}

if (!function_exists('fn_delisend_get_ip_address')) {

    /**
     * Get user IP address
     *
     * @return string|null
     * @throws Exception
     * @since 1.0.0
     */
    function fn_delisend_get_ip_address()
    {
        $ip_address = WC_Geolocation::get_ip_address();
        return (! empty( $ip_address ) ? $ip_address : null );
    }

}