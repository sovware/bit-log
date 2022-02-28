<?php
/**
 * Bit Log
 *
 * @package           BitLog
 * @author            Sovware
 * @copyright         2022 Sovware
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Bit Log
 * Plugin URI:        https://github.com/sovware/bit-log
 * Description:       A PHP debugging tool for WordPress
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            wpWax
 * Author URI:        https://wpwax.com
 * Text Domain:       bit-log
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://github.com/sovware/bit-log
 */


final class BitLog {

    protected static $instance = null;
    protected $option_key = 'bit-logs';
    
    protected function __construct() {
        $this->init_setup();
    }

    protected function init_setup() {
        add_action( 'rest_api_init', [ $this, 'register_rest_api' ] );
    }

    public function register_rest_api() {
        register_rest_route( 
            'bit-log/v1', '/logs',
            [
                [
                    'methods'             => 'GET',
					'callback'            => [ $this, 'get_rest_api_logs' ],
					'permission_callback' => '__return_true',
                ]
            ]
        );
    }

    public static function isTruthy( $value ) {
        if ( true === $value ) {
            return true;
        }

        if ( 'true' === $value ) {
            return true;
        }

        if ( 1 === $value ) {
            return true;
        }

        if ( '1' === $value ) {
            return true;
        }

        return false;
    }

    public static function isFalsy( $value ) {
        if ( false === $value ) {
            return true;
        }

        if ( 'false' === $value ) {
            return true;
        }

        if ( 0 === $value ) {
            return true;
        }

        if ( '0' === $value ) {
            return true;
        }

        return false;
    }

    public function get_rest_api_logs( $request ) {

        $logs  = get_option( $this->option_key, [] );
        $group = ( isset( $request['group'] ) ) ? $request['group'] : '';

        $reset = true;

        if ( isset( $request['reset'] ) && self::isFalsy( $request['reset'] ) ) {
            $reset = false;
        }

        if ( empty( $group ) ) {

            if ( $reset ) {
                $this->clear_logs();
            }

            return $logs;
        }

        if ( ! isset( $logs[ $group ] ) ) {
            return [];
        }

        if ( $reset ) {
            $this->clear_logs();
        }

        return $logs[ $group ];
    }

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new BitLog();
        }

        return self::$instance;
    }

    public function push( $group, $data, $file = __FILE__, $line = __LINE__ ) {
        $logs = get_option( $this->option_key, [] );

        $logs[ $group ][] = [
            'file'      => $file,
            'line'      => $line,
            'data_type' => gettype( $data ),
            'data'      => $data,
        ];

        update_option( $this->option_key, $logs );
    }

    public function clear_logs() {
        update_option( $this->option_key, [] );
    }

}

if ( ! function_exists( 'BitLog' ) ) {
    function BitLog() {
        return BitLog::get_instance();
    }
}

BitLog();