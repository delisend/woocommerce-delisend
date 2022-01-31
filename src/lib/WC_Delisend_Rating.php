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
     * @param int $order_id
     * @return array|bool|null|object|void
     */
    public function getRating(int $order_id) {

        if (!$this->validated_cart_db) return false;

        global $wpdb;

        $table = "{$wpdb->prefix}delisend_rating_history";
        $statement = "SELECT * FROM $table WHERE id = %d";
        $sql = $wpdb->prepare($statement, $order_id);

        if (($rating = $wpdb->get_row($sql)) && !empty($rating)) {
            return $rating;
        }

        return false;
    }

    /**
     * Push a job onto the queue.
     *
     * @param array $rating
     * @param int $delay
     *
     * @return $this
     */
    public function pushRating(array $rating, int $delay = 0)
    {
        global $wpdb;

        $data = array(
            'data'          => maybe_serialize($rating),
            'available_at' => $this->datetime($delay),
            'created_at'   => $this->datetime(),
        );

        if (!$wpdb->insert($this->table, $data) && $this->create_tables_if_required()) {
            if (!$wpdb->insert($this->table, $data)) {

            }
        }

        return $this;
    }
}

endif;