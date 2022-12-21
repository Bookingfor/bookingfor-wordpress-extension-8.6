<?php
/*
Plugin Name: BookingFor
Description: BookingFor integration Code for Wordpress With Widgets
Version: 8.6.0
Author: BookingFor
Author URI: http://www.bookingfor.com/
*/
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

//defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BookingFor' ) ) :
final class BookingFor {
	
	public $version = '8.6.0.0';
	public $currentOrder = null;
	
	protected static $_instance = null;
	
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
	public function template_path() {
		return apply_filters( 'bookingfor_template_path', 'bookingfor/' );
	}

	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'frontend' :
				return ! is_admin();
//			case 'ajax' :
//				return defined( 'DOING_AJAX' );
//			case 'cron' :
//				return defined( 'DOING_CRON' );
//			case 'frontend' :
//				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}
	public function __construct() {

		$this->define_constants();
		$this->init_hooks();
		$this->includes();

		do_action( 'bookingfor_loaded' );
	}

	private function define_constants() {		
		//--------------- MULTISITE ---------------//
		// First, I define a constant to see if site is network activated
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			// Makes sure the plugin is defined before trying to use it
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
//		if (is_plugin_active(plugin_basename( __FILE__ ))) {  // path to plugin folder and main file
//			$this->define("COM_BOOKINGFORCONNECTOR_NETWORK_ACTIVATED", true);
//		}
//		else {
//			$this->define("COM_BOOKINGFORCONNECTOR_NETWORK_ACTIVATED", false);
//		}
			$this->define("COM_BOOKINGFORCONNECTOR_NETWORK_ACTIVATED", false);


		if ( filter_input( INPUT_GET, 'task' ) ) {
			define( 'SHORINIT', true );
			define( 'DBI_AJAX', true );
		}

		$subscriptionkey= get_option('bfi_subscription_key', '');
		$subscriptionkeydemo= get_option('bfi_subscriptiondemo_key', '');
		$enableSubscriptionTest = get_option('bfi_enablesubscriptiontest_key', 0);
		$apikey= get_option('bfi_api_key', '');
		$settingkey= get_option('bfi_setting_key', '');
		$form_key= get_option('bfi_form_key', '');
		$XGooglePosDef = get_option('bfi_posx_key', 0);
		$YGooglePosDef = get_option('bfi_posy_key', 0);
		$startzoom = get_option('bfi_startzoom_key',15);
		$googlemapskey = get_option('bfi_googlemapskey_key','');
		$enableGooglemapsApi = get_option('bfi_enablegooglemapsapi', 0);
		if (empty($enableGooglemapsApi)) {
				$XGooglePosDef = 0;
				$YGooglePosDef = 0;
				$startzoom = 15;
				$googlemapskey = '';
			
		}
		$itemperpage = get_option('bfi_itemperpage_key',10);
		
		$googlerecaptchaversion = get_option('bfi_googlerecaptcha_version','V2');
		$googlerecaptchakey = get_option('bfi_googlerecaptcha_key','');
		$googlerecaptchasecretkey = get_option('bfi_googlerecaptcha_secret_key','');
		$googlerecaptchathemekey = get_option('bfi_googlerecaptcha_theme_key','light');
		$googlerecaptchasizekey = get_option('bfi_googlerecaptcha_size_key','normal');

		$openstreetmap = get_option('bfi_openstreetmap', 0);

		$isportal = get_option('bfi_isportal_key', 1);
		$enalbleOtherMerchantsResult = get_option('bfi_enalbleothermerchantsresult', 0);
		$enalbleResourceFilter = get_option('bfi_enableresourcefilter', 0);		
		$disalbleInfoForm = get_option('bfi_disableinfoform', 0);		

		$showdata = get_option('bfi_showdata_key', 1);
//		$sendtocart = get_option('bfi_sendtocart_key', 0);
		$sendtocart = 0;
		$showbadge = 1;// get_option('bfi_showbadge_key', 1);

		$enablecoupon = get_option('bfi_enablecoupon_key', 0);
		$showlogincart = get_option('bfi_showlogincart_key', 1);
		$showadvancesetting = get_option('bfi_showadvancesetting_key', 0);
		
		$usessl = get_option('bfi_usessl_key',0);
		$ssllogo = get_option('bfi_ssllogo_key','');

		$useproxy = get_option('bfi_useproxy_key',0);
		$urlproxy = get_option('bfi_urlproxy_key','127.0.0.1:8888');
		
		$gaenabled = get_option('bfi_gaenabled_key', 0);
		$gaaccount = get_option('bfi_gaaccount_key', '');
		$eecenabled = get_option('bfi_eecenabled_key', 0);
		$criteoenabled = get_option('bfi_criteoenabled_key', 0);
		$gtmenabled = get_option('bfi_googletagmanager', 0);

		$fbapienabled = get_option('bfi_fbapienabled_key', 0);
		$fbpixelid = get_option('bfi_fbpixelid_key', '');
		$fbtoken = get_option('bfi_fbtoken_key', '');
		$fbtesteventcode = get_option('bfi_fbtesteventcode_key', '');
		
		$enablecache = get_option('bfi_enablecache_key', 1);
//		$enablecache = 1;

		$bfi_adultsage_key = get_option('bfi_adultsage_key', 18);
		$bfi_adultsqt_key = get_option('bfi_adultsqt_key', 2);
		$bfi_childrensage_key = get_option('bfi_childrensage_key', 12);
		$bfi_senioresage_key = get_option('bfi_senioresage_key', 65);
		$bfi_maxqtSelectable_key = get_option('bfi_maxqtSelectable_key', 20);
		$bfi_defaultdisplaylist_key = get_option('bfi_defaultdisplaylist_key', 0);
		

		$bfi_currentcurrency = get_option('bfi_currentcurrency_key', '');
		$bfi_altcurrency = get_option('bfi_altcurrency_key', '');

		$form_startdate= get_option('bfi_form_startdate', '');

		$nMonthinCalendar = 2;

		$useragent= isset($_SERVER['HTTP_USER_AGENT'])? $_SERVER['HTTP_USER_AGENT']: "";
		$ismobile=false;

		if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
			$nMonthinCalendar = 1;
			$ismobile=true;
		}

		$this->define( 'BFI_VERSION', $this->version );
		$this->define( 'COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR', $nMonthinCalendar );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ISMOBILE', $ismobile );

		$subscriptionkey = strtolower($subscriptionkey);
		$this->define( 'COM_BOOKINGFORCONNECTOR_SUBSCRIPTIONKEY', $subscriptionkey );
		$subscriptionkeydemo = strtolower($subscriptionkeydemo);

		if(strpos($subscriptionkey,'https://') !== false){
			$subscriptionkey = str_replace("https://", "", $subscriptionkey);
			$subscriptionkey = str_replace(".bookingfor.com/modules/bookingfor/services/bookingservice.svc", "", $subscriptionkey);
			$subscriptionkey = str_replace("/", "", $subscriptionkey);
		}
		$bfiBaseUrl = 'https://' . $subscriptionkey . '.bookingfor.com';
		if ($enableSubscriptionTest && !empty($subscriptionkeydemo) ) {
			$bfiBaseUrl = 'https://' . $subscriptionkeydemo . '.bookingfor.com';
		}
//		if ($subscriptionkey=="mars") {
//			$bfiBaseUrl = 'https://marsdemo.bookingfor.com';
//		}
		
		// ------------- LOCALHOST ------------- //
//		$bfiBaseUrl = 'https://localhost:44379';
		// ------------- LOCALHOST ------------- //

		$cachedir = get_option('bfi_cachedir', WP_CONTENT_DIR . '/uploads/cache/bookingfor');
		$cachetime = get_option('bfi_cache_time_key', 86400); // 1 day default
		$cachedirbot = get_option('bfi_cachedir', WP_CONTENT_DIR . '/uploads/cache/bookingforbot');
		$cachetimebot = get_option('bfi_cache_time_bot_key', 1728000); // 20 days default for bot

		$this->define( 'COM_BOOKINGFORCONNECTOR_CACHEDIR', $cachedir );
		$this->define( 'COM_BOOKINGFORCONNECTOR_CACHETIME', $cachetime );
		$this->define( 'COM_BOOKINGFORCONNECTOR_CACHEDIRBOT', $cachedirbot );
		$this->define( 'COM_BOOKINGFORCONNECTOR_CACHETIMEBOT', $cachetimebot );

		// per Search Result 

		$enablegenericsearchdetails = get_option('bfi_enablegenericsearchdetails_key', 1); // 1 
		$showeventbanner = get_option('bfi_showeventbanner_key', 1); // 1 
		$showeventbannerevery = get_option('bfi_showeventbannerevery_key', 5); // 1 
		$showeventbannerrepeated = get_option('bfi_showeventbannerrepeated_key', 1); // 1 
		$this->define( 'COM_BOOKINGFORCONNECTOR_ENABLEGENERICSEARCHDETAILS', $enablegenericsearchdetails );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SHOWEVENTBANNER', $showeventbanner );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SHOWEVENTBANNEREVERY', $showeventbannerevery );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SHOWEVENTBANNERREPEATED', $showeventbannerrepeated );

		$showcontactbanner = get_option('bfi_showcontactbanner_key', 0); 
		$showcontactbannerform = get_option('bfi_showcontactbannerform_key', 0);  
		$contactbannerphone = get_option('bfi_contactbannerphone_key', '');
		$contactbannerphonewhatsapp = get_option('bfi_contactbannerphonewhatsapp_key', '');
		$contactbanneremail = get_option('bfi_contactbanneremail_key', '');
		$contactbannerpage = get_option('bfi_contactbannerpage_key', '');
		$this->define( 'COM_BOOKINGFORCONNECTOR_SHOWCONTACTBANNER', $showcontactbanner );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SHOWCONTACTBANNERFORM', $showcontactbannerform );
		$this->define( 'COM_BOOKINGFORCONNECTOR_CONTACTBANNERPHONE', $contactbannerphone );
		$this->define( 'COM_BOOKINGFORCONNECTOR_CONTACTBANNEREMAIL', $contactbanneremail );
		$this->define( 'COM_BOOKINGFORCONNECTOR_CONTACTBANNERPHONEWHATSAPP', $contactbannerphonewhatsapp );
		$this->define( 'COM_BOOKINGFORCONNECTOR_CONTACTBANNERPAGE', $contactbannerpage );


		$datacrawler = file_get_contents(untrailingslashit( plugin_dir_path( __FILE__ )) .  DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'crawler-user-agents.json');
		$this->define( 'COM_BOOKINGFORCONNECTOR_CRAWLER', $datacrawler );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ISBOT', $this->isBot() );

		$this->define( 'COM_BOOKINGFORCONNECTOR_SUBSCRIPTION_KEY', $subscriptionkey );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SUBSCRIPTION_KEY_DEMO', $subscriptionkeydemo );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ENABLE_SUBSCRIPTION_TEST', $enableSubscriptionTest );

		$this->define( 'COM_BOOKINGFORCONNECTOR_API_KEY', $apikey );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SETTING_KEY', $settingkey );

		$this->define( 'COM_BOOKINGFORCONNECTOR_FORM_KEY', $form_key );
		$this->define( 'COM_BOOKINGFORCONNECTOR_WSURL', $bfiBaseUrl .'/modules/bookingfor/services/bookingservice.svc' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ORDERURL', $bfiBaseUrl .'/Public/{language}/orderlogin' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_PAYMENTURL', $bfiBaseUrl .'/Public/{language}/payment/' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_PRIVACYURL', $bfiBaseUrl .'/Public/{language}/privacy' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_TERMSOFUSEURL', $bfiBaseUrl .'/Public/{language}/termsofuse' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_NEWSLETTERURL', $bfiBaseUrl .'/Public/{language}/newsletterinfos' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_MARKETINGURL', $bfiBaseUrl .'/Public/{language}/marketinginfos' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_DATAPROFILINGURL', $bfiBaseUrl .'/Public/{language}/dataprofilinginfos' );
//		$this->define( 'COM_BOOKINGFORCONNECTOR_ACCOUNTLOGIN', $bfiBaseUrl .'/Public/{language}/?openloginpopup=1' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ACCOUNTREGISTRATION', $bfiBaseUrl .'/Public/{language}/Account/Register' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ACCOUNTFORGOTPASSWORD', $bfiBaseUrl .'/Public/{language}/Account/sendforgotpasswordlink' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ACCOUNTTRAVELPLANNER', $bfiBaseUrl .'/Public/{language}/my-wishlist' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ACCOUNTLOGIN', $bfiBaseUrl .'/Public/{language}/Account/UserProfileExternal' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ACCOUNTTRAVELPLANNERLOGGED', $bfiBaseUrl .'/Public/{language}/Account/UserProfileExternal?viewsection=travelplanner' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SEARCHBYTEXT', $bfiBaseUrl .'/Public/{language}/Home/SearchByText' );

		$this->define( 'COM_BOOKINGFORCONNECTOR_CURRENTCURRENCY', $bfi_currentcurrency );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ALTCURRENCY', $bfi_altcurrency );
		$this->define( 'COM_BOOKINGFORCONNECTOR_MAXATTACHMENTFILES', 3 );
		
		$this->define( 'COM_BOOKINGFORCONNECTOR_IMGURL', $subscriptionkey . '/bookingfor/images' );
//		$this->define( 'COM_BOOKINGFORCONNECTOR_IMGURL_CDN', 'https://az597654.vo.msecnd.net/' );
//		$this->define( 'COM_BOOKINGFORCONNECTOR_BASEIMGURL', 'https://az597654.vo.msecnd.net/' . $subscriptionkey . '/bookingfor/images' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_IMGURL_CDN', 'https://cdnbookingfor.blob.core.windows.net/' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_BASEIMGURL', 'https://cdnbookingfor.blob.core.windows.net/' . $subscriptionkey . '/bookingfor/images' );

		$this->define( 'COM_BOOKINGFORCONNECTOR_ENABLEGOOGLEMAPSAPI', $enableGooglemapsApi );
		$this->define( 'COM_BOOKINGFORCONNECTOR_GOOGLE_POSX', $XGooglePosDef );
		$this->define( 'COM_BOOKINGFORCONNECTOR_GOOGLE_POSY', $YGooglePosDef );
		$this->define( 'COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM', $startzoom );
		$this->define( 'COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY', $googlemapskey );
		$this->define( 'COM_BOOKINGFORCONNECTOR_USE_OPENSTREETMAP', $openstreetmap );

		$this->define( 'COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHAVERSION', $googlerecaptchaversion );
		$this->define( 'COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHAKEY', $googlerecaptchakey );
		
		$this->define( 'COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHASECRETKEY', $googlerecaptchasecretkey );
		$this->define( 'COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHATHEMEKEY', $googlerecaptchathemekey );
		$this->define( 'COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHASIZEKEY', $googlerecaptchasizekey );
		
		$this->define( 'COM_BOOKINGFORCONNECTOR_USEEXTERNALUPDATEORDER', false);
		$this->define( 'COM_BOOKINGFORCONNECTOR_USEEXTERNALUPDATEORDERSYSTEM', "");
		$this->define( 'COM_BOOKINGFORCONNECTOR_ANONYMOUS_TYPE', "3,4");
		$this->define( 'COM_BOOKINGFORCONNECTOR_ITEMPERPAGE', $itemperpage );
		
		$this->define( 'COM_BOOKINGFORCONNECTOR_ENALBLEOTHERMERCHANTSRESULT', $enalbleOtherMerchantsResult );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ENALBLERESOURCEFILTER', $enalbleResourceFilter );
		$this->define( 'COM_BOOKINGFORCONNECTOR_DISALBLEINFOFORM', $disalbleInfoForm );

		$this->define( 'COM_BOOKINGFORCONNECTOR_ISPORTAL', $isportal );

		$this->define( 'COM_BOOKINGFORCONNECTOR_SHOWDATA', $showdata );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SENDTOCART', $sendtocart );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SHOWBADGE', $showbadge );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ENABLECOUPON', $enablecoupon );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SHOWLOGINCART', $showlogincart );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SHOWADVANCESETTING', $showadvancesetting );
		
		$this->define( 'COM_BOOKINGFORCONNECTOR_USESSL', $usessl );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SSLLOGO', $ssllogo );

		$this->define( 'COM_BOOKINGFORCONNECTOR_ADULTSAGE', $bfi_adultsage_key );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ADULTSQT', $bfi_adultsqt_key );
		$this->define( 'COM_BOOKINGFORCONNECTOR_CHILDRENSAGE', $bfi_childrensage_key );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SENIORESAGE', $bfi_senioresage_key );

		$this->define( 'COM_BOOKINGFORCONNECTOR_USEPROXY', $useproxy );
		$this->define( 'COM_BOOKINGFORCONNECTOR_URLPROXY', $urlproxy );

		$this->define( 'COM_BOOKINGFORCONNECTOR_GAENABLED', $gaenabled );
		$this->define( 'COM_BOOKINGFORCONNECTOR_GTMENABLED', $gtmenabled );
		$this->define( 'COM_BOOKINGFORCONNECTOR_GAACCOUNT', $gaaccount );
		$this->define( 'COM_BOOKINGFORCONNECTOR_EECENABLED', $eecenabled );
		$this->define( 'COM_BOOKINGFORCONNECTOR_CRITEOENABLED', $criteoenabled );

		$this->define( 'FB_CAPI', (!empty($fbapienabled) && !empty($fbpixelid) && !empty($fbtoken) ));
		$this->define( 'FB_PIXELID', $fbpixelid );
		$this->define( 'FB_TOKEN', $fbtoken );
		$this->define( 'FB_TESTEVENTCODE', $fbtesteventcode );

		$this->define( 'COM_BOOKINGFORCONNECTOR_ENABLECACHE', $enablecache );

		$this->define( 'COM_BOOKINGFORCONNECTOR_MAXQTSELECTABLE', $bfi_maxqtSelectable_key );
		
		$this->define( 'COM_BOOKINGFORCONNECTOR_DEFAULTDISPLAYLIST', $bfi_defaultdisplaylist_key );
		
		$this->define( 'COM_BOOKINGFORCONNECTOR_DEFAULTIMAGE', $this->plugin_url() . "/assets/images/defaults/default.jpeg");// Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default.jpeg" );
		$this->define( 'COM_BOOKINGFORCONNECTOR_DEFAULTLOGO', $this->plugin_url() . "/assets/images/defaults/default-logo.jpeg"); //Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-logo.jpeg" );

		$this->define( 'COM_BOOKINGFORCONNECTOR_KEY', 'WZgfdUps' );
		

		$this->define( 'COM_BOOKINGFORCONNECTOR_TARGETURL', ($ismobile?'':'target="_blank"'));

		$this->define( 'COM_BOOKINGFORCONNECTOR_FORM_STARTDATE', $form_startdate );

	}

	private function init_hooks() {
		register_activation_hook(__FILE__,array( 'BFI_Install', 'install' ));
//		add_action('init', function() {
//				$regex = '^bfi-api/v1/(/[^/]*)?$';
//				$location = 'index.php?_api_controller=$matches[1]';
//				$priority = 'top';
//				add_rewrite_rule( $regex, $location, $priority );
//		});
		add_action( 'admin_notices', array( $this, 'bfi_plugin_admin_notices' ) );
//		//	REST API
//			add_action( 'rest_api_init', function () {
//			  register_rest_route( 'bookingfor/v1', 'searchbytext', array(
//				'methods' => 'GET',
//				'callback' => 'BFI_Controller::SearchByText',
////				'args' => array(
////				  'term' => array(
////					'validate_callback' => function($param, $request, $key) {
////					  return is_numeric( $param );
////					}
////				  ),
////				),
//			  ) );
//			} );
		add_action('parse_request', array($this, 'sniff_requests'), 0);
//		add_action('parse_request', array($this, 'bfi_change_currency'), 0);
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
//		add_action( 'init', array( $this, 'bfi_StartSession' ), 0 );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( 'bfi_Shortcodes', 'init' ) );
//		add_action( 'wp_logout', array( $this, 'bfi_EndSession' ) );
		if ( $this->is_request( 'frontend' ) ) {

			/* nuova sezione */
			wp_enqueue_script("jquery");
//			wp_enqueue_style('wp-jquery-ui-dialog');
//			wp_enqueue_script('jquery-ui-accordion');
//			wp_enqueue_style('jquery-ui-style', plugins_url( 'assets/jquery-ui/themes/smoothness/jquery-ui.min.css', __FILE__ ),array(),$this->version,'all');
			// script 
			
//			wp_enqueue_script('validate', plugins_url( 'assets/js/jquery-validation/jquery.validate.min.js', __FILE__ ),array(),$this->version);
//			wp_enqueue_script('validateadditional', plugins_url( 'assets/js/jquery-validation/additional-methods.min.js', __FILE__ ),array(),$this->version);
//			wp_enqueue_script('validateadditionalcustom', plugins_url( 'assets/js/jquery.validate.additional-custom-methods.js', __FILE__ ),array(),$this->version,true);
			add_action ( 'wp_head', array( $this , 'bfi_js_variables_overrides' ) );
			/* end nuova sezione */


//			add_action( 'wp_enqueue_scripts', array( $this , 'bfi_load_scripts' ) ,1 ); // spostata priorità altrimenti sovrascrive template
//			add_action( 'wp_enqueue_scripts', array( $this , 'bfi_load_scripts_locale' ) );
//			add_action ( 'wp_head', array( $this , 'bfi_js_variables' ) );
			//remove canonical 

//			add_filter( 'wpseo_canonical', '__return_false' );

		
//			add_filter( 'wpseo_canonical', '__return_false' );
//			$pages = array();                                             
//			$pages[] = 'merchantdetails';
//			$pages[] = 'resourcedetails';
//			$pages[] = 'experiencedetails';
//			$pages[] = 'resourcegroupdetails';
//			$pages[] = 'cartdetails';
//			$pages[] = 'eventdetails';
//			$pages[] = 'pointsofinterestdetails';       
//			$pages[] = 'onselldetails';
//			$pages[] = 'payment';
//			$page_id = get_the_ID();
//			foreach ($pages as $page) {
//				if (bfi_get_page_id( $page ) == $page_id) {
//					add_filter( 'wpseo_canonical', '__return_false' );
//					break;
//				}
//			}
//		
		
		}
		if ( $this->is_request( 'admin' ) ) {
			add_action( 'admin_enqueue_scripts', array( $this , 'bfi_load_admin_scripts' ) );
			if ( in_array( 'elementor/elementor.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				add_action('elementor/editor/before_enqueue_scripts', array( $this , 'bfi_load_admin_scripts' ));

			}
		}

//		register_activation_hook( __FILE__, array( 'BFI_Install', 'install' ) );
//		add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
//		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
//		add_action( 'init', array( $this, 'init' ), 0 );
//		add_action( 'init', array( 'bfi_Shortcodes', 'init' ) );
//		add_action( 'init', array( 'BFI_Emails', 'init_transactional_emails' ) );
//		add_action( 'init', array( $this, 'wpdb_table_fix' ), 0 );
//		add_action( 'switch_blog', array( $this, 'wpdb_table_fix' ), 0 );
	}


	function bfi_plugin_admin_notices() {
		$bfSubscriptionKey = COM_BOOKINGFORCONNECTOR_SUBSCRIPTION_KEY;
		if (is_plugin_active(plugin_basename( __FILE__ )) && empty($bfSubscriptionKey)) {
			echo "<div class='error'><p><b>Complete BookingFor Settings <a href='". admin_url('admin.php?page=bfi-settings')."'>here</a></b></p></div>";
		}
	}
	/**	Sniff Requests 
	*	This is where we hijack all API requests 
	* 	If $_GET['__api'] is set, we kill WP and serve up pug bomb awesomeness 
	*	@return die if API request 
	*/ 
	public function sniff_requests(){ 
		
		global $wp; 
//		echo "<pre>sniff_requests --";
//		echo $wp->query_vars['_api_controller'];
//		echo $_REQUEST['prova'] ;

//		echo "</pre>";
		if(isset($wp->query_vars['_api_controller'])){ 
			include_once( 'includes/BFCHelper.php' );
			include_once( 'includes/wsQueryHelper.php' );
			include_once( 'includes/api/class-bfi-controller.php' );
			$bfi_api = new BFI_Controller;
			$bfi_api->handle_request();
//			die();
			exit; 
		} 
	} 

//	public function bfi_change_currency(){ 
//		
//		global $wp; 
//		if(isset($_REQUEST['bfiselectedcurrency'])){ 
//			bfi_set_currentCurrency($_REQUEST['bfiselectedcurrency']);
//		} 
//	} 

//	public static function bfi_StartSession() {
//		if(!session_id()) {
////			  ini_set('session.save_handler', 'files'); 
//			session_start();
//		}
//	}
//	public static function bfi_EndSession() {
//		session_destroy();
//	}

	public static function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	public function include_template_functions() {
		include_once( 'includes/bfi-template-functions.php' );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {

		include_once( 'includes/BFCHelper.php' );
		include_once( 'includes/wsQueryHelper.php' );

		include_once( 'includes/bfi-core-functions.php' );
		include_once( 'includes/bfi-widget-functions.php' );
		include_once( 'includes/class-bfi-install.php' );
		include_once( 'includes/bfi-page-functions.php' );
		include_once( 'includes/class-bfi-cache.php' );
		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_includes();
//			$this->bfi_load_scripts();			
		}
		if ( $this->is_request( 'admin' ) ) {
			$this->bfi_load_admin_scripts();			
			include_once( 'includes/admin/class-bfi-admin.php' );
//			include_once('includes/model/merchants.php' );
//			include_once('includes/model/resources.php' );
			include_once('includes/model/experience.php' );
			include_once('includes/model/portal.php' );
			include_once('includes/model/tag.php');
//			include_once('includes/model/events.php');
//			include_once('includes/model/pointsofinterests.php');
//			include_once('includes/model/onsellunits.php' );
//			include_once('includes/model/offers.php' );
		}
		include_once( 'includes/class-bfi-query.php' ); // The main query class
		include_once( 'includes/class-bfi-shortcodes.php' );                     // Shortcodes class

		$this->query = new BFI_Query();
		$this->shortcodes = new bfi_Shortcodes();
	}
	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		include_once( 'includes/bfi-template-hooks.php' );
		include_once( 'includes/class-bfi-template-loader.php' );                // Template Loader
		include_once( 'includes/SimpleDOM.php' );
//		include_once( 'includes/model/criteo.php' );
//		include_once('includes/model/services.php' );
//		include_once('includes/model/search.php' );
		include_once('includes/model/experience.php' );
		include_once('includes/model/resource.php' );
//		include_once('includes/model/resources.php' );
		include_once('includes/model/resourcegroup.php' );
//		include_once('includes/model/ratings.php' );
		include_once('includes/model/portal.php' );
//		include_once('includes/model/payment.php' );
//		include_once('includes/model/orders.php' );
//		include_once('includes/model/merchants.php' );
		include_once('includes/model/merchantdetails.php' );
//		include_once('includes/model/inforequest.php' );
		include_once('includes/model/onsellunit.php' );
//		include_once('includes/model/onsellunits.php' );
//		include_once('includes/model/searchonsell.php' );
		include_once('includes/model/tag.php');
		include_once('includes/model/event.php');
//		include_once('includes/model/events.php');
//		include_once('includes/model/searchevents.php' );
//		include_once('includes/model/searchpackages.php' );
		include_once('includes/model/package.php' );
//		include_once('includes/model/searchmapsells.php' );
//		include_once('includes/model/pointsofinterests.php');
		include_once('includes/model/pointsofinterest.php');
//		include_once('includes/model/offers.php' );
	}


	public function bfi_load_admin_scripts(){
		wp_enqueue_script('jquery');
		wp_enqueue_script("jquery-effects-core");
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-tabs');
//		wp_enqueue_style( 'bf_admin_css', plugins_url( 'assets/css/basic.css', __FILE__ ));
		wp_enqueue_style('jquery-ui-style', plugins_url( 'assets/jquery-ui/themes/smoothness/jquery-ui.min.css', __FILE__ ),array(),$this->version,'all');
//		wp_enqueue_style('bookingfor_styles', plugins_url( 'assets/css/bookingfor.css', __FILE__ ),array(),$this->version,'all');
		wp_enqueue_style('bookingfor_admin_styles', plugins_url( 'assets/css/bookingforadmin.css', __FILE__ ),array(),$this->version,'all');
		
		wp_enqueue_script('admin_select2_js', plugins_url( 'assets/js/select2/js/select2.min.js', __FILE__ ), array('jquery'));
		wp_enqueue_style('admin_select2_css', plugins_url( 'assets/js/select2/css/select2.min.css', __FILE__ ),array(),$this->version,'all');
		wp_enqueue_script('bf_admin', plugins_url( 'assets/js/bf_admin.js', __FILE__ ),array(),$this->version);
//		wp_enqueue_style('timepicker_theme_css', BFI()->plugin_url() . '/assets/js/jquery-timepicker/jquery.timepicker.css' );
//		wp_enqueue_script('timepicker_js', BFI()->plugin_url() . '/assets/js/jquery-timepicker/jquery.timepicker.js', array('jquery'));
	}
	
		public function bfi_js_variables_overrides(){
		$url_cart_page = BFCHelper::GetPageUrl('cartdetails');

		if(COM_BOOKINGFORCONNECTOR_USESSL){
			$url_cart_page = str_replace( 'http:', 'https:', $url_cart_page );
		}
//		$currUser = BFCHelper::getSession('bfiUser',null, 'bfi-User');
//		$userLogged = false;
//		if ($currUser!=null && !empty($currUser->Email)) {
//			$userLogged = true;
//		}
		
		?>
	  <script type="text/javascript">
		var bfi_variables_overrides = {
			"bfi_cultureCodeBase":"<?php echo substr($this->language,0,2); ?>",
			"bfi_carturl" : '<?php echo 	BFCHelper::getPageUrl("cartdetails"); ?>',			
			"bfi_url_merchant_page" :  '<?php echo 	BFCHelper::getPageUrl("merchantdetails"); ?>', 
			"bfi_url_resource_page" :  '<?php echo 	BFCHelper::getPageUrl("accommodationdetails"); ?>', 
			"bfi_url_onsell_page" :  '<?php echo 	BFCHelper::getPageUrl("onselldetails"); ?>', 
			"bfi_url_resource_page_experience" :  '<?php echo 	BFCHelper::getPageUrl("experiencedetails"); ?>',
			"bfi_url_resourcegroup_page" :  '<?php echo 	BFCHelper::getPageUrl("resourcegroupdetails"); ?>',
			"bfi_url_package_page" :  '<?php echo 	BFCHelper::getPageUrl("packagesdetails"); ?>',
			"bfi_url_event_page" :  '<?php echo 	BFCHelper::getPageUrl("eventdetails"); ?>',
			"bfi_url_poi_page" :  '<?php echo 	BFCHelper::getPageUrl("pointsofinterestdetails"); ?>',
			"bfi_searchavailabilityurl" :  '<?php echo 	BFCHelper::getPageUrl("searchavailability"); ?>',
			"bfi_searcheventsurl" :  '<?php echo 	BFCHelper::getPageUrl("searchevents"); ?>',
			"bfi_searchonsellsurl" :  '<?php echo 	BFCHelper::getPageUrl("searchonsell"); ?>',
			"baseUrl": "https://az597654.vo.msecnd.net/<?php echo COM_BOOKINGFORCONNECTOR_SUBSCRIPTIONKEY ?>/",
			"baseurlpage": '<?php echo 	BFCHelper::getPageUrl("searchavailability"); ?>',
		};
		</script>
		<!-- Bookingfor Implementation -->
			<script>if (!window.bookingfor) { var s = document.createElement('script');
				s.src = '<?php echo COM_BOOKINGFORCONNECTOR_IMGURL_CDN ?><?php echo COM_BOOKINGFORCONNECTOR_SUBSCRIPTION_KEY ?>/scripts/<?php echo COM_BOOKINGFORCONNECTOR_SETTING_KEY ?>_settings.js';
//				s.src = 'http://localhost:8080/testspiaggia2/scriptsshadow/test_settings.js';
//				s.src = 'http://localhost:8080/testspiaggia2/scripts/test_settings.js';
				s.type = 'text/javascript'; document.getElementsByTagName('head')[0].appendChild(s); } </script>
		<!-- End Bookingfor Implementation -->
		<?php
		$_SESSION['bfi_started'] = "0";
	}

//	public function bfi_load_scripts_locale(){
//		$bfi_variable = array( 
//			'bfi_urlCheck' =>  get_site_url() .'/bfi-api/v1/task',
//			'bfi_cultureCode' => $this->language,
//			'bfi_defaultcultureCode' => 'en-gb',
//			'defaultCurrency' => bfi_get_defaultCurrency(),
//			'currentCurrency' => bfi_get_currentCurrency(),
//			'CurrencyExchanges' => BFCHelper::getCurrencyExchanges(),
//			'bfi_defaultdisplay'=>COM_BOOKINGFORCONNECTOR_DEFAULTDISPLAYLIST,
//			);
//		wp_localize_script( 'bfi', 'bfi_variable', $bfi_variable );
//		if(substr($this->language,0,2)!='en'){
////			wp_enqueue_script('jquery-ui-datepicker_locale',plugins_url( 'assets/jquery-ui/i18n/datepicker-' . substr($this->language,0,2) . '.js', __FILE__ ));
//		}
//	}

	public function seoUrl($string) {
		// remove last space..
		$string = trim($string);
		//Lower case everything
		$string = strtolower($string);
		//Make alphanumeric (removes all other characters)
		$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
		//Clean up multiple dashes or whitespaces
		$string = preg_replace("/[\s-]+/", " ", $string);
		//Convert whitespaces and underscore to dash
		$string = preg_replace("/[\s_]/", "-", $string);
		return $string;
	}

	public function isBot(){
		if(!empty( COM_BOOKINGFORCONNECTOR_CRAWLER )){
			$listCrawler = json_decode(COM_BOOKINGFORCONNECTOR_CRAWLER , true);
			foreach( $listCrawler as $key=>$crawler){
			if (preg_match('/'.$crawler['pattern'].'/', $_SERVER['HTTP_USER_AGENT'])) return true;
			}
		}
		return false;
	}

//	public function isMerchantPage(){
//		global $post;
//		$currdetails_page_id = bfi_get_template_page_id( 'merchantdetails' );
//		if (!empty($post) &&  $post->ID == $currdetails_page_id ){
//			return true;
//		}
//		return false;
//	}
//	public function isResourcegroupPage(){
//		global $post;
//		$currdetails_page_id = bfi_get_template_page_id( 'resourcegroupdetails' );
//		if (!empty($post) &&  $post->ID == $currdetails_page_id ){
//			return true;
//		}
//		return false;
//	}
//	public function isResourcePage(){
//		global $post;
//		$currdetails_page_id = bfi_get_template_page_id( 'accommodationdetails' );
//		if (!empty($post) &&  $post->ID == $currdetails_page_id ){
//			return true;
//		}
//		return false;
//	}
//	public function isExperiencePage(){
//		global $post;
//		$currdetails_page_id = bfi_get_template_page_id( 'experiencedetails' );
//		if (!empty($post) &&  $post->ID == $currdetails_page_id ){
//			return true;
//		}
//		return false;
//	}
//	public function isResourceOnSellPage(){
//		global $post;
//		$currdetails_page_id = bfi_get_template_page_id( 'onselldetails' );
//		if (!empty($post) &&  $post->ID == $currdetails_page_id ){
//			return true;
//		}
//		return false;
//	}
	public function isSearchPage(){
		global $post;
		$currdetails_page_id = bfi_get_template_page_id( 'searchavailability' );
		if (!empty($post) &&  $post->ID == $currdetails_page_id ){
			return true;
		}
		return false;
	}
//	public function isSearchPackagesPage(){
//		global $post;
//		$currdetails_page_id = bfi_get_template_page_id( 'searchpackages' );
//		if (!empty($post) &&  $post->ID == $currdetails_page_id ){
//			return true;
//		}
//		return false;
//	}
	public function isSearchEventsPage(){
		global $post;
		$currdetails_page_id = bfi_get_template_page_id( 'searchevents' );
		if (!empty($post) &&  $post->ID == $currdetails_page_id ){
			return true;
		}
		return false;
	}
	public function isSearchPoisPage(){
		global $post;
		$currdetails_page_id = bfi_get_template_page_id( 'searchpoi' );
		if (!empty($post) &&  $post->ID == $currdetails_page_id ){
			return true;
		}
		return false;
	}
	
	public function isSearchOnSellPage(){
		global $post;
		$currdetails_page_id = bfi_get_template_page_id( 'searchonsell' );
		if (!empty($post) &&  $post->ID == $currdetails_page_id ){
			return true;
		}
		return false;
	}
	public function isSearchMapSellsPage(){
		global $post;
		$currdetails_page_id = bfi_get_template_page_id( 'searchmapsells' );
		if (!empty($post) &&  $post->ID == $currdetails_page_id ){
			return true;
		}
		return false;
	}

	public function isCartPage(){
		global $post;
		$currdetails_page_id = bfi_get_template_page_id( 'cartdetails' );
		if (!empty($post) &&  $post->ID == $currdetails_page_id ){
			return true;
		}
		return false;
	}
	public function isEventPage(){
		global $post;
		$currdetails_page_id = bfi_get_template_page_id( 'eventdetails' );
		if (!empty($post) &&  $post->ID == $currdetails_page_id ){
			return true;
		}
		return false;
	}
	public function isPackagePage(){
		global $post;
		$currdetails_page_id = bfi_get_template_page_id( 'packagesdetails' );
		if (!empty($post) &&  $post->ID == $currdetails_page_id ){
			return true;
		}
		return false;
	}
	public function isPoiPage(){
		global $post;
		$currdetails_page_id = bfi_get_template_page_id( 'pointsofinterestdetails' );
		if (!empty($post) &&  $post->ID == $currdetails_page_id ){
			return true;
		}
		return false;
	}

	public function init() {
		do_action( 'before_bookingfor_init' );


		// Set up localisation.
		$this->load_plugin_textdomain();
		
		// Init action.
		do_action( 'bookingfor_init' );
	}


	function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'bookingfor' );
//		$l = get_locale();
		if(defined('ICL_LANGUAGE_CODE')){
			$locale =ICL_LANGUAGE_CODE;
		}

		$this->language = $this->return_lang_mapping($locale);
		$GLOBALS['bfi_lang'] = $this->language;
		load_plugin_textdomain( 'bfi', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
	}

	function return_lang_mapping($lang) {
		$lang_array = array(
			'en' => 'en-GB',
			'it' => 'it-IT',
			'de' => 'de-DE',
			'pl' => 'pl-PL',
			'ru' => 'ru-RU',
			'hu' => 'hu-HU',
			'cs' => 'cs-CZ',
			'cz' => 'cs-CZ',
			'gr' => 'el-GR',
			'fr' => 'fr-FR',
			'es' => 'es-ES',
			'hr' => 'hr-HR',
			'nl' => 'nl-NL',
			'da' => 'da-DK',
			'sk' => 'sk-SK',
			'ar' => 'ar',
			'zh' => 'zh',
			'zh_CN' => 'zh-CN',
			'zh-CN' => 'zh-CN',
			'en_GB' => 'en-GB',
			'en-GB' => 'en-GB',
			'en_US' => 'en-GB',
			'en-US' => 'en-GB',
			'ru_RU' => 'ru-RU',
			'ru-RU' => 'ru-RU',
			'pl_PL' => 'pl-PL',
			'pl-PL' => 'pl-PL',
			'it_IT' => 'it-IT',
			'it-IT' => 'it-IT',
			'hu_HU' => 'hu-HU',
			'hu-HU' => 'hu-HU',
			'de_DE' => 'de-DE',
			'de-DE' => 'de-DE',
			'cs_CZ' => 'cs-CZ',
			'cs-CZ' => 'cs-CZ',
			'el_GR' => 'el-GR',
			'el-GR' => 'el-GR',
			'fr_FR' => 'fr-FR',
			'fr-FR' => 'fr-FR',
			'es_ES' => 'es-ES',
			'es-ES' => 'es-ES',
			'hr_HR' => 'hr-HR',
			'hr-HR' => 'hr-HR',
			'nl-NL' => 'nl-NL',
			'nl_NL' => 'nl-NL',
			'sk-SK' => 'sk-SK',
			'sk_SK' => 'sk-SK',
			'da-DK' => 'da-DK',
			'da_DK' => 'da-DK'
		);
		if(isset($lang_array[$lang])) {
		  return $lang_array[$lang];
		}
		else {
		  return 'it-IT';
		}
	}

	function return_lang_locale_mapping($lang) {
		$lang_array = array(
			'en' => 'en_GB',
			'it' => 'it_IT',
			'de' => 'de_DE',
			'pl' => 'pl_PL',
			'ru' => 'ru_RU',
			'hu' => 'hu_HU',
			'cs' => 'cs_CZ',
			'cz' => 'cs_CZ',
			'gr' => 'el_GR',
			'fr' => 'fr_FR',
			'es' => 'es_ES',
			'hr' => 'hr_HR',
			'nl' => 'nl_NL',
			'da' => 'da_DK',
			'sk' => 'sk-SK',
			'ar' => 'ar',
			'zh' => 'zh_CN',
		);
		if(isset($lang_array[$lang])) {
		  return $lang_array[$lang];
		}
		else {
		  return 'it_IT';
		}
	}
}

endif;

function BFI() {
	return BookingFor::instance();
}

// Global for backwards compatibility.
$GLOBALS['bookingfor'] = BFI();