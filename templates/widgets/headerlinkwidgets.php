<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if (COM_BOOKINGFORCONNECTOR_ISBOT) {
    return '';
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

	$showlanguages= ( ! empty( $instance['showlanguages'] ) ) ? esc_attr($instance['showlanguages']) : '0';
	$showcurrency= ( ! empty( $instance['showcurrency'] ) ) ? esc_attr($instance['showcurrency']) : '0';
	$showcart= ( ! empty( $instance['showcart'] ) ) ? esc_attr($instance['showcart']) : '0';
	$showlogin= ( ! empty( $instance['showlogin'] ) ) ? esc_attr($instance['showlogin']) : '0';
	$showfavorites= ( ! empty( $instance['showfavorites'] ) ) ? esc_attr($instance['showfavorites']) : '0';

	$customclass="";
	if (!empty($instance['classes'])) {
		$customclass=$instance['classes'];
	}
	if (!empty($instance['g5_classes'])) {
		$customclass=$instance['g5_classes'];
	}
?>
<div class="bfiwidgetcontainer <?php echo $customclass ?>">

	<div class="bookingforwidget bfiheader"
	 data-showlanguages="<?php echo (!empty($showlanguages)) ?"true":"false"; ?>"
	 data-showcurrency="<?php echo (!empty($showcurrency)) ?"true":"false"; ?>"
	 data-showcart="<?php echo (!empty($showcart)) ?"true":"false"; ?>"
	 data-showlogin = "<?php echo (!empty($showlogin)) ?"true":"false"; ?>"
	 data-showfavorites = "<?php echo (!empty($showfavorites)) ?"true":"false"; ?>"
	></div>	
</div>
