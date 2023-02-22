<?php
/**
 * Product Search Widget.
 *
 * @author   BookingFor
 * @category Widgets
 * @package  BookingFor/Widgets
 * @version     2.0.0
 * @extends  WP_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'BFI_Widget_Booking_Search' ) ) {

class BFI_Widget_Booking_Search extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'bfi-widget_booking_search';
		$this->widget_description = __( 'A Search box for multimerchant, monomerchant and sell on.', 'bfi' ) . " " . __( 'These features have been deprecated. This means they are no longer supported and will be removed in the next version', 'bfi' );
		$this->widget_id          = 'bookingfor_booking_search';
		$this->widget_name        = __( 'BookingFor Search Tab', 'bfi' ) . " - " . __( 'DEPRECATED', 'bfi' ) ;
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'bfi' )
			)
		);

		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

//		parent::__construct();
	}


// widget form creation
function form($instance) {
	$language = $GLOBALS['bfi_lang'];
	$languageForm ='';
	if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
			global $sitepress;
			if($sitepress->get_current_language() != $sitepress->get_default_language()){
				$languageForm = "/" .ICL_LANGUAGE_CODE;
			}
	}
	// Check values
	// if( $instance) {
	// 	 $title = esc_attr($instance['title']);
	// } else {
	// 	 $title = '';
	// }
	
	$resultpageidDefault = bfi_get_page_id( 'searchavailability',1 );

	$tablist = array();
	$tablist['0'] = __('Search 1 (default:Booking)', 'bfi');
	$tablist['1'] = __('Search 2 (default:Services)', 'bfi');
	$tablist['2'] = __('Search 3 (default:Activities)', 'bfi');
	$tablist['4'] = __('Search 4 (default:Others)', 'bfi');
	$tablist['3'] = __('Real Estate', 'bfi');
	$tablist['5'] = __('Events', 'bfi');
	$tablist['6'] = __('Search MapSells', 'bfi');
	$tablistRealEstate = array('3');
//	$tablistEvents = array('5');
	$tablistoption = array();
	$tablistoption['0'] = 'bfickbbooking';
	$tablistoption['1'] = 'bfickbbooking';
	$tablistoption['2'] = 'bfickbbooking';
	$tablistoption['4'] = 'bfickbbooking';
	$tablistoption['3'] = 'bfickbrealestate';
	$tablistoption['5'] = 'bfickbevent';
	$tablistoption['6'] = 'bfickbmapsell';

	$availabilityTypeList = array();
	$availabilityTypeList['1'] = __('Nights', 'bfi');
	$availabilityTypeList['0'] = __('Days', 'bfi');
	$availabilityTypeList['2'] = __('Unit of times', 'bfi');
	$availabilityTypeList['3'] = __('Time slot', 'bfi');

	$groupByList = array();
	$groupByList['0'] = __('Resource', 'bfi');
	$groupByList['1'] = __('Merchant', 'bfi');
	$groupByList['2'] = __('Resource group', 'bfi');

	$itemTypeList = array();
	$itemTypeList['0'] = __('Resource', 'bfi');
	$itemTypeList['1'] = __('Service', 'bfi');
	$itemTypeList['6'] = __('Experience', 'bfi');

	$resultViews = array();
	$resultViews['resource'] = __('Resource', 'bfi');
	$resultViews['experience'] = __('Experience', 'bfi');
	$resultViews['rental'] = __('Rental', 'bfi');
	$resultViews['slot'] = __('Time slots', 'bfi');
	$resultViews['mapsells'] = __('Maps Sell', 'bfi');

	$months = array();
	for($i = 1; $i <= 12; $i++){
		$dateObj = DateTime::createFromFormat('!m', $i,new DateTimeZone('UTC'));
		$months[$i]=date_i18n('F',$dateObj->getTimestamp());
	}
	$days = array();

	for($i = 5; $i <= 11; $i++){
		$dateObj = DateTime::createFromFormat('!d', $i,new DateTimeZone('UTC'));
		$days[$i-4]=date_i18n('l',$dateObj->getTimestamp());
	}

//	$allMerchantCategories = BFCHelper::getMerchantCategories();
	$allMerchantCategories = BFCHelper::getMerchantCategories($language);
	$merchantCategories = array();
	if (!empty($allMerchantCategories))
	{
		foreach($allMerchantCategories as $merchantCategory)
		{
			$merchantCategories[$merchantCategory->MerchantCategoryId] = $merchantCategory->Name;
		}
	}
	
	$allUnitCategories =  BFCHelper::GetProductCategoryForSearch($language,1);
	$unitCategories = array();
	if (!empty($allUnitCategories))
	{
		foreach($allUnitCategories as $unitCategory)
		{
			$unitCategories[$unitCategory->ProductCategoryId] = $unitCategory->Name;
		}
	}

	$allUnitCategoriesRealEstate =  BFCHelper::GetProductCategoryForSearch($language,2);
	$unitCategoriesRealEstate = array();
	if (!empty($allUnitCategoriesRealEstate))
	{
		foreach($allUnitCategoriesRealEstate as $unitCategory)
		{
			$unitCategoriesRealEstate[$unitCategory->ProductCategoryId] = $unitCategory->Name;
		}
	}

	$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
	$type = ( ! empty( $instance['type'] ) ) ? esc_attr($instance['type']) : 'multi';
	$tablistSelected = ( ! empty( $instance['tablistSelected'] ) ) ? $instance['tablistSelected'] : array();
	$blockmonths = ( ! empty( $instance['blockmonths'] ) ) ? $instance['blockmonths'] : array();
	$blockdays = ( ! empty( $instance['blockdays'] ) ) ? $instance['blockdays'] : array();

	$showdirection = ( ! empty( $instance['showdirection'] ) ) ? esc_attr($instance['showdirection']) : '0';
	$resultinsamepg = ( ! empty( $instance['resultinsamepg'] ) ) ? esc_attr($instance['resultinsamepg']) : '0';
	$resultpageid = ( ! empty( $instance['resultpageid'] ) ) ? esc_attr($instance['resultpageid']) : $resultpageidDefault;

	$fixedontop = ( ! empty( $instance['fixedontop'] ) ) ? esc_attr($instance['fixedontop']) : '0';
	$fixedontopcorrection = ( ! empty( $instance['fixedontopcorrection'] ) ) ? esc_attr($instance['fixedontopcorrection']) : 0;
	$fixedonbottom = ( ! empty( $instance['fixedonbottom'] ) ) ? esc_attr($instance['fixedonbottom']) : '0';
	
	$moretab = ( ! empty( $instance['moretab'] ) ) ? esc_attr($instance['moretab']) : '0';
	$showadvance = ( ! empty( $instance['showadvance'] ) ) ? esc_attr($instance['showadvance']) : '0';

	$tabnamebooking = ( ! empty( $instance['tabnamebooking'] ) ) ? esc_attr($instance['tabnamebooking']) : ''; //Booking';
	$tabnameservices = ( ! empty( $instance['tabnameservices'] ) ) ? esc_attr($instance['tabnameservices']) : ''; //Services';
	$tabnameactivities = ( ! empty( $instance['tabnameactivities'] ) ) ? esc_attr($instance['tabnameactivities']) : ''; //Activities';
	$tabnameothers = ( ! empty( $instance['tabnameothers'] ) ) ? esc_attr($instance['tabnameothers']) : ''; //Others';

	$tabnameevents = ( ! empty( $instance['tabnameevents'] ) ) ? esc_attr($instance['tabnameevents']) : 'Events';
	$tabnamemapsell = ( ! empty( $instance['tabnamemapsell'] ) ) ? esc_attr($instance['tabnamemapsell']) : 'Search Maps';

	$tabintrobooking = ( ! empty( $instance['tabintrobooking'] ) ) ? esc_attr($instance['tabintrobooking']) : '';
	$tabintroservices = ( ! empty( $instance['tabintroservices'] ) ) ? esc_attr($instance['tabintroservices']) : '';
	$tabintroactivities = ( ! empty( $instance['tabintroactivities'] ) ) ? esc_attr($instance['tabintroactivities']) : '';
	$tabintroothers = ( ! empty( $instance['tabintroothers'] ) ) ? esc_attr($instance['tabintroothers']) : '';

	$tabiconbooking = ( ! empty( $instance['tabiconbooking'] ) ) ? esc_attr($instance['tabiconbooking']) : 'fa fa-suitcase';
	$tabiconservices = ( ! empty( $instance['tabiconservices'] ) ) ? esc_attr($instance['tabiconservices']) : 'fa fa-calendar';
	$tabiconactivities = ( ! empty( $instance['tabiconactivities'] ) ) ? esc_attr($instance['tabiconactivities']) : 'fa fa-calendar';
	$tabiconothers = ( ! empty( $instance['tabiconothers'] ) ) ? esc_attr($instance['tabiconothers']) : 'fa fa-calendar';

	$tabiconevents = ( ! empty( $instance['tabiconevents'] ) ) ? esc_attr($instance['tabiconevents']) : 'fa fa-calendar';
	$tabiconmapsell = ( ! empty( $instance['tabiconmapsell'] ) ) ? esc_attr($instance['tabiconmapsell']) : 'fa fa-calendar';

	$showLocation = ( ! empty( $instance['showLocation'] ) ) ? esc_attr($instance['showLocation']) : '0';
	$showMapIcon = ( ! empty( $instance['showMapIcon'] ) ) ? esc_attr($instance['showMapIcon']) : '0';
	$showSearchText = ( ! empty( $instance['showSearchText'] ) ) ? esc_attr($instance['showSearchText']) : '0';
	$searchTextFields = ( ! empty( $instance['searchTextFields'] ) ) ? $instance['searchTextFields'] : array(5,6,11,13,14,15,17,18);
	$searchTextFieldsEvent = ( ! empty( $instance['searchTextFieldsEvent'] ) ) ? $instance['searchTextFieldsEvent'] : array(5,6,11,13,14,15,17,18);
	$searchTextFieldsMapsell = ( ! empty( $instance['searchTextFieldsMapsell'] ) ) ? $instance['searchTextFieldsMapsell'] : array(5,6,11,13,14,15,17,18);
	
	$showAccomodations = ( ! empty( $instance['showAccomodations'] ) ) ? esc_attr($instance['showAccomodations']) : '0';
	$showDateOneDays = ( ! empty( $instance['showDateOneDays'] ) ) ? esc_attr($instance['showDateOneDays']) : '0';
	$showDateOneDaysMapSell = ( ! empty( $instance['showDateOneDaysMapSell'] ) ) ? esc_attr($instance['showDateOneDaysMapSell']) : '0';
	
	$showDateRange = ( ! empty( $instance['showDateRange'] ) ) ? esc_attr($instance['showDateRange']) : '1';
	$showDateTimeRange = ( ! empty( $instance['showDateTimeRange'] ) ) ? esc_attr($instance['showDateTimeRange']) : '0';
	
	$startDateTimeRange = ( ! empty( $instance['startDateTimeRange'] ) ) ? esc_attr($instance['startDateTimeRange']) : '00:00';
	$endDateTimeRange = ( ! empty( $instance['endDateTimeRange'] ) ) ? esc_attr($instance['endDateTimeRange']) : '24:00';	

	$showResource = ( ! empty( $instance['showResource'] ) ) ? esc_attr($instance['showResource']) : '0';
	$limitResource = ( ! empty( $instance['limitResource'] ) ) ? esc_attr($instance['limitResource']) : '0';
	$minResource = ( ! empty( $instance['minResource'] ) ) ? $instance['minResource'] : 1;
	$maxResource = ( ! empty( $instance['maxResource'] ) ) ? $instance['maxResource'] : 10;
	
	$showRooms = ( ! empty( $instance['showRooms'] ) ) ? esc_attr($instance['showRooms']) : '0';
	$limitRooms = ( ! empty( $instance['limitRooms'] ) ) ? esc_attr($instance['limitRooms']) : '0';
	$minRooms = ( ! empty( $instance['minRooms'] ) ) ? $instance['minRooms'] : 1;
	$maxRooms = ( ! empty( $instance['maxRooms'] ) ) ? $instance['minRooms'] : 10;

	$showPerson = ( ! empty( $instance['showPerson'] ) ) ? esc_attr($instance['showPerson']) : '0';
	$showAdult = ( ! empty( $instance['showAdult'] ) ) ? esc_attr($instance['showAdult']) : '1';
	$showChildren = ( ! empty( $instance['showChildren'] ) ) ? esc_attr($instance['showChildren']) : '1';
	$showSenior = ( ! empty( $instance['showSenior'] ) ) ? esc_attr($instance['showSenior']) : '0';
	$showServices = ( ! empty( $instance['showServices'] ) ) ? esc_attr($instance['showServices']) : '0';
	$showOnlineBooking = ( ! empty( $instance['showOnlineBooking'] ) ) ? esc_attr($instance['showOnlineBooking']) : '0';
	$showVariationCodes = ( ! empty( $instance['showVariationCodes'] ) ) ? esc_attr($instance['showVariationCodes']) : '0';
	$showMaxPrice = ( ! empty( $instance['showMaxPrice'] ) ) ? esc_attr($instance['showMaxPrice']) : '0';
	$showMinFloor = ( ! empty( $instance['showMinFloor'] ) ) ? esc_attr($instance['showMinFloor']) : '0';
	$showContract = ( ! empty( $instance['showContract'] ) ) ? esc_attr($instance['showContract']) : '0';

	$showSearchTextOnSell = ( ! empty( $instance['showSearchTextOnSell'] ) ) ? esc_attr($instance['showSearchTextOnSell']) : '1';
	$showMapIconOnSell = ( ! empty( $instance['showMapIconOnSell'] ) ) ? esc_attr($instance['showMapIconOnSell']) : '0';
	$showAccomodationsOnSell = ( ! empty( $instance['showAccomodationsOnSell'] ) ) ? esc_attr($instance['showAccomodationsOnSell']) : '0';


	$showBedRooms = ( ! empty( $instance['showBedRooms'] ) ) ? esc_attr($instance['showBedRooms']) : '0';
	//$showRooms = ( ! empty( $instance['showRooms'] ) ) ? esc_attr($instance['showRooms']) : '0';
	$showBaths = ( ! empty( $instance['showBaths'] ) ) ? esc_attr($instance['showBaths']) : '0';
	$showOnlyNew = ( ! empty( $instance['showOnlyNew'] ) ) ? esc_attr($instance['showOnlyNew']) : '0';
	$showServicesList = ( ! empty( $instance['showServicesList'] ) ) ? esc_attr($instance['showServicesList']) : '0';

	$showNightSelector = ( ! empty( $instance['showNightSelector'] ) ) ? esc_attr($instance['showNightSelector']) : '0';
	$showDaySelector = ( ! empty( $instance['showDaySelector'] ) ) ? esc_attr($instance['showDaySelector']) : '0';
	$showServicesNightSelector = ( ! empty( $instance['showServicesNightSelector'] ) ) ? esc_attr($instance['showServicesNightSelector']) : '0';
	$showServicesDaySelector = ( ! empty( $instance['showServicesDaySelector'] ) ) ? esc_attr($instance['showServicesDaySelector']) : '0';

	
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

	$availabilityTypesSelectedBooking = ( ! empty( $instance['availabilitytypesbooking'] ) ) ? $instance['availabilitytypesbooking'] : array(1);
	$availabilityTypesSelectedServices = ( ! empty( $instance['availabilitytypesservices'] ) ) ? $instance['availabilitytypesservices'] : array(2,3);
	$availabilityTypesSelectedAvailability = ( ! empty( $instance['availabilitytypesactivities'] ) ) ? $instance['availabilitytypesactivities'] : array(2,3);
	$availabilityTypesSelectedOthers = ( ! empty( $instance['availabilitytypesothers'] ) ) ? $instance['availabilitytypesothers'] : array(2,3);

	$itemTypesSelectedBooking = ( ! empty( $instance['itemtypesbooking'] ) ) ? $instance['itemtypesbooking'] : array(0);
	$itemTypesSelectedServices = ( ! empty( $instance['itemtypesservices'] ) ) ? $instance['itemtypesservices'] : array(1);
	$itemTypesSelectedActivities = ( ! empty( $instance['itemtypesactivities'] ) ) ? $instance['itemtypesactivities'] : array(1);
	$itemTypesSelectedOthers = ( ! empty( $instance['itemtypesothers'] ) ) ? $instance['itemtypesothers'] : array(1);
	
	$groupBySelectedBooking = ( ! empty( $instance['groupbybooking'] ) ) ? $instance['groupbybooking'] : array(2);
	$groupBySelectedServices = ( ! empty( $instance['groupbyservices'] ) ) ? $instance['groupbyservices'] : array(2);
	$groupBySelectedActivities = ( ! empty( $instance['groupbyactivities'] ) ) ? $instance['groupbyactivities'] : array(2);
	$groupBySelectedOthers = ( ! empty( $instance['groupbyothers'] ) ) ? $instance['groupbyothers'] : array(2);

	$resultViewSelectedBooking = ( ! empty( $instance['resultviewsbooking'] ) ) ? $instance['resultviewsbooking'] :  array();
	$resultViewSelectedServices = ( ! empty( $instance['resultviewsservices'] ) ) ? $instance['resultviewsservices'] :  array();
	$resultViewSelectedActivities = ( ! empty( $instance['resultviewsactivities'] ) ) ? $instance['resultviewsactivities'] :  array();
	$resultViewSelectedOthers = ( ! empty( $instance['resultviewsothers'] ) ) ? $instance['resultviewsothers'] :  array();

	$newcodeid = uniqid("newcode");
	?>
	<p class="bfi-deprecated">
		<?php _e('These features have been deprecated. This means they are no longer supported and will be removed in the next version', 'bfi') ?>
	</p>
		<p>
			aggiungere widget HTML con il seguente codice:
			<textarea id="<?php echo $newcodeid ?>" style="width:100%; min-height: 150px;" oninput='this.style.height = "";this.style.height = this.scrollHeight + "px"'>
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
			</textarea>
			<script type="text/javascript">
window.setTimeout( function() {
    jQuery("#<?php echo $newcodeid ?>").height( jQuery("#<?php echo $newcodeid ?>")[0].scrollHeight );
}, 1);	
jQuery("#<?php echo $newcodeid ?>").on( 'visibility', function() {
	window.setTimeout( function() {
    jQuery("#<?php echo $newcodeid ?>").height( jQuery("#<?php echo $newcodeid ?>")[0].scrollHeight );
	}, 100);
});
</script>

		</p>
	<p>
	<label class="bfi-select2" for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wp_widget_plugin'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo ($instance)?esc_attr($instance['title']):''; ?>" />
	</p>
	<p class="">
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showdirection'); ?>" value="1" <?php  echo ($showdirection=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Displays horizontally', 'bfi'); ?></label>
	</p>
	<p class="bookingoptions">
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('fixedontop'); ?>" value="1" <?php  echo ($fixedontop=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Fixed on top', 'bfi'); ?></label>
	</p>
	<p class="bookingoptions">
		<label for="<?php echo $this->get_field_id('fixedontopcorrection'); ?>"><?php _e('Top correction', 'bfi'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('fixedontopcorrection'); ?>" name="<?php echo $this->get_field_name('fixedontopcorrection'); ?>" type="number" value="<?php echo $fixedontopcorrection; ?>" request />
	</p>
	<p class="bookingoptions">
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('fixedonbottom'); ?>" value="1" <?php  echo ($fixedonbottom=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Fixed on bottom for small device', 'bfi'); ?></label>
	</p>
	<p>
		<span class="bfitabselect"><?php _e('Tab', 'bfi'); ?></span><br />
		<label class="checkbox bfitabmoretab"><input type="checkbox" name="<?php echo $this->get_field_name('moretab'); ?>" value="1" <?php  echo ($moretab=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Use more tab', 'bfi'); ?></label><br />

		<?php  foreach ($tablist as $key => $value) { ?>
			<label class="checkbox bfitabsearch <?php  echo $tablistoption[$key]; ?>"><input type="checkbox" class="bfitabsearch_cb"  name="<?php echo $this->get_field_name('tablistSelected'); ?>[]" value="<?php echo $key ?>" <?php  echo (in_array($key, $tablistSelected)) ? 'checked="checked"' : ''; ?> /><?php echo $value ?></label><br />
		<?php } ?>
	</p>
	<p>
		<label class="checkbox"><input type="checkbox" class="bfiadvance-cb" name="<?php echo $this->get_field_name('showadvance'); ?>" value="1" <?php  echo ($showadvance=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Show advance settings', 'bfi'); ?></label>
	</p>

<div class="bfiadvance">
	<?php if(!empty($merchantCategories) || !empty($unitCategories) || !empty($unitCategoriesRealEstate)){  ?>
	<p class="bfitabsearch0 widget-inside" >
		<span class="bfi-titletab"><?php echo $tablist[0] ?></span><br />
		<input class="widefat" id="<?php echo $this->get_field_id('tabnamebooking'); ?>" name="<?php echo $this->get_field_name('tabnamebooking'); ?>" type="text" value="<?php echo $tabnamebooking; ?>" />
		<label for="<?php echo $this->get_field_id('tabintrobooking'); ?>"><?php _e('Intro text', 'bfi'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('tabintrobooking'); ?>" name="<?php echo $this->get_field_name('tabintrobooking'); ?>" type="text" value="<?php echo $tabintrobooking; ?>" />
		<label for="<?php echo $this->get_field_id('tabiconbooking'); ?>"><?php _e('Icon (for expert users)', 'bfi'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('tabiconbooking'); ?>" name="<?php echo $this->get_field_name('tabiconbooking'); ?>" type="text" value="<?php echo $tabiconbooking; ?>" />

		<?php if(!empty($merchantCategories)){  ?>
			<label for="<?php echo $this->get_field_id('merchantcategoriesbooking'); ?>"><?php _e('Merchant category', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('merchantcategoriesbooking'),
					$this->get_field_id('merchantcategoriesbooking')
				);
				foreach ($merchantCategories as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $merchantCategoriesSelectedBooking) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
			</label>
		<?php }  ?>
		<?php if(!empty($unitCategories)){  ?>
			<label for="<?php echo $this->get_field_id('unitcategoriesbooking'); ?>"><?php _e('Product category', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('unitcategoriesbooking'),
					$this->get_field_id('unitcategoriesbooking')
				);
				foreach ($unitCategories as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $unitCategoriesSelectedBooking) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
			</label>
		<?php }  ?>
		<label for="<?php echo $this->get_field_id('availabilitytypesbooking'); ?>"><?php _e('Search availability for', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('availabilitytypesbooking'),
					$this->get_field_id('availabilitytypesbooking')
				);
				foreach ($availabilityTypeList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $availabilityTypesSelectedBooking) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
		<label for="<?php echo $this->get_field_id('itemtypesbooking'); ?>"><?php _e('Items type', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('itemtypesbooking'),
					$this->get_field_id('itemtypesbooking')
				);
				foreach ($itemTypeList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $itemTypesSelectedBooking) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
		<label for="<?php echo $this->get_field_id('groupbybooking'); ?>"><?php _e('Default group by ', 'bfi'); ?>
			<?php 
				printf(
					'<select name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('groupbybooking'),
					$this->get_field_id('groupbybooking')
				);
				foreach ($groupByList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $groupBySelectedBooking) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
		<label for="<?php echo $this->get_field_id('resultviewsbooking'); ?>"><?php _e('result Views ', 'bfi'); ?>
			<?php 
				printf(
					'<select name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('resultviewsbooking'),
					$this->get_field_id('resultviewsbooking')
				);
				foreach ($resultViews as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $resultViewSelectedBooking) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
		
	</p>
	<p class="bfitabsearch1 widget-inside" >
		<span class="bfi-titletab"><?php echo $tablist[1] ?></span><br />
		<input class="widefat" id="<?php echo $this->get_field_id('tabnameservices'); ?>" name="<?php echo $this->get_field_name('tabnameservices'); ?>" type="text" value="<?php echo $tabnameservices; ?>" />
		<label for="<?php echo $this->get_field_id('tabintroservices'); ?>"><?php _e('Intro text', 'bfi'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('tabintroservices'); ?>" name="<?php echo $this->get_field_name('tabintroservices'); ?>" type="text" value="<?php echo $tabintroservices; ?>" />
		<label for="<?php echo $this->get_field_id('tabiconservices'); ?>"><?php _e('Icon (for expert users)', 'bfi'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('tabiconservices'); ?>" name="<?php echo $this->get_field_name('tabiconservices'); ?>" type="text" value="<?php echo $tabiconservices; ?>" />
		<?php if(!empty($merchantCategories)){  ?>
			<label for="<?php echo $this->get_field_id('merchantcategoriesservices'); ?>"><?php _e('Merchant category', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('merchantcategoriesservices'),
					$this->get_field_id('merchantcategoriesservices')
				);
				foreach ($merchantCategories as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $merchantCategoriesSelectedServices) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
			</label>
		<?php }  ?>
		<?php if(!empty($unitCategories)){  ?>
			<label for="<?php echo $this->get_field_id('unitcategoriesservices'); ?>"><?php _e('Product category', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('unitcategoriesservices'),
					$this->get_field_id('unitcategoriesservices')
				);
				foreach ($unitCategories as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $unitCategoriesSelectedServices) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
			</label>
		<?php }  ?>
		<label for="<?php echo $this->get_field_id('availabilitytypesservices'); ?>"><?php _e('Search availability for', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('availabilitytypesservices'),
					$this->get_field_id('availabilitytypesservices')
				);
				foreach ($availabilityTypeList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $availabilityTypesSelectedServices) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
		<label for="<?php echo $this->get_field_id('itemtypesservices'); ?>"><?php _e('Items type', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('itemtypesservices'),
					$this->get_field_id('itemtypesservices')
				);
				foreach ($itemTypeList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $itemTypesSelectedServices) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
		<label for="<?php echo $this->get_field_id('groupbyservices'); ?>"><?php _e('Default group by ', 'bfi'); ?>
			<?php 
				printf(
					'<select name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('groupbyservices'),
					$this->get_field_id('groupbyservices')
				);
				foreach ($groupByList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $groupBySelectedServices) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
		<label for="<?php echo $this->get_field_id('resultviewsservices'); ?>"><?php _e('result Views ', 'bfi'); ?>
			<?php 
				printf(
					'<select name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('resultviewsservices'),
					$this->get_field_id('resultviewsservices')
				);
				foreach ($resultViews as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $resultViewSelectedServices) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
	</p>
	<p class="bfitabsearch2 widget-inside" >
		<span class="bfi-titletab"><?php echo $tablist[2] ?></span><br />
		<input class="widefat" id="<?php echo $this->get_field_id('tabnameactivities'); ?>" name="<?php echo $this->get_field_name('tabnameactivities'); ?>" type="text" value="<?php echo $tabnameactivities; ?>" />
		<label for="<?php echo $this->get_field_id('tabintroactivities'); ?>"><?php _e('Intro text', 'bfi'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('tabintroactivities'); ?>" name="<?php echo $this->get_field_name('tabintroactivities'); ?>" type="text" value="<?php echo $tabintroactivities; ?>" />
		<label for="<?php echo $this->get_field_id('tabiconactivities'); ?>"><?php _e('Icon (for expert users)', 'bfi'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('tabiconactivities'); ?>" name="<?php echo $this->get_field_name('tabiconactivities'); ?>" type="text" value="<?php echo $tabiconactivities; ?>" />
		<?php if(!empty($merchantCategories)){  ?>
			<label for="<?php echo $this->get_field_id('merchantcategoriesactivities'); ?>"><?php _e('Merchant category', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('merchantcategoriesactivities'),
					$this->get_field_id('merchantcategoriesactivities')
				);
				foreach ($merchantCategories as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $merchantCategoriesSelectedActivities) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
			</label>
		<?php }  ?>
		<?php if(!empty($unitCategories)){  ?>
			<label for="<?php echo $this->get_field_id('unitcategoriesactivities'); ?>"><?php _e('Product category', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('unitcategoriesactivities'),
					$this->get_field_id('unitcategoriesactivities')
				);
				foreach ($unitCategories as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $unitCategoriesSelectedActivities) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
			</label>
		<?php }  ?>
		<label for="<?php echo $this->get_field_id('availabilitytypesactivities'); ?>"><?php _e('Search availability for', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('availabilitytypesactivities'),
					$this->get_field_id('availabilitytypesactivities')
				);
				foreach ($availabilityTypeList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $availabilityTypesSelectedAvailability) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
		<label for="<?php echo $this->get_field_id('itemtypesactivities'); ?>"><?php _e('Items type', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('itemtypesactivities'),
					$this->get_field_id('itemtypesactivities')
				);
				foreach ($itemTypeList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $itemTypesSelectedActivities) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
		<label for="<?php echo $this->get_field_id('groupbyactivities'); ?>"><?php _e('Default group by ', 'bfi'); ?>
			<?php 
				printf(
					'<select name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('groupbyactivities'),
					$this->get_field_id('groupbyactivities')
				);
				foreach ($groupByList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $groupBySelectedActivities) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
		<label for="<?php echo $this->get_field_id('resultviewsactivities'); ?>"><?php _e('result Views ', 'bfi'); ?>
			<?php 
				printf(
					'<select name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('resultviewsactivities'),
					$this->get_field_id('resultviewsactivities')
				);
				foreach ($resultViews as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $resultViewSelectedActivities) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>

	</p>
	<p class="bfitabsearch4 widget-inside" >
		<span class="bfi-titletab"><?php echo $tablist[4] ?></span><br />
		<input class="widefat" id="<?php echo $this->get_field_id('tabnameothers'); ?>" name="<?php echo $this->get_field_name('tabnameothers'); ?>" type="text" value="<?php echo $tabnameothers; ?>" />
		<label for="<?php echo $this->get_field_id('tabintroothers'); ?>"><?php _e('Intro text', 'bfi'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('tabintroothers'); ?>" name="<?php echo $this->get_field_name('tabintroothers'); ?>" type="text" value="<?php echo $tabintroothers; ?>" />
		<label for="<?php echo $this->get_field_id('tabiconothers'); ?>"><?php _e('Icon (for expert users)', 'bfi'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('tabiconothers'); ?>" name="<?php echo $this->get_field_name('tabiconothers'); ?>" type="text" value="<?php echo $tabiconothers; ?>" />
		<?php if(!empty($merchantCategories)){  ?>
			<label for="<?php echo $this->get_field_id('merchantcategoriesothers'); ?>"><?php _e('Merchant category', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('merchantcategoriesothers'),
					$this->get_field_id('merchantcategoriesothers')
				);
				foreach ($merchantCategories as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $merchantCategoriesSelectedOthers) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
			</label>
		<?php }  ?>
		<?php if(!empty($unitCategories)){  ?>
			<label for="<?php echo $this->get_field_id('unitcategoriesothers'); ?>"><?php _e('Product category', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('unitcategoriesothers'),
					$this->get_field_id('unitcategoriesothers')
				);
				foreach ($unitCategories as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $unitCategoriesSelectedOthers) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
			</label>
		<?php }  ?>
		<label for="<?php echo $this->get_field_id('availabilitytypesothers'); ?>"><?php _e('Search availability for', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('availabilitytypesothers'),
					$this->get_field_id('availabilitytypesothers')
				);
				foreach ($availabilityTypeList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $availabilityTypesSelectedOthers) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
		<label for="<?php echo $this->get_field_id('itemtypesothers'); ?>"><?php _e('Items type', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('itemtypesothers'),
					$this->get_field_id('itemtypesothers')
				);
				foreach ($itemTypeList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $itemTypesSelectedOthers) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
		<label for="<?php echo $this->get_field_id('groupbyothers'); ?>"><?php _e('Default group by ', 'bfi'); ?>
			<?php 
				printf(
					'<select name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('groupbyothers'),
					$this->get_field_id('groupbyothers')
				);
				foreach ($groupByList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $groupBySelectedOthers) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
		<label for="<?php echo $this->get_field_id('resultviewsothers'); ?>"><?php _e('result Views ', 'bfi'); ?>
			<?php 
				printf(
					'<select name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('resultviewsothers'),
					$this->get_field_id('resultviewsothers')
				);
				foreach ($resultViews as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $resultViewSelectedOthers) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
	</p>
	<p class="bookingoptions">
		<span><?php _e('Fields Visibility');?></span><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showSearchText'); ?>" value="1" <?php  echo ($showSearchText=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Search text', 'bfi'); ?> <?php _e('(Merchants, Products, Tags, Merchants and Products Categories, Regions, States, Cities, Zones)', 'bfi') ?></label><br />
		<label for="<?php echo $this->get_field_id('searchTextFields'); ?>"><?php _e('Research fields', 'bfi'); ?>
		<?php 
			
				printf(
				'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
	            $this->get_field_name('searchTextFields'),
	            $this->get_field_id('searchTextFields')
            );
				foreach (BFCHelper::$listResultClasses as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $searchTextFields) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
            ?>
		</label>
		<br />		
		<?php _e('or');?><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showLocation'); ?>" value="1" <?php  echo ($showLocation=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Destination', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showAccomodations'); ?>" value="1" <?php  echo ($showAccomodations=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Type', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showMapIcon'); ?>" value="1" <?php  echo ($showMapIcon=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Map Button', 'bfi'); ?></label><br />
		<br />
		
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showDateOneDays'); ?>" value="1" <?php  echo ($showDateOneDays=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Select 1 day o more', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showDateRange'); ?>" value="1" <?php  echo ($showDateRange=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Date Range', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showDateTimeRange'); ?>" value="1" <?php  echo ($showDateTimeRange=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Date Time Range', 'bfi'); ?></label><br />
		<label for="<?php echo $this->get_field_id('blockmonths'); ?>"><?php _e('Block Months', 'bfi'); ?>
		<?php 
			printf(
				'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
	            $this->get_field_name('blockmonths'),
	            $this->get_field_id('blockmonths')
            );
			for($i = 1; $i <= 12; $i++){
				printf(
					'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
					$i,
					in_array( $i, $blockmonths) ? 'selected="selected"' : '',
					$months[$i]
				);
			}
            echo '</select>';
            ?>
		</label>
		<br />
		<label for="<?php echo $this->get_field_id('blockdays'); ?>"><?php _e('Disable Days', 'bfi'); ?>
		<?php 
			printf(
				'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
	            $this->get_field_name('blockdays'),
	            $this->get_field_id('blockdays')
            );
			for($i = 1; $i <= 7; $i++){
				printf(
					'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
					$i,
					in_array( $i, $blockdays) ? 'selected="selected"' : '',
					$days[$i]
				);
			}
            echo '</select>';
            ?>
		</label>
		<br />
		<span>start hour</span>
		<input class="widefat ui-timepicker-input bfi-starttime" id="<?php echo $this->get_field_id('startDateTimeRange'); ?>" name="<?php echo $this->get_field_name('startDateTimeRange'); ?>" type="text" value="<?php echo $startDateTimeRange; ?>" autocomplete="off" />
		<br />
		<span>end hour</span>
		<input class="widefat ui-timepicker-input bfi-endtime" id="<?php echo $this->get_field_id('endDateTimeRange'); ?>" name="<?php echo $this->get_field_name('endDateTimeRange'); ?>" type="text" value="<?php echo $endDateTimeRange; ?>" autocomplete="off" />
		<br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showResource'); ?>" value="1" <?php  echo ($showResource=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Show resource number', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" class="bfiCkbminmaxresource" name="<?php echo $this->get_field_name('limitResource'); ?>" value="1" <?php  echo ($limitResource=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Limit resource number', 'bfi'); ?></label><br />
		<span class="bfiminmaxresource">	
			<label for="<?php echo $this->get_field_id('minResource'); ?>"><?php _e('min', 'bfi'); ?>
			<?php 
				printf(
					'<select name="%s[]" id="%s" class="bfiselminresource">',
					$this->get_field_name('minResource'),
					$this->get_field_id('minResource')
				);
				for($i = 1; $i <= 10; $i++){
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$i,
						($i == $minResource) ? 'selected="selected"' : '',
						$i
					);
				}
				echo '</select>';
				?>
			</label>
			<label for="<?php echo $this->get_field_id('maxResource'); ?>"><?php _e('max', 'bfi'); ?>
			<?php 
				printf(
					'<select name="%s[]" id="%s" class="bfiselmaxresource">',
					$this->get_field_name('maxResource'),
					$this->get_field_id('maxResource')
				);
				for($i = 1; $i <= 10; $i++){
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$i,
						($i == $maxResource) ? 'selected="selected"' : '',
						$i
					);
				}
				echo '</select>';
				?>
			</label>
			<br />
		</span>
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showRooms'); ?>" value="1" <?php  echo ($showRooms=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Show rooms number', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" class="bfiCkbminmaxrooms" name="<?php echo $this->get_field_name('limitRooms'); ?>" value="1" <?php  echo ($limitRooms=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Limit rooms number', 'bfi'); ?></label><br />
		<span class="bfiminmaxrooms">	
			<label for="<?php echo $this->get_field_id('minRooms'); ?>"><?php _e('min', 'bfi'); ?>
			<?php 
				printf(
					'<select name="%s[]" id="%s" class="bfiselminrooms">',
					$this->get_field_name('minRooms'),
					$this->get_field_id('minRooms')
				);
				for($i = 1; $i <= 10; $i++){
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$i,
						($i == $minRooms) ? 'selected="selected"' : '',
						$i
					);
				}
				echo '</select>';
				?>
			</label>
			<label for="<?php echo $this->get_field_id('maxRooms'); ?>"><?php _e('max', 'bfi'); ?>
			<?php 
				printf(
					'<select name="%s[]" id="%s" class="bfiselmaxrooms">',
					$this->get_field_name('maxRooms'),
					$this->get_field_id('maxRooms')
				);
				for($i = 1; $i <= 10; $i++){
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$i,
						($i == $maxRooms) ? 'selected="selected"' : '',
						$i
					);
				}
				echo '</select>';
				?>
			</label>
			<br />
		</span>
		<label class="checkbox bfi_showpersons"><input type="checkbox" name="<?php echo $this->get_field_name('showPerson'); ?>" value="1" <?php  echo ($showPerson=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Persons', 'bfi'); ?></label><br />
		<span class="bfi_nopersons">
			<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showAdult'); ?>" value="1" <?php  echo ($showAdult=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Adults', 'bfi'); ?></label><br />
			<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showChildren'); ?>" value="1" <?php  echo ($showChildren=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Childrens', 'bfi'); ?></label><br />
			<!-- <label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showSenior'); ?>" value="1" <?php  echo ($showSenior=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Senior', 'bfi'); ?></label><br /> -->
		</span>
		<!-- <label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showServices'); ?>" value="1" <?php  echo ($showServices=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Services', 'bfi'); ?></label><br /> -->
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showOnlineBooking'); ?>" value="1" <?php  echo ($showOnlineBooking=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Only Online Booking', 'bfi'); ?></label><br />
		<br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showVariationCodes'); ?>" value="1" <?php  echo ($showVariationCodes=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Coupon', 'bfi'); ?></label>
		<br /><br />
	</p>
	<p class="bfitabsearch3 widget-inside" >
		<span class="bfi-titletab"><?php echo $tablist[3] ?></span><br />
		<?php if(!empty($merchantCategories)){  ?>
			<label for="<?php echo $this->get_field_id('merchantcategories'); ?>"><?php _e('Merchant category', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('merchantcategoriesrealestate'),
					$this->get_field_id('merchantcategoriesrealestate')
				);
				foreach ($merchantCategories as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $merchantCategoriesSelectedRealEstate) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
			</label>
		<?php }  ?>
		<?php if(!empty($unitCategoriesRealEstate)){  ?>
			<label for="<?php echo $this->get_field_id('unitcategories'); ?>"><?php _e('Product category', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('unitcategoriesrealestate'),
					$this->get_field_id('unitcategoriesrealestate')
				);
				foreach ($unitCategoriesRealEstate as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $unitCategoriesSelectedRealEstate) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
			</label>
		<?php }  ?>
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showSearchTextOnSell'); ?>" value="1" <?php  echo ($showSearchTextOnSell=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Search text', 'bfi'); ?> <?php _e('(Regions, States, Cities, Zones)', 'bfi') ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showMapIconOnSell'); ?>" value="1" <?php  echo ($showMapIconOnSell=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Map Button', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showContract'); ?>" value="1" <?php  echo ($showContract=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Contract', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showAccomodationsOnSell'); ?>" value="1" <?php  echo ($showAccomodationsOnSell=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Type', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showMaxPrice'); ?>" value="1" <?php  echo ($showMaxPrice=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Price', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showMinFloor'); ?>" value="1" <?php  echo ($showMinFloor=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Floor Area', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showBedRooms'); ?>" value="1" <?php  echo ($showBedRooms=='1') ? 'checked="checked"' : ''; ?> /><?php _e('BedRooms', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showBaths'); ?>" value="1" <?php  echo ($showBaths=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Baths', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showOnlyNew'); ?>" value="1" <?php  echo ($showOnlyNew=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Only New', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showServicesList'); ?>" value="1" <?php  echo ($showServicesList=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Services list', 'bfi'); ?></label><br />

	</p>
	<?php }  ?>

	<p class="bfitabsearch5 widget-inside" > <!-- event -->
		<span class="bfi-titletab"><?php echo $tablist[5] ?></span><br />
		<input class="widefat" id="<?php echo $this->get_field_id('tabnameevents'); ?>" name="<?php echo $this->get_field_name('tabnameevents'); ?>" type="text" value="<?php echo $tabnameevents; ?>" />
		<label for="<?php echo $this->get_field_id('tabiconevents'); ?>"><?php _e('Icon (for expert users)', 'bfi'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('tabiconevents'); ?>" name="<?php echo $this->get_field_name('tabiconevents'); ?>" type="text" value="<?php echo $tabiconevents; ?>" />
		<br />		
		<label for="<?php echo $this->get_field_id('searchTextFieldsEvent'); ?>"><?php _e('Research fields', 'bfi'); ?>
		<?php 
			
				printf(
				'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
	            $this->get_field_name('searchTextFieldsEvent'),
	            $this->get_field_id('searchTextFieldsEvent')
            );
				foreach (BFCHelper::$listResultClasses as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $searchTextFieldsEvent) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
            ?>
		</label>
	</p>
	<p class="bfitabsearch6 widget-inside" > <!-- spiaggie -->
		<span class="bfi-titletab"><?php echo $tablist[6] ?></span><br />
		<input class="widefat" id="<?php echo $this->get_field_id('tabnamemapsell'); ?>" name="<?php echo $this->get_field_name('tabnamemapsell'); ?>" type="text" value="<?php echo $tabnamemapsell; ?>" />
		<label for="<?php echo $this->get_field_id('tabiconmapsell'); ?>"><?php _e('Icon (for expert users)', 'bfi'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('tabiconmapsell'); ?>" name="<?php echo $this->get_field_name('tabiconmapsell'); ?>" type="text" value="<?php echo $tabiconmapsell; ?>" />
		<br />		
		<label for="<?php echo $this->get_field_id('searchTextFieldsMapsell'); ?>"><?php _e('Research fields', 'bfi'); ?>
		<?php 
			
				printf(
				'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
	            $this->get_field_name('searchTextFieldsMapsell'),
	            $this->get_field_id('searchTextFieldsMapsell')
            );
				foreach (BFCHelper::$listResultClasses as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $searchTextFieldsMapsell) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
            ?>
		</label>

		<br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showDateOneDaysMapSell'); ?>" value="1" <?php  echo ($showDateOneDaysMapSell=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Select 1 day o more', 'bfi'); ?></label><br />

	</p>

	</div><!-- advances -->


	<p class="realestateoptions">
	</p>
	<p class="eventoptions">
	</p>
	<p class="mapsellptions">
	</p>
<?php 
if ($this->number=="__i__"){
//echo "<p><strong>Widget ID is</strong>: Please save the widget</p>"   ;
}  else {
?>
	<p class="">
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('resultinsamepg'); ?>" value="1" <?php  echo ($resultinsamepg=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Risultato nella stessa pagina (solo se usato con shortcode)', 'bfi'); ?></label>
		
		<label for="<?php echo $this->get_field_id('resultpageid'); ?>"><?php _e('Or Select Result Page', 'bfi'); ?>
			<?php 

//				printf(
//					'<select name="%s[]" id="%s" class="widefat select2full">',
//					$this->get_field_name('resultpageid'),
//					$this->get_field_id('resultpageid')
//				);

//echo "<pre>$resultpageid == ";
//echo bfi_get_default_page_id($resultpageid);
//echo " == ";
//echo bfi_get_translated_page_id($resultpageid);
//echo "</pre>";

				  $argsPage = array(
					  'depth' => 0,
					  'child_of' => 0, 
					  'echo' => 1, 
					  'exclude' => '', 
					  'exclude_tree' => '',
					  'hierarchical' => 1, 
					  'class' => 'widefat select2full',
					  'name' => $this->get_field_name('resultpageid'),
					  'id' => $this->get_field_id('resultpageid'),
					  'post_type' => 'page',
					  'selected' => bfi_get_translated_page_id($resultpageid),  //$resultpageid,
					  'post_status' => 'publish',
					  'sort_column' => 'post_title',
					  'sort_order' => 'ASC'
				  );
if ( ! function_exists( 'my_list_pages_result_default' ) ) {
	function my_list_pages_result_default( $title, $page ) {
		$searchAvailability_pageID = bfi_get_page_id( 'searchavailability',1 );
				
		if ($page->ID == $searchAvailability_pageID) {
			$title = $title . ' (default) ';
		}
		return $title;
	}
}
add_filter( 'list_pages', 'my_list_pages_result_default', 10, 2 );
				wp_dropdown_pages($argsPage);
remove_filter( 'list_pages', 'my_list_pages_result_default', 10 );
//				echo '</select>';
				?>
		</label>

	<br />
	Shortcode for page:<br />
[bookingfor_dowidget id=bookingfor_booking_search-<?php echo $this->number ?>]
<?php if($resultinsamepg==1) { ?>[bookingfor_search_result]
<?php } ?>


<p>

<?php 
	}
?>

	<?php
	}


	// update widget
	function update($new_instance, $old_instance) {

		  $instance = $old_instance;
		  // Fields
		  $instance['title'] = strip_tags($new_instance['title']);

		  $instance['tablistSelected'] =  ! empty( $new_instance[ 'tablistSelected' ] ) ? esc_sql( $new_instance['tablistSelected'] ) : "";
		  $instance['blockmonths'] = ! empty( $new_instance[ 'blockmonths' ] ) ? esc_sql( $new_instance['blockmonths'] ) : "";
		  $instance['blockdays'] = ! empty( $new_instance[ 'blockdays' ] ) ? esc_sql( $new_instance['blockdays'] ) : "";

		  $instance['merchantcategoriesbooking'] = ! empty( $new_instance[ 'merchantcategoriesbooking' ] ) ? esc_sql( $new_instance['merchantcategoriesbooking'] ) : "";
		  $instance['merchantcategoriesservices'] = ! empty( $new_instance[ 'merchantcategoriesservices' ] ) ? esc_sql( $new_instance['merchantcategoriesservices'] ) : "";
		  $instance['merchantcategoriesactivities'] = ! empty( $new_instance[ 'merchantcategoriesactivities' ] ) ? esc_sql( $new_instance['merchantcategoriesactivities'] ) : "";
		  $instance['merchantcategoriesothers'] = ! empty( $new_instance[ 'merchantcategoriesothers' ] ) ? esc_sql( $new_instance['merchantcategoriesothers'] ) : "";
		  $instance['merchantcategoriesrealestate'] = ! empty( $new_instance[ 'merchantcategoriesrealestate' ] ) ? esc_sql( $new_instance['merchantcategoriesrealestate'] ) : "";

		  $instance['unitcategoriesbooking'] = ! empty( $new_instance[ 'unitcategoriesbooking' ] ) &&  in_array(0,$instance['tablistSelected']) ? esc_sql( $new_instance['unitcategoriesbooking'] ) : "";
		  $instance['unitcategoriesservices'] = ! empty( $new_instance[ 'unitcategoriesservices' ] ) &&  in_array(1,$instance['tablistSelected']) ? esc_sql( $new_instance['unitcategoriesservices'] ) : "";
		  $instance['unitcategoriesactivities'] = ! empty( $new_instance[ 'unitcategoriesactivities' ] ) &&  in_array(2,$instance['tablistSelected']) ? esc_sql( $new_instance['unitcategoriesactivities'] ) : "";
		  $instance['unitcategoriesothers'] = ! empty( $new_instance[ 'unitcategoriesothers' ] ) &&  in_array(4,$instance['tablistSelected']) ? esc_sql( $new_instance['unitcategoriesothers'] ) : "";
		  $instance['unitcategoriesrealestate'] = ! empty( $new_instance[ 'unitcategoriesrealestate' ] ) &&  in_array(3,$instance['tablistSelected']) ? esc_sql( $new_instance['unitcategoriesrealestate'] ) : "";

		  $instance['tabnamebooking'] = ! empty( $new_instance[ 'tabnamebooking' ] ) && is_array($instance['tablistSelected']) &&  in_array(0,$instance['tablistSelected']) ? strip_tags( $new_instance['tabnamebooking'] ) : "";
		  $instance['tabnameservices'] = ! empty( $new_instance[ 'tabnameservices' ] ) && is_array($instance['tablistSelected']) &&  in_array(1,$instance['tablistSelected']) ? strip_tags( $new_instance['tabnameservices'] ) : "";
		  $instance['tabnameactivities'] = ! empty( $new_instance[ 'tabnameactivities' ] ) && is_array($instance['tablistSelected']) &&  in_array(2,$instance['tablistSelected']) ? strip_tags( $new_instance['tabnameactivities'] ) : "";
		  $instance['tabnameothers'] = ! empty( $new_instance[ 'tabnameothers' ] ) && is_array($instance['tablistSelected']) &&  in_array(4,$instance['tablistSelected']) ? strip_tags( $new_instance['tabnameothers'] ) : "";
		  
		  $instance['tabnameevents'] = ! empty( $new_instance[ 'tabnameevents' ] ) && is_array($instance['tablistSelected']) &&  in_array(5,$instance['tablistSelected']) ? strip_tags( $new_instance['tabnameevents'] ) : "";
		  $instance['tabnamemapsell'] = ! empty( $new_instance[ 'tabnamemapsell' ] ) && is_array($instance['tablistSelected']) &&  in_array(6,$instance['tablistSelected']) ? strip_tags( $new_instance['tabnamemapsell'] ) : "";

		  $instance['tabintrobooking'] = ! empty( $new_instance[ 'tabintrobooking' ] ) && is_array($instance['tablistSelected']) &&  in_array(0,$instance['tablistSelected']) ? strip_tags( $new_instance['tabintrobooking'] ) : "";
		  $instance['tabintroservices'] = ! empty( $new_instance[ 'tabintroservices' ] ) && is_array($instance['tablistSelected']) &&  in_array(1,$instance['tablistSelected']) ? strip_tags( $new_instance['tabintroservices'] ) : "";
		  $instance['tabintroactivities'] = ! empty( $new_instance[ 'tabintroactivities' ] ) && is_array($instance['tablistSelected']) &&  in_array(2,$instance['tablistSelected']) ? strip_tags( $new_instance['tabintroactivities'] ) : "";
		  $instance['tabintroothers'] = ! empty( $new_instance[ 'tabintroothers' ] ) && is_array($instance['tablistSelected']) &&  in_array(4,$instance['tablistSelected']) ? strip_tags( $new_instance['tabintroothers'] ) : "";
		  
		  $instance['tabiconbooking'] = ! empty( $new_instance[ 'tabiconbooking' ] ) && is_array($instance['tablistSelected']) &&  in_array(0,$instance['tablistSelected']) ? strip_tags( $new_instance['tabiconbooking'] ) : "";
		  $instance['tabiconservices'] = ! empty( $new_instance[ 'tabiconservices' ] ) && is_array($instance['tablistSelected']) &&  in_array(1,$instance['tablistSelected']) ? strip_tags( $new_instance['tabiconservices'] ) : "";
		  $instance['tabiconactivities'] = ! empty( $new_instance[ 'tabiconactivities' ] ) && is_array($instance['tablistSelected']) &&  in_array(2,$instance['tablistSelected']) ? strip_tags( $new_instance['tabiconactivities'] ) : "";
		  $instance['tabiconothers'] = ! empty( $new_instance[ 'tabiconothers' ] ) && is_array($instance['tablistSelected']) &&  in_array(4,$instance['tablistSelected']) ? strip_tags( $new_instance['tabiconothers'] ) : "";

		  $instance['tabiconevents'] = ! empty( $new_instance[ 'tabiconevents' ] ) && is_array($instance['tablistSelected']) &&  in_array(5,$instance['tablistSelected']) ? strip_tags( $new_instance['tabiconevents'] ) : "";
		  $instance['tabiconmapsell'] = ! empty( $new_instance[ 'tabiconmapsell' ] ) && is_array($instance['tablistSelected']) &&  in_array(6,$instance['tablistSelected']) ? strip_tags( $new_instance['tabiconmapsell'] ) : "";

		  $instance['availabilitytypesbooking'] = ! empty( $new_instance[ 'availabilitytypesbooking' ] ) && is_array($instance['tablistSelected']) &&  in_array(0,$instance['tablistSelected']) ? esc_sql( $new_instance['availabilitytypesbooking'] ) : "";
		  $instance['availabilitytypesservices'] = ! empty( $new_instance[ 'availabilitytypesservices' ] ) && is_array($instance['tablistSelected']) &&  in_array(1,$instance['tablistSelected']) ? esc_sql( $new_instance['availabilitytypesservices'] ) : "";
		  $instance['availabilitytypesactivities'] = ! empty( $new_instance[ 'availabilitytypesactivities' ] ) && is_array($instance['tablistSelected']) &&  in_array(2,$instance['tablistSelected']) ? esc_sql( $new_instance['availabilitytypesactivities'] ) : "";
		  $instance['availabilitytypesothers'] = ! empty( $new_instance[ 'availabilitytypesothers' ] ) && is_array($instance['tablistSelected']) &&  in_array(4,$instance['tablistSelected']) ? esc_sql( $new_instance['availabilitytypesothers'] ) : "";

		  $instance['itemtypesbooking'] = ! empty( $new_instance[ 'itemtypesbooking' ] )  && is_array($instance['tablistSelected']) &&  in_array(0,$instance['tablistSelected']) ? esc_sql( $new_instance['itemtypesbooking'] ) : "";
		  $instance['itemtypesservices'] = ! empty( $new_instance[ 'itemtypesservices' ] )  && is_array($instance['tablistSelected']) &&  in_array(1,$instance['tablistSelected']) ? esc_sql( $new_instance['itemtypesservices'] ) : "";
		  $instance['itemtypesactivities'] = ! empty( $new_instance[ 'itemtypesactivities' ] )  && is_array($instance['tablistSelected']) &&  in_array(2,$instance['tablistSelected']) ? esc_sql( $new_instance['itemtypesactivities'] ) : "";
		  $instance['itemtypesothers'] = ! empty( $new_instance[ 'itemtypesothers' ] )  && is_array($instance['tablistSelected']) &&  in_array(4,$instance['tablistSelected']) ? esc_sql( $new_instance['itemtypesothers'] ) : "";

		  $instance['groupbybooking'] = ! empty( $new_instance[ 'groupbybooking' ] ) && is_array($instance['tablistSelected']) &&  in_array(0,$instance['tablistSelected']) ? esc_sql( $new_instance['groupbybooking'] ) : "";
		  $instance['groupbyservices'] = ! empty( $new_instance[ 'groupbyservices' ] ) && is_array($instance['tablistSelected']) &&   in_array(1,$instance['tablistSelected']) ? esc_sql( $new_instance['groupbyservices'] ) : "";
		  $instance['groupbyactivities'] = ! empty( $new_instance[ 'groupbyactivities' ] ) && is_array($instance['tablistSelected']) &&   in_array(2,$instance['tablistSelected']) ? esc_sql( $new_instance['groupbyactivities'] ) : "";
		  $instance['groupbyothers'] = ! empty( $new_instance[ 'groupbyothers' ] ) && is_array($instance['tablistSelected']) &&   in_array(4,$instance['tablistSelected']) ? esc_sql( $new_instance['groupbyothers'] ) : "";

		  $instance['resultviewsbooking'] = ! empty( $new_instance[ 'resultviewsbooking' ] ) && is_array($instance['tablistSelected']) &&  in_array(0,$instance['tablistSelected']) ? esc_sql( $new_instance['resultviewsbooking'] ) : "";
		  $instance['resultviewsservices'] = ! empty( $new_instance[ 'resultviewsservices' ] ) && is_array($instance['tablistSelected']) &&   in_array(1,$instance['tablistSelected']) ? esc_sql( $new_instance['resultviewsservices'] ) : "";
		  $instance['resultviewsactivities'] = ! empty( $new_instance[ 'resultviewsactivities' ] ) && is_array($instance['tablistSelected']) &&   in_array(2,$instance['tablistSelected']) ? esc_sql( $new_instance['resultviewsactivities'] ) : "";
		  $instance['resultviewsothers'] = ! empty( $new_instance[ 'resultviewsothers' ] ) && is_array($instance['tablistSelected']) &&   in_array(4,$instance['tablistSelected']) ? esc_sql( $new_instance['resultviewsothers'] ) : "";

		  $instance['showdirection'] =! empty( $new_instance[ 'showdirection' ] ) ? 1 : 0;
		  $instance['resultinsamepg'] =! empty( $new_instance[ 'resultinsamepg' ] ) ? 1 : 0;
		  $instance['resultpageid'] = ! empty( $new_instance[ 'resultpageid' ] ) ?  bfi_get_default_page_id(esc_sql( $new_instance['resultpageid']) ) : "";

		  $instance['fixedontop'] =! empty( $new_instance[ 'fixedontop' ] ) ? 1 : 0;
		  $instance['fixedontopcorrection'] =! empty( $new_instance[ 'fixedontopcorrection' ] ) ? esc_sql( $new_instance['fixedontopcorrection'] ) : 0;
		  $instance['fixedonbottom'] =! empty( $new_instance[ 'fixedonbottom' ] ) ? 1 : 0;

		  $instance['moretab'] =! empty( $new_instance[ 'moretab' ] ) ? 1 : 0;
		  $instance['showadvance'] =! empty( $new_instance[ 'showadvance' ] ) ? 1 : 0;

		  $instance['showLocation'] = ! empty( $new_instance[ 'showLocation' ] ) ? 1 : 0;
		  $instance['showMapIcon'] = ! empty( $new_instance[ 'showMapIcon' ] ) ? 1 : 0;
		  $instance['showSearchText'] = ! empty( $new_instance[ 'showSearchText' ] ) ? 1 : 0;
		  $instance['searchTextFields'] = ! empty( $new_instance[ 'searchTextFields' ] ) ? esc_sql( $new_instance['searchTextFields'] ) : "";
		  $instance['searchTextFieldsMapsell'] = ! empty( $new_instance[ 'searchTextFieldsMapsell' ] ) ? esc_sql( $new_instance['searchTextFieldsMapsell'] ) : "";
		  $instance['searchTextFieldsEvent'] = ! empty( $new_instance[ 'searchTextFieldsEvent' ] ) ? esc_sql( $new_instance['searchTextFieldsEvent'] ) : "";
		  
		  $instance['showAccomodations'] = ! empty( $new_instance[ 'showAccomodations' ] ) ? 1 : 0;
		  $instance['showDateOneDays'] = ! empty( $new_instance[ 'showDateOneDays' ] ) ? 1 : 0;
		  $instance['showDateOneDaysMapSell'] = ! empty( $new_instance[ 'showDateOneDaysMapSell' ] ) ? 1 : 0;
		  $instance['showDateRange'] = ! empty( $new_instance[ 'showDateRange' ] ) ? 1 : 0;
		  $instance['showDateTimeRange'] = ! empty( $new_instance[ 'showDateTimeRange' ] ) ? 1 : 0;

		  $instance['startDateTimeRange'] = ! empty( $new_instance[ 'startDateTimeRange' ] ) ? esc_sql( $new_instance['startDateTimeRange'] ) : "00:00";
		  $instance['endDateTimeRange'] = ! empty( $new_instance[ 'endDateTimeRange' ] ) ? esc_sql( $new_instance['endDateTimeRange'] ) : "24:00";

		  $instance['showResource'] = ! empty( $new_instance[ 'showResource' ] ) ? 1 : 0;
		  $instance['limitResource'] = ! empty( $new_instance[ 'limitResource' ] ) ? 1 : 0;
		  $instance['minResource'] = ! empty( $new_instance[ 'minResource' ] ) && is_array($new_instance['minResource']) ? esc_sql( $new_instance[ 'minResource' ][0]) : 1;
		  $instance['maxResource'] = ! empty( $new_instance[ 'maxResource' ] )  && is_array($new_instance['maxResource'])? esc_sql( $new_instance[ 'maxResource' ][0]) : 10;

		  $instance['showRooms'] = ! empty( $new_instance[ 'showRooms' ] ) ? 1 : 0;
		  $instance['limitRooms'] = ! empty( $new_instance[ 'limitRooms' ] ) ? 1 : 0;
		  $instance['minRooms'] = ! empty( $new_instance[ 'minRooms' ] ) && is_array($new_instance['minRooms']) ? esc_sql( $new_instance[ 'minRooms' ][0]) : 1;
		  $instance['maxRooms'] = ! empty( $new_instance[ 'maxRooms' ] )  && is_array($new_instance['maxRooms'])? esc_sql( $new_instance[ 'maxRooms' ][0]) : 10;
		  
		  $instance['showPerson'] = ! empty( $new_instance[ 'showPerson' ] ) ? 1 : 0;
		  $instance['showAdult'] = ! empty( $new_instance[ 'showAdult' ] ) ? 1 : 0;
		  $instance['showChildren'] = ! empty( $new_instance[ 'showChildren' ] ) ? 1 : 0;
//		  $instance['showSenior'] = ! empty( $new_instance[ 'showSenior' ] ) ? 1 : 0;
		  $instance['showSenior'] =0;
		  $instance['showServices'] = ! empty( $new_instance[ 'showServices' ] ) ? 1 : 0;
		  $instance['showOnlineBooking'] = ! empty( $new_instance[ 'showOnlineBooking' ] ) ? 1 : 0;
		  $instance['showVariationCodes'] = ! empty( $new_instance[ 'showVariationCodes' ] ) ? 1 : 0;
		  $instance['showMaxPrice'] = ! empty( $new_instance[ 'showMaxPrice' ] ) ? 1 : 0;
		  $instance['showMinFloor'] = ! empty( $new_instance[ 'showMinFloor' ] ) ? 1 : 0;
		  $instance['showContract'] = ! empty( $new_instance[ 'showContract' ] ) ? 1 : 0;


		  $instance['showSearchTextOnSell'] = ! empty( $new_instance[ 'showSearchTextOnSell' ] ) ? 1 : 0;
		  $instance['showMapIconOnSell'] = ! empty( $new_instance[ 'showMapIconOnSell' ] ) ? 1 : 0;
		  $instance['showAccomodationsOnSell'] = ! empty( $new_instance[ 'showAccomodationsOnSell' ] ) ? 1 : 0;
		  $instance['showBedRooms'] = ! empty( $new_instance[ 'showBedRooms' ] ) ? 1 : 0;
		  $instance['showBaths'] = ! empty( $new_instance[ 'showBaths' ] ) ? 1 : 0;
		  $instance['showOnlyNew'] = ! empty( $new_instance[ 'showOnlyNew' ] ) ? 1 : 0;
		  $instance['showServicesList'] = ! empty( $new_instance[ 'showServicesList' ] ) ? 1 : 0;
if ($this->number=="__i__"){
//echo "<p><strong>Widget ID is</strong>: Please save the widget</p>"   ;
}  else {
		$instanceContext = 'BookingFor Search Widget - ' . $this->number;
		$instance['currid'] = $this->number;
		$instance['currcontext'] = $instanceContext;
//		// WPML >= 3.2
//		if ( defined( 'ICL_SITEPRESS_VERSION' ) && version_compare( ICL_SITEPRESS_VERSION, '3.2', '>=' ) ) {
//			//$this->register_wpml_strings();
//		// WPML and Polylang compatibility
//		} elseif ( function_exists( 'icl_register_string' ) ) {
		if ( function_exists( 'icl_register_string' ) ) {
			icl_unregister_string ( $instanceContext, 'Search 1');
			icl_unregister_string ( $instanceContext, 'Search 2');
			icl_unregister_string ( $instanceContext, 'Search 3');
			icl_unregister_string ( $instanceContext, 'Search 4');

			icl_register_string( $instanceContext, 'Search 1', $instance['tabnamebooking'] );
			icl_register_string( $instanceContext, 'Search 2', $instance['tabnameservices'] );
			icl_register_string( $instanceContext, 'Search 3', $instance['tabnameactivities'] );
			icl_register_string( $instanceContext, 'Search 4', $instance['tabnameothers'] );

			icl_register_string( $instanceContext, 'Search 5', $instance['tabnameevents'] );
			icl_register_string( $instanceContext, 'Search 6', $instance['tabnamemapsell'] );

			icl_unregister_string ( $instanceContext, 'Search 1 intro');
			icl_unregister_string ( $instanceContext, 'Search 2 intro');
			icl_unregister_string ( $instanceContext, 'Search 3 intro');
			icl_unregister_string ( $instanceContext, 'Search 4 intro');

			icl_register_string( $instanceContext, 'Search 1 intro', $instance['tabintrobooking'] );
			icl_register_string( $instanceContext, 'Search 2 intro', $instance['tabintroservices'] );
			icl_register_string( $instanceContext, 'Search 3 intro', $instance['tabintroactivities'] );
			icl_register_string( $instanceContext, 'Search 4 intro', $instance['tabintroothers'] );
		}
}
		 return $instance;
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		// these are the widget options
		$title = apply_filters('widget_title', $instance['title']);
		$args["title"] =  $title;
		$args["instance"] =  $instance;
		bfi_get_template("widgets/booking-search.php",$args);
//		include(BFI()->plugin_path() .'/templates/widgets/booking-search.php');

//		$this->widget_end( $args );
	}
}
}