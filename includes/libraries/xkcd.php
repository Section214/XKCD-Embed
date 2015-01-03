<?php
/**
 * XKCD API Controller
 *
 * @package     XKCD_Embed\XKCD
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


if( ! class_exists( 'XKCD' ) ) {


    /**
     * Main XKCD controller class
     *
     * @since       1.0.0
     */
    class XKCD {
        

        /**
         * Has an error occurred?
         *
         * @access      private
         * @since       1.0.0
         * @var         bool $is_error True if an error has occurred, false otherwise
         */
        private $is_error = false;


        /**
         * Setup the controller
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function __construct() {}


        /**
         * Retrieve a given comic
         *
         * @access      public
         * @since       1.0.0
         * @param       mixed $comic The comic to retrieve
         * @return      string $return The JSON response for the given comic
         */
        public function get( $comic = 'latest' ) {
            // Maybe get latest comic
            $latest = get_transient( 'xkcd_embed_latest' );
            $cached = get_option( 'xkcd_embed' );

            if( ! $latest ) {
                $latest = $this->fetch_comic();

                if( $latest ) {
                    set_transient( 'xkcd_embed_latest', $latest, $this->get_expiration() );
                }
            }

            if( ! $this->is_error ) {
                if( $comic == 'latest' ) {
                    $return = $latest;
                } elseif( is_numeric( $comic ) ) {
                    $latest_data = json_decode( $latest, true );

                    if( array_key_exists( $comic, $cached ) ) {
                        $return = $cached[$comic];
                    } else {
                        if( $comic > $latest_data['num'] ) {
                            $return = $latest;
                        } else {
                            $data   = $this->fetch_comic( $comic );
                            $return = $data;
                        }
                    }
                } elseif( $comic == 'random' ) {
                    $latest_data = json_decode( $latest, true );
                    $comic       = rand( 1, $latest_data['num'] );

                    if( array_key_exists( $comic, $cached ) ) {
                        $return = $cached[$comic];
                    } else {
                        $data   = $this->fetch_comic( $comic );
                        $return = $data;
                    }
                }
            }

            if( $this->is_error ) {
                $return = false;
            }

            return json_decode( $return );
        }


        /**
         * Fetch a specific comic
         *
         * @access      public
         * @since       1.0.0
         * @param       int $id The ID of a given comic
         * @return      string $return The data for a given comic
         */
        public function fetch_comic( $id = false ) {
            $cached = get_option( 'xkcd_embed' );

            if( ! $id || ! array_key_exists( $id, $cached ) ) {
                $url  = 'http://xkcd.com/' . ( $id ? $id . '/' : '' ) . 'info.0.json';
                $args = array(
                    'timeout'   => 5,
                    'sslverify' => false
                );

                $data = wp_remote_retrieve_body( wp_remote_get( $url, $args ) );

                if( ! is_wp_error( $data ) ) {
                    $cached[$id] = $return = $data;

                    update_option( 'xkcd_embed', $cached );
                } else {
                    $return = false;
                    $this->is_error = true;
                }
            } else {
                $return = $cached[$id];
            }

            return $return;
        }


        /**
         * Get the expiration for the 'latest' comic
         * We always want it to reset at midnight
         *
         * @access      public
         * @since       1.0.0
         * @return      string $expiration The expiration in seconds
         */
        public function get_expiration() {
            $timestamp  = current_time( 'timestamp' );
            $tomorrow   = strtotime( 'tomorrow', $timestamp );
            $expiration = $tomorrow - $timestamp;

            return $expiration;
        }
    }
}
