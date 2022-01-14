<?php

namespace Delisend\WC\Lib;

if (!defined('ABSPATH')) { exit; }


if ( ! class_exists( 'WC_Delisend_Rating' ) ) :

/**
 * Class WC_Delisend_Commerce
 * @package Delisend\WC\Lib
 * @TODO implementovat nastavenie pre automaticky overovanie pri otvoreni objednavky
 */
class WC_Delisend_Rating
{

    /** @var WC_Delisend_Rating singleton instance */
    protected static $instance;

    /**
     * WC_Delisend_Rating constructor.
     */
    public function __construct() {

    }

    /**
     * Gets the plugin singleton instance.
     *
     * @return WC_Delisend_Rating the plugin singleton instance
     */
    public static function instance(): WC_Delisend_Rating {

        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param $uid
     * @return array|bool|null|object|void
     */
    protected function getCart($uid) {

        if (!$this->validated_cart_db) return false;

        global $wpdb;

        $table = "{$wpdb->prefix}delisend_rating_history";
        $statement = "SELECT * FROM $table WHERE id = %s";
        $sql = $wpdb->prepare($statement, $uid);

        if (($saved_cart = $wpdb->get_row($sql)) && !empty($saved_cart)) {
            return $saved_cart;
        }

        return false;
    }
}

endif;