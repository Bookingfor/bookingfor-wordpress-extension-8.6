<?php
/**
 * The Template for displaying all merchant list
 *
 *
 * @see 	   
 * @author 		Bookingfor
 * @package 	        Bookingfor/Templates
 * @version             2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
bfi()->define( "DONOTCACHEPAGE", true ); // Do not cache this page
if (COM_BOOKINGFORCONNECTOR_ISBOT) {
	exit;
}

$language = $GLOBALS['bfi_lang'];
$languageForm ='';
$base_url = get_site_url();
$cultureCode = strtolower(substr($language, 0, 2));
$portalinfo =  BFCHelper::getSubscriptionInfos();
$layout = get_query_var( 'bfi_layout', '' );

get_header();
do_action( 'bookingfor_before_main_content' );

?>
<bfi-page class="bfi-page-container bfi-cart-page ">
	<div class="bfi_page_container" style="width: 100%;">
<?php 
	switch ( $layout) {
		case 'thanks' :
			$orderid = 	BFCHelper::getVar('orderid');
?>
				<!-- widget -->
				<div class="bookingforwidget" path="thanks" 
					data-languages="<?php echo $cultureCode ?>"
					data-id="<?php echo $orderid ?>"
					>
				<div id="bficontainer" class="bfi-loader"></div>
				</div>
				<!-- widget END -->
<?php 		    		    
		    break;
		case 'error' :
?>
				<!-- widget -->
				<div class="bookingforwidget" path="error" 
					data-languages="<?php echo $cultureCode ?>">
				<div id="bficontainer" class="bfi-loader"></div>
				</div>
				<!-- widget END -->
<?php 		    		    
		    
		    break;
		default:
?>
				<!-- widget -->
				<div class="bookingforwidget" path="cart" 
					data-languages="<?php echo $cultureCode ?>">
				<div id="bficontainer" class="bfi-loader"></div>
				</div>
				<!-- widget END -->
<?php 
	}
?>
	</div>		
</bfi-page>		
<?php
	do_action( 'bookingfor_after_main_content' );
?>
<?php get_footer( ); ?>
