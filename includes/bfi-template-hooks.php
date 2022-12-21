<?php
/**
 * BookingFor Template Hooks
 *
 * Action/filter hooks used for BookingFor functions/templates.
 *
 * @author 		BookingFor
 * @category 	Core
 * @package 	BookingFor/Templates
 * @version     2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_filter( 'body_class', 'bfi_body_class' );

/**
 * WP Header.
 *
 * @see  bfi_products_rss_feed()
 * @see  bfi_generator_tag()
 */
//add_action( 'wp_head', 'bfi_products_rss_feed' );
add_action( 'get_the_generator_html', 'bfi_generator_tag', 10, 2 );
add_action( 'get_the_generator_xhtml', 'bfi_generator_tag', 10, 2 );
add_action('init', 'do_output_buffer');
function do_output_buffer() {
        ob_start();
}

/**
 * Comments.
 *
 * Disable Jetpack comments.
 */
add_filter( 'jetpack_comment_form_enabled_for_product', '__return_false' );