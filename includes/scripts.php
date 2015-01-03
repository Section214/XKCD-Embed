<?php
/**
 * Scripts
 *
 * @package     XKCD_Embed\Scripts
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Load frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function xkcd_embed_scripts() {
    wp_enqueue_style( 'xkcd-embed', XKCD_URL . 'assets/css/style.css', array(), XKCD_VER );
}
add_action( 'wp_enqueue_scripts', 'xkcd_embed_scripts' );
