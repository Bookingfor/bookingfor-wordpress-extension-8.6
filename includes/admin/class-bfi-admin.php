<?php
/**
 * BookingFor Admin
 *
 * @class    BFI_Admin
 * @author   Bookingfor
 * @category Admin
 * @package  Bookingfor/Admin
 * @version     2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'BFI_Admin' ) ) :
/**
 * BFI_Admin class.
 */
class BFI_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action("admin_menu", array( $this, 'admin_menu' ), 9 );
		add_action("admin_init", array( $this, 'display_bfi_fields' ));
		add_action( 'current_screen', array( $this, 'conditional_includes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
	}
	public function admin_menu() {
		$icon = BFI()->plugin_url() . '/assets/images/logo_16.png';

		add_menu_page("BookingFor", "BookingFor", "manage_options", "bfi-settings", null, $icon, '99.3');
		add_submenu_page('bfi-settings',"BookingFor Settings", "Settings", "manage_options", "bfi-settings", array( $this, 'bfi_settings_page' ));
	}


	public function admin_styles() {
		global $wp_scripts;

		$screen         = get_current_screen();
		$screen_id      = $screen ? $screen->id : '';
		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
	}

	/**
	 * Output buffering allows admin screens to make redirects later on.
	 */
	public function buffer() {
		ob_start();
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {

	}

	/**
	 * Include admin files conditionally.
	 */
	public function conditional_includes() {
		if ( ! $screen = get_current_screen() ) {
			return;
		}

		switch ( $screen->id ) {
			case 'options-permalink' :
				include( 'class-bfi-admin-permalink-settings.php' );
			break;
		}
	}

	public function bfi_settings_page(){
		$valid_nonce = isset($_REQUEST['_wpnonce']) ? wp_verify_nonce($_REQUEST['_wpnonce'], 'bfi-cache') : false;
		if ( $valid_nonce ) {
			if(isset($_REQUEST['bfi_delete_cache'])) {
				self::bfi_delete_files_cache(COM_BOOKINGFORCONNECTOR_CACHEDIR);
			}
		}
		$valid_nonce = isset($_REQUEST['_wpnonce']) ? wp_verify_nonce($_REQUEST['_wpnonce'], 'bfi-cache-bot') : false;
		if ( $valid_nonce ) {
			if(isset($_REQUEST['bfi_delete_cache-bot'])) {
				self::bfi_delete_files_cache(COM_BOOKINGFORCONNECTOR_CACHEDIRBOT);
			}
		}

		$valid_nonce = isset($_REQUEST['_wpnonce']) ? wp_verify_nonce($_REQUEST['_wpnonce'], 'bfi-missing-pages') : false;
		if ( $valid_nonce ) {
			if(isset($_REQUEST['bfi_create_missing_pages'])) {
				self::bfi_create_missing_pages();
			}
		}

		include('views/html-admin-settings.php');
	}
	public function is_dir_empty($dir) {
		if (!is_readable($dir)) return NULL; 
		return (count(scandir($dir)) == 2);
	}

	function bfi_create_missing_pages() {
		$pages = apply_filters( 'bookingfor_create_pages', array(
				'searchavailability' => array(
					'name'    => 'search-availability',
					'title'   => 'Search Availability',
					'content' => ''
				)
				,
				'searchmapsells' => array(
					'name'    => 'search-mapsells',
					'title'   => 'Search Map Sells',
					'content' => ''
				)
				,
				'searchevents' => array(
					'name'    => 'search-events',
					'title'   => 'Search Events',
					'content' => ''
				)
				,
				'searchonsell' => array(
					'name'    => 'properties-for-sale',
					'title'   => 'Search Properties for sale',
					'content' => ''
				)
				,
				'eventdetails' => array(
					'name'    => 'eventdetails',
					'title'   => 'Event Details',
					'content' => ''
				)
				,
				'pointsofinterestdetails' => array(
					'name'    => 'pointsofinterestdetails',
					'title'   => 'Pointsofinterest Details',
					'content' => ''
				)
				,					
				'merchantdetails' => array(
					'name'    => 'merchantdetails',
					'title'   => 'Merchant Details',
					'content' => ''
				)
				,
				'resourcegroupdetails' => array(
					'name'    => 'resourcegroupdetails',
					'title'   => 'Resourcegroup Details',
					'content' => ''
				)
				,
				'experiencedetails' => array(
					'name'    => 'experience-details',
					'title'   => 'Experience Details',
					'content' => ''
				)
				,
				'accommodationdetails' => array(
					'name'    => 'accommodation-details',
					'title'   => 'Accommodation Details',
					'content' => ''
				)
				,
				'onselldetails' => array(
					'name'    => 'onsell-details',
					'title'   => 'On Sell Details',
					'content' => ''
				)
				,
				'searchpackages' => array(
					'name'    => 'search-packages',
					'title'   => 'Search Packages',
					'content' => ''
				),
				'packagesdetails' => array(
					'name'    => 'packages-details',
					'title'   => 'Packages Details',
					'content' => ''
				),
				'payment' => array(
					'name'    => 'payment',
					'title'   => 'Payment Details',
					'content' => ''
				),
				'cartdetails' => array(
					'name'    => 'cartdetails',
					'title'   => 'Cart Details',
					'content' => ''
				),
				'genericrequest' => array(
					'name'    => 'genericrequest',
					'title'   => 'Generic Request',
					'content' => ''
				)
			)
		);
		
		//wpml plugin
		if(defined( 'ICL_SITEPRESS_VERSION' ) && !ICL_PLUGIN_INACTIVE ){
			global $sitepress;
			$languages = $sitepress->get_active_languages();
			$wpml_element_type = apply_filters( 'wpml_element_type', 'page' );
			$default_lang = apply_filters('wpml_default_language', NULL );

            foreach ( $pages as $key => $page ) {

				$currposts = [];
                foreach ( $languages as $key => $language ) {
                    $currName = bfi_get_translated_text($page['name'], 'Page slug', 'bfi',  BFI()->return_lang_locale_mapping($language['code']));
                    $currTitle = bfi_get_translated_text($page['title'], 'Page slug', 'bfi',  BFI()->return_lang_locale_mapping($language['code']));
					$currPageId = BFI_Install::bfi_create_page( esc_sql( $currName ), 'bookingfor_' . $language['code'] . '_' . $page['name'] .  '_page_id', $currTitle, $page['content'], ! empty( $page['parent'] ) ? bfi_get_page_id( $page['parent'] ) : '' );
                       
                    $currposts[$language['code']] = $currPageId;
                }
				$get_language_args = array('element_id' => $currposts[$default_lang], 'element_type' => 'page' );
				$original_post_language_info = apply_filters( 'wpml_element_language_details', null, $get_language_args );
 
				foreach ( $currposts as $key => $currpost ) {
//					if ($key!=$default_lang) {
						$set_language_args = array(
							'element_id'    => $currpost,
							'element_type'  => $wpml_element_type,
							'trid'   => $original_post_language_info->trid,
							'language_code'   => $key,
							'source_language_code' => $original_post_language_info->language_code
						);

						do_action( 'wpml_set_element_language_details', $set_language_args );
					    
//					}
				}

            }
        }

		//polylang plugin
        if(defined( 'POLYLANG_VERSION' ) ){
			global $polylang;
			$languages = pll_languages_list();
			$usedTranslation = array();
            foreach ( $pages as $key => $page ) {
                $currposts = [];
   				foreach ( $languages as $key => $language ) {
                    $currName = bfi_get_translated_text($page['name'], 'Page slug', 'bfi',  BFI()->return_lang_locale_mapping($language));
                    $currTitle = bfi_get_translated_text($page['title'], 'Page slug', 'bfi',  BFI()->return_lang_locale_mapping($language));
                    if (! in_array($currName, $usedTranslation)){
						
						array_push($usedTranslation, $currName);
						$currPageId = BFI_Install::bfi_create_page( esc_sql( $currName ), 'bookingfor_' . $language . '_' . $page['name'] .  '_page_id', $currTitle, $page['content'], ! empty( $page['parent'] ) ? bfi_get_page_id( $page['parent'] ) : '' );
					  
						if (function_exists('pll_set_post_language') && function_exists('pll_save_post_translations')  ) {
							pll_set_post_language($currPageId, $language );
							$currposts[$language] = $currPageId;
						}
					}
                }
				
                pll_save_post_translations($currposts);
				
            }
			
        }

		return true;
	}

	function pll_translate_string( $string, $lang ) {
		static $cache; // Cache object to avoid loading the same translations object several times

		if ( empty( $cache ) ) {
			$cache = new PLL_Cache();
		}

		if ( false === $mo = $cache->get( $lang ) ) {
			$mo = new PLL_MO();
			$mo->import_from_db( PLL()->model->get_language( $lang ) );
			$cache->set( $lang, $mo );
		}

		return $mo->translate( $string );
	}

	function bfi_delete_files_cache($cachedir) {
	
		if (!file_exists ($cachedir)) {
			return false;
		}

		$dir = trailingslashit($cachedir);

		if ( is_dir( $dir ) && $dh = @opendir( $dir ) ) {
			while ( ( $file = readdir( $dh ) ) !== false ) {
				if ( $file != '.' && $file != '..' && $file != '.htaccess' && is_file( $dir . $file ) )
					@unlink( $dir . $file );
			}
			closedir( $dh );
			@rmdir( $dir );
?>
<div class="updated notice">
    <p><?php _e('Cache are cleaned', 'bfi') ?></p>
</div>
<?php 
		}
		return true;

	}
	
	public function display_bfi_subscription_key_element()
	{
		$subscriptionkey= get_option('bfi_subscription_key', '');
		$enablesubscriptiontest= get_option('bfi_enablesubscriptiontest_key', 0);
		$subscriptionkeydemo = get_option('bfi_subscriptiondemo_key', '');
		if ($enablesubscriptiontest && !empty($subscriptionkeydemo) ) {
			$subscriptionkey = $subscriptionkeydemo;
		}
	$constbfi = '<?php' . "\n" . "$" . "key='" . $subscriptionkey . "';\n";
	$constbfi = $constbfi . "$" . "apikey='" . get_option('bfi_api_key'). "';\n" ;
	$constbfi = $constbfi . "$" . "label='" . get_option('bfi_form_key'). "';\n";
	$constbfi = $constbfi . "\n" . '?>';
	
	$bfifile = BFI()->plugin_path() .  DIRECTORY_SEPARATOR . 'includes'.  DIRECTORY_SEPARATOR  ."bfi_const.php";
	file_put_contents($bfifile, $constbfi);
		?>
			<input type="text" name="bfi_subscription_key" id="bfi_subscription_key" value="<?php echo get_option('bfi_subscription_key'); ?>"  style="line-height:normal;" required/>
		<?php
	}
	public function display_bfi_enablesubscriptiontest_key_element()
	{
				
		?>
			<input type="checkbox" id="bfi_enablesubscriptiontest_key" name="bfi_enablesubscriptiontest_key" value="1" <?php checked(get_option('bfi_enablesubscriptiontest_key',0), 1, true ); ?> />
		<?php
	}
	public function display_bfi_subscriptiondemo_key_element()
	{
		?>
			<input type="text" name="bfi_subscriptiondemo_key" id="bfi_subscriptiondemo_key" value="<?php echo get_option('bfi_subscriptiondemo_key'); ?>"  style="line-height:normal;" />
		<?php
	}
	public function display_bfi_api_key_element()
	{
		?>
			<textarea type="text" name="bfi_api_key" id="bfi_api_key" rows="6" cols="50" style="line-height:normal;" required><?php echo get_option('bfi_api_key'); ?></textarea>
		<?php
	}
	public function display_bfi_setting_key_element()
	{
		?>
			<textarea type="text" name="bfi_setting_key" id="bfi_api_key" rows="6" cols="50" style="line-height:normal;" required><?php echo get_option('bfi_setting_key'); ?></textarea>
		<?php
	}

	public function display_bfi_itemperpage_key_element()
	{
		?>
			<input name="bfi_itemperpage_key" id="bfi_itemperpage_key" value="<?php echo get_option('bfi_itemperpage_key',10); ?>"  style="line-height:normal;" 
			type="number" style="width:50px;" class="" placeholder="" min="5" man="20" step="1" />
		<?php
	}

	public function display_bfi_maxqtSelectable_key_element(){
		?>
			<input name="bfi_maxqtselectable_key" id="bfi_maxqtselectable_key" value="<?php echo get_option('bfi_maxqtselectable_key',20); ?>"  style="line-height:normal;" 
				type="number" style="width:50px;" class="" placeholder="" min="0" man="50" step="1" />
	<?php
	}

	public function display_bfi_form_key_element()
	{
		?>
			<input type="text" name="bfi_form_key" id="bfi_form_key" value="<?php echo get_option('bfi_form_key',site_url()); ?>"  style="line-height:normal;" />
		<?php
	}

	public function display_bfi_form_startdate_element()
	{
		?>
			<input type="text" name="bfi_form_startdate" id="bfi_form_startdate" value="<?php echo get_option('bfi_form_startdate',""); ?>"  style="line-height:normal;" />
		<?php
	}

	public function display_bfi_ssllogo_key_element()
	{
		?>
			<textarea type="text" name="bfi_ssllogo_key" id="bfi_ssllogo_key" rows="6" cols="50" style="line-height:normal;"><?php echo get_option('bfi_ssllogo_key'); ?></textarea>
			<br />(html code)
		<?php
	}

	public function display_bfi_useproxy_key_element()
	{
		?>
			<input type="checkbox" id="bfi_useproxy_key" name="bfi_useproxy_key" value="1" <?php checked(get_option('bfi_useproxy_key',0), 1, true ); ?> />
		<?php
	}

	public function display_bfi_usessl_key_element()
	{
		?>
			<input type="checkbox" id="bfi_usessl_key" name="bfi_usessl_key" value="1" <?php checked(get_option('bfi_usessl_key',0), 1, true ); ?> />
		<?php
	}

	public function display_bfi_isportal_key_element()
	{
		?>
			<input type="checkbox" id="bfi_isportal_key" name="bfi_isportal_key" value="1" <?php checked(get_option('bfi_isportal_key',1), 1, true ); ?> />
		<?php
	}
	
	public function display_bfi_showdata_key_element()
	{
		?>
			<input type="checkbox" id="bfi_showdata_key" name="bfi_showdata_key" value="1" <?php checked(get_option('bfi_showdata_key',1), 1, true ); ?> />
		<?php
	}

	public function display_bfi_sendtocart_key_element()
	{
		?>
			<input type="checkbox" id="bfi_sendtocart_key" name="bfi_sendtocart_key" value="1" <?php checked(get_option('bfi_sendtocart_key',0), 1, true ); ?> />
		<?php
	}

	public function display_bfi_showbadge_key_element()
	{
		?>
			<input type="checkbox" id="bfi_showbadge_key" name="bfi_showbadge_key" value="1" <?php checked(get_option('bfi_showbadge_key',1), 1, true ); ?> />
		<?php
	}

	public function display_bfi_enablecoupon_key_element()
	{
		?>
			<input type="checkbox" id="bfi_enablecoupon_key" name="bfi_enablecoupon_key" value="1" <?php checked(get_option('bfi_enablecoupon_key',0), 1, true ); ?> />
		<?php
	}

	public function display_bfi_showlogincart_key_element()
	{
		?>
			<input type="checkbox" id="bfi_showlogincart_key" name="bfi_showlogincart_key" value="1" <?php checked(get_option('bfi_showlogincart_key',0), 1, true ); ?> />
		<?php
	}
	public function display_bfi_showadvancesetting_key_element()
	{
		?>
			<input type="checkbox" id="bfi_showadvancesetting_key" name="bfi_showadvancesetting_key" value="1" <?php checked(get_option('bfi_showadvancesetting_key',0), 1, true ); ?> />
		<?php
	}


	public function display_bfi_enalbleothermerchantsresult_element()
	{
		?>
			<input type="checkbox" id="bfi_enalbleothermerchantsresult_key" name="bfi_enalbleothermerchantsresult" value="1" <?php checked(get_option('bfi_enalbleothermerchantsresult',0), 1, true ); ?> />
		<?php
	}
	public function display_bfi_disableinfoform_element()
	{
		?>
			<input type="checkbox" id="bfi_disableinfoform_key" name="bfi_disableinfoform" value="1" <?php checked(get_option('bfi_disableinfoform',0), 1, true ); ?> />
		<?php
	}
	public function display_bfi_enableresourcefilter_element()
	{
		?>
			<input type="checkbox" id="bfi_enableresourcefilter_key" name="bfi_enableresourcefilter" value="1" <?php checked(get_option('bfi_enableresourcefilter',0), 1, true ); ?> />
		<?php
	}

	public function display_bfi_urlproxy_key_element()
	{
		?>
			<input type="text" name="bfi_urlproxy_key" id="bfi_urlproxy_key" value="<?php echo get_option('bfi_urlproxy_key','127.0.0.1:8888'); ?>"  style="line-height:normal;" />
		<?php
	}
	
	public function display_bfi_enablegooglemapsapi_key_element()
	{
				
		?>
			<input type="checkbox" id="bfi_enablegooglemapsapi_key" name="bfi_enablegooglemapsapi" value="1" <?php checked(get_option('bfi_enablegooglemapsapi',0), 1, true ); ?> />
		<?php
	}

	public function display_bfi_posx_key_element()
	{
		?>
			<input type="text" name="bfi_posx_key" id="bfi_posx_key" value="<?php echo get_option('bfi_posx_key'); ?>"  style="line-height:normal;" />
		<?php
	}
	public function display_bfi_posy_key_element()
	{
		?>
			<input type="text" name="bfi_posy_key" id="bfi_posy_key" value="<?php echo get_option('bfi_posy_key'); ?>"  style="line-height:normal;" />
		<?php
	}
	public function display_bfi_startzoom_key_element()
	{
		?>
		<select id="bfi_startzoom_key" name="bfi_startzoom_key">
				<?php
				foreach (range(5, 17) as $number) {
					?> <option value="<?php echo $number ?>" <?php selected( get_option('bfi_startzoom_key',15), $number ); ?>><?php echo $number ?></option><?php
				}
				?>
		</select>
		<?php
	}

	public function display_bfi_openstreetmap_key_element(){
		?>
		<select id="bfi_openstreetmap_key" name="bfi_openstreetmap">
			<option value="0" <?php echo get_option('bfi_openstreetmap',0) == 0 ? "selected" : "" ?>>Google Maps</option>
			<option value="1" <?php echo get_option('bfi_openstreetmap',1) == 1 ? "selected" : "" ?>>Openstreetmap</option>
		</select>
	<?php
	}

	public function display_bfi_googlemapskey_key_element()
	{
		?>
			<textarea type="text" name="bfi_googlemapskey_key" id="bfi_googlemapskey_key" rows="6" cols="50" style="line-height:normal;"><?php echo get_option('bfi_googlemapskey_key'); ?></textarea>
			<br />
			<!-- Get a key <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank" >https://developers.google.com/maps/documentation/javascript/get-api-key</a> 
			<br />Enable 'Google Static Maps API' and 'Google Maps JavaScript API' -->
			<div class="bfi-alert bfi-alert-info"><h4>Google Map</h4>Per utilizzare le mappe di google e' necessario utilizzare una Google map API key, vai su <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">https://developers.google.com/maps/documentation/javascript/get-api-key</a> per crearne una e abilitare le 'Google Static Maps API' e 'Google Maps JavaScript API'</div>			
			<div class="bfi-alert bfi-alert-danger"><b>Reminder:</b> To use the Maps JavaScript API, you must get an API key and you must enable billing. You can enable billing when you get your API key (see the Quick guide) or as a separate process (see Usage and Billing).</div>
		<?php
	}

	public function display_bfi_googlerecaptcha_version_element(){
		?>
		<select id="bfi_googlerecaptcha_version" name="bfi_googlerecaptcha_version">
			<option value="V2" <?php echo get_option('bfi_googlerecaptcha_version','V2') == 'V2' ? "selected" : "" ?>>V2</option>
			<option value="V3" <?php echo get_option('bfi_googlerecaptcha_version','V2') == 'V3' ? "selected" : "" ?>>V3</option> 
		</select>
	<?php
	}

	public function display_bfi_googlerecaptcha_key_element()
	{
		?>
			<input type="text" name="bfi_googlerecaptcha_key" id="bfi_googlerecaptcha_key" value="<?php echo get_option('bfi_googlerecaptcha_key'); ?>"  style="line-height:normal;" /> <a href="https://www.google.com/recaptcha/admin" target="_blank" >Get reCAPTCHA</a> 
		<?php
	}
	public function display_bfi_googlerecaptcha_secret_key_element()
	{
		?>
			<input type="text" name="bfi_googlerecaptcha_secret_key" id="bfi_googlerecaptcha_secret_key" value="<?php echo get_option('bfi_googlerecaptcha_secret_key'); ?>"  style="line-height:normal;" /> 
		<?php
	}
	
	

	public function display_bfi_defaultdisplaylist_key_element(){
		?>
		<select id="bfi_defaultdisplaylist_key" name="bfi_defaultdisplaylist_key">
			<option value="0" <?php echo get_option('bfi_defaultdisplaylist_key',0) == 0 ? "selected" : "" ?>>List</option>
			<option value="1" <?php echo get_option('bfi_defaultdisplaylist_key',1) == 1 ? "selected" : "" ?>>Grid</option>
		</select>
	<?php
	}

	public function display_bfi_googlerecaptcha_theme_key_element(){
		?>
		<select id="bfi_googlerecaptcha_theme_key" name="bfi_googlerecaptcha_theme_key">
			<option value="light" <?php echo get_option('bfi_googlerecaptcha_theme_key','light') == 'light' ? "selected" : "" ?>>light</option>
			<option value="dark" <?php echo get_option('bfi_googlerecaptcha_theme_key','light') == 'dark' ? "selected" : "" ?>>dark</option>
		</select>
	<?php
	}
	
	public function display_bfi_googletagmanager_key_element(){
		?>
		<select id="bfi_googletagmanager_key" name="bfi_googletagmanager">
			<option value="0" <?php echo get_option('bfi_googletagmanager',0) == 0 ? "selected" : "" ?>>Google Analytics </option>
			<option value="1" <?php echo get_option('bfi_googletagmanager',0) == 1 ? "selected" : "" ?>>Google Data Layer (beta)</option>
		</select>
		
		<div class="bfi-alert bfi-alert-info"><h4>Google Tag Manager</h4> Google Data Layer:<a href="https://cdnbookingfor.blob.core.windows.net/bf6/scripts/gtm/GTM-container_bfeec.json" >scarica template di esempio</a></div>			

	<?php
	}
	
	public function display_bfi_gaenabled_key_element()
	{
		?>
			<input type="checkbox" id="bfi_gaenabled_key" name="bfi_gaenabled_key" value="1" <?php checked(get_option('bfi_gaenabled_key',0), 1, true ); ?> />
		<?php
	}

	public function display_bfi_gaaccount_key_element()
	{
		?>
			<input type="text" name="bfi_gaaccount_key" id="bfi_gaaccount_key" value="<?php echo get_option('bfi_gaaccount_key'); ?>"  style="line-height:normal;" /> 
		<?php
	}

	public function display_bfi_criteoenabled_key_element()
	{
		?>
			<input type="checkbox" id="bfi_criteoenabled_key" name="bfi_criteoenabled_key" value="1" <?php checked(get_option('bfi_criteoenabled_key',0), 1, true ); ?> />
		<?php
	}

	public function display_bfi_eecenabled_key_element()
	{
		?>
			<input type="checkbox" id="bfi_eecenabled_key" name="bfi_eecenabled_key" value="1" <?php checked(get_option('bfi_eecenabled_key',0), 1, true ); ?> />
		<?php
	}

	public function display_bfi_fbapienabled_key_element()
	{
		?>
			<input type="checkbox" id="bfi_fbapienabled_key" name="bfi_fbapienabled_key" value="1" <?php checked(get_option('bfi_fbapienabled_key',0), 1, true ); ?> />

			<div class="bfi-alert bfi-alert-info">Attivando questa opzioni alcuni dati personali (IP, Email) dei tuoi utenti saranno trasferiti ai server Facebook negli stati uniti. Verifica che le tue policy privacy siano adeguate a questo tipo di trattamento
			<h4>Google Tag Manager</h4> Facebook Pixel:<a href="https://cdnbookingfor.blob.core.windows.net/bf6/scripts/gtm/GTM-container_FbPixel.json" >scarica template di esempio</a>
			</div>	
		<?php
	}

	public function display_bfi_fbpixelid_key_element()
	{
		?>
			<input type="text" name="bfi_fbpixelid_key" id="bfi_fbpixelid_key" value="<?php echo get_option('bfi_fbpixelid_key'); ?>"  style="line-height:normal;" /> 
		<?php
	}
	public function display_bfi_fbtoken_key_element()
	{
		?>
			<input type="text" name="bfi_fbtoken_key" id="bfi_fbtoken_key" value="<?php echo get_option('bfi_fbtoken_key'); ?>"  style="line-height:normal;" /> 
		<?php
	}

	public function display_bfi_fbtesteventcode_key_element()
	{
		?>
			<input type="text" name="bfi_fbtesteventcode_key" id="bfi_fbtesteventcode_key" value="<?php echo get_option('bfi_fbtesteventcode_key'); ?>"  style="line-height:normal;" /> 
		<?php
	}

	public function display_bfi_enablecache_key_element()
	{
		?>
			<span><input type="checkbox" id="bfi_enablecache_key" name="bfi_enablecache_key" value="1" <?php checked(get_option('bfi_enablecache_key',1), 1, true ); ?> /></span>
			<div class="bfi-alert bfi-alert-danger"><b>Reminder:</b> Disabilitare solo per test, un rallentamento della velocità dei risultati è normale in quanto nulla, in caso si dovessero riscontrare abusi verrà sospeso l'account.</div>

		<?php
	}

	public function display_bfi_cache_time_key_element()
	{
		?>
<!-- select fisso con 8h 24h 2gg 7gg 20gg 30gg -->
<?php 
		$currentCache_time = get_option('bfi_cache_time_key',86400);
		$cache_times = array(28800 => __('8h', 'bfi'),
						86400 => __('24h', 'bfi'),
						172800 => __('2gg', 'bfi'),   
						604800 => __('7gg', 'bfi'),
						1728000 => __('20gg', 'bfi'),
						2592000 => __('30gg', 'bfi')
					);
?>

		<select id="bfi_cache_time_key" name="bfi_cache_time_key">
		<?php 
		foreach ($cache_times as $cache_time => $cache_times_text ) {
		?>
			<option value="<?php echo $cache_time ?>" <?php echo $currentCache_time == $cache_time ? "selected" : "" ?>><?php echo $cache_times[$cache_time] ?></option>
		<?php 
		}
		?>
		</select>

		<?php
	}
	public function display_bfi_cache_time_bot_key_element()
	{
		?>
<!-- select fisso con 8h 24h 2gg 7gg 20gg 30gg -->
<?php 
		$currentCache_time = get_option('bfi_cache_time_bot_key',1728000);
		$cache_times = array(
						86400 => __('24h', 'bfi'),
						172800 => __('2gg', 'bfi'),   
						604800 => __('7gg', 'bfi'),
						1728000 => __('20gg', 'bfi'),
						2592000 => __('30gg', 'bfi')
					);
?>

		<select id="bfi_cache_time_bot_key" name="bfi_cache_time_bot_key">
		<?php 
		foreach ($cache_times as $cache_time => $cache_times_text ) {
		?>
			<option value="<?php echo $cache_time ?>" <?php echo $currentCache_time == $cache_time ? "selected" : "" ?>><?php echo $cache_times[$cache_time] ?></option>
		<?php 
		}
		?>
		</select>

		<?php
	}

	public function display_bfi_adultsage_key_element(){
		?>
		<select id="bfi_adultsage_key" name="bfi_adultsage_key">
			<?php
			foreach (range(0, 120) as $number) {
				?> <option value="<?php echo $number ?>" <?php selected( get_option('bfi_adultsage_key',18), $number ); ?>><?php echo $number ?></option><?php
			}
			?>
		</select>
	<?php
	}

	public function display_bfi_adultsqt_key_element(){
		?>
		<select id="bfi_adultsqt_key" name="bfi_adultsqt_key">
			<?php
			foreach (range(0, 12) as $number) {
				?> <option value="<?php echo $number ?>" <?php selected( get_option('bfi_adultsqt_key',2), $number ); ?>><?php echo $number ?></option><?php
			}
			?>
		</select>
	<?php
	}

	public function display_bfi_childrensage_key_element(){
		?>
		<select id="bfi_childrensage_key" name="bfi_childrensage_key">
			<?php
			foreach (range(0, 25) as $number) {
				?> <option value="<?php echo $number ?>" <?php selected( get_option('bfi_childrensage_key',12), $number ); ?>><?php echo $number ?></option><?php
			}
			?>
		</select>
	<?php
	}

	public function display_bfi_senioresage_key_element(){
		?>
		<select id="bfi_senioresage_key" name="bfi_senioresage_key">
			<?php
			foreach (range(40, 120) as $number) {
				?> <option value="<?php echo $number ?>" <?php selected( get_option('bfi_senioresage_key',65), $number ); ?>><?php echo $number ?></option><?php
			}
			?>
		</select>
	<?php
	}


	public function display_bfi_currentcurrency_key_element(){
	$currency_text = array('978' => __('Euro', 'bfi'),
					'191' => __('Kune', 'bfi'),
					'840' => __('U.S. dollar', 'bfi'),   
					'392' => __('Japanese yen', 'bfi'),
					'124' => __('Canadian dollar', 'bfi'),
					'36' => __('Australian dollar', 'bfi'),
					'643' => __('Russian Ruble', 'bfi'),  
					'200' => __('Czech koruna', 'bfi'),
					'702' => __('Singapore dollar', 'bfi'),  
					'826' => __('Pound sterling ', 'bfi')                            
				);
		$defaultCurrency = bfi_get_defaultCurrency();
		$currencyExchanges = bfi_get_currencyExchanges();
		?>
		<select id="bfi_currentcurrency_key" name="bfi_currentcurrency_key">
		<?php 
		if (!empty($currencyExchanges)) {
			foreach ($currencyExchanges as $currencyExchangeCode => $currencyExchange ) {
			?>
				<option value="<?php echo $currencyExchangeCode ?>" <?php echo get_option('bfi_currentcurrency_key',$defaultCurrency) == $currencyExchangeCode ? "selected" : "" ?>><?php echo $currency_text[$currencyExchangeCode] ?></option>
			<?php 
			}
		}
		?>
		</select>
		<?php
	}
	public function display_bfi_altcurrency_key_element(){
	$currency_text = array('978' => __('Euro', 'bfi'),
					'191' => __('Kune', 'bfi'),
					'840' => __('U.S. dollar', 'bfi'),   
					'392' => __('Japanese yen', 'bfi'),
					'124' => __('Canadian dollar', 'bfi'),
					'36' => __('Australian dollar', 'bfi'),
					'643' => __('Russian Ruble', 'bfi'),  
					'200' => __('Czech koruna', 'bfi'),
					'702' => __('Singapore dollar', 'bfi'),  
					'826' => __('Pound sterling ', 'bfi')                            
				);
		$defaultCurrency = bfi_get_defaultCurrency();
		$currencyExchanges = bfi_get_currencyExchanges();
		?>
		<select id="bfi_altcurrency_key" name="bfi_altcurrency_key">
				<option value="" <?php echo get_option('bfi_altcurrency_key',"") == "" ? "selected" : "" ?>>Nessuna</option>
		<?php 
		if (!empty($currencyExchanges)) {
			foreach ($currencyExchanges as $currencyExchangeCode => $currencyExchange ) {
			?>
				<option value="<?php echo $currencyExchangeCode ?>" <?php echo get_option('bfi_altcurrency_key',"") == $currencyExchangeCode ? "selected" : "" ?>><?php echo $currency_text[$currencyExchangeCode] ?></option>
			<?php 
			}
		}
		?>
		</select>
		<?php
	}

	public function display_bfi_enablegenericsearchdetails_key_element()
	{
		?>
			<input type="checkbox" id="bfi_enablegenericsearchdetails_key" name="bfi_enablegenericsearchdetails_key" value="1" <?php checked(get_option('bfi_enablegenericsearchdetails_key',1), 1, true ); ?> />
		<?php
	}

	public function display_bfi_showcontactbanner_key_element()
	{
		?>
			<input type="checkbox" id="bfi_showcontactbanner_key" name="bfi_showcontactbanner_key" value="1" <?php checked(get_option('bfi_showcontactbanner_key',0), 1, true ); ?> />
		<?php
	}
	public function display_bfi_showcontactbannerform_key_element()
	{
		?>
			<input type="checkbox" id="bfi_showcontactbannerform_key" name="bfi_showcontactbannerform_key" value="1" <?php checked(get_option('bfi_showcontactbannerform_key',0), 1, true ); ?> />
		<?php
	}

	public function display_bfi_contactbannerform_categories_key_element()
	{
		$allUnitCategories =  BFCHelper::GetProductCategoryForSearch($language,1);
		$unitCategories = array();
		if (!empty($allUnitCategories))
		{
			foreach($allUnitCategories as $unitCategory)
			{
				$unitCategories[$unitCategory->ProductCategoryId] = $unitCategory->Name;
			}
		}
		$unitCategoriesSelected = ! empty(get_option('bfi_contactbannerform_categories_key'))? get_option('bfi_contactbannerform_categories_key') : array();
		if(!empty($unitCategories)){   
		?>
			<div class="bfi-alert bfi-alert-info">Scegli le categorie da visualizzare</div>			
			<br />
		<?php 
					printf(
						'<select multiple="multiple" name="%s[]" id="%s" class="select2full bfi-select2">',
						'bfi_contactbannerform_categories_key',
						'bfi_contactbannerform_categories_key'
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
		} 
	}


	public function display_bfi_contactbannerphone_key_element()
	{
		?>
			<div class="bfi-alert bfi-alert-info">Lasciare vuoto per non visualizzarlo (è consentito solo un numero di telefono senza spazi e eventualmente il "+" iniziale)</div>			
			<br />
			<input type="tel" pattern="[+]?\d*" name="bfi_contactbannerphone_key" id="bfi_contactbannerphone_key" value="<?php echo get_option('bfi_contactbannerphone_key'); ?>"  style="line-height:normal;" />
		<?php
	}
	public function display_bfi_contactbannerphonewhatsapp_key_element()
	{
		?>
			<div class="bfi-alert bfi-alert-info">Lasciare vuoto per non visualizzarlo (è consentito solo un numero di telefono senza spazi e eventualmente il "+" iniziale)</div>			
			<br />
			<input type="tel" pattern="[+]?\d*" name="bfi_contactbannerphonewhatsapp_key" id="bfi_contactbannerphonewhatsapp_key" value="<?php echo get_option('bfi_contactbannerphonewhatsapp_key'); ?>"  style="line-height:normal;" />
		<?php
	}

	public function display_bfi_contactbanneremail_key_element()
	{
		?>
			<div class="bfi-alert bfi-alert-info">Lasciare vuoto per non visualizzarlo</div>			
			<br />
			<input type="email" name="bfi_contactbanneremail_key" id="bfi_contactbanneremail_key" value="<?php echo get_option('bfi_contactbanneremail_key'); ?>"  style="line-height:normal;" />
		<?php
	}

	public function display_bfi_contactbannerpage_key_element()
	{
		?>
			<div class="bfi-alert bfi-alert-info">Scegli la pagina dove inviare l'utente per la chat</div>			
			<br />
		<?php
		  $argsPage = array(
			  'depth' => 0,
			  'child_of' => 0, 
			  'echo' => 1, 
			  'exclude' => '', 
			  'exclude_tree' => '',
			  'hierarchical' => 1, 
			  'class' => 'widefat select2full',
			  'name' => 'bfi_contactbannerpage_key',
			  'id' => 'bfi_contactbannerpage_key',
			  'post_type' => 'page',
			  'selected' => get_option('bfi_contactbannerpage_key',''),
			  'post_status' => 'publish',
			  'sort_column' => 'post_title',
			  'sort_order' => 'ASC',
			  'show_option_none' => 'nessuna', 
		  );
		wp_dropdown_pages($argsPage);
	}

	
	public function display_bfi_showeventbanner_key_element()
	{
		?>
			<input type="checkbox" id="bfi_showeventbanner_key" name="bfi_showeventbanner_key" value="1" <?php checked(get_option('bfi_showeventbanner_key',1), 1, true ); ?> />
		<?php
	}
	public function display_bfi_showeventbannerrepeated_key_element()
	{
		?>
			<input type="checkbox" id="bfi_showeventbannerrepeated_key" name="bfi_showeventbannerrepeated_key" value="1" <?php checked(get_option('bfi_showeventbannerrepeated_key',1), 1, true ); ?> />
		<?php
	}

	public function display_bfi_showeventbannerevery_key_element(){
		?>
		<select id="bfi_showeventbannerevery_key" name="bfi_showeventbannerevery_key">
			<?php
			foreach (range(2, 7) as $number) {
				?> <option value="<?php echo $number ?>" <?php selected( get_option('bfi_showeventbannerevery_key',5), $number ); ?>><?php echo $number ?></option><?php
			}
			?>
		</select>
	<?php
	}

	public function display_bfi_fields()
	{
		add_settings_section(
			"section",		// ID used to identify this section and with which to register options
			"Settings", // Title to be displayed on the administration page
			null,			// Callback used to render the description of the section
			"bfi-options"	// Page on which to add this section of options
		);
		
		add_settings_field(
			"bfi_subscription_key",									// ID used to identify the field throughout the theme
			"Subscription Key *",									// The label to the left of the option interface element
			array( $this, 'display_bfi_subscription_key_element'),	// The name of the function responsible for rendering the option interface
			"bfi-options",											// The page on which this option will be displayed
			"section"												// The name of the section to which this field belongs
		);

		add_settings_field("bfi_enablesubscriptiontest_key", "Enable Subscription DEMO",  array( $this, 'display_bfi_enablesubscriptiontest_key_element'), "bfi-options", "section");
		add_settings_field("bfi_subscriptiondemo_key", "Subscription Key DEMO",  array( $this, 'display_bfi_subscriptiondemo_key_element'), "bfi-options", "section");
		
		add_settings_field("bfi_api_key", "API Key *",  array( $this, 'display_bfi_api_key_element'), "bfi-options", "section");
		add_settings_field("bfi_setting_key", "Setting Key *",  array( $this, 'display_bfi_setting_key_element'), "bfi-options", "section");
		add_settings_field("bfi_form_key", "Referrer *",  array( $this, 'display_bfi_form_key_element'), "bfi-options", "section");		
//		add_settings_field("bfi_form_startdate", "Start date (dd/mm/yyyy)",  array( $this, 'display_bfi_form_startdate_element'), "bfi-options", "section");		
//		add_settings_field("bfi_currentcurrency_key", "Default currency",  array( $this, 'display_bfi_currentcurrency_key_element'), "bfi-options", "section");
//		add_settings_field("bfi_altcurrency_key", "Alternative currency",  array( $this, 'display_bfi_altcurrency_key_element'), "bfi-options", "section");
//		add_settings_field("bfi_itemperpage_key", "Item per page",  array( $this, 'display_bfi_itemperpage_key_element'), "bfi-options", "section");
//		add_settings_field("bfi_maxqtselectable_key", "Max selectable item",  array( $this, 'display_bfi_maxqtSelectable_key_element'), "bfi-options", "section");
//		add_settings_field("bfi_defaultdisplaylist_key", "Default list view",  array( $this, 'display_bfi_defaultdisplaylist_key_element'), "bfi-options", "section");
		add_settings_field("bfi_isportal_key", "Portal", array( $this, 'display_bfi_isportal_key_element'), "bfi-options", "section");
//		add_settings_field("bfi_enalbleothermerchantsresult", "Check availability at other facilities", array( $this, 'display_bfi_enalbleothermerchantsresult_element'), "bfi-options", "section");
//		add_settings_field("bfi_disableinfoform", "Disable info form in detail page", array( $this, 'display_bfi_disableinfoform_element'), "bfi-options", "section");
//		add_settings_field("bfi_enableresourcefilter", "Enable filter in details", array( $this, 'display_bfi_enableresourcefilter_element'), "bfi-options", "section");

//		add_settings_field("bfi_showdata_key", "Show Descriptions on lists", array( $this, 'display_bfi_showdata_key_element'), "bfi-options", "section");
//		add_settings_field("bfi_sendtocart_key", "Send guest directly to cart", array( $this, 'display_bfi_sendtocart_key_element'), "bfi-options", "section");
//		add_settings_field("bfi_showbadge_key", "Show badge number items on cart", array( $this, 'display_bfi_showbadge_key_element'), "bfi-options", "section");
//		add_settings_field("bfi_enablecoupon_key", "Enable coupon feature", array( $this, 'display_bfi_enablecoupon_key_element'), "bfi-options", "section");
//		add_settings_field("bfi_showlogincart_key", "Show Login on cart", array( $this, 'display_bfi_showlogincart_key_element'), "bfi-options", "section");
		add_settings_field("bfi_showadvancesetting_key", "Show advance setting", array( $this, 'display_bfi_showadvancesetting_key_element'), "bfi-options", "section");
		
//		add_settings_section("sectionmaps", "Maps Settings", null, "bfi-options-maps");
//		add_settings_field("bfi_enablegooglemapsapi", "Show Maps",  array( $this, 'display_bfi_enablegooglemapsapi_key_element'), "bfi-options-maps", "sectionmaps");
//		add_settings_field("bfi_posx_key", "Longitude *",  array( $this, 'display_bfi_posx_key_element'), "bfi-options-maps", "sectionmaps");
//		add_settings_field("bfi_posy_key", "Latitude *",  array( $this, 'display_bfi_posy_key_element'), "bfi-options-maps", "sectionmaps");
//		add_settings_field("bfi_startzoom_key", "Start Zoom",  array( $this, 'display_bfi_startzoom_key_element'), "bfi-options-maps", "sectionmaps");
//		add_settings_field("bfi_openstreetmap", "Map System",  array( $this, 'display_bfi_openstreetmap_key_element'), "bfi-options-maps", "sectionmaps");
//		add_settings_field("bfi_googlemapskey_key", "Google maps key *",  array( $this, 'display_bfi_googlemapskey_key_element'), "bfi-options-maps", "sectionmaps");
		
		
//		add_settings_section("sectionrecaptcha", "Google recaptcha Settings", null, "bfi-options-recaptcha");
//		add_settings_field("bfi_googlerecaptcha_version", "Version",  array( $this, 'display_bfi_googlerecaptcha_version_element'), "bfi-options-recaptcha", "sectionrecaptcha");
//		add_settings_field("bfi_googlerecaptcha_key", "Site key",  array( $this, 'display_bfi_googlerecaptcha_key_element'), "bfi-options-recaptcha", "sectionrecaptcha");
//		add_settings_field("bfi_googlerecaptcha_secret_key", "Secret key",  array( $this, 'display_bfi_googlerecaptcha_secret_key_element'), "bfi-options-recaptcha", "sectionrecaptcha");
//		add_settings_field("bfi_googlerecaptcha_theme_key", "Theme",  array( $this, 'display_bfi_googlerecaptcha_theme_key_element'), "bfi-options-recaptcha", "sectionrecaptcha");
//
//		add_settings_section("sectionsecurity", "Security settings", null, "bfi-options-security");
//		add_settings_field("bfi_usessl_key", "Use SSL",  array( $this, 'display_bfi_usessl_key_element'), "bfi-options-security", "sectionsecurity");
//		add_settings_field("bfi_ssllogo_key", "Certificate logo",  array( $this, 'display_bfi_ssllogo_key_element'), "bfi-options-security", "sectionsecurity");

//		add_settings_section("sectionperson", "Person Settings", null, "bfi-options-person");
//		add_settings_field("bfi_adultsage_key", "Min adult's age",  array( $this, 'display_bfi_adultsage_key_element'), "bfi-options-person", "sectionperson");
//		add_settings_field("bfi_adultsqt_key", "Preset adults in search",  array( $this, 'display_bfi_adultsqt_key_element'), "bfi-options-person", "sectionperson");
//		add_settings_field("bfi_childrensage_key", "Preset children's age in search",  array( $this, 'display_bfi_childrensage_key_element'), "bfi-options-person", "sectionperson");
//		add_settings_field("bfi_senioresage_key", "Min seniores's age",  array( $this, 'display_bfi_senioresage_key_element'), "bfi-options-person", "sectionperson");
		
		add_settings_section("sectionproxy", "Proxy Settings", null, "bfi-options-proxy");
		add_settings_field("bfi_useproxy_key", "Use Proxy",  array( $this, 'display_bfi_useproxy_key_element'), "bfi-options-proxy", "sectionproxy");
		add_settings_field("bfi_urlproxy_key", "Url's Proxy",  array( $this, 'display_bfi_urlproxy_key_element'), "bfi-options-proxy", "sectionproxy");

//		add_settings_section("sectionanalyitics", "Marketing Settings", null, "bfi-options-analyitics");
//		add_settings_field("bfi_gaenabled_key", "Enable GA Tracking",  array( $this, 'display_bfi_gaenabled_key_element'), "bfi-options-analyitics", "sectionanalyitics");
//		add_settings_field("bfi_googletagmanager", "Implementation",  array( $this, 'display_bfi_googletagmanager_key_element'), "bfi-options-analyitics", "sectionanalyitics");
//		add_settings_field("bfi_gaaccount_key", "Analytics account ID",  array( $this, 'display_bfi_gaaccount_key_element'), "bfi-options-analyitics", "sectionanalyitics");
//		add_settings_field("bfi_eecenabled_key", "Enable Enhanced Ecommerce",  array( $this, 'display_bfi_eecenabled_key_element'), "bfi-options-analyitics", "sectionanalyitics");
		
//		add_settings_field("bfi_fbapienabled_key", "Enable Facebook Api",  array( $this, 'display_bfi_fbapienabled_key_element'), "bfi-options-analyitics", "sectionanalyitics");
//		add_settings_field("bfi_fbpixelid_key", "Facebook Pixel Id",  array( $this, 'display_bfi_fbpixelid_key_element'), "bfi-options-analyitics", "sectionanalyitics");
//		add_settings_field("bfi_fbtoken_key", "Facebook Token",  array( $this, 'display_bfi_fbtoken_key_element'), "bfi-options-analyitics", "sectionanalyitics");
//		add_settings_field("bfi_fbtesteventcode_key", "Facebook TestEventCode (remove on production)",  array( $this, 'display_bfi_fbtesteventcode_key_element'), "bfi-options-analyitics", "sectionanalyitics");
//
//		add_settings_field("bfi_criteoenabled_key", "Enable Criteo",  array( $this, 'display_bfi_criteoenabled_key_element'), "bfi-options-analyitics", "sectionanalyitics");

		add_settings_section("sectionperformance", "Performance Settings", null, "bfi-options-performance");
		add_settings_field("bfi_enablecache_key", "Use Cache",  array( $this, 'display_bfi_enablecache_key_element'), "bfi-options-performance", "sectionperformance");
		add_settings_field("bfi_cache_time_key", "Cache Time",  array( $this, 'display_bfi_cache_time_key_element'), "bfi-options-performance", "sectionperformance");
		add_settings_field("bfi_cache_time_bot_key", "Cache Time for Bot",  array( $this, 'display_bfi_cache_time_bot_key_element'), "bfi-options-performance", "sectionperformance");

//		add_settings_section("sectionsearchresult", "Search Result Settings", null, "bfi-options-searchresult");

//		add_settings_field("bfi_enablegenericsearchdetails_key", "Enable generic search in page details",  array( $this, 'display_bfi_enablegenericsearchdetails_key_element'), "bfi-options-searchresult", "sectionsearchresult");
//		add_settings_field("bfi_showcontactbanner_key", "Show contact banner",  array( $this, 'display_bfi_showcontactbanner_key_element'), "bfi-options-searchresult", "sectionsearchresult");
//		add_settings_field("bfi_showcontactbannerform_key", "Show Contact us",  array( $this, 'display_bfi_showcontactbannerform_key_element'), "bfi-options-searchresult", "sectionsearchresult");
//		add_settings_field("bfi_contactbannerform_categories_key", "Resources' Categories",  array( $this, 'display_bfi_contactbannerform_categories_key_element'), "bfi-options-searchresult", "sectionsearchresult");

//		add_settings_field("bfi_contactbannerphone_key", "Show Call us",  array( $this, 'display_bfi_contactbannerphone_key_element'), "bfi-options-searchresult", "sectionsearchresult");
//		add_settings_field("bfi_contactbannerphonewhatsapp_key", "Show Wathapps",  array( $this, 'display_bfi_contactbannerphonewhatsapp_key_element'), "bfi-options-searchresult", "sectionsearchresult");
//		add_settings_field("bfi_contactbanneremail_key", "Show E-mail us",  array( $this, 'display_bfi_contactbanneremail_key_element'), "bfi-options-searchresult", "sectionsearchresult");
//		add_settings_field("bfi_contactbannerpage_key", "Show Talk to us",  array( $this, 'display_bfi_contactbannerpage_key_element'), "bfi-options-searchresult", "sectionsearchresult");
//		
//		add_settings_field("bfi_showeventbanner_key", "Show event banner",  array( $this, 'display_bfi_showeventbanner_key_element'), "bfi-options-searchresult", "sectionsearchresult");
//		add_settings_field("bfi_showeventbannerrepeated_key", "Event banner is repeated",  array( $this, 'display_bfi_showeventbannerrepeated_key_element'), "bfi-options-searchresult", "sectionsearchresult");
//		add_settings_field("bfi_showeventbannerevery_key", "Show event banner every how many results",  array( $this, 'display_bfi_showeventbannerevery_key_element'), "bfi-options-searchresult", "sectionsearchresult");

		register_setting("section", "bfi_subscription_key");
		register_setting("section", "bfi_enablesubscriptiontest_key");
		register_setting("section", "bfi_subscriptiondemo_key");
		register_setting("section", "bfi_api_key");
		register_setting("section", "bfi_setting_key");
		register_setting("section", "bfi_form_key");
//		register_setting("section", "bfi_form_startdate");
//		register_setting("section", "bfi_currentcurrency_key");
//		register_setting("section", "bfi_altcurrency_key");
//		register_setting("section", "bfi_usessl_key");
//		register_setting("section", "bfi_ssllogo_key");
//		register_setting("section", "bfi_itemperpage_key");
//		register_setting("section", "bfi_maxqtselectable_key");
//		register_setting("section", "bfi_defaultdisplaylist_key");
//		register_setting("section", "bfi_enalbleothermerchantsresult");
//		register_setting("section", "bfi_enableresourcefilter");
//		register_setting("section", "bfi_disableinfoform");
		
		register_setting("section", "bfi_isportal_key");
//		register_setting("section", "bfi_showdata_key");
//		register_setting("section", "bfi_sendtocart_key");
//		register_setting("section", "bfi_showbadge_key");
//		register_setting("section", "bfi_enablecoupon_key");
//		register_setting("section", "bfi_showlogincart_key");
		register_setting("section", "bfi_showadvancesetting_key");
			
//		register_setting("section", "bfi_enablegooglemapsapi");
//		register_setting("section", "bfi_posx_key");
//		register_setting("section", "bfi_posy_key");
//		register_setting("section", "bfi_startzoom_key");
//		register_setting("section", "bfi_openstreetmap");
//		register_setting("section", "bfi_googlemapskey_key");
//
//		register_setting("section", "bfi_googlerecaptcha_version");
//		register_setting("section", "bfi_googlerecaptcha_key");
//		register_setting("section", "bfi_googlerecaptcha_secret_key");
//		register_setting("section", "bfi_googlerecaptcha_theme_key");
//
//		register_setting("section", "bfi_adultsage_key");
//		register_setting("section", "bfi_adultsqt_key");
//		register_setting("section", "bfi_childrensage_key");
//		register_setting("section", "bfi_senioresage_key");
//
		register_setting("section", "bfi_useproxy_key");
		register_setting("section", "bfi_urlproxy_key");

//		register_setting("section", "bfi_gaenabled_key");
//		register_setting("section", "bfi_googletagmanager");
//		register_setting("section", "bfi_gaaccount_key");
//		register_setting("section", "bfi_eecenabled_key");
//
//		register_setting("section", "bfi_fbapienabled_key");
//		register_setting("section", "bfi_fbpixelid_key");
//		register_setting("section", "bfi_fbtoken_key");
//		register_setting("section", "bfi_fbtesteventcode_key");
//
//		register_setting("section", "bfi_criteoenabled_key");

		register_setting("section", "bfi_enablecache_key");
		register_setting("section", "bfi_cache_time_key");
		register_setting("section", "bfi_cache_time_bot_key");

//		register_setting("section", "bfi_enablegenericsearchdetails_key");
//
//		register_setting("section", "bfi_showcontactbanner_key");
//		register_setting("section", "bfi_showcontactbannerform_key");
////		register_setting("section", "bfi_contactbannerform_categories_key");
//		register_setting("section", "bfi_contactbannerphone_key");
//		register_setting("section", "bfi_contactbannerphonewhatsapp_key");
//		register_setting("section", "bfi_contactbanneremail_key");
//		
//		register_setting("section", "bfi_contactbannerpage_key");
//
//		register_setting("section", "bfi_showeventbanner_key");
//		register_setting("section", "bfi_showeventbannerevery_key");
//		register_setting("section", "bfi_showeventbannerrepeated_key");



	}
	
	
	
	/**
	 * Change the admin footer text on BookingFor admin pages.
	 *
	 * @param  string $footer_text
	 * @return string
	 */
	public function admin_footer_text( $footer_text ) {
		if ( ! current_user_can( 'manage_bookingfor' ) ) {
			return;
		}
		$current_screen = get_current_screen();
		$bfi_pages       = bfi_get_screen_ids();

		// Set only wc pages
		$bfi_pages = array_flip( $bfi_pages );
		if ( isset( $bfi_pages['profile'] ) ) {
			unset( $bfi_pages['profile'] );
		}
		if ( isset( $bfi_pages['user-edit'] ) ) {
			unset( $bfi_pages['user-edit'] );
		}
		$bfi_pages = array_flip( $bfi_pages );

		// Check to make sure we're on a BookingFor admin page
		if ( isset( $current_screen->id ) && apply_filters( 'bookingfor_display_admin_footer_text', in_array( $current_screen->id, $bfi_pages ) ) ) {
			// Change the footer text
			if ( ! get_option( 'bookingfor_admin_footer_text_rated' ) ) {
				$footer_text = sprintf( __( 'If you like <strong>BookingFor</strong> please leave us a %s&#9733;&#9733;&#9733;&#9733;&#9733;%s rating. A huge thank you from Bookingfor in advance!', 'bfi' ), '<a href="https://wordpress.org/support/view/plugin-reviews/bookingfor?filter=5#postform" target="_blank" class="bfi-rating-link" data-rated="' . esc_attr__( 'Thanks', 'bfi' ) . '">', '</a>' );
				bfi_enqueue_js( "
					jQuery( 'a.bfi-rating-link' ).click( function() {
						jQuery.post( '" . BFI()->ajax_url() . "', { action: 'bookingfor_rated' } );
						jQuery( this ).parent().text( jQuery( this ).data( 'rated' ) );
					});
				" );
			} else {
				$footer_text = __( 'Thank you for selling with BookingFor.', 'bfi' );
			}
		}

		return $footer_text;
	}
}
endif;
return new BFI_Admin();