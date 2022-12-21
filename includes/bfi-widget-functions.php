<?php
/**
 * BookingFor Widget Functions
 *
 * Widget related functions and widget registration.
 *
 * @author		BookingFor
 * @category	Core
 * @package		BookingFor/Functions
 * @version     2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include widget classes.
include_once( 'widgets/class-bfi-widget-search-events.php' );
include_once( 'widgets/class-bfi-widget-carouselmerchant.php' );
include_once( 'widgets/class-bfi-widget-carouselevents.php' );
include_once( 'widgets/class-bfi-widget-carouselpoi.php' );
include_once( 'widgets/class-bfi-widget-carouselresources.php' );
include_once( 'widgets/class-bfi-widget-carouselresourcegroups.php' );
//include_once( 'widgets/class-bfi-widget-search-filters.php' );
//include_once( 'widgets/class-bfi-widget-merchant-vcard.php' );
include_once( 'widgets/class-bfi-widget-booking-search.php' );
//include_once( 'widgets/class-bfi-widget-booking-currency-switcher.php' );
//include_once( 'widgets/class-bfi-widget-booking-cart.php' );
//include_once( 'widgets/class-bfi-widget-booking-login.php' );
include_once( 'widgets/class-bfi-widget-booking-headerlink.php' );
//include_once( 'widgets/class-bfi-widget-booking-reviews.php' );
//include_once( 'widgets/class-bfi-widget-smallmap.php' );
include_once( 'widgets/class-bfi-widget-search-resources.php' );
include_once( 'widgets/class-bfi-widget-search-mapsells.php' );
//include_once( 'widgets/class-bfi-widget-search-rental.php' );
include_once( 'widgets/class-bfi-widget-search-slot.php' );
include_once( 'widgets/class-bfi-widget-search-experience.php' );
//include_once( 'widgets/class-bfi-widget-search-packages.php' );

/**
 * Register Widgets.
 *
 * @since 2.3.0
 */
function bfi_register_widgets() {
	register_widget( 'BFI_Widget_CarouselMerchants' );
	register_widget( 'BFI_Widget_CarouselEvents' );
	register_widget( 'BFI_Widget_CarouselPoi' );
	register_widget( 'BFI_Widget_CarouselResources' );
	register_widget( 'BFI_Widget_CarouselResourceGroups' );
	register_widget( 'BFI_Widget_Headerlink' );
	register_widget( 'BFI_Widget_Booking_Search_Resources' );
	register_widget( 'BFI_Widget_Search_Events' );
	register_widget( 'BFI_Widget_Booking_Search_Experience' );
	register_widget( 'BFI_Widget_Search_MapSells' );
	register_widget( 'BFI_Widget_Booking_Search_Slot' );
	register_widget( 'BFI_Widget_Booking_search' );

//	register_widget( 'BFI_Widget_Currency_Switcher' );
//	register_widget( 'BFI_Widget_Cart' );
//	register_widget( 'BFI_Widget_Login' );
//	register_widget( 'BFI_Widget_Booking_Search_Rental' );
//	register_widget( 'BFI_Widget_Booking_Search_Packages' );
//	
$bfiSidebars = array ( 
	'bfisidebar' => array(
						'id' =>	'bfisidebar',
						'name' =>	__( 'bfisidebar', 'bfi' ),
						'widgets' => array()
						),
	'bfisidebarrental' => array(
						'id' =>	'bfisidebarrental',
						'name' =>	__( 'bfisidebar Rental', 'bfi' ),
						'widgets' => array()
						),
	'bfisidebarmapsells' => array(
						'id' =>	'bfisidebarmapsells',
						'name' =>	__( 'bfisidebar MapSell', 'bfi' ),
						'widgets' => array()
						),
	'bfisidebarslots' => array(
						'id' =>	'bfisidebarslots',
						'name' =>	__( 'bfisidebar Time Slots', 'bfi' ),
						'widgets' => array()
						),
	'bfisidebarexperience' => array(
						'id' =>	'bfisidebarexperience',
						'name' =>	__( 'bfisidebarExperience', 'bfi' ),
						'widgets' => array()
						),
	'bfisidebarpackages' => array(
						'id' =>	'bfisidebarpackages',
						'name' =>	__( 'bfisidebarPackages', 'bfi' ),
						'widgets' => array()
						),
	'bookingforsearch' => array(
						'id' =>	'bookingforsearch',
						'name' =>	__( 'bfisidebar', 'bfi' ),
						'widgets' => array()
						),
	'bfisidebarhidden' => array(
						'id' =>	'bfisidebarhidden',
						'name' =>	__( 'bfisidebar Hidden', 'bfi' ),
						'widgets' => array()
						),
	);
	foreach ( $bfiSidebars as $sidebarId => $sidebar )
		{
			register_sidebar(
				array (
					'id'            => $sidebarId,
					'name'          => $sidebar['name'],
					'description'   => __( 'These features have been deprecated. This means they are no longer supported and will be removed in the next version', 'bfi' ),
					'before_widget' => '<section id="%1$s" class="widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h2 class="widget-title">',
					'after_title'   => '</h2>',
				)
			);
		}

	$active_widgets = get_option( 'sidebars_widgets' );
	
}
add_action( 'widgets_init', 'bfi_register_widgets' );
