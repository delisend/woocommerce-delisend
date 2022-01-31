<?php

namespace Delisend\WC;

if (!defined('ABSPATH')) { exit; }

use Delisend\WC\Lib\WC_Delisend_Definitions;
use Delisend\WC\Lib\WC_Delisend_Install;
use Delisend\WC\Lib\WC_Delisend_Plugin;
use Delisend\WC\Lib\WC_Delisend_Utils;


if ( ! class_exists( 'WC_Delisend_Loader' ) ) :

/**
 * Class WC_Delisend_Loader
 * @package Delisend\WC
 */
final class WC_Delisend_Loader {

    /** @var WC_Delisend_Loader */
    private static $instance;

    /** @var WC_Delisend_Plugin */
    public $delisend;

    /**
     * WC_Delisend_Loader constructor.
     *
     * @return void
     */
    public function __construct() {
        register_activation_hook(__FILE__, array($this, 'activation_check'));
        register_activation_hook(__FILE__, array($this, 'install'));
        register_deactivation_hook(__FILE__, array($this, 'uninstall'));

        add_action('plugins_loaded', array($this, 'delisend_testing'), 1);

        $this->delisend = new WC_Delisend_Plugin();
    }


    /**
     * Gets the main \Delisend instance.
     * Ensures only one instance can be loaded.
     *
     * @return WC_Delisend_Loader
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Checks the server environment and other factors and deactivates plugins as necessary.
     *
     * @return void
     */
    public function activation_check() {
        if (!WC_Delisend_Utils::is_environment_compatible()) {
            $this->deactivate_plugin();
            wp_die( WC_Delisend_Definitions::PLUGIN_NAME . ' could not be activated. ' . $this->get_environment_message() );
        }
    }


    /**
     * When active plugin function will be call
     *
     * @return bool
     */
    public function install(): bool {
        global $wp_version;

        // kontrola verzie wordpressu
        if (version_compare($wp_version, WC_Delisend_Definitions::MINIMUM_WP_VERSION, "<")) {
            deactivate_plugins(basename(__FILE__)); // Deactivate our plugin
            wp_die("This plugin requires WordPress version " . WC_Delisend_Definitions::MINIMUM_WP_VERSION . " or higher.");
        }

        return true;
    }


    /**
     * When deactivate function will be call
     */
    public function uninstall()
    {

    }


    /**
     * Displays any admin notices added with \Delisend::add_admin_notice()
     *
     * @internal
     */
    public function admin_notices()
    {
        foreach ((array)$this->notices as $notice_key => $notice) {
            ?>
            <div class="<?php echo esc_attr($notice['class']); ?>">
                <p><?php echo wp_kses($notice['message'], array('a' => array('href' => array()))); ?></p>
            </div>
            <?php
        }
    }


    /**
    * Returns true if the identified notice hasn't been cleared, or we're on
    * the plugin settings page (where notices are always displayed)
    *
    * @param string $message_id the message id
    * @param array $params Optional parameters.
    *
    * @type bool $dismissible             If the notice should be dismissible
    * @type bool $always_show_on_settings If the notice should be forced to display on the
    *                                     plugin settings page, regardless of `$dismissible`.
    *
    * @return bool
    */
    public function should_display_notice( $message_id, $params = array() ) {

        // bail out if user is not a shop manager
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return false;
        }

        $params = wp_parse_args( $params, array (
            'dismissible'             => true,
            'always_show_on_settings' => true,
        ) );

        // if the notice is always shown on the settings page, and we're on the settings page
        if ( $params['always_show_on_settings'] && $this->is_plugin_settings()) {
            return true;
        }

        // non-dismissible, always display
        if ( ! $params['dismissible'] ) {
            return true;
        }

        // dismissible: display if notice has not been dismissed
        return ! $this->is_notice_dismissed( $message_id );
    }


    /**
     * Returns true if the identified admin notice has been dismissed for the given user
     *
     * @param string $message_id the message identifier
     * @param int $user_id optional user identifier, defaults to current user
     * @return boolean true if the message has been dismissed by the admin user
     */
    public function is_notice_dismissed( $message_id, $user_id = null ): bool {

        $dismissed_notices = $this->get_dismissed_notices(  $message_id, $user_id );

        return isset( $dismissed_notices[ $message_id ] ) && $dismissed_notices[ $message_id ];
    }


    /**
     * Returns the full set of dismissed notices for the user identified by $user_id, for this plugin
     *
     * @param int $user_id optional user identifier, defaults to current user
     * @param string $message_id the message identifier
     * @return array of message id to dismissed status (true or false)
     */
    public function get_dismissed_notices( $message_id, $user_id = null  ): array {

        if ( is_null( $user_id ) ) {
            $user_id = get_current_user_id();
        }

        $dismissed_notices = get_user_meta( $user_id, '_wc_plugin_framework_' . $message_id . '_dismissed_messages', true );

        if ( empty( $dismissed_notices ) ) {
            return array();
        }

        return $dismissed_notices;
    }


    /**
     *
     */
    public function woocommerce_loaded() {
        if (!$this->running_setup && current_user_can('manage_woocommerce')) {
            if (is_admin()) {

            }
        }
    }


    /**
     * Determines if viewing the plugin settings in the admin.
     *
     * @return bool
     */
    public function is_plugin_settings() {
        return is_admin() && \Delisend\WC\Lib\WC_Delisend_Settings::PAGE_ID === \Delisend\WC\Lib\WC_Delisend_Helper::get_requested_value( 'page' );
    }


    /**
     * Method for internal testing
     */
    public function delisend_testing () {
        $install = WC_Delisend_Install::instance();
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

}

endif;
