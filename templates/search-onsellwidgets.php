<?php

if ( ! defined( 'ABSPATH' ) ) {
	
	exit; // Exit if accessed directly

}


	$language = $GLOBALS['bfi_lang'];
	get_header( 'searchonsell' );
	do_action( 'bookingfor_before_main_content' );
?>

<div class="bfi-page-container bfi-searchonsell-page ">
	<bfipage path="searchonsells"
			data-languages="<?php echo substr($language,0,2) ?>"
			data-header = "false"
			data-footer = "false"
		>
		<div id="bficontainer" class="bfi-loader"></div>
	</bfipage>
</div>
<?php

	do_action( 'bookingfor_after_main_content' );
	get_footer( 'searchonsell' );
?>