<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'bfi_Shortcodes' ) ) {
/**
 * bfi_Shortcodes class
 *
 * @class       bfi_Shortcodes
 * @version     2.0.5
 * @package     Bookingfor/Classes
 * @category    Class
 * @author      Bookingfor
 */
class bfi_Shortcodes {

	/**
	 * $shortcode_tag
	 * holds the name of the shortcode tag
	 * @var string
	 */
	public $shortcode_tag = 'bfi_panel';

	/**
	 * Init shortcodes.
	 */
	public static function init() {
		$shortcodes = array(
//			'bookingfor_search'           => __CLASS__ . '::bfi_shortcode_search',
			'bookingfor_merchants'           => __CLASS__ . '::bfi_shortcode_merchants',
			'bookingfor_merchant'           => __CLASS__ . '::bfi_shortcode_merchant',
			'bookingfor_merchantscarousel'           => __CLASS__ . '::bfi_shortcode_merchantscarousel',
			'bookingfor_resourcescarousel'           => __CLASS__ . '::bfi_shortcode_resourcescarousel',
			'bookingfor_resourcegroupscarousel'           => __CLASS__ . '::bfi_shortcode_resourcegroupscarousel',
			'bookingfor_eventscarousel'           => __CLASS__ . '::bfi_shortcode_eventscarousel',
			'bookingfor_poicarousel'           => __CLASS__ . '::bfi_shortcode_poicarousel',
			'bookingfor_resources'           => __CLASS__ . '::bfi_shortcode_resources',
			'bookingfor_groupedresource'           => __CLASS__ . '::bfi_shortcode_groupedresource',
			'bookingfor_onsells'           => __CLASS__ . '::bfi_shortcode_onsells',
			'bookingfor_tag'           => __CLASS__ . '::bfi_shortcode_tag',  //***
			'bookingfor_currencyswitcher'           => __CLASS__ . '::bfi_shortcode_currencyswitcher',   //***
			'bookingfor_events'           => __CLASS__ . '::bfi_shortcode_events',
			'bookingfor_poi'           => __CLASS__ . '::bfi_shortcode_pointsofinterests',
			'bookingfor_event'           => __CLASS__ . '::bfi_shortcode_event',
			'bookingfor_offers'           => __CLASS__ . '::bfi_shortcode_offers',   //***
			'bookingfor_offer'           => __CLASS__ . '::bfi_shortcode_offer',
			'bookingfor_packages'           => __CLASS__ . '::bfi_shortcode_packages',
			'bookingfor_dowidget'           => __CLASS__ . '::bfi_shortcode_dowidget',

//			'buildings'                    => __CLASS__ . '::buildings',
//			'real_estates'               => __CLASS__ . '::realestates',
//			'tag'            => __CLASS__ . '::tag',
		);
		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}

	/**
	 * __construct
	 * class constructor will set the needed filter and action hooks
	 *
	 * @param array $args
	 */
	function __construct($args = array()){
//		if ( is_admin() ){
//			add_action( 'admin_head', array( $this, 'admin_head') );
//			add_action( 'admin_enqueue_scripts', array($this , 'admin_enqueue_scripts' ) );
//		}

	}

	/**
	 * admin_head
	 * calls your functions into the correct filters
	 * @return void
	 */
	function admin_head() {
		// check user permissions
		if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
			return;
		}
	}


	/**
	 * admin_enqueue_scripts
	 * Used to enqueue custom styles
	 * @return void
	 */
	function admin_enqueue_scripts(){
	}


	/**
	 * Shortcode Wrapper.
	 *
	 * @param string[] $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts    = array(),
		$wrapper = array(
			'class'  => 'bookingfor',
			'before' => null,
			'after'  => null
		)
	) {
		ob_start();

		echo empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		call_user_func( $function, $atts );
		echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		return ob_get_clean();
	}

	/**
	 * bfi_shortcode_search form shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
//	public static function bfi_shortcode_search( $atts ) {
//		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
//			return '';
//		}
//		ob_start();
//		bfi_get_template("widgets/booking-search.php",array("instance" =>$atts));
//		return ob_get_clean();
//	}

	public static function bfi_shortcode_currencyswitcher( $atts ) {
		ob_start();
		bfi_get_template("widgets/currency-switcher.php",array("instance" =>$atts));
		return ob_get_clean();
	}

	public static function bfi_shortcode_resourcescarousel( $atts ) {
		$atts = shortcode_atts( array(
			'tags'  => '',
			'itemspage'    =>4,
			'maxitems' => 10,  // Slugs
			'descmaxchars' => 300,  // Slugs
		), $atts );


		if ( ! $atts['tags'] ) {
			return '';
		}

		$tags =[];
		if(!empty($atts['tags'])){
			$tags =explode(",",$atts['tags']);
		}

        $instance['tags'] = $tags;
        $instance['itemspage'] = $atts['itemspage'];
        $instance['maxitems'] = $atts['maxitems'];
        $instance['descmaxchars'] = $atts['descmaxchars'];

		ob_start();
		bfi_get_template("widgets/carouselresourceswidgets.php",array("instance" =>$instance,"tags" =>$tags));
		return ob_get_clean();
	}

	public static function bfi_shortcode_resourcegroupscarousel( $atts ) {
		$atts = shortcode_atts( array(
			'tags'  => '',
			'itemspage'    =>4,
			'maxitems' => 10,  // Slugs
			'descmaxchars' => 300,  // Slugs
		), $atts );


		if ( ! $atts['tags'] ) {
			return '';
		}

		$tags =[];
		if(!empty($atts['tags'])){
			$tags =explode(",",$atts['tags']);
		}

        $instance['tags'] = $tags;
        $instance['itemspage'] = $atts['itemspage'];
        $instance['maxitems'] = $atts['maxitems'];
        $instance['descmaxchars'] = $atts['descmaxchars'];

		ob_start();
		bfi_get_template("widgets/carouselresourcegroupswidgets.php",array("instance" =>$instance,"tags" =>$tags));
		return ob_get_clean();
	}

	public static function bfi_shortcode_poicarousel( $atts ) {
		$atts = shortcode_atts( array(
			'tags'  => '',
			'merchantid'  => 0,
			'itemspage'    =>4,
			'maxitems' => 10,  // Slugs
			'descmaxchars' => 300,  // Slugs
		), $atts );


		if ( ! $atts['tags'] && empty($atts['merchantid'])  ) {
			return '';
		}

		$tags =[];
		if(!empty($atts['tags'])){
			$tags =explode(",",$atts['tags']);
		}

        $instance['tags'] = $tags;
        $instance['itemspage'] = $atts['itemspage'];
        $instance['maxitems'] = $atts['maxitems'];
        $instance['descmaxchars'] = $atts['descmaxchars'];

		if ( is_admin() ) {
			return '';
		}
		ob_start();
		bfi_get_template("widgets/carouselpoiwidgets.php",array("instance" =>$instance,"tags" =>$tags,"merchantid" =>$merchantid));
		return ob_get_clean();
	}

	public static function bfi_shortcode_eventscarousel( $atts ) {
		$atts = shortcode_atts( array(
			'tags'  => '',
			'merchantid'  => 0,
			'itemspage'    =>4,
			'maxitems' => 10,  // Slugs
			'descmaxchars' => 300,  // Slugs
		), $atts );


		if ( ! $atts['tags'] && empty($atts['merchantid'])  ) {
			return '';
		}

		$tags =[];
		if(!empty($atts['tags'])){
			$tags =explode(",",$atts['tags']);
		}

        $instance['tags'] = $tags;
        $instance['itemspage'] = $atts['itemspage'];
        $instance['maxitems'] = $atts['maxitems'];
        $instance['descmaxchars'] = $atts['descmaxchars'];

		if ( is_admin() ) {
			return '';
		}
		ob_start();
		bfi_get_template("widgets/carouseleventswidgets.php",array("instance" =>$instance,"tags" =>$tags,"merchantid" =>$merchantid));
		return ob_get_clean();
	}

	public static function bfi_shortcode_merchantscarousel( $atts ) {
		$atts = shortcode_atts( array(
			'tags'  => '',
			'itemspage'    =>4,
			'maxitems' => 10,  // Slugs
			'descmaxchars' => 300,  // Slugs
			'theme' => 1,  // Slugs
		), $atts );


		if ( ! $atts['tags'] ) {
			return '';
		}

		$tags =[];
		if(!empty($atts['tags'])){
			$tags =explode(",",$atts['tags']);
		}

		 $instance['tags'] = $tags;
		 $instance['itemspage'] = $atts['itemspage'];
		 $instance['maxitems'] = $atts['maxitems'];
		 $instance['descmaxchars'] = $atts['descmaxchars'];
		 $instance['theme'] = $atts['theme'];
		if ( is_admin() ) {
			return '';
		}

		ob_start();
		bfi_get_template("widgets/merchantscarouselwidgets.php",array("instance" =>$instance,"tags" =>$tags));
		return ob_get_clean();
	}


	/**
	 * Merchants page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function bfi_shortcode_merchants( $atts ) {

		$atts = shortcode_atts( array(
			'per_page' => COM_BOOKINGFORCONNECTOR_ITEMPERPAGE,
			'orderby'  => 'title',
			'order'    => 'desc',
			'category' => '',  // Slugs
			'rating' => '',  // Slugs
			'cityids' => '',  // Slugs
			'zoneids' => '',  // Slugs
			'onlylist' => '0',  // Slugs
			'tags'  => '',
		), $atts );
		if ( is_admin() ) {
			return '';
		}
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			$atts['onlylist'] = '1';
		}

        //if ( ! $atts['category'] ) {
        //    return '';
        //}
		$onlylist =  !empty($atts['onlylist']) ? $atts['onlylist'] : '0';
		$categoryIds =  !empty($atts['category']) ? $atts['category'] : '';
		$rating = !empty($atts['rating'])?$atts['rating']:'';
		$cityids = !empty($atts['cityids'])?$atts['cityids']:'';
		$zoneIds = !empty($atts['zoneids'])?$atts['zoneids']:'';
		$tags = !empty($atts['tags'])?$atts['tags']:'';

        $language = $GLOBALS['bfi_lang'];
		$page = "" ;

		$fileNameCached = 'bfi_shortcode_merchants_' . $language . '_' . implode("_", array_values($atts)). '_' . $page. '_' . COM_BOOKINGFORCONNECTOR_ITEMPERPAGE ;
		
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			$currContent = BFI_Cache::getCachedContent($fileNameCached);
			if (!empty($currContent)) {
			    return $currContent;
			}
		}
		ob_start();
		?>
		<div class="bookingforwidget" path="merchantlist" 
			data-categoryIds="<?php echo $categoryIds ?>"
			data-rating="<?php echo $rating ?>"
			data-cityids="<?php echo $cityids ?>"
			data-zoneIds="<?php echo $zoneIds ?>"
			data-tags="<?php echo $tags ?>"
			data-onlylist = "<?php echo $onlylist ?>"
			data-languages = "<?php echo substr($language,0,2) ?>"
			>
			<div id="bficontainer" class="bfi-loader"></div>
		</div>
	
	<?php 
		$output =  ob_get_clean();
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			BFI_Cache::setCachedContent($fileNameCached,$output);
		}
		return $output;
	}
	
	/**
	 * Merchant page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function bfi_shortcode_merchant( $atts ) {
		$atts = shortcode_atts( array(
			'id' => 0,  
			'layout' => '',  
		), $atts );

		$merchant_id = !empty($atts['id'])?$atts['id']:0;
		$layout = !empty($atts['layout'])?$atts['layout']:'';
		if ( is_admin() || empty($merchant_id) ) {
			return '';
		}
		$language = $GLOBALS['bfi_lang'];
		$page = "" ;
		$fileNameCached = 'bfi_shortcode_merchant_' . $language . '_' . implode("_", array_values($atts)). '_' . $page. '_' . COM_BOOKINGFORCONNECTOR_ITEMPERPAGE ;
		
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			$currContent = BFI_Cache::getCachedContent($fileNameCached);
			if (!empty($currContent)) {
			    return $currContent;
			}
		}
		ob_start();

		do_action( 'bookingfor_before_main_content' );
		?>
		<div class="bookingforwidget" path="merchantdetails" 
			data-Id="<?php echo $merchant_id ?>"
			data-languages="<?php echo substr($language,0,2) ?>"
			data-layout="<?php echo $layout ?>"
			>
			<div id="bficontainer" class="bfi-loader"></div>
		</div>
		<?php
		do_action( 'bookingfor_after_main_content' );
        //return ob_get_clean();
		$output =  ob_get_clean();
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			BFI_Cache::setCachedContent($fileNameCached,$output);
		}
		return $output;
	}


	/**
	 * offer page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function bfi_shortcode_offer( $atts ) {

		$atts = shortcode_atts( array(
			'id' => 0,  
			'layout' => '',  
		), $atts );

		$offerId = !empty($atts['id'])?$atts['id']:0;
		$layout = !empty($atts['layout'])?$atts['layout']:'';
		if ( is_admin() || empty($offerId) ) {
			return '';
		}
		$language = $GLOBALS['bfi_lang'];
		
		$fileNameCached = 'bfi_shortcode_offer' . '_' . $language . '_' . implode("_", array_values($atts)) ;
		
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			$currContent = BFI_Cache::getCachedContent($fileNameCached);
			if (!empty($currContent)) {
			    return $currContent;
			}
		}
		ob_start();
		?>
		<div class="bookingforwidget" path="offerdetails" 
			data-Id="<?php echo $offerId ?>"
			data-languages="<?php echo substr($language,0,2) ?>"
			>
			<div id="bficontainer" class="bfi-loader"></div>
		</div>

		<?php 
		$output =  ob_get_clean();
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			BFI_Cache::setCachedContent($fileNameCached,$output);
		}
		return $output;

	}
	
	/**
	 * Offers page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function bfi_shortcode_offers( $atts ) {

		$atts = shortcode_atts( array(
			'per_page' => COM_BOOKINGFORCONNECTOR_ITEMPERPAGE,
			/*'orderby'  => 'title',
			'order'    => 'desc',
			'category' => '',  // Slugs
			'rating' => '',  // Slugs
			'cityids' => '',  // Slugs
			'onlylist' => '0',  // Slugs
			'tag' => '',  // Slugs*/
		), $atts );
		
		$model = new BookingForConnectorModelOffers;
		$page = bfi_get_current_page();
		$listName = BFCHelper::$listNameAnalytics[6];
		
		$items = $model->getAllOffers(1/*(absint($page)-1)*$this->itemPerPage*/,50/*$this->itemPerPage*/,null);
		$paramRef = array(
			"offers"=>$items,
			"analyticsListName"=>$listName,

			);


		if(empty( $onlylist )){
?>		
				<div class="bfi-row  bfi-rowcontainer">
					<div class="bfi-rowcontainer-flex ">
						<div class=" bfi-sidebar">
							<?php 
							dynamic_sidebar('bfisidebar');
							/*$setLat = COM_BOOKINGFORCONNECTOR_GOOGLE_POSX;
							$setLon = COM_BOOKINGFORCONNECTOR_GOOGLE_POSY;
							bfi_get_template("widgets/smallmap.php",array("setLat"=>$setLat,"setLon"=>$setLon));*/	

							//bfi_get_template("widgets/search-filter-merchants.php",$paramRef);	
							?>
						</div>
						<div class=" bfi-main">
							<?php 
					
			} //if onlylist
		bfi_get_template("offerslist/offerslist.php",$paramRef);
			if(empty( $onlylist )){
							?>
						</div>
					</div>
				</div>
					<?php 
			} //if onlylist
			//bfi_get_template("offerslist/offerslist.php",$paramRef);
		return ob_get_clean();
	}
	
	
	
	/**
	 * Resources page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function bfi_shortcode_resources( $atts ) {

		$atts = shortcode_atts( array(
			'per_page' => COM_BOOKINGFORCONNECTOR_ITEMPERPAGE,
			'orderby'  => 'title',
			'order'    => 'desc',
			'categories' => '',  // Slugs
			'tags' => '',  // Slugs
			'resourcegroupid' => 0,  // Slugs
			'itemtypeids' => '0,1,2,3,4,5,6',  // Slugs
		), $atts );

		if ( is_admin() ) {
			return '';
		}

		$language = $GLOBALS['bfi_lang'];
		$resourcegroupId =  !empty($atts['resourcegroupid']) ? $atts['resourcegroupid'] : '';
		$categoryIds =  !empty($atts['categories']) ? $atts['categories'] : '';
		$itemTypeIds = !empty($atts['itemtypeids'])?$atts['itemtypeids']:'0,1,2,3,4,5,6';
		$tags = !empty($atts['tags'])?$atts['tags']:'';
		$zoneIds = !empty($atts['zoneids'])?$atts['zoneids']:'';
		$tags = !empty($atts['tags'])?$atts['tags']:'';

		$page = "" ;
		$fileNameCached = 'bfi_shortcode_resources' . '_' . $language . '_' . implode("_", array_values($atts)). '_' . $page. '_' . COM_BOOKINGFORCONNECTOR_ITEMPERPAGE ;
		
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			$currContent = BFI_Cache::getCachedContent($fileNameCached);
			if (!empty($currContent)) {
			    return $currContent;
			}
		}

		ob_start();
		?>
		<bfipage path="resourcelist" 
			data-resourcegroupId = "<?php echo $resourcegroupId ?>"
			data-categoryIds = "<?php echo $categoryIds ?>"
			data-itemTypeIds = "<?php echo $itemTypeIds ?>"
			data-tags = "<?php echo $tags ?>"
			data-languages = "<?php echo substr($language,0,2) ?>"
		>
			<div id="bfiheader" class=""></div>
			<div id="bficontainer" class="bfi-loader"></div>
			<div id="bfifooter" class=""></div>
		</bfipage>
		
		<?php 
				
		$output =  ob_get_clean();
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			BFI_Cache::setCachedContent($fileNameCached,$output);
		}
		return $output;
	}

	/**
	 * GroupedResource page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function bfi_shortcode_groupedresource( $atts ) {

		$atts = shortcode_atts( array(
			'id' => 0,  
			'layout' => '',  
		), $atts );

		$language = $GLOBALS['bfi_lang'];

		$fileNameCached = 'bfi_shortcode_groupedresource' . '_' . $language . '_' . implode("_", array_values($atts));
		
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			$currContent = BFI_Cache::getCachedContent($fileNameCached);
			if (!empty($currContent)) {
			    return $currContent;
			}
		}
		$resource_id = !empty($atts['id'])?$atts['id']:0;
		$layout = !empty($atts['layout'])?$atts['layout']:'';
		if ( is_admin() || empty($resource_id) ) {
			return '';
		}
		$language = $GLOBALS['bfi_lang'];
		$currId =   uniqid('mapsells');
		$currIdcontainer =   uniqid('bficontainer');
		?>
		<div class="bookingforwidget" path="mapsells" 
			id="<?php echo $currId  ?>"
			rel="<?php echo $currIdcontainer  ?>"
			data-Id="<?php echo $resource_id ?>"
			data-languages="<?php echo substr($language,0,2) ?>"
			>
			<div id="<?php echo $currIdcontainer  ?>" class="bfi-loader"></div>
		</div>
		<?php
		$output =  ob_get_clean();
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			BFI_Cache::setCachedContent($fileNameCached,$output);
		}
		return $output;
	}


	/**
	 * Event page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function bfi_shortcode_event( $atts ) {
		if ( is_admin() ) {
			return '';
		}

		$atts = shortcode_atts( array(
			'id' => 0,  
			'layout' => '',  
		), $atts );

		$resource_id = !empty($atts['id'])?$atts['id']:0;
		$layout = !empty($atts['layout'])?$atts['layout']:'';
		if ( is_admin() || empty($resource_id) ) {
			return '';
		}
		$language = $GLOBALS['bfi_lang'];
		
		$fileNameCached = 'bfi_shortcode_event' . '_' . $language . '_' . implode("_", array_values($atts)) ;
		
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			$currContent = BFI_Cache::getCachedContent($fileNameCached);
			if (!empty($currContent)) {
			    return $currContent;
			}
		}
		$currId =   uniqid('event');
?>
	<div class="bookingforwidget" path="event" 
		id="<?php echo $currId  ?>"
		data-Id="<?php echo $resource_id ?>"
		data-layout="<?php echo $layout ?>"
		data-languages="<?php echo substr($language,0,2) ?>"
		>
		<div id="bficontainer" class="bfi-loader"></div>
		<div id="divlistresource"></div>
	</div>
<?php 
		$output =  ob_get_clean();
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			BFI_Cache::setCachedContent($fileNameCached,$output);
		}
		return $output;

	}
	
	
	/**
	 * Resources on sells page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function bfi_shortcode_onsells( $atts ) {

		$atts = shortcode_atts( array(
			'per_page' => COM_BOOKINGFORCONNECTOR_ITEMPERPAGE,
			'orderby'  => 'AddedOn',
			'order'    => 'desc',
			'category' => '',  // Slugs
		), $atts );

		if ( is_admin() ) {
			return '';
		}

        $language = $GLOBALS['bfi_lang'];

		$page = "" ;
		$fileNameCached = 'bfi_shortcode_onsells' . '_' . $language . '_' . implode("_", array_values($atts)). '_' . $page. '_' . COM_BOOKINGFORCONNECTOR_ITEMPERPAGE ;
		
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			$currContent = BFI_Cache::getCachedContent($fileNameCached);
			if (!empty($currContent)) {
			    return $currContent;
			}
		}


		ob_start();
		?>
<bfi-page class="bfi-page-container bfi-searchevents-page ">
			<bfipage path="onselllist" 
				data-languages="<?php echo substr($language,0,2) ?>"
				data-header = "false"
				data-footer = "false"
				>
			</bfipage>
</bfi-page>
		<?php 
		$output =  ob_get_clean();
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			BFI_Cache::setCachedContent($fileNameCached,$output);
		}
		return $output;
	}

	public static function bfi_shortcode_events( $atts ) {
		$atts = shortcode_atts( array(
			'per_page' => COM_BOOKINGFORCONNECTOR_ITEMPERPAGE,
			'orderby'  => '',
			'order'    => 'desc',
			'category' => '',  // Slugs
			'tagid' => '',  // Slugs
			'cityids' => '',  // Slugs
			'onlylist' => '0',  // Slugs
		), $atts );

		if ( is_admin() ) {
			return '';
		}

		$language = $GLOBALS['bfi_lang'];
		
		ob_start();
		?>		
<bfi-page class="bfi-page-container bfi-searchevents-page ">
			<bfipage path="events"
				data-languages="<?php echo substr($language,0,2) ?>"
				data-header = "false"
				data-footer = "false"
				>
			</bfipage>
</bfi-page>
		<?php 
		$output =  ob_get_clean();
		return $output;
	}

	
	/**
	 * Short description.
	 * @param   type    $varname    description
	 * @return  type    description
	 * @access  public or private
	 * @static  makes the class property accessible without needing an instantiation of the class
	 */
	public static function bfi_shortcode_pointsofinterests($atts)
	{
		$atts = shortcode_atts( array(
			'per_page' => COM_BOOKINGFORCONNECTOR_ITEMPERPAGE,
			'orderby'  => '',
			'order'    => 'desc',
			'category' => '',  // Slugs
			'tagid' => '',  // Slugs
			'cityids' => '',  // Slugs
			'onlylist' => '0',  // Slugs
		), $atts );

		if ( is_admin() ) {
			return "";
		}
		
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			$atts['onlylist'] = '1';
		}
		$onlylist =  !empty($atts['onlylist']) ? $atts['onlylist'] : '0';

		$page = "" ;
		$fileNameCached = 'bfi_shortcode_pointsofinterests' . '_' . $language . '_' . implode("_", array_values($atts)). '_' . $page. '_' . COM_BOOKINGFORCONNECTOR_ITEMPERPAGE ;
		
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			$currContent = BFI_Cache::getCachedContent($fileNameCached);
			if (!empty($currContent)) {
			    return $currContent;
			}
		}
		ob_start();
			?>		
		<bfipage path="poi" 
			data-languages="<?php echo substr($language,0,2) ?>"
			>
		</bfipage>
		<?php 
            $output =  ob_get_clean();
            if (COM_BOOKINGFORCONNECTOR_ISBOT) {
                BFI_Cache::setCachedContent($fileNameCached,$output);
            }
            return $output;
	} // end func

	/**
	 * Tag page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function bfi_shortcode_tag( $atts ) {

		$atts = shortcode_atts( array(
			'per_page' => COM_BOOKINGFORCONNECTOR_ITEMPERPAGE,
			'orderby'  => 'Order',
			'order'    => 'asc',
			'tagid' => '',  // Slugs
			'scope' => '1',  // Slugs
			'grouped' => '0',  // Slugs
		), $atts );

		if ( is_admin() ) {
			return '';
		}
		if ( ! $atts['tagid'] ||  ! $atts['scope']) {
			return '';
		}

		$language = $GLOBALS['bfi_lang'];

		$page = "" ;
		$fileNameCached = 'bfi_shortcode_merchant' . '_' . $language . '_' . implode("_", array_values($atts)). '_' . $page. '_' . COM_BOOKINGFORCONNECTOR_ITEMPERPAGE ;
		
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			$currContent = BFI_Cache::getCachedContent($fileNameCached);
			if (!empty($currContent)) {
			    return $currContent;
			}
		}
		
		$tags =  $instance['tagid']; 
		$scope =  $instance['scope']; 

		$category = bfi_TagsScope::Merchant;
		switch (intval($atts['scope'])) {
			case 1: // Merchants
				$category = bfi_TagsScope::Merchant;
				break;
			case 2: // OnSellUnit
				$category = bfi_TagsScope::OnSellUnit;
				break;
			case 3: // Resource
				$category = bfi_TagsScope::Resource;
				break;
			case 4: // ResourceGroup
				$category = bfi_TagsScope::ResourceGroup;
				break;
			case 5: // Offert
				$category = bfi_TagsScope::Offert;
				break;
			case 6: // Event
				$category = bfi_TagsScope::Event;
				break;
			case 7: // Poi
				$category = bfi_TagsScope::Poi;
				break;
		}
			ob_start();

			$showGrouped = 0;
			$list = "";
			$listNameAnalytics = 0;
			$totalItems = array();
			$sendData = true;
			$return = "";

			switch ($category) {
				case bfi_TagsScope::Merchant: // Merchants
?>
		<div class="bookingforwidget" path="merchantlist" 
			data-categoryIds=""
			data-rating=""
			data-cityids=""
			data-zoneIds=""
			data-tags="<?php echo $tags ?>"
			data-onlylist = "true"
			data-languages = "<?php echo substr($language,0,2) ?>"
			>
			<div id="bficontainer" class="bfi-loader"></div>
		</div>
<?php 
					break;
				case bfi_TagsScope::Onsellunit: // Onsellunit
?>
		<bfipage path="onselllist" 
			data-languages="<?php echo substr($language,0,2) ?>"
			>
		</bfipage>					
					<?php
					break;
				case bfi_TagsScope::Resource: // Resource
						if  ($currParam['show_grouped'] == true) {

?>
		<bfipage path="resourcelist" 
			data-resourcegroupId = ""
			data-categoryIds = ""
			data-itemTypeIds = ""
			data-groupresulttype = "2"
			data-tags = "<?php echo $tags ?>"
			data-languages = "<?php echo substr($language,0,2) ?>"
		>
			<div id="bfiheader" class=""></div>
			<div id="bficontainer" class="bfi-loader"></div>
			<div id="bfifooter" class=""></div>
		</bfipage>
<?php 
						}else{
?>
		<bfipage path="resourcelist" 
			data-resourcegroupId = ""
			data-categoryIds = ""
			data-itemTypeIds = ""
			data-groupresulttype = "0"
			data-tags = "<?php echo $tags ?>"
			data-languages = "<?php echo substr($language,0,2) ?>"
		>
			<div id="bfiheader" class=""></div>
			<div id="bficontainer" class="bfi-loader"></div>
			<div id="bfifooter" class=""></div>
		</bfipage>
<?php 
						}

					break;
				case bfi_TagsScope::ResourceGroup: // ResourceGroup
?>
		<bfipage path="resourcelist" 
			data-resourcegroupId = ""
			data-categoryIds = ""
			data-itemTypeIds = ""
			data-groupresulttype = "2"
			data-tags = "<?php echo $tags ?>"
			data-languages = "<?php echo substr($language,0,2) ?>"
		>
			<div id="bfiheader" class=""></div>
			<div id="bficontainer" class="bfi-loader"></div>
			<div id="bfifooter" class=""></div>
		</bfipage>
<?php 
					break;
				case bfi_TagsScope::Offert: // Offert

					break;
				case bfi_TagsScope::Event: // Event
?>
				<bfipage path="events" data-languages="<?php echo substr($language,0,2) ?>"></bfipage>
<?php 
					break;
				case bfi_TagsScope::Poi: // Poi
?>
					<bfipage path="poi" data-languages="<?php echo substr($language,0,2) ?>"></bfipage>
<?php 

					break;
			}

		$output =  ob_get_clean();
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			BFI_Cache::setCachedContent($fileNameCached,$output);
		}
		return $output;

	}

	public static function bfi_shortcode_packages( $atts ) {
		$atts = shortcode_atts( array(
			'per_page' => COM_BOOKINGFORCONNECTOR_ITEMPERPAGE,
			'orderby'  => '',
			'order'    => 'desc',
			'tagid' => '',  // Slugs
			'cityids' => '',  // Slugs
			'onlylist' => '0',  // Slugs
		), $atts );

		if ( is_admin() ) {
			return '';
		}
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			$atts['onlylist'] = '1';
		}
		$onlylist =  !empty($atts['onlylist']) ? $atts['onlylist'] : '0';
		$language = $GLOBALS['bfi_lang'];

		$page = "" ;
		$fileNameCached = 'bfi_shortcode_packages' . '_' . $language . '_' . implode("_", array_values($atts)). '_' . $page. '_' . COM_BOOKINGFORCONNECTOR_ITEMPERPAGE ;
		
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			$currContent = BFI_Cache::getCachedContent($fileNameCached);
			if (!empty($currContent)) {
			    return $currContent;
			}
		}

		ob_start();
		?>
			<bfipage path="packages" 
				data-languages="<?php echo substr($language,0,2) ?>"
			>
				<div id="bfiheader" class=""></div>
				<div id="bfifooter" class=""></div>
			</bfipage>
		<?php 
		$output =  ob_get_clean();
		if (COM_BOOKINGFORCONNECTOR_ISBOT) {
			BFI_Cache::setCachedContent($fileNameCached,$output);
		}
		return $output;
	}

public static function bfi_shortcode_dowidget($atts) {

global $wp_registered_widgets, $_wp_sidebars_widgets, $wp_registered_sidebars;

/* check if the widget is in  the shortcode x sidebar  if not , just use generic, 
if it is in, then get the instance  data and use that */

	if (is_admin()) {return '';}  // eg in case someone decides to apply content filters when apost is saved, and not all widget stuff is there.
	extract(shortcode_atts(array(
		'sidebar' => 'Widgets for Shortcodes', //default
		'id' => '',
		'name' => '', /* MKM added explicit 'name' attribute.  For existing users we still need to allow prev method, else too many support queries will happen */
		'title' => '',   /* do the default title unless they ask us not to - use string here not boolean */
		'class' => 'bfi_widget', /* the widget class is picked up automatically.  If we want to add an additional class at the wrap level to try to match a theme, use this */
		'wrap' => '', /* wrap the whole thing - title plus widget in a div - maybe the themes use a div, maybe not, maybe we want that styling, maybe not */
		'widget_classes' =>  ''  /* option to disassociate from themes widget styling */
	), $atts));
	
	if (isset($_wp_sidebars_widgets) ) {
//		amr_show_widget_debug('which one', $name, $id, $sidebar);  //check for debug prompt and show widgets in shortcode sidebar if requested and logged in etc
	}
	else { 
		$output = '<br />No widgets defined at all in any sidebar!'; 
		return ($output);
	}
	
	/* compatibility check - if the name is not entered, then the first parameter is the name */
	if (empty($name) and !empty($atts[0]))  
		$name = $atts[0];
	/* the widget need not be specified, [do_widget widgetname] is adequate */
	if (!empty($name)) {  // we have a name
		$widget = $name;
		
		foreach ($wp_registered_widgets as $i => $w) { /* get the official internal name or id that the widget was registered with  */
			if (strtolower($w['name']) === strtolower($widget)) 
				$widget_ids[] = $i;
			//if ($debug) {echo '<br /> Check: '.$w['name'];}
		}	
		
		if (!($sidebarid = amr_get_sidebar_id ($sidebar))) {
			$sidebarid=$sidebar;   /* get the official sidebar id for this widget area - will take the first one */		
		}	
		
		
	}	
	else { /* check for id if we do not have a name */
//	$id="bookingfor_booking_search-".$id;
			if (!empty($id))  { 	/* if a specific id has been specified */			
				foreach ($wp_registered_widgets as $i => $w) { /* get the official internal name or id that the widget was registered with  */
					if ($w['id'] === $id) {
						$widget_ids[] = $id;
					}	
				}
//				echo '<h2>We have an id: '.$id.'</h2>'; 	if (!empty($widget_ids)) var_dump($widget_ids);	
//				return $output;		
			}
			else {
				$output = '<br />No valid widget name or id given in shortcode parameters';	
			
				return $output;		
			}
			// if we have id, get the sidebar for it
						
			$sidebarid = amr_get_widgets_sidebar($id);
			if (!$sidebarid) {
				$output =  '<br />Widget not in any sidebars<br />';
				return $output;
			}	
	}
	
	if (empty($widget)) 	$widget = '';
	if (empty($id)) 		$id = '';
	
	if (empty ($widget_ids)) { 
		$output =  '<br />Error: Your Requested widget "'.$widget.' '.$id.'" is not in the widget list.<br />';
//		$output .= amr_show_widget_debug('empty', $name, $id, $sidebar);
		return ($output) ;
	}		

		
	if (empty($widget)) 
		$widget = '';

	$content = ''; 			
	/* if the widget is in our chosen sidebar, then use the options stored for that */

//	if ((!isset ($_wp_sidebars_widgets[$sidebarid])) or (empty ($_wp_sidebars_widgets[$sidebarid]))) { // try upgrade
//		amr_upgrade_sidebar();
//	}
	
	//if we have a specific sidebar selected, use that
	if ((isset ($_wp_sidebars_widgets[$sidebarid])) and (!empty ($_wp_sidebars_widgets[$sidebarid]))) {
			/* get the intersect of the 2 widget setups so we just get the widget we want  */

		$wid = array_intersect ($_wp_sidebars_widgets[$sidebarid], $widget_ids );
	
	}
	else { /* the sidebar is not defined or selected - should not happen */
			if (isset($debug)) {  // only do this in debug mode
				if (!isset($_wp_sidebars_widgets[$sidebarid]))
					$output =  '<p>Error: Sidebar "'.$sidebar.'" with sidebarid "'.$sidebarid.'" is not defined.</p>'; 
				 // shouldnt happen - maybe someone running content filters on save
				else 
					$output =  '<p>Error: Sidebar "'.$sidebar.'" with sidebarid "'.$sidebarid.'" is empty (no widgets)</p>'; 
			}		
		}
	
	$output = '';
	if (empty ($wid) or (!is_array($wid)) or (count($wid) < 1)) { 

		$output = '<p>Error: Your requested Widget "'.$widget.'" is not in the "'.$sidebar.'" sidebar</p>';
//		$output .= amr_show_widget_debug('empty', $name, $id, $sidebar);

		unset($sidebar); 
		unset($sidebarid);

		}
	else {	
		/*  There may only be one but if we have two in our chosen widget then it will do both */
		$output = '';
		
		foreach ($wid as $i=>$widget_instance) {
			ob_start();  /* catch the echo output, so we can control where it appears in the text  */
			amr_shortcode_sidebar($widget_instance, $sidebar, $title, $class, $wrap, $widget_classes);
			$output .= ob_get_clean();
			}
	}
			
	return ($output);
	}
}
};


/*
Reference to wordpress plugin amr shortcode any widget 
url: https://it.wordpress.org/plugins/amr-shortcode-any-widget/
*/
if ( ! function_exists( 'amr_shortcode_sidebar' ) ) {
	function amr_shortcode_sidebar( $widget_id, 
		$name="widgets_for_shortcode", 
		$title=true, 
		$class='', 
		$wrap='', 
		$widget_classes='') { /* This is basically the wordpress code, slightly modified  */
		global $wp_registered_sidebars, $wp_registered_widgets;
		
	//	$debug = amr_check_if_widget_debug();

		$sidebarid = amr_get_sidebar_id ($name);

	//	$amr_sidebars_widgets = wp_get_sidebars_widgets(); //201711 do we need?
		$sidebar =null;
		if (in_array($sidebarid, $wp_registered_sidebars)) {
			$sidebar = $wp_registered_sidebars[$sidebarid];  // has the params etc
		}
		$did_one = false;
		
	//	echo "<pre>";
	//	echo $widget_id;
	//	echo "</pre>";
	//	
	//	echo "<pre>";
	//	echo print_r($wp_registered_widgets[$widget_id]);
	//	echo "</pre>";
		
	//		 echo "<pre>";
	//		 echo print_r(get_option('widget_bookingfor_booking_search'));
	//		 echo "</pre>";

		/* lifted from wordpress code, keep as similar as possible for now */

			if ( !isset($wp_registered_widgets[$widget_id]) ) return; // wp had c o n t i n u e

			$params = array_merge(
							array( 
								array_merge( 
	//								$sidebar, 
									array('widget_id' => $widget_id, 
										'widget_name' => $wp_registered_widgets[$widget_id]['name']
										) 
								) 
							),
							(array) $wp_registered_widgets[$widget_id]['params']
						);	
				
			$validtitletags = array ('h1','h2','h3','h4','h5','header','strong','em');
			$validwraptags = array ('div','p','main','aside','section');
			
			if (!empty($wrap)) { /* then folks want to 'wrap' with their own html tag, or wrap = yes  */		
				if ((!in_array( $wrap, $validwraptags))) 
					$wrap = ''; 
				  /* To match a variety of themes, allow for a variety of html tags. */
				  /* May not need if our sidebar match attempt has worked */
			}

			if (!empty ($wrap)) {
				$params[0]['before_widget'] = '<'.$wrap.' id="%1$s" class="%2$s">';
				$params[0]['after_widget'] = '</'.$wrap.'>';
			}
			
			// wp code to get classname
			$classname_ = '';
			//foreach ( (array) $wp_registered_widgets[$widget_id]['classname'] as $cn ) {
				$cn = $wp_registered_widgets[$widget_id]['classname'];
				if ( is_string($cn) )
					$classname_ .= '_' . $cn;
				elseif ( is_object($cn) )
					$classname_ .= '_' . get_class($cn);
			//}
			$classname_ = ltrim($classname_, '_');
			
			// add MKM and others requested class in to the wp classname string
			// if no class specfied, then class will = amrwidget.  These classes are so can reverse out unwanted widget styling.

			// $classname_ .= ' widget '; // wordpress seems to almost always adds the widget class
			

			$classname_ .= ' '.$class;

			// we are picking up the defaults from the  thems sidebar ad they have registered heir sidebar to issue widget classes?
			
			
			// Substitute HTML id and class attributes into before_widget		
			if (!empty($params[0]['before_widget'])) 
				$params[0]['before_widget'] = sprintf($params[0]['before_widget'], $widget_id, $classname_);
			else 
				$params[0]['before_widget'] = '';
			
			if (empty($params[0]['before_widget'])) 
				$params[0]['after_widget'] = '';

			$params = apply_filters( 'dynamic_sidebar_params', $params );  
			// allow, any pne usingmust ensure they apply to the correct sidebars
			
			if (!empty($title)) {
				if ($title=='false') { /* amr switch off the title html, still need to get rid of title separately */
					$params[0]['before_title'] = '<span style="display: none">';
					$params[0]['after_title'] = '</span>';
					}
				else {
					if (in_array( $title, $validtitletags)) {
						$class = ' class="widget-title" ';					
							
						$params[0]['before_title'] = '<'.$title.' '.$class.' >';
						$params[0]['after_title'] = '</'.$title.'>';
					}
				}			
			}
			
			if (!empty($widget_classes) and ($widget_classes == 'none') ) {
				$params = amr_remove_widget_class($params);  // also called in widget area shortcode
			}
			

			$callback = $wp_registered_widgets[$widget_id]['callback'];
			if ( is_callable($callback) ) {
				call_user_func_array($callback, $params);
				$did_one = true;
			}
	//	}
		return $did_one;
}
}

if ( ! function_exists( 'amr_get_sidebar_id' ) ) {
	function amr_get_sidebar_id ($name) { 
	/* walk through the registered sidebars with a name and find the id - will be something like sidebar-integer.  
	take the first one that matches */
	global $wp_registered_sidebars;	

		foreach ($wp_registered_sidebars as $i => $a) {
			if ((isset ($a['name'])) and ( $a['name'] === $name)) 
			return ($i);
		}
		return (false);
	}
}
if ( ! function_exists( 'amr_get_widgets_sidebar' ) ) {
	function amr_get_widgets_sidebar($wid) { 
	/* walk through the registered sidebars with a name and find the id - will be something like sidebar-integer.  
	take the first one that matches */
	global $_wp_sidebars_widgets;	
		foreach ($_wp_sidebars_widgets as $sidebarid => $sidebar) {	
			
			if (is_array($sidebar) ) { // ignore the 'array version' sidebarid that isnt actually a sidebar
				foreach ($sidebar as $i=> $w) {						
					if ($w == $wid) { 
						return 	$sidebarid;
					}	
				};	
			}	
		}
		return (false); // widget id not in any sidebar
	}
}
if ( ! function_exists( 'amr_remove_widget_class' ) ) {
	function amr_remove_widget_class($params) {  // remove the widget classes
		if (!empty($params[0]['before_widget'])) {
			$params[0]['before_widget'] = 
				str_replace ('"widget ','"',$params[0]['before_widget']);
		}
		
		if (!empty($params[0]['before_title'])) {  

			$params[0]['before_title'] = 
				$params[0]['before_title'] = str_replace ('widget-title','',$params[0]['before_title']);
				
		}
		
		return ($params);
	}
}
