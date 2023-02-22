<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
if ($isportal != 1){
	exit; 
}

$language = $GLOBALS['bfi_lang'];
$languageForm ='';
$base_url = get_site_url();
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$base_url = "/" .ICL_LANGUAGE_CODE;
		}
}

$cols = !empty($instance['itemspage'])? $instance['itemspage']: 4;
$tags =  $instance['tags']; 
$maxitems = !empty($instance['maxitems'])? $instance['maxitems']: 10; 
$descmaxchars = !empty($instance['descmaxchars'])? $instance['descmaxchars']: 300; 
$theme = !empty($instance['theme'])? $instance['theme']: 0; 
$carouselid = uniqid("bficarouselpoi");

if(!empty( $tags )){
?>
<?php
if (!empty( $before_widget )) {
    echo $before_widget;
	// Check if title is set
	if (!empty($title)) {
        echo $before_title . $title . $after_title;
	}
}
?>
<div id="<?php echo $carouselid; ?>" class="bookingfor_carousel bficarouselpoi"
    data-tags="<?php echo implode(',', $tags) ?>"
    data-descmaxchars="<?php echo$descmaxchars ?>"
    data-cols="<?php echo $cols ?>"
    data-theme="<?php echo $theme ?>"
    data-maxitems="<?php echo $maxitems ?>"
    data-details="<?php _e('Details', 'bfi') ?>"></div>
<?php
	if (!empty( $after_widget )) {
		echo $after_widget;
	}
}
?>