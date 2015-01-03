<?php
/**
 * Widgets
 *
 * @package     XKCD_Embed\Widgets
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * XKCD widget class
 *
 * @since       1.0.0
 * @return      string $comic The formatted XKCD comic
 */
class xkcd_widget extends WP_Widget {


    /**
     * Constructor
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function __construct() {
        parent::__construct(
            'xkcd_embed_widget',
            __( 'XKCD Embed', 'xkcd-embed' ),
            array(
                'description'   => __( 'Display an XKCD comic', 'xkcd-embed' )
            )
        );
    }


    /**
     * Display the widget
     *
     * @access      public
     * @since       1.0.0
     * @param       array $args Arguments passed to the widget
     * @param       array $instance This widget instance
     * @return      void
     */
    public function widget( $args, $instance ) {
        $args['id']         = ( isset( $args['id'] ) ) ? $args['id'] : 'xkcd_embed_widget';
        $instance['title']  = ( isset( $instance['title'] ) ) ? $instance['title'] : '';
        $instance['comic']  = ( isset( $instance['comic'] ) ) ? $instance['comic'] : 'latest';
        $instance['num']    = ( isset( $instance['num'] ) ) ? $instance['num'] : false;

        if( $instance['comic'] == 'num' ) {
            $num = $instance['num'];
        } else {
            $num = $instance['comic'];
        }

        $xkcd = new XKCD();
        $comic_data = $xkcd->get( $num );
        
        $title = apply_filters( 'widget_title', $instance['title'], $instance, $args['id'] );

        echo $args['before_widget'];

        if( $title ) {
            echo $args['before_title'] . ( $title == '{{title}}' ? esc_attr( $comic_data->title ) : $title ) . $args['after_title'];
        }

        do_action( 'xkcd_embed_before_widget' );

        // Format the output
        echo '<a href="http://xkcd.com/' . $comic_data->num . '" target="_blank">';
        echo '<img src="' . esc_url( $comic_data->img ) . '" title="' . esc_attr( $comic_data->alt ) . '" />';
        echo '</a>';

        do_action( 'xkcd_embed_after_widget' );

        echo $args['after_widget'];
    }


    /**
     * Update widget on save
     *
     * @access      public
     * @since       1.0.0
     * @param       array $new_instance The updated settings
     * @param       array $old_instance The old settings
     * @return      void
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title']  = strip_tags( $new_instance['title'] );
        $instance['comic']  = strip_tags( $new_instance['comic'] );
        $instance['num']    = isset( $new_instance['num'] ) ? $new_instance['num'] : '';

        return $instance;
    }


    /**
     * Display widget form
     *
     * @access      public
     * @since       1.0.0
     * @param       array $instance The widget settings
     * @return      void
     */
    public function form( $instance ) {
        // Set up some defaults
        $defaults = array(
            'title'     => '',
            'comic'     => 'latest',
            'num'       => '1'
        );

        $instance = wp_parse_args( (array) $instance, $defaults );

        // Title
        echo '<p>';
        echo '<label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">' . __( 'Title:', 'xkcd-embed' ) . '</label>';
        echo '<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . $instance['title'] . '" />';
        echo '<span class="description">' . __( 'Set to <em>{{title}}</em> to use the comic title.', 'xkcd-embed' ) . '</span>';
        echo '</p>';

        // Comic Type
        echo '<p>';
        echo '<label for="' . esc_attr( $this->get_field_id( 'comic' ) ) . '">' . __( 'Comic:', 'xkcd-embed' ) . '</label>';
        echo '<select class="widefat xkcd_embed_widget_select" id="' . esc_attr( $this->get_field_id( 'comic' ) ) . '" name="' . esc_attr( $this->get_field_name( 'comic' ) ) . '">';
        echo '<option value="latest" ' . selected( 'latest', $instance['comic'], false ) . '>' . __( 'Latest', 'xkcd-embed' ) . '</option>';
        echo '<option value="random" ' . selected( 'random', $instance['comic'], false ) . '>' . __( 'Random', 'xkcd-embed' ) . '</option>';
        echo '<option value="num" ' . selected( 'num', $instance['comic'], false ) . '>' . __( 'Specific Comic', 'xkcd-embed' ) . '</option>';
        echo '</select>';
        echo '</p>';

        // Comic Number
        echo '<p>';
        echo '<label for="' . esc_attr( $this->get_field_id( 'num' ) ) . '">' . __( 'Comic ID:', 'xkcd-embed' ) . '</label>';
        echo '<input class="widefat" id="' . esc_attr( $this->get_field_id( 'num' ) ) . '" name="' . esc_attr( $this->get_field_name( 'num' ) ) . '" type="number" min="1" value="' . $instance['num'] . '" />';
        echo '<span class="description">' . __( 'Ignored unless <em>Comic</em> is set to <em>Specific Comic</em>.', 'xkcd-embed' ) . '</span>';
        echo '</p>';
    }
}


/**
 * Register widgets
 *
 * @since       1.0.0
 * @return      void
 */
function xkcd_embed_register_widgets() {
    register_widget( 'xkcd_widget' );
}
add_action( 'widgets_init', 'xkcd_embed_register_widgets' );
