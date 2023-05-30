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
$currModID = uniqid('bfisearchresources');

$searchType = "0";
$categoryIdResource = 0;
$merchantCategoryIdResource = 0;

$zoneId = 0;
$cityId = 0;
$pricemax = '';
$pricemin = '';
$areamin = '';
$areamax = '';
$points = '';
$services = '';
$isnewbuilding='';
$zoneIdsSplitted = array();
$checkoutspan = '+1 day';
$checkin = new DateTime('UTC');
$checkout = new DateTime('UTC');
$paxes = 2;
$paxages = array();
$masterTypeId = '';
$checkinId = uniqid('checkin');
$checkoutId = uniqid('checkout');
$durationId = uniqid('duration');
$duration = 1;
$bookableonly = 0;

$stateIds = "";
$regionIds = "";
$cityIds = "";
$zoneIds = '';
$getBaseFiltersFor = "";
$merchantIds = "";
$groupresourceIds = "";
$searchterm = '';
$searchTermValue = '';
$merchantTagIds = '';
$productTagIds = '';
$groupTagIds = '';

$merchantCategoryId = '';
$minRooms = 1;
$maxRooms = 10;
$searchvariationcodes = '';

$instanceContext = ( ! empty( $instance['currcontext'] ) ) ? $instance['currcontext'] : uniqid('currcontext'); ;

$btntext = ( ! empty( $instance['btntext'] ) ) ? esc_attr($instance['btntext']) : 'Find availability';

// WPML >= 3.2
if ( defined( 'ICL_SITEPRESS_VERSION' ) && version_compare( ICL_SITEPRESS_VERSION, '3.2', '>=' ) ) {
	$btntext = apply_filters( 'wpml_translate_single_string', $instance['btntext'], $instanceContext, 'btntext' );
// WPML and Polylang compatibility
} elseif ( function_exists( 'icl_t' ) ) {

	//sisitemare
	$btntext = icl_t( $instanceContext, 'btntext', $btntext );
}else{
	$btntext = __( $btntext, 'bfi');
}

$btntext = ( ! empty( $btntext ) ) ? $btntext : __('Find availability', 'bfi');

	$minRooms = (  ! empty( $instance['minRooms'] ) ) ? ($instance['minRooms']) : 1;
	$maxRooms = (  ! empty( $instance['maxRooms'] ) ) ? ($instance['maxRooms']) : 10;

$minrooms = $minRooms;
$maxrooms = $maxRooms;
$minqt = 1;
$currFilterOrder = "";
$currFilterOrderDirection = "";

$tabiconbooking = ( ! empty( $instance['tabiconbooking'] ) ) ? ($instance['tabiconbooking']) : 'fa fa-suitcase';

$showdirection = ( ! empty( $instance['showdirection'] ) ) ? esc_attr($instance['showdirection']) : '0';
$fixedontop= ( ! empty( $instance['fixedontop'] ) ) ? esc_attr($instance['fixedontop']) : '0';
$fixedontopcorrection= ( ! empty( $instance['fixedontopcorrection'] ) ) ? esc_attr($instance['fixedontopcorrection']) : '0';

$resultinsamepg = ( ! empty( $instance['resultinsamepg'] ) ) ? esc_attr($instance['resultinsamepg']) : '0';

$currId =  ( ! empty( $instance['currid'] ) ) ? $instance['currid'] : uniqid('currid');
if ($currId == 'REPLACE_TO_ID') { // fix for elementor
    $currId =   uniqid('currid');
}
$currModID = 'bfiresources'. $currId;


$fixedonbottom= ( ! empty( $instance['fixedonbottom'] ) ) ? ($instance['fixedonbottom']) : '0';

$showLocation = (  ! empty( $instance['showLocation'] ) ) ? esc_attr($instance['showLocation']) : '0';
$showMapIcon = (  ! empty( $instance['showMapIcon'] )  && !empty(COM_BOOKINGFORCONNECTOR_ENABLEGOOGLEMAPSAPI) ) ? esc_attr($instance['showMapIcon']) : '0';
$showSearchText = (  ! empty( $instance['showSearchText'] ) ) ? esc_attr($instance['showSearchText']) : '0';
$searchTextFields = '6,11,13,14,15,17,18';
if(!empty($instance['searchTextFields']) && count($instance['searchTextFields'])>0){
	$searchTextFields = implode(',', $instance['searchTextFields']) ;
}

$showAccomodations = (  ! empty( $instance['showAccomodations'] ) ) ? esc_attr($instance['showAccomodations']) : '0';

$showDateRange = (  ! empty( $instance['showDateRange'] ) ) ? esc_attr($instance['showDateRange']) : '0';
$showDateTimeRange = (  ! empty( $instance['showDateTimeRange'] ) ) ? esc_attr($instance['showDateTimeRange']) : '0';

$startDateTimeRange = ( ! empty( $instance['startDateTimeRange'] ) ) ? ($instance['startDateTimeRange']) : '00:00';
$endDateTimeRange = ( ! empty( $instance['endDateTimeRange'] ) ) ? ($instance['endDateTimeRange']) : '24:00';

$startDate =  new DateTime('UTC');
$startDate->setTime(0,0,0);

if (!empty(COM_BOOKINGFORCONNECTOR_FORM_STARTDATE) && DateTime::createFromFormat('d/m/Y',COM_BOOKINGFORCONNECTOR_FORM_STARTDATE,new DateTimeZone('UTC')) !== false ) {
	$startDatetmp = DateTime::createFromFormat('d/m/Y',COM_BOOKINGFORCONNECTOR_FORM_STARTDATE,new DateTimeZone('UTC'));
	$startDatetmp->setTime(0,0,0);
	if ($startDatetmp > $startDate){
		$startDate = $startDatetmp;
	}
}

if($showDateTimeRange){ 
	if (strpos($startDateTimeRange,":")!== false) {
		$checkinTime = explode(':',$startDateTimeRange.":0");
		$startDate->setTime((int)$checkinTime[0], (int)$checkinTime[1]); 
		$checkin->setTime((int)$checkinTime[0], (int)$checkinTime[1]); 
	}
	if (strpos($endDateTimeRange,":")!== false) {
		$checkoutTime = explode(':',$endDateTimeRange.":0");
		$checkout->setTime((int)$checkoutTime[0], (int)$checkoutTime[1]); 
	}
}else{
	$startDate->setTime(0,0,0);
	$checkin->setTime(0,0,0);
	$checkout->setTime(0,0,0);

}

if ($checkin < $startDate){
	$checkin = $startDate;
	$checkout = clone $checkin;
    $checkout->modify($checkoutspan); 
}

if ($checkin > $checkout){
	$checkout = clone $checkin;
	$checkout->modify($checkoutspan);
}
if ($checkin == $checkout){
	$checkout->modify($checkoutspan);
}

$duration = $checkin->diff($checkout);

$onlystay = (  ! empty( $instance['onlystay'] ) ) ? ($instance['onlystay']) : '0';

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

$showResource = (  ! empty( $instance['showResource'] ) ) ? ($instance['showResource']) : '0';
$minResource = 1;
$maxResource = 10;
if ( !empty($instance['limitResource'])) {
	$minResource = (  ! empty( $instance['minResource'] ) ) ? ($instance['minResource']) : 1;
	$maxResource = (  ! empty( $instance['maxResource'] ) ) ? ($instance['maxResource']) : 10;
}

$showRooms = (  ! empty( $instance['showRooms'] ) ) ? ($instance['showRooms']) : '0';

/*
    $minqt = $minResource;
if ($maxqt<$minResource || $maxqt>$maxResource ) {
    $maxqt = $minResource;
}
*/
$showPerson = (  ! empty( $instance['showPerson'] ) ) ? esc_attr($instance['showPerson']) : '0';
$showAdult = (  ! empty( $instance['showAdult'] ) ) ? esc_attr($instance['showAdult']) : '0';
$showChildren = (  ! empty( $instance['showChildren'] ) ) ? esc_attr($instance['showChildren']) : '0';
$showSenior = (  ! empty( $instance['showSenior'] ) ) ? esc_attr($instance['showSenior']) : '0';
$showOnlineBooking = (  ! empty( $instance['showOnlineBooking'] ) ) ? esc_attr($instance['showOnlineBooking']) : '0';
$showVariationCodes = ( ! empty( $instance['showVariationCodes'] ) ) ? esc_attr($instance['showVariationCodes']) : '0';

$merchantCategoriesSelected = ( ! empty( $instance['merchantcategories'] ) ) ? $instance['merchantcategories'] : array();
$unitCategoriesSelected = ( ! empty( $instance['unitcategories'] ) ) ? $instance['unitcategories'] : array();

$merchantCategoriesResource = array();
$unitCategoriesResource = array();

$listmerchantCategoriesResource = "";

$availabilityTypeList = array();
$availabilityTypeList['1'] = __('Nights', 'bfi');
$availabilityTypeList['0'] = __('Days', 'bfi');

$availabilityTypesSelected = ( ! empty( $instance['availabilitytypes'] ) ) ? $instance['availabilitytypes'] : array();

$itemTypesSelected = ( ! empty( $instance['itemtypes'] ) ) ? $instance['itemtypes'] : array();

$groupBySelected = ( ! empty( $instance['groupby'] ) ) ? $instance['groupby'] : [0];

$tmpMerchantCategoryIdResource = (strpos($merchantCategoryIdResource, ',') !== FALSE )?"0":$merchantCategoryIdResource;
$tmpmasterTypeId = (strpos($masterTypeId, ',') !== FALSE )?"0":$masterTypeId;



if ((empty(COM_BOOKINGFORCONNECTOR_ISMOBILE ) &&!$showdirection )|| BFI()->isSearchPage()) {
	$fixedonbottom = 0;    
}
if (!empty( $before_widget) ){
	echo $before_widget;
}
// Check if title is set
//if (!empty( $title) ) {
////  echo $before_title . $title . $after_title;
//  echo  $title ;
//}

?>
<div class="bookingforwidget bfisearchresources"
	data-direction="<?php echo $showdirection?"1":"0"; ?>"
	data-languages="<?php echo substr($language,0,2) ?>"
	data-showperson="<?php echo $showPerson ?>"
	data-groupresulttype="<?php echo implode("",$groupBySelected) ?>"
	data-merchantcategories="<?php echo (!empty($merchantCategoriesSelected)?implode(",",$merchantCategoriesSelected):"") ?>"
	data-resourcescategories="<?php echo (!empty($unitCategoriesSelected)?implode(",",$unitCategoriesSelected):"") ?>"
	data-producttagids="<?php echo $productTagIds ?>"
	data-showaccomodations="<?php echo $showAccomodations ?>"
	data-showvariationcodes="<?php echo $showVariationCodes ?>"
	data-scope="<?php echo $searchTextFields ?>"
></div>	
<?php
if (!empty($after_widget)) {
	echo $after_widget;
    }
?>