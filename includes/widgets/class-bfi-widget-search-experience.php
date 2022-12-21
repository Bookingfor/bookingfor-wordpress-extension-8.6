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
if ( ! class_exists( 'BFI_Widget_Booking_Search_Experience' ) ) {

	class BFI_Widget_Booking_Search_Experience extends WP_Widget {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->widget_cssclass    = 'bfi-widget_booking_search_experience';
			$this->widget_description = __( 'A Search box for Experience.', 'bfi' ) . " " . __( 'These features have been deprecated. This means they are no longer supported and will be removed in the next version', 'bfi' );
			$this->widget_id          = 'bookingfor_booking_search_experience';
			$this->widget_name        = __( 'BookingFor Search Experience', 'bfi' ) . " - " . __( 'DEPRECATED', 'bfi' ) ;
			$this->widget_sidebar    = 'bfisidebarExperience';
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
		$availabilityTypeList['1'] = __('Nights', 'bfi');
		$availabilityTypeList['0'] = __('Days', 'bfi');
		$availabilityTypeList['2'] = __('Unit of times', 'bfi');
		$availabilityTypeList['3'] = __('Time slot', 'bfi');

		$groupByList = array();
		$groupByList['0'] = __('Resource', 'bfi');
	//	$groupByList['1'] = __('Merchant', 'bfi');
	//	$groupByList['2'] = __('Resource group', 'bfi');

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

		$availabilityTypesSelected = ( ! empty( $instance['availabilitytypes'] ) ) ? $instance['availabilitytypes'] : array(1,3);
		
		$groupBySelected = ( ! empty( $instance['groupby'] ) ) ? $instance['groupby'] : array(2);

		$defaultdurationSelected = ( ! empty( $instance['defaultduration'] ) ) ? esc_attr($instance['defaultduration']) : '+1 day';
		
		$widgettoshowSelected = ( ! empty( $instance['widgettoshow'] ) ) ? esc_attr($instance['widgettoshow']) : '';

		$showVariationCodes = ( ! empty( $instance['showVariationCodes'] ) ) ? esc_attr($instance['showVariationCodes']) : '0';

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
		<p class="options" style="display:;">
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
			<br />
			<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showVariationCodes'); ?>" value="1" <?php  echo ($showVariationCodes=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Coupon', 'bfi'); ?></label><br />
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
	<p>

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
			  $instance['showVariationCodes'] = ! empty( $new_instance[ 'showVariationCodes' ] ) ? 1 : 0;
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
			 if(!empty( $instance['title'] )){
				$title = apply_filters('widget_title', $instance['title']);
				$args["title"] =  $title;
			 }
		 
			$args["instance"] =  $instance;

			bfi_get_template("widgets/booking-search-experience.php",$args);
		}
	}
}