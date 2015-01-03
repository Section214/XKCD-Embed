<?php
/**
 * Plugin Name:     XKCD Embed
 * Plugin URI:      http://section214.com
 * Description:     Display XKCD comics on your website!
 * Version:         1.0.0
 * Author:          Daniel J Griffiths
 * Author URI:      http://ghost1227.com
 * Text Domain:     xkcd-embed
 *
 * @package         XKCDEmbed
 * @author          Daniel J Griffiths <dgriffiths@section214.com>
 * @copyright       Copyright (c) 2014, Daniel J Griffiths
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


if( ! class_exists( 'XKCD_Embed' ) ) {


    /**
     * Main XKCD_Embed class
     *
     * @since       1.0.0
     */
    class XKCD_Embed {


        /**
         * @access      private
         * @since       1.0.0
         * @var         XKCD_Embed $instance The one true XKCD_Embed
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      self::$instance The one true XKCD_Embed
         */
        public static function instance() {
            if( ! self::$instance ) {
                self::$instance = new XKCD_Embed();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'XKCD_VER', '1.0.0' );

            // Plugin path
            define( 'XKCD_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'XKCD_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Include required files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {
            require_once XKCD_DIR . 'includes/libraries/xkcd.php';
            require_once XKCD_DIR . 'includes/scripts.php';
            require_once XKCD_DIR . 'includes/shortcodes.php';
            require_once XKCD_DIR . 'includes/widgets.php';
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function hooks() {
            // Run setup
            add_action( 'admin_init', array( $this, 'setup' ) );
        }


        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
            $lang_dir = apply_filters( 'xkcd_embed_lang_dir', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), '' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'xkcd-embed', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/xkcd-embed/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/xkcd-embed/ folder
                load_textdomain( 'xkcd-embed', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/xkcd-embed/languages/ folder
                load_textdomain( 'xkcd-embed', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'xkcd-embed', false, $lang_dir );
            }
        }


        /**
         * Initial setup
         *
         * @access      public
         * @since       1.0.0
         * @global      string $xkcd_cache_dir Our cache directory
         * @return      void
         */
        public function setup() {
            // Setup options
            $xkcd_options = get_option( 'xkcd_embed' );

            if( ! $xkcd_options ) {
                add_option( 'xkcd_embed', array() );
            }
        }
    }
}


/**
 * The main function responsible for returning the one true XKCD_Embed
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      XKCD_Embed The one true XKCD_Embed
 */
function xkcd_embed() {
    return XKCD_Embed::instance();
}
add_action( 'plugins_loaded', 'xkcd_embed' );
