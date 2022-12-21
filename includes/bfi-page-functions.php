<?php
/**
 * BookingFor  Page Functions
 *
 * Functions related to pages and menus.
 *
 * @author   BookingFor
 * @category Core
 * @package  BookingFor /Functions
 * @version     2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Retrieve page ids - used for myaccount, edit_address, shop, cart, checkout, pay, view_order, terms. returns -1 if no page is found.
 *
 * @param string $page
 * @return int
 */
function bfi_get_page_id( $page ) {

//	$pageid = apply_filters( 'bookingfor_get_' . $page . '_page_id', get_option('bookingfor_' . $page . '_page_id' ) );
//--------------- MULTISITE ---------------//
	$pageid = apply_filters( 'bookingfor_get_' . $page . '_page_id', bfi_get_option('bookingfor_' . $page . '_page_id' ) );

	//wpml plugin
	if( isset($pageid) && !is_admin() && defined( 'ICL_SITEPRESS_VERSION' ) && !ICL_PLUGIN_INACTIVE ){
		global $wp,$sitepress;
		$current_lang = $sitepress->get_current_language();
		$page_lang = $sitepress->get_language_for_element( $pageid, 'post_page');
		if($current_lang!=$page_lang){
			$translPageid = apply_filters( 'translate_object_id', $pageid, 'page', true, $current_lang );
			$pageid = $translPageid ;
		}
	}
	//polylang plugin
	if( isset($pageid) && !is_admin() && defined( 'POLYLANG_VERSION' ) ){
		global $wp,$polylang;
		$current_lang = pll_current_language();
		$page_lang = pll_get_post_language( $pageid);
		if($current_lang!=$page_lang){
			$translPageid = pll_get_post( $pageid, $current_lang);
			if(!empty($translPageid)){
				$pageid = $translPageid ;
			}
		}

	}
	return $pageid ? absint( $pageid ) : -1;
}

function bfi_get_default_page_id( $pageid ) {
	//wpml plugin
	if( isset($pageid) && defined( 'ICL_SITEPRESS_VERSION' ) && !ICL_PLUGIN_INACTIVE ){
			$wpml_options = get_option( 'icl_sitepress_settings' );
			$default_lang = $wpml_options['default_language'];
			$pageid = apply_filters( 'wpml_object_id', $pageid, 'post', FALSE, $default_lang ) ;
	}
	if( isset($pageid) && defined( 'POLYLANG_VERSION' ) ){
			$defaultLanguage = pll_default_language();
			$translations = pll_get_post_translations($pageid);
			if (in_array($defaultLanguage,$translations)) {
				$pageid = $translations[$defaultLanguage];
			}
	}
	return $pageid ? absint( $pageid ) : -1;
}
function bfi_get_translated_page_id( $pageid ) {
	//wpml plugin
	if( isset($pageid) && defined( 'ICL_SITEPRESS_VERSION' ) && !ICL_PLUGIN_INACTIVE ){
		global $wp,$sitepress;
		$current_lang = $sitepress->get_current_language();
		$page_lang = $sitepress->get_language_for_element( $pageid, 'post_page');
		if($current_lang!=$page_lang){
			$translPageid = apply_filters( 'translate_object_id', $pageid, 'page', true, $current_lang );
			$pageid = $translPageid ;
		}
	}
	if( isset($pageid) && defined( 'POLYLANG_VERSION' ) ){
		global $wp,$polylang;
		$current_lang = pll_current_language();
		$page_lang = pll_get_post_language( $pageid);
		if($current_lang!=$page_lang){
			$translPageid = pll_get_post( $pageid, $current_lang);
			if(!empty($translPageid)){
				$pageid = $translPageid ;
			}
		}
	}
	return $pageid ? absint( $pageid ) : -1;
}

function bfi_get_template_page_id( $page ) {
	$pageid = apply_filters( 'bookingfor_get_' . $page . '_page_id', get_option('bookingfor_' . $page . '_page_id' ) );

	//wpml plugin
	if( isset($pageid) && !is_admin() && defined( 'ICL_SITEPRESS_VERSION' ) && !ICL_PLUGIN_INACTIVE ){
		global $wp,$sitepress;
		$current_lang = $sitepress->get_current_language();
		$page_lang = $sitepress->get_language_for_element( $pageid, 'post_page');
		if($current_lang!=$page_lang){
			$translPageid = apply_filters( 'translate_object_id', $pageid, 'page', true, $current_lang );
			$pageid = $translPageid ;
		}
	}
	//polylang plugin
	if( isset($pageid) && !is_admin() && defined( 'POLYLANG_VERSION' ) ){
		global $wp,$polylang;
		$current_lang = pll_current_language();
		$page_lang = pll_get_post_language( $pageid);
		if($current_lang!=$page_lang){
			$translPageid = pll_get_post( $pageid, $current_lang);
			if(!empty($translPageid)){
				$pageid = $translPageid ;
			}
			$pageid = $translPageid ;
		}

	}

	return $pageid ? absint( $pageid ) : -1;
}



/**
 * Retrieve page permalink.
 *
 * @param string $page
 * @return string
 */
function bfi_get_page_permalink( $page ) {
	$page_id   = bfi_get_page_id( $page );
	$permalink = $page_id ? get_permalink( $page_id ) : get_home_url();
	return apply_filters( 'bookingfor_get_' . $page . '_page_permalink', $permalink );
}

function bfi_sanitize_permalink( $value ) {
	global $wpdb;

	$value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', $value );

	if ( is_wp_error( $value ) ) {
		$value = '';
	}

	$value = esc_url_raw( $value );
	$value = str_replace( 'http://', '', $value );
	return untrailingslashit( $value );
}

function bfi_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'bfi_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

function bfi_get_current_page() {

	$currpage = (get_query_var('paged')) ? get_query_var('paged') : ((get_query_var('page')) ? get_query_var('page') : (get_query_var('currpage')? get_query_var('currpage') : 1));
	if(isset($_REQUEST['paged'])){
		$currpage = absint($_REQUEST['paged']);
	}
	return $currpage ;
}

function bfi_add_json_ld($payloadresource){
	echo '<script type="application/ld+json">';
	echo json_encode($payloadresource);
	echo '</script>';
}

/*-------------------- new function for languages  -----------------------*/

function bfi_get_pages() {
	$pages = apply_filters( 'bfi_installed_pages', array(
		'bookingfor_searchpackages_page_id',
		'bookingfor_packagesdetails_page_id',
		'bookingfor_searchavailability_page_id',
		'bookingfor_searchmapsells_page_id',
		'bookingfor_searchevents_page_id',
		'bookingfor_searchonsell_page_id',
		'bookingfor_eventdetails_page_id',
		'bookingfor_pointsofinterestdetails_page_id',
		'bookingfor_merchantdetails_page_id',
		'bookingfor_resourcegroupdetails_page_id',
		'bookingfor_experiencedetails_page_id',
		'bookingfor_accommodationdetails_page_id',
		'bookingfor_onselldetails_page_id',
		'bookingfor_payment_page_id',
		'bookingfor_cartdetails_page_id',
		'bookingfor_genericrequest_page_id',
			
	) );

	foreach ( $pages as &$page ) {
		$page = preg_replace( '/(bookingfor_)(.*)(_page_id)/', "$2", $page );
	}
	return $pages;
}
	function bfi_get_linked_pages() {

		$check_pages = bfi_get_pages();

		$missing_lang      = array();
		$pages_in_progress = array();
		
		$status = array();

		foreach ( $check_pages as $page ) {
			$page_id  = bfi_get_page_id( $page );
			$page_obj = get_post( $page_id );
			if ( ! $page_id || ! $page_obj || $page_obj->post_status != 'publish' ) {
				$status[$page] = 'non_exist';
			}else{
				$status[$page] = $page_id  ;
			}
		}
		return $status;
	}

	/**
	 * get missing pages
	 * return array;
	 */
	function bfi_get_missing_pages() {

		$check_pages = bfi_get_pages();

		$missing_lang      = array();
		$pages_in_progress = array();

		foreach ( $check_pages as $page ) {
			$page_id  = bfi_get_page_id( $page );
			$page_obj = get_post( $page_id );
			if ( ! $page_id || ! $page_obj || $page_obj->post_status != 'publish' ) {
				return 'non_exist:' . $page;
			}
		}

		$missing_lang_codes = array();
		$missing_lang_locales = array();
		$missing_page = array();
		//wpml plugin
		if(defined( 'ICL_SITEPRESS_VERSION' ) && !ICL_PLUGIN_INACTIVE ){
			
			
			global $sitepress;
			$languages = $sitepress->get_active_languages();
						
			foreach ( $check_pages as $page ) {
				$store_page_id               = bfi_get_page_id( $page );
				$trid                        = $sitepress->get_element_trid( $store_page_id, 'post_page' );
				$translations                = $sitepress->get_element_translations( $trid, 'post_page', true );
				$pages_in_progress_miss_lang = '';
												
				foreach ( $languages as $language ) {
					if ( ! isset( $translations[ $language['code'] ] ) || ( ! is_null( $translations[ $language['code'] ]->element_id ) && get_post_status( $translations[ $language['code'] ]->element_id ) != 'publish' ) ) {
												
						if (! in_array( $language['code'], $missing_lang_codes ) ) {						    
							$missing_lang_codes[] = $language['code'];

							$missing_lang[] = $language['display_name'];
						}

						continue;
					}

					if ( !isset( $translations[ $language['code'] ] ) || (isset( $translations[ $language['code'] ] ) && is_null( $translations[ $language['code'] ]->element_id ) )) {

						$pages_in_progress[ $store_page_id ][] = $language;

					}
				}
			}

		}
		//polylang plugin
		if(defined( 'POLYLANG_VERSION' ) ){
			global $polylang;
			$languages = pll_languages_list();
			$languagenames = pll_languages_list(array( 'fields' => 'name'));
			$languagelocales = pll_languages_list(array( 'fields' => 'locale'));

			foreach ( $check_pages as $page ) {

				$curr_page_id = bfi_get_page_id( $page );
				$pages_in_progress_miss_lang = '';
				foreach ( $languages as $key => $language ) {
					$translPageid = pll_get_post( $curr_page_id, $language);

					if ( ! in_array( $language, $missing_lang_codes ) &&

					( ! isset( $translPageid ) || ( ! is_null( $translPageid ) && get_post_status( $translPageid ) != 'publish' ) ) ) {

						$missing_page[] = $page;
						$missing_lang_codes[] = $language;
						$missing_lang_locales[] = $languagelocales[$key];
						$missing_lang[] = $languagenames[$key];

						continue;
					}

					if ( empty($translPageid) ) {

						$pages_in_progress[ $curr_page_id ][] = $language;

					}
				}
			}
		}

		foreach ( $pages_in_progress as $key => $page_in_progress ) {
			$pages_in_progress_notice[ $key ]['page'] = get_the_title( $key );
			$pages_in_progress_notice[ $key ]['slug'] = get_post_field( 'post_name', get_post( $key ) );
			$pages_in_progress_notice[ $key ]['lang'] = $page_in_progress;

		}

		$status = array();

		if ( ! empty( $missing_lang ) ) {
			$status['page']  = $missing_page;
			$status['lang']  = $missing_lang;
			$status['codes'] = $missing_lang_codes;
			$status['locales'] = $missing_lang_locales;
		}

		if ( ! empty( $pages_in_progress_notice ) ) {
			$status['in_progress'] = $pages_in_progress_notice;
		}

		if ( ! empty( $status ) ) {
			return $status;
		} else {
			return false;
		}
	}
