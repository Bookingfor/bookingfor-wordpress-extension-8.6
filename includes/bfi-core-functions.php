<?php
/**
 * BookingFor Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @author 		BookingFor
 * @category 	Core
 * @package 	Bookingfor/Functions
 * @version     2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bfi_get_template_part( $slug, $name = '' ) {
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/bookingfor/slug-name.php
	if ( $name && ! BFI_TEMPLATE_DEBUG_MODE ) {
		$template = locate_template( array( "{$slug}-{$name}.php", BFI()->template_path() . "{$slug}-{$name}.php" ) );
	}

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( BFI()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
		$template = BFI()->plugin_path() . "/templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/bookingfor/slug.php
	if ( ! $template && ! BFI_TEMPLATE_DEBUG_MODE ) {
		$template = locate_template( array( "{$slug}.php", BFI()->template_path() . "{$slug}.php" ) );
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'bfi_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
function bfi_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

	$located = bfi_locate_template( $template_name, $template_path, $default_path );
	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$located = apply_filters( 'bfi_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'bookingfor_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'bookingfor_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Like bfi_get_template, but returns the HTML instead of outputting.
 * @see bfi_get_template
 * @since 2.5.0
 * @param string $template_name
 */
function bfi_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
	bfi_get_template( $template_name, $args, $template_path, $default_path );
	return ob_get_clean();
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *		yourtheme		/	$template_path	/	$template_name
 *		yourtheme		/	$template_name
 *		$default_path	/	$template_name
 *
 * @access public
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function bfi_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = BFI()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = BFI()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template/
	if ( ! $template || BFI_TEMPLATE_DEBUG_MODE ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'bookingfor_locate_template', $template, $template_name, $template_path );
}


/**
 * Get an image size.
 *
 * Variable is filtered by bookingfor_get_image_size_{image_size}.
 *
 * @param mixed $image_size
 * @return array
 */
function bfi_get_image_size( $image_size ) {
	if ( is_array( $image_size ) ) {
		$width  = isset( $image_size[0] ) ? $image_size[0] : '300';
		$height = isset( $image_size[1] ) ? $image_size[1] : '300';
		$crop   = isset( $image_size[2] ) ? $image_size[2] : 1;

		$size = array(
			'width'  => $width,
			'height' => $height,
			'crop'   => $crop
		);

		$image_size = $width . '_' . $height;

	} elseif ( in_array( $image_size, array( 'shop_thumbnail', 'shop_catalog', 'shop_single' ) ) ) {
		$size           = get_option( $image_size . '_image_size', array() );
		$size['width']  = isset( $size['width'] ) ? $size['width'] : '300';
		$size['height'] = isset( $size['height'] ) ? $size['height'] : '300';
		$size['crop']   = isset( $size['crop'] ) ? $size['crop'] : 0;

	} else {
		$size = array(
			'width'  => '300',
			'height' => '300',
			'crop'   => 1
		);
	}

	return apply_filters( 'bookingfor_get_image_size_' . $image_size, $size );
}

/**
 * Queue some JavaScript code to be output in the footer.
 *
 * @param string $code
 */
function bfi_enqueue_js( $code ) {
	global $bfi_queued_js;

	if ( empty( $bfi_queued_js ) ) {
		$bfi_queued_js = '';
	}

	$bfi_queued_js .= "\n" . $code . "\n";
}

/**
 * Output any queued javascript code in the footer.
 */
function bfi_print_js() {
	global $bfi_queued_js;

	if ( ! empty( $bfi_queued_js ) ) {
		// Sanitize.
		$bfi_queued_js = wp_check_invalid_utf8( $bfi_queued_js );
		$bfi_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $bfi_queued_js );
		$bfi_queued_js = str_replace( "\r", '', $bfi_queued_js );

		$js = "<!-- BookingFor JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) { $bfi_queued_js });\n</script>\n";

		/**
		 * bookingfor_queued_js filter.
		 *
		 * @since 2.6.0
		 * @param string $js JavaScript code.
		 */
		echo apply_filters( 'bookingfor_queued_js', $js );

		unset( $bfi_queued_js );
	}
}


/**
 * BookingFor Core Supported Themes.
 *
 * @since 2.2
 * @return string[]
 */
function bfi_get_core_supported_themes() {
	return array( 'twentysixteen', 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentyeleven', 'twentytwelve', 'twentyten' );
}

/**
 * Enables template debug mode.
 */
function bfi_template_debug_mode() {
	if ( ! defined( 'BFI_TEMPLATE_DEBUG_MODE' ) ) {
		$status_options = get_option( 'bookingfor_status_options', array() );
		if ( ! empty( $status_options['template_debug_mode'] ) && current_user_can( 'manage_options' ) ) {
			define( 'BFI_TEMPLATE_DEBUG_MODE', true );
		} else {
			define( 'BFI_TEMPLATE_DEBUG_MODE', false );
		}
	}
}
add_action( 'after_setup_theme', 'bfi_template_debug_mode', 20 );


/**
 * Display a BookingFor help tip.
 *
 * @since  2.5.0
 *
 * @param  string $tip        Help tip text
 * @param  bool   $allow_html Allow sanitized HTML if true or escape
 * @return string
 */
if ( ! function_exists( 'bfi_help_tip' ) ) {
	function bfi_help_tip( $tip, $allow_html = false ) {
		if ( $allow_html ) {
			$tip = bfi_sanitize_tooltip( $tip );
		} else {
			$tip = esc_attr( $tip );
		}

		return '<span class="bookingfor-help-tip" data-tip="' . $tip . '"></span>';
	}
}

if ( ! function_exists( 'bfi_remove_querystring_var' ) ) {
	function bfi_remove_querystring_var($url, $key) {
		$url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
		$url = substr($url, 0, -1);
		return $url;
	}
}
if ( ! function_exists( 'bfi_add_querystring_var' ) ) {
	function bfi_add_querystring_var($url, $key, $value) {
		$url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
		$url = substr($url, 0, -1);
		if (strpos($url, '?') === false) {
			return ($url . '?' . $key . '=' . $value);
		} else {
			return ($url . '&' . $key . '=' . $value);
		}
	}
}

// Wordpress function 'get_site_option' and 'get_option'
if ( ! function_exists( 'bfi_get_option' ) ) {
	function bfi_get_option($option_name) {

		if(COM_BOOKINGFORCONNECTOR_NETWORK_ACTIVATED== true) {

			// Get network site option
			return get_site_option($option_name);
		}
		else {

			// Get blog option
			return get_option($option_name);
		}
	}
}

// Wordpress function 'update_site_option' and 'update_option'
if ( ! function_exists( 'bfi_update_option' ) ) {
	function bfi_update_option($option_name, $option_value) {

		if(COM_BOOKINGFORCONNECTOR_NETWORK_ACTIVATED== true) {


			// Update network site option
			return update_site_option($option_name, $option_value);
		}
		else {

		// Update blog option
		return update_option($option_name, $option_value);
		}
	}
}

if ( ! function_exists( 'bfi_get_translated_text' ) ) {
	function bfi_get_translated_text( $text, $context, $domain = 'default', $the_locale = 'en_GB' ){
        global $l10n;
		// get the global
		global $locale;
		// save the current value
		$old_locale = $locale;
		// override it with our desired locale
		$locale = $the_locale;
		$backup = null;
        if(isset($l10n[$domain])) {
			$backup = $l10n[$domain];
			unset( $l10n[ $domain ] );
        }
        load_textdomain($domain, BFI()->plugin_path() . '/lang/bfi-'. $the_locale . '.mo');

		// get the translated text (note that the 'locale' filter will be applied)
		$translated = _x( $text, $context, $domain );
		// reset the locale
		$locale = $old_locale;
		if(!empty($backup)) $l10n[$domain] = $backup;
		// return the translated text
		return $translated;
	}
}
