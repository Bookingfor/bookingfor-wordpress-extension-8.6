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
$currModID = uniqid('bfisearch');

// get searchresult page...

$searchtypetab = -1;
$contractTypeId = 0;
$searchType = "0";
$searchTypeonsell = "0";

$categoryIdRealEstate = 0;
$categoryIdResource = 0;
$merchantCategoryIdRealEstate = 0;
$merchantCategoryIdResource = 0;

$zoneId = 0;
$cityId = 0;
$pricemax = '';
$pricemin = '';
$areamin = '';
$areamax = '';
$points = '';
$pointsonsell = '';
$roomsmin = '';
$roomsmax = '';
$bathsmin = '';
$bathsmax = '';
$services = '';
$isnewbuilding='';
$zoneIdsSplitted = array();
$bedroomsmin = '';
$bedroomsmax = '';
$checkoutspan = '+1 day';
//$checkoutspan = '+0 day';
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
$merchantCategoryId = '';
$minRooms = 1;
$maxRooms = 10;
$searchvariationcodes = '';

$tablistSelected = ( ! empty( $instance['tablistSelected'] ) ) ? $instance['tablistSelected'] : array();

$tablistResources = array_intersect($tablistSelected,array(0,1,2,4));
$tablistRealEstate = array_intersect($tablistSelected, array(3));
$tablistEvents = array_intersect($tablistSelected, array(5));
$tablistMapSells = array_intersect($tablistSelected, array(6));


if (!empty($tablistResources) && !empty($instance['limitRooms'])) {
	$minRooms = ( !empty($tablistResources) && ! empty( $instance['minRooms'] ) ) ? ($instance['minRooms']) : 1;
	$maxRooms = ( !empty($tablistResources) && ! empty( $instance['maxRooms'] ) ) ? ($instance['maxRooms']) : 10;
}
$minrooms = $minRooms;
$maxrooms = $maxRooms;
$minqt = 1;
$currFilterOrder = "";
$currFilterOrderDirection = "";


if(!in_array($searchtypetab,$tablistSelected)){
	$searchtypetab = -1;
}

$tabiconbooking = ( ! empty( $instance['tabiconbooking'] ) ) ? ($instance['tabiconbooking']) : 'fa fa-suitcase';
$tabiconservices = ( ! empty( $instance['tabiconservices'] ) ) ? ($instance['tabiconservices']) : 'fa fa-calendar';
$tabiconactivities = ( ! empty( $instance['tabiconactivities'] ) ) ? ($instance['tabiconactivities']) : 'fa fa-calendar';
$tabiconothers = ( ! empty( $instance['tabiconothers'] ) ) ? ($instance['tabiconothers']) : 'fa fa-calendar';

$tabiconrealestate = ( ! empty( $instance['tabiconrealestate'] ) ) ? ($instance['tabiconrealestate']) : 'fa fa-home';

$tabiconevents = ( ! empty( $instance['tabiconevents'] ) ) ? ($instance['tabiconevents']) : 'fa fa-home';
$tabiconmapsell = ( ! empty( $instance['tabiconmapsell'] ) ) ? ($instance['tabiconmapsell']) : 'fa fa-home';

$showdirection = ( ! empty( $instance['showdirection'] ) ) ? esc_attr($instance['showdirection']) : '0';
$fixedontop= ( ! empty( $instance['fixedontop'] ) ) ? esc_attr($instance['fixedontop']) : '0';
$fixedontopcorrection= ( ! empty( $instance['fixedontopcorrection'] ) ) ? esc_attr($instance['fixedontopcorrection']) : '0';

if($fixedontop){
// Add styles
//$style = '.bfi-affix-top'.$currModID.'.bfiAffixTop {'
//        . 'top: '.$fixedontopcorrection.'px !important;'
//        . '}' 
//        . '.bfi-calendar-affixtop'.$currModID.'{'
//        . 'top:'.($fixedontopcorrection + 110).'px !important;'
//        . '}';
//$document->addStyleDeclaration($style);
}

$currId =  ( ! empty( $instance['currid'] ) ) ? $instance['currid'] : uniqid('currid');
if ($currId == 'REPLACE_TO_ID') { // fix for elementor
    $currId =   uniqid('currid');
}
$currModID = $currId;


$showLocation = ( !empty($tablistResources) && ! empty( $instance['showLocation'] ) ) ? esc_attr($instance['showLocation']) : '0';
$showMapIcon = ( !empty($tablistResources) && ! empty( $instance['showMapIcon'] )  && !empty(COM_BOOKINGFORCONNECTOR_ENABLEGOOGLEMAPSAPI) ) ? esc_attr($instance['showMapIcon']) : '0';
$showSearchText = ( !empty($tablistResources) && ! empty( $instance['showSearchText'] ) ) ? esc_attr($instance['showSearchText']) : '0';
$searchTextFields = '6,11,13,14,15,17,18';
if(!empty($instance['searchTextFields']) && count($instance['searchTextFields'])>0){
	$searchTextFields = implode(',', $instance['searchTextFields']) ;
}
$searchTextFieldsMapsell = '6,11,13,14,15,17,18';
if(!empty($instance['searchTextFieldsMapsell']) && count($instance['searchTextFieldsMapsell'])>0){
	$searchTextFieldsMapsell = implode(',', $instance['searchTextFieldsMapsell']) ;
}
$searchTextFieldsEvent = '6,11,13,14,15,17,18';
if(!empty($instance['searchTextFieldsEvent']) && count($instance['searchTextFieldsEvent'])>0){
	$searchTextFieldsEvent = implode(',', $instance['searchTextFieldsEvent']) ;
}


$showAccomodations = ( !empty($tablistResources) && ! empty( $instance['showAccomodations'] ) ) ? esc_attr($instance['showAccomodations']) : '0';
$showDateRange = ( !empty($tablistResources) && ! empty( $instance['showDateRange'] ) ) ? esc_attr($instance['showDateRange']) : '0';
$showDateTimeRange = ( !empty($tablistResources) && ! empty( $instance['showDateTimeRange'] ) ) ? esc_attr($instance['showDateTimeRange']) : '0';
$showDateOneDays = ( !empty($tablistResources) && ! empty( $instance['showDateOneDays'] ) ) ? esc_attr($instance['showDateOneDays']) : '0';
$showDateOneDaysMapSell = ( !empty($tablistMapSells) && ! empty( $instance['showDateOneDaysMapSell'] ) ) ? esc_attr($instance['showDateOneDaysMapSell']) : '0';

$startDateTimeRange = ( ! empty( $instance['startDateTimeRange'] ) ) ? ($instance['startDateTimeRange']) : '00:00';
$endDateTimeRange = ( ! empty( $instance['endDateTimeRange'] ) ) ? ($instance['endDateTimeRange']) : '24:00';

$startDate =  new DateTime('UTC');

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

$onlystay = ( !empty($tablistResources) && ! empty( $instance['onlystay'] ) ) ? ($instance['onlystay']) : '0';

if($showSearchText) {
	$showLocation = '0';
	$showAccomodations = '0';
	if (empty($showSearchText)) {
	    $masterTypeId = '';
	}
}

$showResource = ( !empty($tablistResources) && ! empty( $instance['showResource'] ) ) ? ($instance['showResource']) : '0';
$minResource = 1;
$maxResource = 10;
if (!empty($tablistResources) && !empty($instance['limitResource'])) {
	$minResource = ( !empty($tablistResources) && ! empty( $instance['minResource'] ) ) ? ($instance['minResource']) : 1;
	$maxResource = ( !empty($tablistResources) && ! empty( $instance['maxResource'] ) ) ? ($instance['maxResource']) : 10;
}

$showRooms = ( !empty($tablistResources) && ! empty( $instance['showRooms'] ) ) ? ($instance['showRooms']) : '0';

/*
    $minqt = $minResource;
if ($maxqt<$minResource || $maxqt>$maxResource ) {
    $maxqt = $minResource;
}
*/
$showPerson = ( !empty($tablistResources) && ! empty( $instance['showPerson'] ) ) ? esc_attr($instance['showPerson']) : '0';
$showAdult = ( !empty($tablistResources) && ! empty( $instance['showAdult'] ) ) ? esc_attr($instance['showAdult']) : '0';
$showChildren = ( !empty($tablistResources) && ! empty( $instance['showChildren'] ) ) ? esc_attr($instance['showChildren']) : '0';
$showSenior = ( !empty($tablistResources) && ! empty( $instance['showSenior'] ) ) ? esc_attr($instance['showSenior']) : '0';
$showServices = ( !empty($tablistResources) && ! empty( $instance['showServices'] ) ) ? esc_attr($instance['showServices']) : '0';
$showOnlineBooking = ( !empty($tablistResources) && ! empty( $instance['showOnlineBooking'] ) ) ? esc_attr($instance['showOnlineBooking']) : '0';
$showVariationCodes = ( !empty($tablistResources) && ! empty( $instance['showVariationCodes'] ) ) ? esc_attr($instance['showVariationCodes']) : '0';

$showSearchTextOnSell = ( !empty($tablistRealEstate) && ! empty( $instance['showSearchTextOnSell'] ) ) ? esc_attr($instance['showSearchTextOnSell']) : '0';
$showMapIconOnSell = ( !empty($tablistRealEstate) && ! empty( $instance['showMapIconOnSell'] ) ) ? esc_attr($instance['showMapIconOnSell']) : '0';
$showAccomodationsOnSell = ( !empty($tablistRealEstate) && ! empty( $instance['showAccomodationsOnSell'] ) ) ? esc_attr($instance['showAccomodationsOnSell']) : '0';
$showMaxPrice = ( !empty($tablistRealEstate) && ! empty( $instance['showMaxPrice'] ) ) ? esc_attr($instance['showMaxPrice']) : '0';
$showMinFloor = ( !empty($tablistRealEstate) && ! empty( $instance['showMinFloor'] ) ) ? esc_attr($instance['showMinFloor']) : '0';
$showContract = ( !empty($tablistRealEstate) && ! empty( $instance['showContract'] ) ) ? esc_attr($instance['showContract']) : '0';
$showBedRooms = ( !empty($tablistRealEstate) && ! empty( $instance['showBedRooms'] ) ) ? esc_attr($instance['showBedRooms']) : '0';
//$showRooms = ( !empty($tablistRealEstate) && ! empty( $instance['showRooms'] ) ) ? esc_attr($instance['showRooms']) : '0';
$showBaths = ( !empty($tablistRealEstate) && ! empty( $instance['showBaths'] ) ) ? esc_attr($instance['showBaths']) : '0';
$showOnlyNew = ( !empty($tablistRealEstate) && ! empty( $instance['showOnlyNew'] ) ) ? esc_attr($instance['showOnlyNew']) : '0';
$showServicesList = ( !empty($tablistRealEstate) && ! empty( $instance['showServicesList'] ) ) ? esc_attr($instance['showServicesList']) : '0';

$merchantCategoriesSelectedBooking = ( ! empty( $instance['merchantcategoriesbooking'] ) ) ? $instance['merchantcategoriesbooking'] : array();
$merchantCategoriesSelectedServices = ( ! empty( $instance['merchantcategoriesservices'] ) ) ? $instance['merchantcategoriesservices'] : array();
$merchantCategoriesSelectedActivities = ( ! empty( $instance['merchantcategoriesactivities'] ) ) ? $instance['merchantcategoriesactivities'] : array();
$merchantCategoriesSelectedOthers = ( ! empty( $instance['merchantcategoriesothers'] ) ) ? $instance['merchantcategoriesothers'] : array();
$merchantCategoriesSelectedRealEstate = ( ! empty( $instance['merchantcategoriesrealestate'] ) ) ? $instance['merchantcategoriesrealestate'] : array();

$unitCategoriesSelectedBooking = ( ! empty( $instance['unitcategoriesbooking'] ) ) ? $instance['unitcategoriesbooking'] : array();
$unitCategoriesSelectedServices = ( ! empty( $instance['unitcategoriesservices'] ) ) ? $instance['unitcategoriesservices'] : array();
$unitCategoriesSelectedActivities = ( ! empty( $instance['unitcategoriesactivities'] ) ) ? $instance['unitcategoriesactivities'] : array();
$unitCategoriesSelectedOthers = ( ! empty( $instance['unitcategoriesothers'] ) ) ? $instance['unitcategoriesothers'] : array();
$unitCategoriesSelectedRealEstate = ( ! empty( $instance['unitcategoriesrealestate'] ) ) ? $instance['unitcategoriesrealestate'] : array();

$tabnamebooking = ( ! empty( $instance['tabnamebooking'] ) ) ? esc_attr($instance['tabnamebooking']) : 'Booking';
$tabnameservices = ( ! empty( $instance['tabnameservices'] ) ) ? esc_attr($instance['tabnameservices']) : 'Services';
$tabnameactivities = ( ! empty( $instance['tabnameactivities'] ) ) ? esc_attr($instance['tabnameactivities']) : 'Activities';
$tabnameothers = ( ! empty( $instance['tabnameothers'] ) ) ? esc_attr($instance['tabnameothers']) : 'Others';

$tabnameevents = ( ! empty( $instance['tabnameevents'] ) ) ? esc_attr($instance['tabnameevents']) : 'Events';
$tabnamemapsell = ( ! empty( $instance['tabnamemapsell'] ) ) ? esc_attr($instance['tabnamemapsell']) : 'Search Maps';


$tabintrobooking = ( ! empty( $instance['tabintrobooking'] ) ) ? esc_attr($instance['tabintrobooking']) : '';
$tabintroservices = ( ! empty( $instance['tabintroservices'] ) ) ? esc_attr($instance['tabintroservices']) : '';
$tabintroactivities = ( ! empty( $instance['tabintroactivities'] ) ) ? esc_attr($instance['tabintroactivities']) : '';
$tabintroothers = ( ! empty( $instance['tabintroothers'] ) ) ? esc_attr($instance['tabintroothers']) : '';

$instanceContext = ( ! empty( $instance['currcontext'] ) ) ? $instance['currcontext'] : uniqid('currcontext'); ;
// translation
// WPML >= 3.2
if ( defined( 'ICL_SITEPRESS_VERSION' ) && version_compare( ICL_SITEPRESS_VERSION, '3.2', '>=' ) ) {
	$tabnamebooking = apply_filters( 'wpml_translate_single_string', $instance['tabnamebooking'], $instanceContext, 'Search 1' );
	$tabnameservices = apply_filters( 'wpml_translate_single_string',  $instance['tabnameservices'], $instanceContext, 'Search 2' );
	$tabnameactivities = apply_filters( 'wpml_translate_single_string', $instance['tabnameactivities'], $instanceContext, 'Search 3' );
	$tabnameothers = apply_filters( 'wpml_translate_single_string', $instance['tabnameothers'], $instanceContext, 'Search 4' );

	$tabnameevents = apply_filters( 'wpml_translate_single_string', $instance['tabnameevents'], $instanceContext, 'Search 5' );
	$tabnamemapsell = apply_filters( 'wpml_translate_single_string', $instance['tabnamemapsell'], $instanceContext, 'Search 6' );

	$tabintrobooking = apply_filters( 'wpml_translate_single_string', $instance['tabintrobooking'], $instanceContext, 'Search 1' );
	$tabintroservices = apply_filters( 'wpml_translate_single_string',  $instance['tabintroservices'], $instanceContext, 'Search 2' );
	$tabintroactivities = apply_filters( 'wpml_translate_single_string', $instance['tabintroactivities'], $instanceContext, 'Search 3' );
	$tabintroothers = apply_filters( 'wpml_translate_single_string', $instance['tabintroothers'], $instanceContext, 'Search 4' );

// WPML and Polylang compatibility
} elseif ( function_exists( 'icl_t' ) ) {
	$tabnamebooking = icl_t( $instanceContext, 'Search 1', $tabnamebooking );
	$tabnameservices = icl_t( $instanceContext, 'Search 2', $tabnameservices );
	$tabnameactivities = icl_t( $instanceContext, 'Search 3', $tabnameactivities );
	$tabnameothers = icl_t( $instanceContext, 'Search 4', $tabnameothers );

	$tabnameevents = icl_t( $instanceContext, 'Search 5', $tabnameevents );
	$tabnamemapsell = icl_t( $instanceContext, 'Search 6', $tabnamemapsell );

	$tabintrobooking = icl_t( $instanceContext, 'Search 1', $tabintrobooking );
	$tabintroservices = icl_t( $instanceContext, 'Search 2', $tabintroservices );
	$tabintroactivities = icl_t( $instanceContext, 'Search 3', $tabintroactivities );
	$tabintroothers = icl_t( $instanceContext, 'Search 4', $tabintroothers );
}else{
	$tabnamebooking = __( $tabnamebooking, 'bfi');
	$tabnameservices = __( $tabnameservices, 'bfi');
	$tabnameactivities = __( $tabnameactivities, 'bfi');
	$tabnameothers = __( $tabnameothers, 'bfi');

	$tabintrobooking = __( $tabintrobooking, 'bfi');
	$tabintroservices = __( $tabintroservices, 'bfi');
	$tabintroactivities = __( $tabintroactivities, 'bfi');
	$tabintroothers = __( $tabintroothers, 'bfi');

}

$tabnamebooking = ( ! empty( $tabnamebooking ) ) ? $tabnamebooking : __('Booking', 'bfi');
$tabnameservices = ( ! empty( $tabnameservices ) ) ? $tabnameservices : __('Services', 'bfi');
$tabnameactivities = ( ! empty( $tabnameactivities ) ) ? $tabnameactivities : __('Activities', 'bfi');
$tabnameothers = ( ! empty( $tabnameothers ) ) ? $tabnameothers : __('Others', 'bfi');

$tabiconbooking = ( ! empty( $instance['tabiconbooking'] ) ) ? esc_attr($instance['tabiconbooking']) : 'fa fa-suitcase';
$tabiconservices = ( ! empty( $instance['tabiconservices'] ) ) ? esc_attr($instance['tabiconservices']) : 'fa fa-calendar';
$tabiconactivities = ( ! empty( $instance['tabiconactivities'] ) ) ? esc_attr($instance['tabiconactivities']) : 'fa fa-calendar';
$tabiconothers = ( ! empty( $instance['tabiconothers'] ) ) ? esc_attr($instance['tabiconothers']) : 'fa fa-calendar';

$tabiconevents = ( ! empty( $instance['tabiconevents'] ) ) ? esc_attr($instance['tabiconevents']) : 'fa fa-calendar';
$tabiconmapsell = ( ! empty( $instance['tabiconmapsell'] ) ) ? esc_attr($instance['tabiconmapsell']) : 'fa fa-calendar';

$merchantCategoriesResource = array();
$merchantCategoriesRealEstate = array();
$unitCategoriesResource = array();
$unitCategoriesRealEstate = array();

$listmerchantCategoriesResource = "";
$listmerchantCategoriesRealEstate = "";

$availabilityTypeList = array();
$availabilityTypeList['1'] = __('Nights', 'bfi');
$availabilityTypeList['0'] = __('Days', 'bfi');

$availabilityTypesSelectedBooking = ( ! empty( $instance['availabilitytypesbooking'] ) ) ? $instance['availabilitytypesbooking'] : array();
$availabilityTypesSelectedServices = ( ! empty( $instance['availabilitytypesservices'] ) ) ? $instance['availabilitytypesservices'] : array();
$availabilityTypesSelectedActivities = ( ! empty( $instance['availabilitytypesactivities'] ) ) ? $instance['availabilitytypesactivities'] : array();
$availabilityTypesSelectedOthers = ( ! empty( $instance['availabilitytypesothers'] ) ) ? $instance['availabilitytypesothers'] : array();

$itemTypesSelectedBooking = ( ! empty( $instance['itemtypesbooking'] ) ) ? $instance['itemtypesbooking'] : array();
$itemTypesSelectedServices = ( ! empty( $instance['itemtypesservices'] ) ) ? $instance['itemtypesservices'] : array();
$itemTypesSelectedActivities = ( ! empty( $instance['itemtypesactivities'] ) ) ? $instance['itemtypesactivities'] : array();
$itemTypesSelectedOthers = ( ! empty( $instance['itemtypesothers'] ) ) ? $instance['itemtypesothers'] : array();

$groupBySelectedBooking = ( ! empty( $instance['groupbybooking'] ) ) ? $instance['groupbybooking'] : [0];
$groupBySelectedServices = ( ! empty( $instance['groupbyservices'] ) ) ? $instance['groupbyservices'] : [0];
$groupBySelectedActivities = ( ! empty( $instance['groupbyactivities'] ) ) ? $instance['groupbyactivities'] : [0];
$groupBySelectedOthers = ( ! empty( $instance['groupbyothers'] ) ) ? $instance['groupbyothers'] : [0];

$resultViewSelectedBooking = ( ! empty( $instance['resultviewsbooking'] ) ) ? $instance['resultviewsbooking'] :  array('resource');
$resultViewSelectedServices = ( ! empty( $instance['resultviewsservices'] ) ) ? $instance['resultviewsservices'] :  array('resource');
$resultViewSelectedActivities = ( ! empty( $instance['resultviewsactivities'] ) ) ? $instance['resultviewsactivities'] :  array('resource');
$resultViewSelectedOthers = ( ! empty( $instance['resultviewsothers'] ) ) ? $instance['resultviewsothers'] :  array('resource');

$tmpMerchantCategoryIdResource = (strpos($merchantCategoryIdResource, ',') !== FALSE )?"0":$merchantCategoryIdResource;
$tmpmasterTypeId = (strpos($masterTypeId, ',') !== FALSE )?"0":$masterTypeId;

$groupBySelected = ( ! empty( $instance['groupbybooking'] ) ) ? $instance['groupbybooking'] : [0];

$blockmonths = '14';
$blockdays = '7';

if(!empty($instance['blockmonths']) && count($instance['blockmonths'])>0){
	$blockmonths = implode(',', $instance['blockmonths']) ;
}

if(!empty($instance['blockdays']) && count($instance['blockdays'])>0){
	$blockdays = implode(',', $instance['blockdays']) ;
}



if (!empty($services) ) {
	$filtersServices = explode(",", $services);
}

if (isset($filters)) {
	if (!empty($filters['services'])) {
		$filtersServices = explode(",", $filters['services']);
	}

}

$zonesString="";
		
$tabActive = "";
$totalTabs = count($tablistSelected);
if(empty( $totalTabs )){
	$totalTabs=1;
}
$widthTabs = 100/$totalTabs;
if ((empty(COM_BOOKINGFORCONNECTOR_ISMOBILE ) &&!$showdirection )|| BFI()->isSearchPage()) {
	$fixedonbottom = 0;    
}
if (!empty( $before_widget) ){
	echo $before_widget;
}

?>
<?php if(!empty($tablistResources)){ 
$totalfields=0;
?>
<div class="bookingforwidget bfisearchresources"
	data-direction="<?php echo $showdirection?"1":"0"; ?>"
	data-languages="<?php echo substr($language,0,2) ?>"
	data-showperson="<?php echo $showPerson ?>"
	data-groupresulttype="<?php echo implode("",$groupBySelectedBooking) ?>"
	data-merchantcategories="<?php echo (!empty($merchantCategoriesSelectedBooking)?implode(",",$merchantCategoriesSelectedBooking):"") ?>"
	data-resourcescategories="<?php echo (!empty($unitCategoriesSelectedBooking)?implode(",",$unitCategoriesSelectedBooking):"") ?>"
	data-producttagids="<?php echo $productTagIds ?>"
	data-showaccomodations="<?php echo $showAccomodations ?>"
	data-showvariationcodes="<?php echo $showVariationCodes ?>"
></div>	

<?php }  ?>


<?php if(!empty($tablistEvents)){ 
$totalfields=0;		

$stateIds = "";
$regionIds = "";
$cityIds = "";
$zoneIds = '';
$eventId = 0;
$categoryIds = '';
$tagids = '';
$eventId = 0;
$pointOfInterestId = 0;
$merchantIds = "";
$searchterm = '';
$searchTermValue = '';

$checkoutspan = '+1 day';
$checkin = new DateTime('UTC');
$checkout = new DateTime('UTC');

$checkinId = uniqid('checkin');
$checkoutId = uniqid('checkout');

$bookableonly = 0;
$showdirection =0;
//$showdirection = ( ! empty( $instance['showdirection'] ) ) ? esc_attr($instance['showdirection']) : '0';

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

if ($checkin == $checkout){
	$checkout->modify($checkoutspan);
}

////only for Joomla
//$checkin = new JDate($checkin->format('Y-m-d')); 
//$checkout = new JDate($checkout->format('Y-m-d')); 

$blockmonths = '14';
$blockdays = '7';
?>
<?php }  ?>
<?php if(!empty($tablistMapSells)){ 
$totalfields=0;			

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


<?php }  ?>
<?php if(!empty($tablistRealEstate)){ 
$totalfields=0;			
?>
<div class="bookingforwidget bfisearchonsell"
	data-direction="<?php echo $showdirection?"1":"0"; ?>"
	data-languages="<?php echo substr($language,0,2) ?>"
	data-showaccomodations="<?php echo $showAccomodationsOnSell ?>"
	data-showmaxprice = "<?php echo $showMaxPrice?"1":"0"; ?>"
	data-showminfloor = "<?php echo $showMinFloor?"1":"0"; ?>"
	data-showbedrooms = "<?php echo $showBedRooms?"1":"0"; ?>"
	data-showrooms= "<?php echo $showRooms?"1":"0"; ?>"
></div>	
<?php
} // if tablistRealEstate

if (!empty($after_widget)) {
	echo $after_widget;
    }
} // if isbot
?>