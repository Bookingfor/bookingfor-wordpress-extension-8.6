<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if (COM_BOOKINGFORCONNECTOR_ISBOT) {
	return '';
}
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
$currModID = uniqid('bfisearchexperience');
// get searchresult page...

$customclass="";
if (!empty($instance['classes'])) {
	$customclass=$instance['classes'];
}
if (!empty($instance['g5_classes'])) {
	$customclass=$instance['g5_classes'];
}

$stateIds = "";
$regionIds = "";
$cityIds = "";
$zoneIds = '';
$categoryIds = '';
$tagids = '';
$merchantIds = "";
$searchterm = '';
$searchTermValue = '';
$zoneIdsSplitted = array();
$merchantTagIds = '';
$groupresourceIds = "";
$productTagIds = '';
$merchantCategoryId = '';
$searchvariationcodes = '';
$groupTagIds = '';

$widgettoshow =  ( ! empty( $instance['widgettoshow'] ) ) ? $instance['widgettoshow'] : '';


$defaultdurationSelected =  ( ! empty( $instance['defaultduration'] ) ) ? $instance['defaultduration'] : 0;
//$checkoutspan = '+1 day';
$checkoutspan = '+'.$defaultdurationSelected.' day';
$checkin = new DateTime('UTC');
$checkout = new DateTime('UTC');
$paxes = 0;
$paxages = array();

$checkinId = uniqid('checkin');
$checkoutId = uniqid('checkout');
$categoryIdResource = 0;
$merchantCategoryIdResource = 0;
$masterTypeId = 0;
$bookableonly = 0;
//$showdirection =0;
$showdirection = ( ! empty( $instance['showdirection'] ) ) ? esc_attr($instance['showdirection']) : '0';
$showDateRange = (! empty( $instance['showDateRange'] ) ) ? esc_attr($instance['showDateRange']) : '1';
$showDateTimeRange = (! empty( $instance['showDateTimeRange'] ) ) ? esc_attr($instance['showDateTimeRange']) : '0';
$startDateTimeRange = ( ! empty( $instance['startDateTimeRange'] ) ) ? ($instance['startDateTimeRange']) : '00:00';
$endDateTimeRange = ( ! empty( $instance['endDateTimeRange'] ) ) ? ($instance['endDateTimeRange']) : '24:00';
$fixedontop= ( ! empty( $instance['fixedontop'] ) ) ? esc_attr($instance['fixedontop']) : '0';
$fixedontopcorrection= ( ! empty( $instance['fixedontopcorrection'] ) ) ? esc_attr($instance['fixedontopcorrection']) : '0';
$showSearchText = (! empty( $instance['showSearchText'] ) ) ? esc_attr($instance['showSearchText']) : '0';
$searchTextFields = '6,11,13,14,15,17,18';
$groupBySelected = ( ! empty( $instance['groupby'] ) ) ? $instance['groupby'] : [0];
$showLocation = (  ! empty( $instance['showLocation'] ) ) ? esc_attr($instance['showLocation']) : '0';
$showAccomodations = (  ! empty( $instance['showAccomodations'] ) ) ? esc_attr($instance['showAccomodations']) : '0';
$availabilitytype = isset($instance['availabilitytypes']) ? $instance['availabilitytypes'] : array(1,3);

if(!empty($instance['searchTextFields']) && count($instance['searchTextFields'])>0){
	$searchTextFields = implode(',', $instance['searchTextFields']) ;
}
$showCheckin = true;
$dateselected = 0;
//sospeso
$dateselected = 1;
$zonesString="";




$merchantCategoriesSelected = ( ! empty( $instance['merchantcategories'] ) ) ? $instance['merchantcategories'] : array();
$unitCategoriesSelected = ( ! empty( $instance['unitcategories'] ) ) ? $instance['unitcategories'] : array();
//$masterTypeId = implode(',', $unitCategoriesSelected);

$merchantCategoriesResource = array();
$unitCategoriesResource = array();

$listmerchantCategoriesResource = "";
$tmpMerchantCategoryIdResource = (strpos($merchantCategoryIdResource, ',') !== FALSE )?"0":$merchantCategoryIdResource;
$tmpmasterTypeId = (strpos($masterTypeId, ',') !== FALSE )?"0":$masterTypeId;

$currModID = uniqid('currid');
$startDate =  new DateTime('UTC');

$startDate->setTime(0,0,0);
$checkin->setTime(0,0,0);
$checkout->setTime(0,0,0);

if ($checkin < $startDate){
	$checkin = $startDate;
	$checkout = clone $checkin;
    $checkout->modify($checkoutspan); 
}

if ($checkin > $checkout){
	$checkout = clone $checkin;
	$checkout->modify($checkoutspan);
}

//if ($checkin == $checkout){
//	$checkout->modify($checkoutspan);
//}

$showPerson = (  ! empty( $instance['showPerson'] ) ) ? esc_attr($instance['showPerson']) : '0';
$showAdult = (  ! empty( $instance['showAdult'] ) ) ? esc_attr($instance['showAdult']) : '0';
$showChildren = (  ! empty( $instance['showChildren'] ) ) ? esc_attr($instance['showChildren']) : '0';
$showSenior = (  ! empty( $instance['showSenior'] ) ) ? esc_attr($instance['showSenior']) : '0';
$showOnline = (  ! empty( $instance['showOnline'] ) ) ? esc_attr($instance['showOnline']) : '0';
$showVariationCodes = ( ! empty( $instance['showVariationCodes'] ) ) ? esc_attr($instance['showVariationCodes']) : '0';

$blockmonths = '14';
$blockdays = '7';

if($showSearchText) {
	$showLocation = '0';
	$showAccomodations = '0';
	if (empty($showSearchText)) {
	    $masterTypeId = '';
	}
}else{
	if (empty($showAccomodations)) {
	    $showAccomodations = "-1";
	}
}
?>
<?php 
if (!empty($before_widget)) {
	echo $before_widget;
}
// Check if title is set
//if (!empty($title)) {
//	  echo (!empty($before_title) ?$before_title:"") . $title . (!empty($after_title) ?$after_title:"");
//}

$fixedonbottom= ( ! empty( $instance['fixedonbottom'] ) ) ? ($instance['fixedonbottom']) : '0';
if (!empty(COM_BOOKINGFORCONNECTOR_ISMOBILE )) {
//	$fixedonbottom = 1;    
}
$resultinsamepg = ( ! empty( $instance['resultinsamepg'] ) ) ? esc_attr($instance['resultinsamepg']) : '0';
$currId =  ( ! empty( $instance['currid'] ) ) ? $instance['currid'] : uniqid('currid');
if ($currId == 'REPLACE_TO_ID') { // fix for elementor
    $currId =   uniqid('currid');
}
$currModID = 'experience'. $currId;
$totalfields=0;

?>
<div class="bookingforwidget bfisearchexperiences"
	data-direction="<?php echo $showdirection?"1":"0"; ?>"
	data-languages="<?php echo substr($language,0,2) ?>"
	data-showperson="<?php echo $showPerson ?>"
	data-groupresulttype="<?php echo implode("",$groupBySelected) ?>"
	data-merchantcategories="<?php echo (!empty($merchantCategoriesSelected)?implode(",",$merchantCategoriesSelected):"") ?>"
	data-resourcescategories="<?php echo (!empty($unitCategoriesSelected)?implode(",",$unitCategoriesSelected):"") ?>"
	data-producttagids="<?php echo $productTagIds ?>"
	data-showaccomodations="<?php echo $showAccomodations ?>"
	data-showvariationcodes="<?php echo $showVariationCodes ?>"
></div>	
<?php 
if (!empty($after_widget)) {
	echo $after_widget;
    }
?>