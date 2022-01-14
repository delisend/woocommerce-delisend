<?php

namespace Delisend\WC\Lib\Screens;

if (!defined('ABSPATH')) {
    exit;
}

use Delisend\WC\Lib\WC_Delisend_Definitions;
use Delisend\WC\Lib\WC_Delisend_Connection;
use Delisend\WC\Lib\WC_Delisend_Helper;
use Delisend\WC\Lib\WC_Delisend_Utils;

if (!class_exists(\Delisend\WC\Lib\Screens\Connection::class)) :

    /**
     * Class Connection
     */
    class Connection extends Abstract_Settings_Screen
    {

        /** @var string screen ID */
        public const ID = 'connection';

        /** @var bool */
        protected bool $is_connected;


        /**
         * Connection constructor.
         */
        public function __construct()
        {
            $this->id = self::ID;
            $this->label = __('Delisend API Connection', WC_Delisend_Definitions::TEXT_DOMAIN);
            $this->title = __('Delisend API Connection', WC_Delisend_Definitions::TEXT_DOMAIN);

            $connection = new WC_Delisend_Connection();
            $this->is_connected = $connection->is_connected();

            add_action('admin_notices', array($this, 'add_notices'));
        }


        /**
         * @return array|void
         */
        public function get_settings()
        {
            return array(

                array(
                    'title' => $this->title,
                    'type' => 'title',
                ),

                array(
                    'id' => WC_Delisend_Definitions::OPTION_STATUS,
                    'title' => __('Enable/Disable', WC_Delisend_Definitions::TEXT_DOMAIN),
                    'type' => 'checkbox',
                    'desc' => __('Enable checking orders', WC_Delisend_Definitions::TEXT_DOMAIN),
                    'label' => __('Enable Delisend plugin', WC_Delisend_Definitions::TEXT_DOMAIN),
                    'default' => 'yes'
                ),

                array(
                    'id' => WC_Delisend_Definitions::OPTION_TRACKING_ID,
                    'title' => __('Tracking ID', WC_Delisend_Definitions::TEXT_DOMAIN),
                    'type' => 'text',
                    'desc_tip' => true,
                ),

                array(
                    'id' => WC_Delisend_Definitions::OPTION_ACCESS_TOKEN,
                    'title' => __('Barber API Token', WC_Delisend_Definitions::TEXT_DOMAIN),
                    'description' => __('Log plugin events for debugging', WC_Delisend_Definitions::TEXT_DOMAIN),
                    'type' => 'textarea',
                    'default' => '',
                    'class' => 'input-delisend-api-token',
                    'css' => 'width:500px; height: 250px;',
                    'placeholder' => '',
                    'attributes' => array(),
                ),

                array(
                    'id' => WC_Delisend_Definitions::OPTION_ENABLE_AUTOMATIC_CHECK,
                    'title' => __('Enable Automatic check', WC_Delisend_Definitions::TEXT_DOMAIN),
                    'type' => 'checkbox',
                    'desc' => __('Enable automatic check when opening an order', WC_Delisend_Definitions::TEXT_DOMAIN),
                    'desc_tip' => true,
                    'default' => 'no',
                ),

                array(
                    'id' => WC_Delisend_Definitions::OPTION_ENABLE_DEBUG_MODE,
                    'title' => __('Enable debug mode', WC_Delisend_Definitions::TEXT_DOMAIN),
                    'type' => 'checkbox',
                    'desc' => __('Log plugin events for debugging', WC_Delisend_Definitions::TEXT_DOMAIN),
                    'desc_tip' => true,
                    'default' => 'no',
                ),

                array(
                    'type' => 'sectionend',
                    'id' => 'delisend_endpoint_options',
                ),
            );
        }


        /**
         * Adds admin notices.
         *
         * @internal
         */
        public function add_notices()
        {

            // display a notice if the connection has previously failed
            if (get_transient('wc_delisend_connection_failed')) {

                $message = sprintf(
                /* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag, %3$s - <a> tag, %4$s - </a> tag, %5$s - <a> tag, %6$s - </a> tag */
                    __('%1$sHeads up!%2$s It looks like there was a problem with reconnecting your site to Delisend. Please %3$sclick here%4$s to try again, or %5$sget in touch with our support team%6$s for assistance.', WC_Delisend_Definitions::TEXT_DOMAIN),
                    '<strong>',
                    '</strong>',
                    '<a href="' . esc_url(WC_Delisend_Utils::get_connect_url()) . '">',
                    '</a>',
                    '<a href="' . esc_url(WC_Delisend_Utils::get_support_url()) . '" target="_blank">',
                    '</a>'
                );

                WC_Delisend_Helper::wc_delisend()->get_notice_handler()->add_admin_notice(
                    $message,
                    'wc_delisend_connection_failed',
                    array(
                        'notice_class' => 'error',
                    )
                );

                delete_transient('wc_delisend_connection_failed');
            }
        }


        /**
         * Renders the screen.
         *
         */
        public function render()
        {
            /**
             * Filters the screen settings.
             *
             * @param array $settings settings
             */
            $settings = (array)apply_filters('wc_delisend_admin_' . $this->get_id() . '_settings', $this->get_settings(), $this);
            ?>

            <?php WC_Delisend_Helper::wc_delisend()->get_message_handler()->show_messages(); ?>

            <form class="wc-delisend-settings" method="post" id="mainform" action="" enctype="multipart/form-data">

                <?php if ($this->is_connected) : ?>
                <?php // @TODO Moze sa pouzit ak sa nebude token pridavat napevno do nastaveni, ale plugin sa bude do Delisendu prihlasovat cez meno / heslo ?>
                <?php endif; ?>

                <?php woocommerce_admin_fields($settings); ?>
                <input type="hidden" name="screen_id" value="<?php echo esc_attr($this->get_id()); ?>">
                <?php wp_nonce_field('wc_delisend_admin_save_' . $this->get_id() . '_settings'); ?>
                <?php submit_button(__('Save changes', 'wc-delisend'), 'primary', 'save_' . $this->get_id() . '_settings'); ?>

            </form>

            <?php

            parent::render();
        }
    }

endif;