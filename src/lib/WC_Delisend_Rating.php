<?php

namespace Delisend\WC\Lib;

if (!defined('ABSPATH')) { exit; }

if (!class_exists('WC_Delisend_Rating')) :

    /**
     * Class WC_Delisend_Commerce
     *
     * @package Delisend\WC\Lib
     */
    class WC_Delisend_Rating
    {
        const DATE_FORMAT = 'Y-m-d H:i:s';
        const TABLE = 'delisend_rating_history';
        const DEFAULT_DATE_FORMAT = 'Y-m-d';
        const DEFAULT_TIME_FORMAT = 'H:i:s';
        const DEFAULT_DATE_TIME_FORMAT = 'Y-m-d H:i:s';

        /** @var WC_Delisend_Rating singleton instance */
        protected static WC_Delisend_Rating $instance;

        /**
         * @var false|mixed|string|null
         */
        protected mixed $dateFormat;

        /**
         * @var false|mixed|string|null
         */
        protected mixed $timeFormat;

        /**
         * @var bool
         */
        protected bool $evaluation = false;

        /**
         * WC_Delisend_Rating constructor.
         */
        public function __construct()
        {
            $this->dateFormat = get_option('date_format');
            if (empty($this->dateFormat)) $this->dateFormat = self::DEFAULT_DATE_FORMAT;

            $this->timeFormat = get_option('time_format');
            if (empty($this->timeFormat)) $this->timeFormat = self::DEFAULT_TIME_FORMAT;

        }


        /**
         * @return bool
         */
        public function isEvaluation(): bool
        {
            return $this->evaluation;
        }


        /**
         * @param bool $evaluation
         */
        public function setEvaluation(bool $evaluation): void
        {
            $this->evaluation = $evaluation;
        }

        /**
         * Gets the plugin singleton instance.
         *
         * @return WC_Delisend_Rating the plugin singleton instance
         */
        public static function instance(): WC_Delisend_Rating
        {

            if (null === self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }


        /**
         * Get log by primery key
         *
         * @param int $order_id
         *
         * @return array|null
         */
        public function read(int $order_id): ?array
        {
            global $wpdb;

            $table = $this->table();
            $statement = "SELECT * FROM $table WHERE id = %d";
            $sql = $wpdb->prepare($statement, $order_id);

            if (($rating = $wpdb->get_row($sql)) && !empty($rating)) {
                $rating['rating_data'] = maybe_unserialize($rating['rating_data']);
                return $rating;
            }

            return null;
        }


        /**
         * Get log by delisend rating ID
         *
         * @param string $rating_id
         *
         * @return array|null
         */
        public function getByRatingId(string $rating_id): ?array
        {
            global $wpdb;

            $table = $this->table();
            $statement = "SELECT * FROM $table WHERE rating_id = %s";
            $sql = $wpdb->prepare($statement, $rating_id);

            if (($rating = $wpdb->get_row($sql, 'ARRAY_A')) && !empty($rating)) {
                $rating['rating_data'] = maybe_unserialize($rating['rating_data']);
                return $rating;
            }

            return null;
        }


        /**
         * Get log by order ID
         *
         * @param int $order_id
         *
         * @return array|null
         */
        public function getByOrderId(int $order_id): ?array
        {
            global $wpdb;

            $table = $this->table();
            $statement = "SELECT * FROM $table WHERE order_id = %s and type ='get'";
            $sql = $wpdb->prepare($statement, $order_id);

            if (($rating = $wpdb->get_row($sql, 'ARRAY_A')) && !empty($rating)) {
                $rating['rating_data'] = maybe_unserialize($rating['rating_data']);
                return $rating;
            }

            return null;
        }
        
        /**
         * Get log by order ID
         *
         * @param int $order_id
         *
         * @return array|null
         */
        public function get_by_submit_order_to_delisend(int $order_id): ?array
        {
            global $wpdb;

            $table = $this->table();
            $statement = "SELECT * FROM $table WHERE `order_id` = %s and `type` ='create' ORDER BY `created_at` DESC";
            $sql = $wpdb->prepare($statement, $order_id);

            if (($rating = $wpdb->get_row($sql, 'ARRAY_A')) && !empty($rating)) {
                $rating['rating_data'] = maybe_unserialize($rating['rating_data']);
                return $rating;
            }

            return null;
        }


        /**
         * Create a new log in the database.
         *
         * @param int $customer_id
         * @param int $order_id
         * @param array $rating
         * @param string $type
         *
         * @return mixed int|false The number of rows inserted, or false on error.
         * @throws \Exception
         */
        public function create_get_log(int $customer_id, int $order_id, array $rating, string $type = 'get')
        {
            global $wpdb;

            $checkIfRatingExist = $this->getByRatingId($rating['rating_id']);

            if (!empty($checkIfRatingExist)) {
                return $checkIfRatingExist;
            }

            if (!$this->isEvaluation()) {
                unset($rating['results']['evaluation']);
            }

            $rating_data = $rating;

            return $wpdb->insert($this->table(), [
                'customer_id' => $customer_id,
                'order_id' => $order_id,
                'type' => $type,
                'rating_id' => $rating['rating_id'],
                'rating_data' => maybe_serialize($rating_data),
                'created_at' => $this->datetime()->format(self::DEFAULT_DATE_TIME_FORMAT),
            ]);
        }

        /**
         * Create a new log in the database.
         *
         * @param int $customer_id
         * @param int $order_id
         * @param array $response
         * @param string $type
         *
         * @return mixed int|false The number of rows inserted, or false on error.
         * @throws \Exception
         */
        public function add_create_type_log(int $customer_id, int $order_id, array $response, string $type = 'create')
        {
            global $wpdb;
            return $wpdb->insert($this->table(), [
                'customer_id' => $customer_id,
                'order_id' => $order_id,
                'type' => $type,
                'rating_id' => null,
                'rating_data' => maybe_serialize($response),
                'created_at' => $this->datetime()->format(self::DEFAULT_DATE_TIME_FORMAT),
            ]);
        }

        /**
         * Build table name with prefix
         *
         * @return string
         */
        protected function table(): string
        {
            global $wpdb;
            return "{$wpdb->prefix}" . self::TABLE;
        }


        /**
         * @param string|null $date
         * @param string|null $format
         *
         * @return \DateTime
         * @throws \Exception
         */
        protected function dateTime(string $date = null, string $format = null): \DateTime
        {
            if ($date === null) {
                return $this->getCurrentDateTime();
            }

            if ($date && $format === null) {
                return \DateTime::createFromFormat(self::DATE_FORMAT, $date);
            }

            return \DateTime::createFromFormat($format, $date);
        }


        /**
         * @throws \Exception
         */
        protected function getCurrentDateTime(): \DateTime
        {
            return new \DateTime("now", wp_timezone());
        }
    }

endif;