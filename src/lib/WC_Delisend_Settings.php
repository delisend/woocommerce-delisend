<?php

namespace Delisend\WC\Lib;

if (!defined('ABSPATH')) {
    exit;
}

use Automattic\WooCommerce\Admin\Features\Navigation\Menu as WooAdminMenu;
use Delisend\WC\Lib\Screens\Abstract_Settings_Screen;

if (!class_exists('WC_Delisend_Settings')) :

    /**
     * Class WC_Delisend_Settings
     * @package Delisend\WC\Lib
     */
    class WC_Delisend_Settings
    {

        /**
         * @var string The key to identify plugin settings amongst WP options.
         */
        const SETTINGS_KEY = 'wc-delisend';

        /**
         * @var string base settings page ID
         */
        const PAGE_ID = 'wc-delisend';

        /**
         * @var Abstract_Settings_Screen[]
         */
        private $screens;

        /**
         * @var string The plugin version
         */
        public static $version = WC_Delisend_Definitions::PLUGIN_VERSION;

        /**
         * Whether the new Woo nav should be used.
         *
         * @var bool
         */
        public $use_woo_nav = false;

        /**
         * Settings constructor.
         */
        public function __construct()
        {
            $this->screens = [
                Screens\Connection::ID => new Screens\Connection(),
            ];

            add_action('admin_menu', array($this, 'add_menu_item'));
            add_action('wp_loaded', array($this, 'save'));
        }

        /**
         * Adds the Delisend menu item.
         */
        public function add_menu_item()
        {

            $root_menu_item = 'woocommerce';

            add_submenu_page(
                $root_menu_item,
                __('Delisend for WooCommerce', 'wc-delisend'),
                __('Delisend', 'wc-delisend'),
                'manage_woocommerce',
                self::PAGE_ID,
                array($this, 'render'),
                1000
            );

            $this->register_woo_nav_menu_items();
        }

        /**
         * Renders wrapper for the settings page.
         *
         * @internal
         */
        public function render()
        {

            $benefits = array(
                //__('Create an ad in a few steps', WC_Delisend_Definitions::TEXT_DOMAIN),
                //__('Use built-in best practices for online sales', WC_Delisend_Definitions::TEXT_DOMAIN),
                //__('Get reporting on sales and revenue', WC_Delisend_Definitions::TEXT_DOMAIN),
            );

            $tabs = $this->get_tabs();
            $current_tab = WC_Delisend_Helper::get_requested_value('tab');

            if (!$current_tab) {
                $current_tab = current(array_keys($tabs));
            }

            $screen = $this->get_screen($current_tab);
            ?>

            <div class="wrap woocommerce delisend-settings">
                <?php
                echo '<h2>' . $this->page_title() . '</h2>';
                echo '<p>' . $this->page_description() . '</p>';
                ?>

                <?php if (!$this->use_woo_nav) : ?>
                    <nav class="nav-tab-wrapper woo-nav-tab-wrapper">
                        <?php foreach ($tabs as $id => $label) : ?>
                            <a href="<?php echo esc_html(admin_url('admin.php?page=' . self::PAGE_ID . '&tab=' . esc_attr($id))); ?>"
                               class="nav-tab <?php echo $current_tab === $id ? 'nav-tab-active' : ''; ?>"><?php echo esc_html($label); ?></a>
                        <?php endforeach; ?>
                    </nav>
                <?php endif; ?>

                <?php if ($screen) : ?>
                    <div class="delisend-content-options">

                        <p><?php echo wp_kses_post($screen->get_description()); ?></p>
                        <ul class="benefits">
                            <?php foreach ($benefits as $key => $benefit) : ?>
                                <li class="benefit benefit-<?php echo esc_attr($key); ?>"><?php echo esc_html($benefit); ?></li>
                            <?php endforeach; ?>
                        </ul>

                        <?php $screen->render(); ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        }

        /**
         * Saves the settings page.
         *
         * @return void
         */
        public function save()
        {

            if (!is_admin() || WC_Delisend_Helper::get_requested_value('page') !== self::PAGE_ID) {
                return;
            }

            $screen = $this->get_screen(WC_Delisend_Helper::get_posted_value('screen_id'));

            if (!$screen) {
                return;
            }

            if (!WC_Delisend_Helper::get_posted_value('save_' . $screen->get_id() . '_settings')) {
                return;
            }

            if (!current_user_can('manage_woocommerce')) {
                wp_die(__('You do not have permission to save these settings.', 'wc-delisend'));
            }

            check_admin_referer('wc_delisend_admin_save_' . $screen->get_id() . '_settings');

            try {

                $screen->save();
                //WC_Delisend_Helper::wc_delisend();

            } catch (WC_Delisend_Exception $exception) {

                WC_Delisend_Helper::wc_delisend()->get_message_handler()->add_error(
                    sprintf(__('Your settings could not be saved. %s', 'wc-delisend'), $exception->getMessage())
                );
            }
        }

        /**
         * Gets a settings screen object based on ID.
         *
         * @param string $screen_id desired screen ID
         * @return \Delisend\WC\Lib\Screens\Abstract_Settings_Screen|null
         */
        public function get_screen($screen_id)
        {

            $screens = $this->get_screens();

            return !empty($screens[$screen_id]) && $screens[$screen_id] instanceof Abstract_Settings_Screen ? $screens[$screen_id] : null;
        }

        /**
         * Gets the available screens.
         *
         * @return Abstract_Settings_Screen[]
         */
        public function get_screens()
        {

            /**
             * Filters the admin settings screens.
             *
             * @param array $screens available screen objects
             */
            $screens = (array)apply_filters('wc_delisend_admin_settings_screens', $this->screens, $this);

            // ensure no bogus values are added via filter
            $screens = array_filter(
                $screens,
                function ($value) {
                    return $value instanceof Abstract_Settings_Screen;
                }
            );

            return $screens;
        }

        /**
         * Gets the tabs.
         *
         * @return array
         */
        public function get_tabs()
        {

            $tabs = array();

            foreach ($this->get_screens() as $screen_id => $screen) {
                $tabs[$screen_id] = $screen->get_label();
            }

            /**
             * Filters the admin settings tabs.
             *
             * @param array $tabs tab data, as $id => $label
             */
            return (array)apply_filters('wc_delisend_admin_settings_tabs', $tabs, $this);
        }

        /**
         * Returns the title for the settings page.
         *
         * @return string
         */
        public function page_title()
        {
            return __('Delisend - Settings', WC_Delisend_Definitions::TEXT_DOMAIN) .
                sprintf('&nbsp;(v. %s)', WC_Delisend_Definitions::PLUGIN_VERSION);
        }

        /**
         * Returns the description for the settings page.
         *
         * @return string
         */
        public function page_description()
        {
            // @TODO Restore link to documentation
            return __('In this page you can configure the settings for the Delisend plugin.',
                WC_Delisend_Definitions::TEXT_DOMAIN);
        }

        /**
         * Register nav items for new Woo nav.
         *
         * @return void
         */
        private function register_woo_nav_menu_items()
        {

            if (!$this->use_woo_nav) {
                return;
            }

            WooAdminMenu::add_plugin_category(
                array(
                    'id' => 'woocommerce-delisend',
                    'title' => __('Delisend', WC_Delisend_Definitions::TEXT_DOMAIN),
                    'capability' => 'manage_woocommerce',
                )
            );

            $order = 1;

            foreach ($this->get_screens() as $screen_id => $screen) {
                $url = 'wc-delisend&tab=' . $screen->get_id();

                WooAdminMenu::add_plugin_item(
                    array(
                        'id' => 'woocommerce-delisend' . $screen->get_id(),
                        'parent' => 'woocommerce-delisend',
                        'title' => $screen->get_label(),
                        'url' => $url,
                        'order' => $order,
                    )
                );
                $order++;
            }
        }
    }

endif;
