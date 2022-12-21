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
if ( ! class_exists( 'BFI_Widget_Booking_Search_Slot' ) ) {

	class BFI_Widget_Booking_Search_Slot extends WP_Widget {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->widget_cssclass    = 'bfi-widget_booking_search_slot';
			$this->widget_description = __( 'A Search box for time slots.', 'bfi' ) . " " . __( 'These features have been deprecated. This means they are no longer supported and will be removed in the next version', 'bfi' );
			$this->widget_id          = 'bookingfor_booking_search_slot';
			$this->widget_name        = __( 'BookingFor Search slot', 'bfi' ) . " - " . __( 'DEPRECATED', 'bfi' ) ;
			$this->widget_sidebar    = 'bfisidebar Time Slot';
			$this->settings           = array(
				'title'  => array(
					'type'  => 'text',
					'std'   => '',
					'label' => __( 'Title', 'bfi' )
				)
			);

			$widget_ops = array(
				'classname'   => $this->widget_cssclass,
				'description' => $this->widget_description,
				'sidebar' => $this->widget_sidebar,
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


		$availabilityTypeList = array();
		$availabilityTypeList['2'] = __('Unit of times', 'bfi');

		$groupByList = array();
		$groupByList['0'] = __('Resource', 'bfi');
		$groupByList['1'] = __('Merchant', 'bfi');
		$groupByList['2'] = __('Resource group', 'bfi');

		$itemTypeList = array();
	//	$itemTypeList['0'] = __('Resource', 'bfi');
		$itemTypeList['1'] = __('Service', 'bfi');
	//	$itemTypeList['2'] = __('Package', 'bfi');
	//	$itemTypeList['3'] = __('slot', 'bfi');

		$defaultdurationList = array();
		$defaultdurationList[1] = __('1 day', 'bfi');
		$defaultdurationList[7] = __('7 days', 'bfi');

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

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
		$showadvance = ( ! empty( $instance['showadvance'] ) ) ? esc_attr($instance['showadvance']) : '0';

		$blockmonths = ( ! empty( $instance['blockmonths'] ) ) ? $instance['blockmonths'] : array();
		$blockdays = ( ! empty( $instance['blockdays'] ) ) ? $instance['blockdays'] : array();

		$showdirection = ( ! empty( $instance['showdirection'] ) ) ? esc_attr($instance['showdirection']) : '0';
		$resultinsamepg = ( ! empty( $instance['resultinsamepg'] ) ) ? esc_attr($instance['resultinsamepg']) : '1';
		$resultpageid = ( ! empty( $instance['resultpageid'] ) ) ? esc_attr($instance['resultpageid']) : $resultpageidDefault;

		$fixedontop = ( ! empty( $instance['fixedontop'] ) ) ? esc_attr($instance['fixedontop']) : '0';
		$fixedontopcorrection = ( ! empty( $instance['fixedontopcorrection'] ) ) ? esc_attr($instance['fixedontopcorrection']) : 0;
		$fixedonbottom = ( ! empty( $instance['fixedonbottom'] ) ) ? esc_attr($instance['fixedonbottom']) : '0';


		$showLocation = ( ! empty( $instance['showLocation'] ) ) ? esc_attr($instance['showLocation']) : '0';
		$showSearchText = ( ! empty( $instance['showSearchText'] ) ) ? esc_attr($instance['showSearchText']) : '0';
		$searchTextFields = ( ! empty( $instance['searchTextFields'] ) ) ? $instance['searchTextFields'] : array(5,6,11,13,14,15,17,18);
		
		$showAccomodations = ( ! empty( $instance['showAccomodations'] ) ) ? esc_attr($instance['showAccomodations']) : '0';
			
		$showResource = ( ! empty( $instance['showResource'] ) ) ? esc_attr($instance['showResource']) : '0';
		$limitResource = ( ! empty( $instance['limitResource'] ) ) ? esc_attr($instance['limitResource']) : '0';
		$minResource = ( ! empty( $instance['minResource'] ) ) ? $instance['minResource'] : 1;
		$maxResource = ( ! empty( $instance['maxResource'] ) ) ? $instance['maxResource'] : 10;
		
		$showPerson = ( ! empty( $instance['showPerson'] ) ) ? esc_attr($instance['showPerson']) : '0';
		$showAdult = ( ! empty( $instance['showAdult'] ) ) ? esc_attr($instance['showAdult']) : '0';
		$showChildren = ( ! empty( $instance['showChildren'] ) ) ? esc_attr($instance['showChildren']) : '0';
		$showSenior = ( ! empty( $instance['showSenior'] ) ) ? esc_attr($instance['showSenior']) : '0';
		$showOnline = ( ! empty( $instance['showOnline'] ) ) ? esc_attr($instance['showOnline']) : '0';
		
		$merchantCategoriesSelected = ( ! empty( $instance['merchantcategories'] ) ) ? $instance['merchantcategories'] : array();

		$unitCategoriesSelected = ( ! empty( $instance['unitcategories'] ) ) ? $instance['unitcategories'] : array();

		$availabilityTypesSelected = ( ! empty( $instance['availabilitytypes'] ) ) ? $instance['availabilitytypes'] : array(2);

		$itemTypesSelected = ( ! empty( $instance['itemtypes'] ) ) ? $instance['itemtypes'] : array(1);
		
		$groupBySelected = ( ! empty( $instance['groupby'] ) ) ? $instance['groupby'] : array(2);

		$defaultdurationSelected = ( ! empty( $instance['defaultduration'] ) ) ? esc_attr($instance['defaultduration']) : '+1 day';

		$widgettoshowSelected = ( ! empty( $instance['widgettoshow'] ) ) ? esc_attr($instance['widgettoshow']) : '';

		// aggiunta id del widget nel titolo
		if ($this->number=="__i__"){
		}  else {
			$instance[ 'title' ] = $this->number ;
		}

		?>
		<p class="bfi-deprecated">
			<?php _e('These features have been deprecated. This means they are no longer supported and will be removed in the next version', 'bfi') ?>
		</p>
		<?php 
		if ($this->number=="__i__"){
			//echo "<p><strong>Widget ID is</strong>: Please save the widget</p>"   ;
		}  else {
		?>
		ID: <b><?php echo $this->widget_id ?>-<?php echo $this->number ?></b>
		<?php }  ?>
		<p>
		<label class="bfitabmoretab" for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wp_widget_plugin'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo ($instance)?esc_attr($instance['title']):''; ?>" />
		</p>
		<p class="">
			<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showdirection'); ?>" value="1" <?php  echo ($showdirection=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Displays horizontally', 'bfi'); ?></label>
		</p>
		<p class="options" style="display:none;">
			<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('fixedontop'); ?>" value="1" <?php  echo ($fixedontop=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Fixed on top', 'bfi'); ?></label>
		</p>
		<p class="options" style="display:none;">
			<label for="<?php echo $this->get_field_id('fixedontopcorrection'); ?>"><?php _e('Top correction', 'bfi'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('fixedontopcorrection'); ?>" name="<?php echo $this->get_field_name('fixedontopcorrection'); ?>" type="number" value="<?php echo $fixedontopcorrection; ?>" request />
		</p>
		<p class="options" style="display:none;">
			<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('fixedonbottom'); ?>" value="1" <?php  echo ($fixedonbottom=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Fixed on bottom for small device', 'bfi'); ?></label>
		</p>
		<p>
			<label class="checkbox"><input type="checkbox" class="bfiadvance-cb" name="<?php echo $this->get_field_name('showadvance'); ?>" value="1" <?php  echo ($showadvance=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Show advance settings', 'bfi'); ?></label>
		</p>

	<div class="bfiadvance">
		<?php if(!empty($merchantCategories) || !empty($unitCategories)){  ?>
		<p class="options" >
			<?php if(!empty($merchantCategories)){  ?>
				<label for="<?php echo $this->get_field_id('merchantcategories'); ?>"><?php _e('Merchant category', 'bfi'); ?>
				<?php 
					printf(
						'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
						$this->get_field_name('merchantcategories'),
						$this->get_field_id('merchantcategories')
					);
					foreach ($merchantCategories as $key => $value) {
						printf(
							'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
							$key,
							in_array( $key, $merchantCategoriesSelected) ? 'selected="selected"' : '',
							$value
						);
					}
					echo '</select>';
					?>
				</label>
			<?php }  ?>
			<?php if(!empty($unitCategories)){  ?>
				<label for="<?php echo $this->get_field_id('unitcategories'); ?>"><?php _e('Product category', 'bfi'); ?>
				<?php 
					printf(
						'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
						$this->get_field_name('unitcategories'),
						$this->get_field_id('unitcategories')
					);
					foreach ($unitCategories as $key => $value) {
						printf(
							'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
							$key,
							in_array( $key, $unitCategoriesSelected) ? 'selected="selected"' : '',
							$value
						);
					}
					echo '</select>';
					?>
				</label>
			<?php }  ?>
			<label for="<?php echo $this->get_field_id('availabilitytypes'); ?>"><?php _e('Search availability for', 'bfi'); ?>
				<?php 
					printf(
						'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
						$this->get_field_name('availabilitytypes'),
						$this->get_field_id('availabilitytypes')
					);
					foreach ($availabilityTypeList as $key => $value) {
						printf(
							'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
							$key,
							in_array( $key, $availabilityTypesSelected) ? 'selected="selected"' : '',
							$value
						);
					}
					echo '</select>';
					?>
			</label>
			<label for="<?php echo $this->get_field_id('itemtypes'); ?>"><?php _e('Items type', 'bfi'); ?>
				<?php 
					printf(
						'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
						$this->get_field_name('itemtypes'),
						$this->get_field_id('itemtypes')
					);
					foreach ($itemTypeList as $key => $value) {
						printf(
							'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
							$key,
							in_array( $key, $itemTypesSelected) ? 'selected="selected"' : '',
							$value
						);
					}
					echo '</select>';
					?>
			</label>
			<label for="<?php echo $this->get_field_id('groupby'); ?>"><?php _e('Default group by ', 'bfi'); ?>
				<?php 
					printf(
						'<select name="%s[]" id="%s" class="widefat select2full">',
						$this->get_field_name('groupby'),
						$this->get_field_id('groupby')
					);
					foreach ($groupByList as $key => $value) {
						printf(
							'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
							$key,
							in_array( $key, $groupBySelected) ? 'selected="selected"' : '',
							$value
						);
					}
					echo '</select>';
					?>
			</label>
		</p>
		<p class="options">
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
	<!-- 		<br />
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
	 -->	
		<br />
		<label class="checkbox bfi_showpersons"><input type="checkbox" name="<?php echo $this->get_field_name('showPerson'); ?>" value="1" <?php  echo ($showPerson=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Persons', 'bfi'); ?></label><br />
			<span class="bfi_nopersons">
				<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showAdult'); ?>" value="1" <?php  echo ($showAdult=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Adults', 'bfi'); ?></label><br />
				<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showChildren'); ?>" value="1" <?php  echo ($showChildren=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Childrens', 'bfi'); ?></label><br />
				<!-- <label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showSenior'); ?>" value="1" <?php  echo ($showSenior=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Senior', 'bfi'); ?></label><br /> -->
			</span>
			<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showOnline'); ?>" value="1" <?php  echo ($showOnline=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Only Online Booking', 'bfi'); ?></label><br />
			<label for="<?php echo $this->get_field_id('defaultduration'); ?>"><?php _e('Search days interval ', 'bfi'); ?>
				<?php 
					printf(
						'<select name="%s" id="%s" class="widefat">',
						$this->get_field_name('defaultduration'),
						$this->get_field_id('defaultduration')
					);
					foreach ($defaultdurationList as $key => $value) {
						printf(
							'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
							$key,
							( $key == $defaultdurationSelected) ? 'selected="selected"' : '',
							$value
						);
					}
					echo '</select>';
					?>
			</label>
			<br /><br />
		</p>
		<?php }  ?>


		</div><!-- advances -->
	<?php 
	if ($this->number=="__i__"){
	//echo "<p><strong>Widget ID is</strong>: Please save the widget</p>"   ;
	}  else {
	?>
		<p class="">
			<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('resultinsamepg'); ?>" value="1" <?php  echo ($resultinsamepg=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Pagina dei risultati: ', 'bfi'); ?></label>
			
			<label for="<?php echo $this->get_field_id('resultpageid'); ?>"><?php _e('Select', 'bfi'); ?>
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
	<?php 

		 global $wp_registered_widgets;
		 $sidebarWidget = array();
		 foreach ($wp_registered_widgets as $key=>$value) {
			 if (strrpos($key,$this->widget_id) >-1 ) {
				 array_push($sidebarWidget,$key);
			 }
		 }
	?>
			<label for="<?php echo $this->get_field_id('widgettoshow'); ?>"><?php _e('Visualizza il seguente widget nella sidebar ' . $this->widget_sidebar, 'bfi'); ?>
				<?php 
					printf(
						'<select name="%s" id="%s" class="widefat">',
						$this->get_field_name('widgettoshow'),
						$this->get_field_id('widgettoshow')
					);
						printf(
							'<option value="" style="margin-bottom:3px;">%s</option>',
							"tutti",
							( '' == $widgettoshowSelected) ? 'selected="selected"' : '',
							''
						);
					foreach ($sidebarWidget as $value) {
						printf(
							'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
							$value,
							( $value == $widgettoshowSelected) ? 'selected="selected"' : '',
							$value
						);
					}
					echo '</select>';
					?>
			</label>


		<br />
		Shortcode for page:<br />
	[bookingfor_dowidget id=<?php echo $this->widget_id ?>-<?php echo $this->number ?>]
	<?php if($resultinsamepg==1) { ?>[bookingfor_search_result_slot]
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
	
			  $instance['blockmonths'] = ! empty( $new_instance[ 'blockmonths' ] ) ? esc_sql( $new_instance['blockmonths'] ) : "";
			  $instance['blockdays'] = ! empty( $new_instance[ 'blockdays' ] ) ? esc_sql( $new_instance['blockdays'] ) : "";
	
			  $instance['merchantcategories'] = ! empty( $new_instance[ 'merchantcategories' ] ) ? esc_sql( $new_instance['merchantcategories'] ) : "";
	
			  $instance['unitcategories'] = ! empty( $new_instance[ 'unitcategories' ] ) ? esc_sql( $new_instance['unitcategories'] ) : "";		  
			  
			  $instance['availabilitytypes'] = ! empty( $new_instance[ 'availabilitytypes' ] ) ? esc_sql( $new_instance['availabilitytypes'] ) : "";
	
			  $instance['itemtypes'] = ! empty( $new_instance[ 'itemtypes' ]  ) ? esc_sql( $new_instance['itemtypes'] ) : "";
	
			  $instance['groupby'] = ! empty( $new_instance[ 'groupby' ] ) ? esc_sql( $new_instance['groupby'] ) : "";
			  $instance['defaultduration'] = ! empty( $new_instance[ 'defaultduration' ] ) ? esc_sql( $new_instance['defaultduration'] ) : 1;

			  $instance['showdirection'] =! empty( $new_instance[ 'showdirection' ] ) ? 1 : 0;
			  $instance['resultinsamepg'] =! empty( $new_instance[ 'resultinsamepg' ] ) ? 1 : 0;
			  $instance['resultpageid'] = ! empty( $new_instance[ 'resultpageid' ] ) ?  bfi_get_default_page_id(esc_sql( $new_instance['resultpageid']) ) : "";

			  $instance['widgettoshow'] = ! empty( $new_instance[ 'widgettoshow' ] ) ?  esc_sql( $new_instance['widgettoshow']) : "";

			  $instance['fixedontop'] =! empty( $new_instance[ 'fixedontop' ] ) ? 1 : 0;
			  $instance['fixedontopcorrection'] =! empty( $new_instance[ 'fixedontopcorrection' ] ) ? esc_sql( $new_instance['fixedontopcorrection'] ) : 0;
			  $instance['fixedonbottom'] =! empty( $new_instance[ 'fixedonbottom' ] ) ? 1 : 0;

			  $instance['showadvance'] =! empty( $new_instance[ 'showadvance' ] ) ? 1 : 0;

			  $instance['showLocation'] = ! empty( $new_instance[ 'showLocation' ] ) ? 1 : 0;
			  $instance['showSearchText'] = ! empty( $new_instance[ 'showSearchText' ] ) ? 1 : 0;
			  $instance['searchTextFields'] = ! empty( $new_instance[ 'searchTextFields' ] ) ? esc_sql( $new_instance['searchTextFields'] ) : "";
			  
			  $instance['showAccomodations'] = ! empty( $new_instance[ 'showAccomodations' ] ) ? 1 : 0;
	
			  $instance['showResource'] = ! empty( $new_instance[ 'showResource' ] ) ? 1 : 0;
			  $instance['limitResource'] = ! empty( $new_instance[ 'limitResource' ] ) ? 1 : 0;
			  $instance['minResource'] = ! empty( $new_instance[ 'minResource' ] ) && is_array($new_instance['minResource']) ? esc_sql( $new_instance[ 'minResource' ][0]) : 1;
			  $instance['maxResource'] = ! empty( $new_instance[ 'maxResource' ] )  && is_array($new_instance['maxResource'])? esc_sql( $new_instance[ 'maxResource' ][0]) : 10;
			  
			  $instance['showPerson'] = ! empty( $new_instance[ 'showPerson' ] ) ? 1 : 0;
			  $instance['showAdult'] = ! empty( $new_instance[ 'showAdult' ] ) ? 1 : 0;
			  $instance['showChildren'] = ! empty( $new_instance[ 'showChildren' ] ) ? 1 : 0;
	//		  $instance['showSenior'] = ! empty( $new_instance[ 'showSenior' ] ) ? 1 : 0;
			  $instance['showSenior'] =0;
			  $instance['showOnline'] = ! empty( $new_instance[ 'showOnline' ] ) ? 1 : 0;
			  if ($this->number=="__i__"){
				//echo "<p><strong>Widget ID is</strong>: Please save the widget</p>"   ;
			  }  else {
						$instanceContext = 'BookingFor Search Widget - ' . $this->number;
						$instance['currid'] = $this->number;
						$instance['currcontext'] = $instanceContext;
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
			//chech widget to show
			bfi_get_template("widgets/booking-search-slot.php",$args);
		}
	}
}