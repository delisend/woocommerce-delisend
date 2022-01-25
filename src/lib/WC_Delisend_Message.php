<?php

namespace Delisend\WC\Lib;

if (!defined('ABSPATH')) { exit; }

if ( ! class_exists( 'WC_Delisend_Message' ) ) :

/**
 * Class WC_Delisend_Message
 * @package Delisend\WC\Lib
 */
class WC_Delisend_Message {

    /** @var string transient message prefix */
    const MESSAGE_TRANSIENT_PREFIX = '_wp_admin_message_';

    /** @var string the message id GET name */
    const MESSAGE_ID_GET_NAME = 'wpamhid';

    /** @var string unique message identifier, defaults to __FILE__ unless otherwise set */
    private $message_id;

    /** @var array array of messages */
    private $messages = array();

    /** @var array array of error messages */
    private $errors = array();

    /** @var array array of warning messages */
    private $warnings = array();

    /** @var array array of info messages */
    private $infos = array();

    /**
     * Construct and initialize the admin message handler class
     *
     * @param string $message_id optional message id.  Best practice is to set
     *        this to a unique identifier based on the client plugin, such as __FILE__
     */
    public function __construct( $message_id = null ) {

        $this->message_id = $message_id;

        // load any available messages
        $this->load_messages();

        add_filter( 'wp_redirect', array( $this, 'redirect' ), 1, 2 );
    }


    /**
     * Persist messages
     *
     * @return boolean true if any messages were set, false otherwise
     */
    public function set_messages() {

        // any messages to persist?
        if ( $this->message_count() > 0 || $this->info_count() > 0 || $this->warning_count() > 0 || $this->error_count() > 0 ) {

            set_transient(
                self::MESSAGE_TRANSIENT_PREFIX . $this->get_message_id(),
                array(
                    'errors'   => $this->errors,
                    'warnings' => $this->warnings,
                    'infos'    => $this->infos,
                    'messages' => $this->messages,
                ),
                60 * 60
            );

            return true;
        }

        return false;
    }


    /**
     * Loads messages
     *
     * @return void
     */
    public function load_messages() {

        if ( isset( $_GET[ self::MESSAGE_ID_GET_NAME ] ) && $this->get_message_id() == $_GET[ self::MESSAGE_ID_GET_NAME ] ) {

            $memo = get_transient( self::MESSAGE_TRANSIENT_PREFIX . $_GET[ self::MESSAGE_ID_GET_NAME ] );

            if ( isset( $memo['errors'] ) )   $this->errors   = $memo['errors'];
            if ( isset( $memo['warnings'] ) ) $this->warnings = $memo['warnings'];
            if ( isset( $memo['infos'] ) )    $this->infos    = $memo['infos'];
            if ( isset( $memo['messages'] ) ) $this->messages = $memo['messages'];

            $this->clear_messages( $_GET[ self::MESSAGE_ID_GET_NAME ] );
        }
    }


    /**
     * Clear messages and errors
     *
     * @param string $id the messages identifier
     */
    public function clear_messages( $id ) {
        delete_transient( self::MESSAGE_TRANSIENT_PREFIX . $id );
    }

    /**
     * Add an error message.
     *
     * @param string $error error message
     */
    public function add_error( $error ) {
        $this->errors[] = $error;
    }


    /**
     * Adds a warning message.
     *
     * @param string $message warning message to add
     */
    public function add_warning( $message ) {
        $this->warnings[] = $message;
    }


    /**
     * Adds a info message.
     *
     * @param string $message info message to add
     */
    public function add_info( $message ) {
        $this->infos[] = $message;
    }


    /**
     * Add a message.
     *
     * @param string $message the message to add
     */
    public function add_message( $message ) {
        $this->messages[] = $message;
    }


    /**
     * Get error count.
     *
     * @return int error message count
     */
    public function error_count() {
        return count( $this->errors );
    }


    /**
     * Gets the warning message count.
     *
     * @return int warning message count
     */
    public function warning_count() {
        return count( $this->warnings );
    }


    /**
     * Gets the info message count.
     *
     * @return int info message count
     */
    public function info_count() {
        return count( $this->infos );
    }


    /**
     * Get message count.
     *
     * @return int message count
     */
    public function message_count() {
        return count( $this->messages );
    }


    /**
     * Get error messages
     *
     * @return array of error message strings
     */
    public function get_errors() {
        return $this->errors;
    }


    /**
     * Get an error message
     *
     * @param int $index the error index
     * @return string the error message
     */
    public function get_error( $index ) {
        return isset( $this->errors[ $index ] ) ? $this->errors[ $index ] : '';
    }


    /**
     * Gets all warning messages.
     *
     * @return array
     */
    public function get_warnings() {
        return $this->warnings;
    }


    /**
     * Gets a specific warning message.
     *
     * @param int $index warning message index
     * @return string
     */
    public function get_warning( $index ) {
        return isset( $this->warnings[ $index ] ) ? $this->warnings[ $index ] : '';
    }


    /**
     * Gets all info messages.
     *
     * @return array
     */
    public function get_infos() {
        return $this->infos;
    }


    /**
     * Gets a specific info message.
     *
     * @param int $index info message index
     * @return string
     */
    public function get_info( $index ) {
        return isset( $this->infos[ $index ] ) ? $this->infos[ $index ] : '';
    }


    /**
     * Get messages
     *
     * @return array of message strings
     */
    public function get_messages() {
        return $this->messages;
    }


    /**
     * Get a message
     *
     * @since 1.0.0
     * @param int $index the message index
     * @return string the message
     */
    public function get_message( $index ) {
        return isset( $this->messages[ $index ] ) ? $this->messages[ $index ] : '';
    }


    /**
     * Render the errors and messages.
     *
     * @param array $params {
     *     Optional parameters.
     *
     *     @type array $capabilities Any user capabilities to check if the user is allowed to view the messages,
     *                               default: `manage_woocommerce`
     * }
     */
    public function show_messages( $params = array() ) {

        $params = wp_parse_args( $params, array(
            'capabilities' => array(
                'manage_woocommerce',
            ),
        ) );

        $check_user_capabilities = array();

        // check if user has at least one capability that allows to see messages
        foreach ( $params['capabilities'] as $capability ) {
            $check_user_capabilities[] = current_user_can( $capability );
        }

        // bail out if user has no minimum capabilities to see messages
        if ( ! in_array( true, $check_user_capabilities, true ) ) {
            return;
        }

        $output = '';

        if ( $this->error_count() > 0 ) {
            $output .= '<div id="wp-admin-message-handler-error" class="notice-error notice"><ul><li><strong>' . implode( '</strong></li><li><strong>', $this->get_errors() ) . '</strong></li></ul></div>';
        }

        if ( $this->warning_count() > 0 ) {
            $output .= '<div id="wp-admin-message-handler-warning"  class="notice-warning notice"><ul><li><strong>' . implode( '</strong></li><li><strong>', $this->get_warnings() ) . '</strong></li></ul></div>';
        }

        if ( $this->info_count() > 0 ) {
            $output .= '<div id="wp-admin-message-handler-warning"  class="notice-info notice"><ul><li><strong>' . implode( '</strong></li><li><strong>', $this->get_infos() ) . '</strong></li></ul></div>';
        }

        if ( $this->message_count() > 0 ) {
            $output .= '<div id="wp-admin-message-handler-message"  class="notice-success notice"><ul><li><strong>' . implode( '</strong></li><li><strong>', $this->get_messages() ) . '</strong></li></ul></div>';
        }

        echo wp_kses_post( $output );
    }


    /**
     * Redirection hook which persists messages into session data.
     *
     * @param string $location the URL to redirect to
     * @param int $status the http status
     * @return string the URL to redirect to
     */
    public function redirect( $location, $status ) {

        // add the admin message id param to the
        if ( $this->set_messages() ) {
            $location = add_query_arg( self::MESSAGE_ID_GET_NAME, $this->get_message_id(), $location );
        }

        return $location;
    }


    /**
     * Generate a unique id to identify the messages
     *
     * @return string unique identifier
     */
    protected function get_message_id() {

        if ( ! isset( $this->message_id ) ) $this->message_id = __FILE__;

        return wp_create_nonce( $this->message_id );

    }
}

endif;