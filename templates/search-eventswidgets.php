<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if (COM_BOOKINGFORCONNECTOR_ISBOT) {
	exit;
}
	$language = $GLOBALS['bfi_lang'];

	get_header( 'searchevents' );
	do_action( 'bookingfor_before_main_content' );
	?>
<bfi-page class="bfi-page-container bfi-searchevents-page ">
			<bfipage path="events"
				data-languages="<?php echo substr($language,0,2) ?>"
				data-header = "false"
				data-footer = "false"
				>
				<div id="bficontainer" ></div>
			</bfipage>
</bfi-page>
<?php
	do_action( 'bookingfor_after_main_content' );
	get_footer( 'searchevents' ); 
?>
