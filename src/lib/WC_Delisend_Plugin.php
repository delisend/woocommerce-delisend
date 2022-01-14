<?php

namespace Delisend\WC\Lib;

if (!defined('ABSPATH')) { exit; }


use \ReflectionClass;
use \Exception;
use SkyVerge\WooCommerce\PluginFramework\v5_10_0\SV_WP_Admin_Message_Handler;

if ( ! class_exists( 'WC_Delisend_Plugin' ) ) :

/**
 * Class WC_Delisend_Plugin
 * @package Delisend\WC\Lib
 */
class WC_Delisend_Plugin {

    /** @var WC_Delisend_Plugin singleton instance */
    protected static $instance;

    /** @var string plugin id */
    private $id;

    /** @var string The plugin version */
    private $version;

    /** @var string The plugin slug */
    public static $plugin_slug = WC_Delisend_Definitions::PLUGIN_SLUG;

    /** @var string The plugin text domain */
    public static $text_domain = WC_Delisend_Definitions::TEXT_DOMAIN;

    /**  @var string TThe plugin name, for displaying notices */
    public static $plugin_name = WC_Delisend_Definitions::PLUGIN_NAME;

    /**  @var bool Indicates if the setup process of the plugin is running */
    protected $running_setup = false;

    /** @var array the admin notices to add */
    protected $notices = [];

    /**  @var array This array will contain the paths used by the plugin */
    protected $paths = [];

    /** @var array This array will contain the URLs used by the plugin */
    protected $urls = [];

    /** @var WC_Delisend_Settings settings handler */
    private $settings_handler;

    /** @var WC_Delisend_Connection connection handler */
    private $connection_handler;

    /** @var WC_Delisend_Rating rating handler */
    private $rating_handler;

    /** @var WC_Delisend_Order order handler */
    private $order_handler;

    /** @var WC_Delisend_Message message handler */
    private $message_handler;

    /** @var WC_Delisend_Notice the admin notice handler class */
    private $notice_handler;

    /**
     * WC_Delisend_Plugin constructor.
     */
    public function __construct() {

        // required params
        $this->id      = WC_Delisend_Definitions::PLUGIN_ID;
        $this->version = WC_Delisend_Definitions::PLUGIN_VERSION;

        // Set plugin's paths
        $this->set_paths();
        // Set plugin's URLs
        $this->set_urls();
        // Set all required hooks
        $this->set_hooks();
    }


    /**
     * Gets the plugin singleton instance.
     *
     * @see delisend_for_woocommerce()
     * @return WC_Delisend_Plugin the plugin singleton instance
     */
    public static function instance() {

        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Builds the settings handler instance.
     */
    protected function set_settings_handler() {
        $this->settings_handler = new WC_Delisend_Settings();
    }


    /**
     * Builds the connection handler instance.
     */
    protected function set_connection_handler() {
        $this->connection_handler = new WC_Delisend_Connection( );
    }


    /**
     * Builds the rating handler instance.
     */
    protected function set_rating_handler() {
        $this->rating_handler = new WC_Delisend_Rating( );
    }


    /**
     * Builds the order handler instance.
     */
    protected function set_order_handler() {
        $this->order_handler = new WC_Delisend_Order();
    }


    /**
     * Builds the admin message handler instance.
     */
    protected function set_message_handler() {
        $this->message_handler = new WC_Delisend_Message( $this->get_id() );
    }

    /**
     * Builds the admin notice handler instance.
     */
    protected function set_notice_handler() {
        $this->notice_handler = new WC_Delisend_Notice( $this->get_id() );
    }


    /**
     * Builds and stores the paths used by the plugin.
     *
     * @return void
     */
    protected function set_paths(): void {
        $this->paths['plugin'] = WP_PLUGIN_DIR . '/' . $this->plugin_dir();
        $this->paths['admin'] = $this->path('plugin') . '/admin';
        $this->paths['assets'] = $this->path('plugin') . '/assets';
        $this->paths['languages'] = $this->path('plugin') . '/languages';
        $this->paths['vendor'] = $this->path('plugin') . '/vendor';
        $this->paths['src'] = $this->path('plugin') . '/src';
        $this->paths['lib'] = $this->path('src') . '/lib';
        $this->paths['views'] = $this->path('src') . '/views';
        $this->paths['admin_views'] = $this->path('views') . '/admin';
        $this->paths['js'] = $this->path('assets') . '/js';
        $this->paths['js_admin'] = $this->path('js') . '/admin';
        $this->paths['js_storefront'] = $this->path('js') . '/storefront';
        $this->paths['languages_rel'] = $this->path('plugin') . '/languages';
    }


    /**
     * Builds and stores the URLs used by the plugin.
     *
     * @return void
     */
    protected function set_urls(): void {
        $this->urls['plugin'] = plugins_url() . '/' . $this->plugin_dir();
        $this->urls['assets'] = $this->url('plugin') . '/assets';
        $this->urls['css'] = $this->url('assets') . '/css';
        $this->urls['images'] = $this->url('assets') . '/images';
        $this->urls['js'] = $this->url('assets') . '/js';
        $this->urls['js_admin'] = $this->url('js') . '/admin';
        $this->urls['js_storefront'] = $this->url('js') . '/storefront';
    }


    /**
     * Sets the hook handlers for WC and WordPress.
     *
     * @return void
     */
    protected function set_hooks(): void {

        // load admin handlers, before admin_init
        if (is_admin()) {
            if (WC_Delisend_Utils::is_environment_compatible()) {
                // if the environment check fails, initialize the plugin
                add_action('admin_init', array($this, 'check_environment'));
                add_action('admin_init', array($this, 'add_plugin_notices'));
                add_action('admin_notices', array($this, 'admin_notices'), 15);
                add_action('admin_enqueue_scripts', array( $this, 'enqueue_assets' ));
            }

            add_action('plugins_loaded', array($this, 'init_plugin'), 15);

            // UI
            add_action('add_meta_boxes', array($this, 'add_meta_boxes'));

            if (WC_Delisend_Utils::isWoocommerceIntegration()) {

                $this->set_settings_handler();
                $this->set_connection_handler();
                $this->set_rating_handler();
                $this->set_order_handler();
                $this->set_message_handler();
                $this->set_notice_handler();

                // Called after all plugins have loaded
                add_action('plugins_loaded', array($this, 'plugins_loaded'));
                add_action('woocommerce_init', array($this, 'woocommerce_loaded'), 1);

                add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'delisend_my_orders_actions' ), 10, 2 );
                add_action( 'woocommerce_order_status_changed' , array( $this, 'delisend_status_changed' ), 10 , 3 );
                add_action( 'woocommerce_order_details_after_order_table',array($this, 'delisend_details_after_order_table'));
                add_action( 'woocommerce_order_partially_refunded',array($this, 'delisend_order_partially_refunded'));

            }
        }
    }


    /**
     * Returns the plugin id
     *
     * @return string plugin id
     */
    public function get_id() {
        return $this->id;
    }


    /**
     * Gets the settings handler.
     *
     * @return WC_Delisend_Settings
     */
    public function get_settings_handler() {
        return $this->settings_handler;
    }


    /**
     * Gets the connection handler.
     *
     * @return WC_Delisend_Connection
     */
    public function get_connection_handler() {
        return $this->connection_handler;
    }


    /**
     * Gets the rating handler.
     *
     * @return WC_Delisend_Rating
     */
    public function get_rating_handler() {
        return $this->rating_handler;
    }


    /**
     * Gets the order handler.
     *
     * @return WC_Delisend_Order
     */
    public function get_order_handler() {
        return $this->order_handler;
    }


    /**
    * Gets the admin message handler.
    *
    * @return WC_Delisend_Message
    */
    public function get_message_handler() {
        return $this->message_handler;
    }


    /**
    * Gets the admin notice handler.
    *
    * @return WC_Delisend_Notice
    */
    public function get_notice_handler() {
        return $this->notice_handler;
    }


    /**
     * Performs operation when woocommerce has been loaded.
     */
    public function woocommerce_loaded() {
        // To be implemented by descendant classes
    }


    /**
     * Returns the full path corresponding to the specified key.
     *
     * @param string The path key.
     * @return string
     */
    public function path($key): string {
        return $this->paths[$key] ?? '';
    }


    /**
     * Returns the URL corresponding to the specified key.
     *
     * @param string The URL key.
     * @return string
     */
    public function url($key): string {
        return $this->urls[$key] ?? '';
    }


    /**
     * Returns the directory in which the plugin is stored. Only the base name of
     * the directory is returned (i.e. without path).
     *
     * @return string
     */
    public function plugin_dir() {
        if (empty($this->plugin_directory)) {
            $reflector = new ReflectionClass($this);
            $this->plugin_directory = basename(dirname($reflector->getFileName(), 3));
        }

        return $this->plugin_directory;
    }


    /**
     * Adds meta boxes to the admin interface.
     *
     * @return void
     * @see add_meta_boxes().
     */
    public function add_meta_boxes() {
        add_meta_box('wc_delisend_info_box',
            __('Delisend information', self::$text_domain),
            array($this, 'render_order_delisend_info_box'),
            'shop_order',
            'side',
            'default');
    }


    /**
     * Renders the box with the VAT information associated to an order.
     *
     * @param int order_id The ID of the target order. If empty, the ID of the
     * current post is taken.
     */
    public function render_order_delisend_info_box($order_id = null) {
        if (empty($order_id)) {
            global $post;
            $order_id = $post->ID;
        }

        $order = new WC_Delisend_Order($order_id);

        include_once($this->path('views') . '/admin/order-delisend-info-box.php');
    }


    /**
     * Initializes the plugin.
     *
     * @return bool
     */
    public function init_plugin(): bool {
        if (!$this->plugins_compatible()) {
            return false;
        }
        return true;
    }


    /**
     * Performs operation when all plugins have been loaded.
     */
    public function plugins_loaded() {
        load_plugin_textdomain(static::$text_domain, false, $this->path('languages_rel') . '/');
    }


    /**
     * Deactivates the plugin.
     *
     * @internal
     */
    protected function deactivate_plugin(): void {
        deactivate_plugins(plugin_basename(__FILE__));

        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
    }


    /**
     * Displays any admin notices added with \Delisend::add_admin_notice()
     *
     * @internal
     */
    public function admin_notices() {
        foreach ((array)$this->notices as $notice_key => $notice) {
            ?>
            <div class="<?php echo esc_attr($notice['class']); ?>">
                <p><?php echo wp_kses($notice['message'], array('a' => array('href' => array()))); ?></p>
            </div>
            <?php
        }
    }


    /**
     * Checks the environment on loading WordPress, just in case the environment changes after activation.
     *
     * * @return void
     */
    public function check_environment() {
        if (!WC_Delisend_Utils::is_environment_compatible() && is_plugin_active(plugin_basename(__FILE__))) {
            $this->deactivate_plugin();
            $this->add_admin_notice('bad_environment', 'error', WC_Delisend_Definitions::PLUGIN_NAME . ' has been deactivated. ' . $this->get_environment_message());
        }
    }


    /**
     * Take care of anything that needs to be done as soon
     * as WordPress finished loading.
     *
     * @return void
     */
    public function admin_loaded() {
        if (is_admin()) {
            //$this->register_common_admin_scripts();
        }
    }


    /**
     * Adds notices for out-of-date WordPress and/or WooCommerce versions.
     *
     * @internal
     * @return void
     */
    public function add_plugin_notices(): void {
        if (!$this->is_wp_compatible()) {

            $this->add_admin_notice('update_wordpress', 'error', sprintf(
                '%s requires WordPress version %s or higher. Please %supdate WordPress &raquo;%s',
                '<strong>' . WC_Delisend_Definitions::PLUGIN_NAME . '</strong>',
                WC_Delisend_Definitions::MINIMUM_WP_VERSION,
                '<a href="' . esc_url(admin_url('update-core.php')) . '">', '</a>'
            ));
        }

        if (!$this->is_wc_compatible()) {

            $this->add_admin_notice('update_woocommerce', 'error', sprintf(
                '%1$s requires WooCommerce version %2$s or higher. Please %3$supdate WooCommerce%4$s to the latest version, or %5$sdownload the minimum required version &raquo;%6$s',
                '<strong>' . WC_Delisend_Definitions::PLUGIN_NAME . '</strong>',
                WC_Delisend_Definitions::MINIMUM_WC_VERSION,
                '<a href="' . esc_url(admin_url('update-core.php')) . '">', '</a>',
                '<a href="' . esc_url('https://downloads.wordpress.org/plugin/woocommerce.' . WC_Delisend_Definitions::MINIMUM_WC_VERSION . '.zip') . '">', '</a>'
            ));
        }
    }


    /**
     * Returns global instance of WooCommerce.
     *
     * @return object The global instance of WooCommerce.
     * @see WC()
     */
    protected function wc() {
        return wc();
    }


    /**
     * Indicates if current visitor is a bot.
     *
     * @return bool
     */
    protected static function visitor_is_bot(): bool {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $bot_types = 'bot|crawl|slurp|spider';

        $result = !empty($user_agent) ? preg_match("/$bot_types/", $user_agent) > 0 : false;
        return apply_filters('wc_aelia_visitor_is_bot', $result);
    }


    /**
     * Adds an admin notice to be displayed.
     *
     * @param string $slug the slug for the notice
     * @param string $class the css class for the notice
     * @param string $message the notice message
     *
     * @internal
     */
    private function add_admin_notice( $slug, $class, $message, $dismissible = false ) {
        $this->notices[$slug] = [
            'class'       => $class,
            'message'     => $message,
            'dismissible' => $dismissible,
        ];
    }


    /**
     * Gets the message for display when the environment is incompatible with this plugin.
     *
     * @return string
     */
    private function get_environment_message(): string {
        return sprintf('The minimum PHP version required for this plugin is %1$s. You are running %2$s.', WC_Delisend_Definitions::MINIMUM_PHP_VERSION, PHP_VERSION);
    }


    /**
     * Determines if the required plugins are compatible.
     *
     * @return bool
     */
    private function plugins_compatible(): bool {
        return $this->is_wp_compatible() && $this->is_wc_compatible();
    }


    /**
     * Determines if the WordPress compatible.
     *
     * @return bool
     */
    private function is_wp_compatible(): bool {
        if (WC_Delisend_Definitions::MINIMUM_WP_VERSION) {
            return true;
        }

        return version_compare(get_bloginfo('version'), WC_Delisend_Definitions::MINIMUM_WP_VERSION, '>=');
    }


    /**
     * Determines if the WooCommerce compatible.
     *
     * @return bool
     *
     */
    private function is_wc_compatible() {

        if (!WC_Delisend_Definitions::MINIMUM_WC_VERSION) {
            return true;
        }

        return defined('WC_VERSION') && version_compare(WC_VERSION, WC_Delisend_Definitions::MINIMUM_WC_VERSION, '>=');
    }


    /**
     * Enqueue the assets.
     *
     * @internal
     */
    public function enqueue_assets() {
        wp_enqueue_style( WC_Delisend_Definitions::PLUGIN_SLUG, WC_Delisend_Utils::get_plugin_url() . '/assets/css/delisend-style.css', array(), WC_Delisend_Definitions::PLUGIN_VERSION );

        wp_register_script(WC_Delisend_Definitions::PLUGIN_SLUG . '-admin-lib', WC_Delisend_Utils::get_plugin_url() . '/assets/js/admin/delisend-lib.js', array('jquery'), WC_Delisend_Definitions::PLUGIN_VERSION, true);
        wp_register_script(WC_Delisend_Definitions::PLUGIN_SLUG . '-admin-common', WC_Delisend_Utils::get_plugin_url() . '/assets/js/admin/delisend-admin.js', array('jquery'), WC_Delisend_Definitions::PLUGIN_VERSION, true);
        wp_enqueue_script(WC_Delisend_Definitions::PLUGIN_SLUG . '-admin-lib');
        wp_enqueue_script(WC_Delisend_Definitions::PLUGIN_SLUG . '-admin-common');

        do_action('wc_delisend_load_admin_scripts');

        //$this->localize_admin_scripts();
    }


    /**
     * Loads the settings that will be used by the admin scripts.
     *
     *  @return void
     */
    protected function localize_admin_scripts() {
        // Prepare parameters for common admin scripts
        $admin_scripts_params = array(
            'ajax_action' => $this->ajax_action(),
            'ajax_url' => admin_url('admin-ajax.php', 'relative'),
            'home_url' => home_url(),
            'wp_nonce' => wp_create_nonce($this->ajax_nonce_id()),
        );

        $admin_scripts_params = apply_filters('wc_delisend_admin_script_params', $admin_scripts_params);

        wp_localize_script(WC_Delisend_Definitions::PLUGIN_SLUG . '-admin-common',
            'wc_delisend_admin_params',
            $admin_scripts_params);
    }


    /**
     * Returns the Nonce ID used by the Ajax call handled by the plugin.
     *
     *  @return string
     */
    protected function ajax_nonce_id() {
        return WC_Delisend_Definitions::PLUGIN_SLUG . '-nonce';
    }


    /**
     * Returns the action used to route Ajax calls to this plugin.
     *
     * @return string
     */
    protected function ajax_action() {
        return WC_Delisend_Definitions::AJAX_ACTION;
    }
}

endif;