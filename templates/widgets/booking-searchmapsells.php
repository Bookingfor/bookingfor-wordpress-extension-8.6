<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$isbot = false;
//define( "DONOTCACHEPAGE", true ); // Do not cache this page
	if(!empty( COM_BOOKINGFORCONNECTOR_CRAWLER )){
		$listCrawler = json_decode(COM_BOOKINGFORCONNECTOR_CRAWLER , true);
		foreach( $listCrawler as $key=>$crawler){
		if (preg_match('/'.$crawler['pattern'].'/', $_SERVER['HTTP_USER_AGENT'])) $isbot = true;
		}
		
	}
if (!$isbot) {
	$base_url = get_site_url();

	$language = $GLOBALS['bfi_lang'];
	$languageForm ='';
	if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
			global $sitepress;
			if($sitepress->get_current_language() != $sitepress->get_default_language()){
				$languageForm = "/" .ICL_LANGUAGE_CODE;
			}
	}
	$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
	$currModID = uniqid('bfisearchmapsells');
}
$showdirection = ( ! empty( $instance['showdirection'] ) ) ? esc_attr($instance['showdirection']) : '0';

?>
<?php 
if (!empty($before_widget)) {
	echo $before_widget;
}
// Check if title is set
//if (!empty($title)) {
//	  echo $before_title . $title . $after_title;
//}

$fixedonbottom= ( ! empty( $instance['fixedonbottom'] ) ) ? ($instance['fixedonbottom']) : '0';
if (!empty(COM_BOOKINGFORCONNECTOR_ISMOBILE )) {
	$fixedonbottom = 1;    
}
$currId =  ( ! empty( $instance['currid'] ) ) ? $instance['currid'] : uniqid('currid');
if ($currId == 'REPLACE_TO_ID') { // fix for elementor
    $currId =   uniqid('currid');
}
$currModID = 'mapsells'. $currId;


?>
<div class="bookingforwidget bfisearchmapsells"
	data-direction="<?php echo $showdirection?"1":"0"; ?>"
	data-languages="<?php echo substr($language,0,2) ?>"
	data-showperson="0"
	data-groupresulttype="2"
	data-merchantcategories=""
	data-resourcescategories=""
	data-showvariationcodes = "1"
></div>			

<?php 
if (!empty($after_widget)) {
	echo $after_widget;
    }
?>