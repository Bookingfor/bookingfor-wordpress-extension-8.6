<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if (COM_BOOKINGFORCONNECTOR_ISBOT) {
	exit;
}
	$language = $GLOBALS['bfi_lang'];

	get_header( 'searchavailability' ); 
	do_action( 'bookingfor_before_main_content' );
		$layoutresult= ( ! empty( $_REQUEST['resview'] ) ) ? ($_REQUEST['resview']) : ''; 
		$widgetpath = 'booking';
		switch ($layoutresult) {
			case 'rental':
				$widgetpath = 'booking';
				break;
			case 'mapsells':
				$widgetpath = 'mapsells';
				break;
			case 'slot':
				$widgetpath = 'booking';
				break;
			case 'experience':
				$widgetpath = 'experience';
				break;
			default:      
		}

	?>
			<bfipage path="<?php echo $widgetpath ?>"
				data-languages="<?php echo substr($language,0,2) ?>"
				data-header = "false"
				data-footer = "false"
				>
				<div id="bficontainer" ></div>
			</bfipage>
<?php
	do_action( 'bookingfor_after_main_content' );
	get_footer( 'searchavailability' ); 
?>
