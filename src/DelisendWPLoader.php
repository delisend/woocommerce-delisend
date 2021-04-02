<?php

namespace Delisend\WC;

use Delisend\WC\Lib\DelisendWPPlugin;

/**
 * Class Delisend
 * @package Delisend\WC
 */
final class DelisendWPLoader extends DelisendWPPlugin
{
    /**
     * Minimum PHP version required by this plugin
     */
    const MINIMUM_PHP_VERSION = '7.2.0';

    /**
     * Minimum WordPress version required by this plugin
     */
    const MINIMUM_WP_VERSION = '5.6';

    /**
     * Minimum WooCommerce version required by this plugin
     */
    const MINIMUM_WC_VERSION = '3.5.0';

    /**
     * The plugin name, for displaying notices
     */
    const PLUGIN_NAME = 'Delisend for WooCommerce';

    /**
     * @var DelisendWPLoader
     */
    private static $instance;

    /**
     * @var array the admin notices to add
     */
    private $notices = array();

    /**
     * Delisend constructor.
     */
    public function __construct()
    {
        register_activation_hook( __FILE__, array( $this, 'activation_check' ) );

        add_action( 'admin_init', array( $this, 'check_environment' ) );
        add_action( 'admin_init', array( $this, 'add_plugin_notices' ) );

        add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );

        // if the environment check fails, initialize the plugin
        if ( $this->is_environment_compatible() ) {
            add_action('plugins_loaded', array( $this, 'init_plugin' ), 15);
        }
    }

    /**
     * Initializes the plugin.
     *
     * @since 1.0.0
     */
    public function init_plugin() {
        if ( !$this->plugins_compatible() ) {
            return;
        }

        $this->load_framework();

        /*require_once( plugin_dir_path( __FILE__ ) . 'class-wc-facebookcommerce.php' );

        // fire it up!
        if ( function_exists( 'facebook_for_woocommerce' ) ) {
            facebook_for_woocommerce();
        }*/
    }

    /**
     * Gets the main \Delisend instance.
     * Ensures only one instance can be loaded.
     *
     * @return DelisendWPLoader
     */
    public static function instance() {

        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     *
     */
    public function activation_check()
    {
        echo 'kurna je to nainstalovane';
    }

    public function check_environment()
    {
        if ( ! $this->is_environment_compatible() && is_plugin_active( plugin_basename( __FILE__ ) ) ) {

            $this->deactivate_plugin();

            $this->add_admin_notice( 'bad_environment', 'error', self::PLUGIN_NAME . ' has been deactivated. ' . $this->get_environment_message() );
        }
    }

    /**
     * Displays any admin notices added with \Delisend::add_admin_notice()
     *
     * @internal
     */
    public function admin_notices()
    {

        foreach ( (array) $this->notices as $notice_key => $notice ) {

            ?>
            <div class="<?php echo esc_attr( $notice['class'] ); ?>">
                <p><?php echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) ); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Adds an admin notice to be displayed.
     *
     * @param string $slug the slug for the notice
     * @param string $class the css class for the notice
     * @param string $message the notice message
     */
    private function add_admin_notice( $slug, $class, $message )
    {

        $this->notices[ $slug ] = array(
            'class'   => $class,
            'message' => $message
        );
    }

    /**
     * Deactivates the plugin.
     *
     * @internal
     */
    protected function deactivate_plugin()
    {

        deactivate_plugins( plugin_basename( __FILE__ ) );

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }

    /**
     * Gets the message for display when the environment is incompatible with this plugin.
     *
     * @since 1.10.0
     *
     * @return string
     */
    private function get_environment_message() {

        return sprintf( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', self::MINIMUM_PHP_VERSION, PHP_VERSION );
    }

    /**
     * Determines if the required plugins are compatible.
     *
     * @since 1.10.0
     *
     * @return bool
     */
    private function plugins_compatible()
    {
        return $this->is_wp_compatible() && $this->is_wc_compatible();
    }

    /**
     * Determines if the WooCommerce compatible.
     *
     * @since 1.10.0
     *
     * @return bool
     */
    private function is_wc_compatible()
    {

        if ( ! self::MINIMUM_WC_VERSION ) {
            return true;
        }

        return defined( 'WC_VERSION' ) && version_compare( WC_VERSION, self::MINIMUM_WC_VERSION, '>=' );
    }

    /**
     * Determines if the server environment is compatible with this plugin.
     * Override this method to add checks for more than just the PHP version.
     *
     * @return bool
     */
    private function is_environment_compatible()
    {
        return version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=' );
    }

    /**
     * Loads the base framework classes.
     *
     * @since 1.0.0
     */
    private function load_framework()
    {

    }

    /**
     * @since 1.0.0
     */
    public function add_plugin_notices()
    {
        if ( ! $this->is_wp_compatible() ) {

        }
    }

    /**
     * @since 1.0.0
     */
    public function install () : string
    {
        global $wp_version;

        if ( version_compare( $wp_version, self::MINIMUM_WP_VERSION, "<" ) ) {
            return "Tvoj Wordpress je velmi starucky";
        }

        return false;
    }

    /**
     * Determines if the WordPress compatible.
     * @since 1.0.0
     * @return bool
     */
    private function is_wp_compatible(): bool
    {
        if ( ! self::MINIMUM_WP_VERSION ) {
            return true;
        }

        return version_compare( get_bloginfo( 'version' ), self::MINIMUM_WP_VERSION, '>=' );
    }

}
