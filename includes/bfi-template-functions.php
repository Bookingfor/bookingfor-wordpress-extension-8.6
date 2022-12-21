<?php
/**
 * bookingfor Template
 *
 * Functions for the templating system.
 *
 * @author   BookingFor
 * @category Core
 * @package  bookingfor/Functions
 * @version     2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

	/**
	 * Output generator tag.
	 *
	 * @access public
	 */
	function bfi_generator_tag( $gen, $type ) {
		switch ( $type ) {
			case 'html':
				$gen .= PHP_EOL . '<meta name="generator" content="bookingfor ' . esc_attr( BFI_VERSION ) . '">'. PHP_EOL;
				break;
			case 'xhtml':
				$gen .= PHP_EOL . '<meta name="generator" content="bookingfor ' . esc_attr( BFI_VERSION ) . '" />'. PHP_EOL;
				break;
		}
		return $gen;
	}

	function bfi_add_meta_keywords($keywords) {
		echo '<meta name="keywords" content="'. esc_attr( strip_tags( stripslashes( $keywords ) ) ). '"/>'. PHP_EOL;
	}


	function bfi_add_meta_description($description) {
		echo '<meta name="description" content="'. esc_attr( strip_tags( stripslashes( $description) ) ). '"/>'. PHP_EOL;
	}
	function bfi_add_canonicalurl($link) {
		if(substr($link , -1)!= '/'){
			$link .= '/';
		}
		echo "<link rel='canonical' href='$link' />". PHP_EOL;
	}
	// OpenGrah function
	// opengraph_title og:title
	// opengraph_desc og:description
	// opengraph_url og:url
	// opengraph_image og:image
	function bfi_add_opengraph_title($title) {
		echo '<meta property="og:title" content="'. esc_attr( strip_tags( stripslashes( $title) ) ). '"/>'. PHP_EOL;
	}
	function bfi_add_opengraph_desc($description) {
		echo '<meta property="og:description" content="'. esc_attr( strip_tags( stripslashes( $description) ) ). '"/>'. PHP_EOL;
	}
	function bfi_add_opengraph_url($link) {
		if(substr($link , -1)!= '/'){
			$link .= '/';
		}
		echo '<meta property="og:url" content="'.$link.'" />'. PHP_EOL;
	}
	function bfi_add_opengraph_image($img) {
		echo '<meta property="og:image" content="'.$img.'"/>'. PHP_EOL;
	}
	function bfi_add_opengraph_image_size($image_meta) {
			$image_tags = [
				'width'     => 'width',
				'height'    => 'height',
				'mime-type' => 'type',
				'secure_url'  => 'secure_url',
			];
			foreach ( $image_tags as $key => $value ) {
				if ( empty( $image_meta[ $key ] ) ) {
					continue;
				}
				echo \PHP_EOL . "\t" . '<meta property="og:image:' . \esc_attr( $key ) . '" content="' . $image_meta[ $key ] . '" />';
			}
	}

	function bfi_add_meta_robots() {
		echo '<meta name="robots" content="index,follow"/>'. PHP_EOL;
	}



/**
 * Add body classes for BF pages.
 *
 * @param  array $classes
 * @return array
 */
function bfi_body_class( $classes ) {
	$classes = (array) $classes;
	return array_unique( $classes );
}


/** Template pages ********************************************************/


/** Global ****************************************************************/

if ( ! function_exists( 'bfi_get_template' ) ) {

	function bfi_get_template($file) {
		$template       = locate_template($file );

//			if ( ! $template || BFI_TEMPLATE_DEBUG_MODE ) {
		if ( ! $template ) {
			$template = BFI()->plugin_path() . '/templates/' . $file;
		}
		include( $template );
	}
}


if ( ! function_exists( 'bookingfor_demo_store' ) ) {

	/**
	 * Adds a demo store banner to the site if enabled.
	 *
	 */
	function bookingfor_demo_store() {
		if ( ! is_store_notice_showing() ) {
			return;
		}

		$notice = get_option( 'bookingfor_demo_store_notice' );

		if ( empty( $notice ) ) {
			$notice = __( 'This is a demo store for testing purposes &mdash; no orders shall be fulfilled.', 'bfi' );
		}

		echo apply_filters( 'bookingfor_demo_store', '<p class="demo_store">' . wp_kses_post( $notice ) . '</p>'  );
	}
}

/** Loop ******************************************************************/
