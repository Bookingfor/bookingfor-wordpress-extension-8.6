<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

$language = $GLOBALS['bfi_lang'];
$cultureCode = strtolower(substr($language, 0, 2));

get_header( 'genericrequest' );
do_action( 'bookingfor_before_main_content' );
?>

<div class="bfi-page-container bfi-genericrequest-page ">
	<div style="width: 100%;">

				<!-- widget -->
				<bfipage path="infocontact" data-languages="<?php echo $cultureCode ?>">
					<div id="bfiheader" class=""></div>
					<div id="bficontainer" class="bfi-loader"></div>
					<div id="bfifooter" class=""></div>
				</bfipage>
				<!-- widget END -->

	</div>		
</div>		

<?php 
do_action( 'bookingfor_after_main_content' );
get_footer( 'genericrequest' ); 
?>