<?php
/**
 * Shortcodes
 *
 * @package     XKCD_Embed\Shortcodes
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * XKCD Shortcode
 *
 * @since       1.0.0
 * @param       array $atts Shortcode attributes
 * @param       string $contents Shortcode contents
 * @return      string $comic The formatted XKCD comic
 */
function xkcd_embed_xkcd_shortcode( $atts, $content = null ) {
    $xkcd = new XKCD();
    $atts = shortcode_atts( array(
        'id'        => 'latest',
        'title'     => 'true'
    ),
    $atts, 'xkcd' );

    // If an invalid ID is passed, default to 'latest'
    if( $atts['id'] != 'random' && ! is_numeric( $atts['id'] ) ) {
        $atts['id'] = 'latest';
    }

    $comic_data = $xkcd->get( $atts['id'] );

    // Format the output
    $comic  = '<div class="xkcd-embed">';

    if( $atts['title'] == 'true' ) {
        $comic .= '<div class="xkcd-embed-title">' . esc_attr( $comic_data->title ) . '</div>';
    }

    $comic .= '<a href="http://xkcd.com/' . $comic_data->num . '" target="_blank">';
    $comic .= '<img src="' . esc_url( $comic_data->img ) . '" title="' . esc_attr( $comic_data->alt ) . '" />';
    $comic .= '</a>';
    $comic .= '</div>';

    return $comic;
}
add_shortcode( 'xkcd', 'xkcd_embed_xkcd_shortcode' );
