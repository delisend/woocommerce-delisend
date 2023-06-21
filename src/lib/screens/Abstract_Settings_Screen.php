<?php

namespace Delisend\WC\Lib\Screens;

if (!defined('ABSPATH')) { exit; }

use Delisend\WC\Lib\WC_Delisend_Definitions;
use Delisend\WC\Lib\WC_Delisend_Exception;
use Delisend\WC\Lib\WC_Delisend_Helper as Helper;
use Delisend\WC\Lib\WC_Delisend_Settings;


if ( ! class_exists(\Delisend\WC\Lib\Screens\Abstract_Settings_Screen::class) ) :

/**
 * The base settings screen object.
 */
abstract class Abstract_Settings_Screen {

	/**
     * @var string screen ID
     */
	protected string $id;

	/**
     * @var string screen label, for display
     */
	protected string $label;

	/**
     * @var string screen title, for display
     */
	protected string $title;

	/**
     * @var string screen description, for display
     */
	protected string $description = "Get started with Delisend. Connect or create a Delisend account.<br>You get a solution for fraud prevention solution to protect the online businesses.";

	/**
	 * Renders the screen for settings .
	 */
	public function render(): void
    {
        /**
         * Filters the screen settings.
         *
         * @param array $settings settings
         */
        $settings = (array) apply_filters( 'wc_delisend_admin_' . $this->get_id() . '_settings', $this->get_settings(), $this );

        if ( empty( $settings ) ) {
            return;
        }

        $connection_handler = Helper::wc_delisend()->get_connection_handler();
        $is_connected = $connection_handler->is_connected();

        ?>

        <?php if ( $is_connected ) : ?>
			<div class="notice notice-info">
                <p><?php echo wp_kses_post( $this->get_disconnected_message() ); ?></p>
            </div>
		<?php else: ?>
            <?php  $this->get_connect_status();  ?>
		<?php endif; ?>

        <?php
	}


	/**
	 * Saves the settings.
     *
     * @return void
     */
	public function save(): void
    {
		woocommerce_update_options( $this->get_settings() );
	}


	/**
	 * Determines whether the current screen is the same as identified by the current class.
	 *
	 * @return bool
	 */
	protected function is_current_screen_page(): bool
    {
		if ( WC_Delisend_Settings::PAGE_ID !== Helper::get_requested_value( 'page' ) ) {
			return false;
		}

		// assume we are on the Connection tab by default because the link under Marketing doesn't include the tab query arg
		$tab = Helper::get_requested_value( 'tab', 'connection' );

		return ! empty( $tab ) && $tab === $this->get_id();
	}


	/**
	 * Gets the settings.
	 *
	 * Should return a multi-dimensional array of settings in the format expected by \WC_Admin_Settings
	 *
	 * @return array
	 */
	abstract public function get_settings(): array;


    /**
     * This method is used to stripe connect button.
     */
    public function get_connect_status( ): void
    {
        do_action( 'wc_delisend_before_connection_with_delisend' );
    }


    /**
	 * Get the notice-info message to display when the plugin is disconnected.
	 *
	 * @return string
	 */
	public function get_disconnected_message(): string
    {
		return 'Delisend requires a REST API connection to the service. Have questions about connecting with Delisend? Read <a href="https://delisend.com/docs/delisend-api-settings" target="_blank"> document. </a>.';
	}


	/**
	 * Gets the screen ID.
	 *
	 * @return string
	 */
	public function get_id(): string
    {
		return $this->id;
	}


	/**
	 * Gets the screen label.
	 *
	 * @return string
	 */
	public function get_label(): string
    {
		/**
		 * Filters the screen label.
		 *
		 * @param string $label screen label, for display
		 */
		return (string) apply_filters( 'wc_delisend_admin_settings_' . $this->get_id() . '_screen_label', $this->label, $this );
	}


	/**
	 * Gets the screen title.
	 *
	 * @return string
	 */
	public function get_title(): string
    {
		/**
		 * Filters the screen title.
		 *
		 * @param string $title screen title, for display
		 */
		return (string) apply_filters( 'wc_delisend_admin_settings_' . $this->get_id() . '_screen_title', $this->title, $this );
	}


	/**
	 * Gets the screen description.
	 *
	 * @return string
	 */
	public function get_description(): string
    {
		/**
		 * Filters the screen description.
		 *
		 * @param string $description screen description, for display
		 */
		return (string) apply_filters( 'wc_delisend_admin_settings_' . $this->get_id() . '_screen_description', __( $this->description, WC_Delisend_Definitions::TEXT_DOMAIN ), $this );
	}
}

endif;