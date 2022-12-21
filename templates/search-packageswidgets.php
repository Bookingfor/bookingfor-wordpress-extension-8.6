<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


	
if(!empty( COM_BOOKINGFORCONNECTOR_CRAWLER )){
	$listCrawler = json_decode(COM_BOOKINGFORCONNECTOR_CRAWLER , true);
	foreach( $listCrawler as $key=>$crawler){
	if (preg_match('/'.$crawler['pattern'].'/', $_SERVER['HTTP_USER_AGENT'])) exit;
	}
	
}
	$language = $GLOBALS['bfi_lang'];

$listNameAnalytics = 15;
get_header( 'searchpackages' ); 
do_action( 'bookingfor_before_main_content' );
?>
<bfi-page class="bfi-page-container bfi-searchpackages-page ">
		<bfipage path="packages"
			data-languages="<?php echo substr($language,0,2) ?>"
			data-header = "false"
			data-footer = "false"
		>
			<div id="bficontainer" class="bfi-loader"></div>
		</bfipage>
</bfi-page>
<?php
	do_action( 'bookingfor_after_main_content' );
	get_footer( 'searchpackages' ); ?>
