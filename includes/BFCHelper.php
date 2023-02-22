<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'bfi_ItemType' ) ) {

	class bfi_ItemType {
		const Accommodation = 0;
		const Service = 1;
		const Package = 2;
		const Rental = 3;
		const Place = 4;
		const Beach = 5;
		const Experience = 6;
		const Ticket = 7;

		// etc. }
	}
}
if ( ! class_exists( 'bfi_InputType' ) ) {

	class bfi_InputType {
		const yesno = 0;
		const text = 1;
		const textarea = 2;
		const number = 3;
		const data = 4;
		const datahours = 5;
		const dropdown = 6;
		const dropdownmultiple = 7;

		// etc. }
	}
}


if ( ! class_exists( 'bfi_TagsScope' ) ) {
	class bfi_TagsScope {
		const Merchant = 2**0;
		const Onsellunit = 2**1;
		const Resource = 2**2;
		const ResourceGroup = 2**3;
		const Offert = 2**4;
		const Event = 2**5;
		const Poi = 2**7;
		const Package = 2**8;
		// etc. }
	}
}
if ( ! class_exists( 'bfi_Meal' ) ) {
	class bfi_Meal {
		const Breakfast = 1;
		const Lunch = 2;
		const Dinner = 4;
		const AllInclusive = 8;
		const BreakfastLunch = 3;
		const BreakfastDinner = 5;
		const LunchDinner = 6;
		const BreakfastLunchDinner = 7;
		const BreakfastLunchDinnerAllInclusive = 15;

		// etc. }
	}
}
if ( ! class_exists( 'bfiAgeType' ) ) {

	class bfiAgeType {
		public static $Adult = 0;
		public static $Seniors = 1;
		public static $Reduced = 2;


		// etc. }
	}
}
if ( ! class_exists( 'bfiRoomType' ) ) {

	class bfiRoomType {
		public static $Bedroom = 0;
		public static $Livingroom = 1;
		public static $Sittingroom = 2;
		public static $Kitchen = 3;
		public static $Mezzanine = 4;
		public static $Livingarea = 5;
		public static $Lunchroom = 6;
		public static $Cellar = 7;
		public static $Terrace = 8;


		// etc. }
	}
}

if ( ! class_exists( 'BFCHelper' ) ) {

	class BFCHelper {
		public static $defaultFallbackCode = 'en-gb';
		private static $sessionSeachParamKey = 'searchparams';
		private static $image_basePath = COM_BOOKINGFORCONNECTOR_BASEIMGURL;
		private static $image_basePathCDN = COM_BOOKINGFORCONNECTOR_IMGURL_CDN;
		private static $currentState = array();

		// utilizzata per stati..
		public static $listCountries = array(
				'AX' => 'Åland Islands',
				'AF' => 'Afghanistan',
				'AL' => 'Albania',
				'DZ' => 'Algeria',
				'AD' => 'Andorra',
				'AO' => 'Angola',
				'AI' => 'Anguilla',
				'AQ' => 'Antarctica',
				'AG' => 'Antigua and Barbuda',
				'AR' => 'Argentina',
				'AM' => 'Armenia',
				'AW' => 'Aruba',
				'AU' => 'Australia',
				'AT' => 'Austria',
				'AZ' => 'Azerbaijan',
				'BS' => 'Bahamas',
				'BH' => 'Bahrain',
				'BD' => 'Bangladesh',
				'BB' => 'Barbados',
				'BY' => 'Belarus',
				'PW' => 'Belau',
				'BE' => 'Belgium',
				'BZ' => 'Belize',
				'BJ' => 'Benin',
				'BM' => 'Bermuda',
				'BT' => 'Bhutan',
				'BO' => 'Bolivia',
				'BQ' => 'Bonaire, Saint Eustatius and Saba',
				'BA' => 'Bosnia and Herzegovina',
				'BW' => 'Botswana',
				'BV' => 'Bouvet Island',
				'BR' => 'Brazil',
				'IO' => 'British Indian Ocean Territory',
				'VG' => 'British Virgin Islands',
				'BN' => 'Brunei',
				'BG' => 'Bulgaria',
				'BF' => 'Burkina Faso',
				'BI' => 'Burundi',
				'KH' => 'Cambodia',
				'CM' => 'Cameroon',
				'CA' => 'Canada',
				'CV' => 'Cape Verde',
				'KY' => 'Cayman Islands',
				'CF' => 'Central African Republic',
				'TD' => 'Chad',
				'CL' => 'Chile',
				'CN' => 'China',
				'CX' => 'Christmas Island',
				'CC' => 'Cocos (Keeling) Islands',
				'CO' => 'Colombia',
				'KM' => 'Comoros',
				'CG' => 'Congo (Brazzaville)',
				'CD' => 'Congo (Kinshasa)',
				'CK' => 'Cook Islands',
				'CR' => 'Costa Rica',
				'HR' => 'Croatia',
				'CU' => 'Cuba',
				'CW' => 'CuraÇao',
				'CY' => 'Cyprus',
				'CZ' => 'Czech Republic',
				'DK' => 'Denmark',
				'DJ' => 'Djibouti',
				'DM' => 'Dominica',
				'DO' => 'Dominican Republic',
				'EC' => 'Ecuador',
				'EG' => 'Egypt',
				'SV' => 'El Salvador',
				'GQ' => 'Equatorial Guinea',
				'ER' => 'Eritrea',
				'EE' => 'Estonia',
				'ET' => 'Ethiopia',
				'FK' => 'Falkland Islands',
				'FO' => 'Faroe Islands',
				'FJ' => 'Fiji',
				'FI' => 'Finland',
				'FR' => 'France',
				'GF' => 'French Guiana',
				'PF' => 'French Polynesia',
				'TF' => 'French Southern Territories',
				'GA' => 'Gabon',
				'GM' => 'Gambia',
				'GE' => 'Georgia',
				'DE' => 'Germany',
				'GH' => 'Ghana',
				'GI' => 'Gibraltar',
				'GR' => 'Greece',
				'GL' => 'Greenland',
				'GD' => 'Grenada',
				'GP' => 'Guadeloupe',
				'GT' => 'Guatemala',
				'GG' => 'Guernsey',
				'GN' => 'Guinea',
				'GW' => 'Guinea-Bissau',
				'GY' => 'Guyana',
				'HT' => 'Haiti',
				'HM' => 'Heard Island and McDonald Islands',
				'HN' => 'Honduras',
				'HK' => 'Hong Kong',
				'HU' => 'Hungary',
				'IS' => 'Iceland',
				'IN' => 'India',
				'ID' => 'Indonesia',
				'IR' => 'Iran',
				'IQ' => 'Iraq',
				'IM' => 'Isle of Man',
				'IL' => 'Israel',
				'IT' => 'Italia',
				'CI' => 'Ivory Coast',
				'JM' => 'Jamaica',
				'JP' => 'Japan',
				'JE' => 'Jersey',
				'JO' => 'Jordan',
				'KZ' => 'Kazakhstan',
				'KE' => 'Kenya',
				'KI' => 'Kiribati',
				'KW' => 'Kuwait',
				'KG' => 'Kyrgyzstan',
				'LA' => 'Laos',
				'LV' => 'Latvia',
				'LB' => 'Lebanon',
				'LS' => 'Lesotho',
				'LR' => 'Liberia',
				'LY' => 'Libya',
				'LI' => 'Liechtenstein',
				'LT' => 'Lithuania',
				'LU' => 'Luxembourg',
				'MO' => 'Macao S.A.R., China',
				'MK' => 'Macedonia',
				'MG' => 'Madagascar',
				'MW' => 'Malawi',
				'MY' => 'Malaysia',
				'MV' => 'Maldives',
				'ML' => 'Mali',
				'MT' => 'Malta',
				'MH' => 'Marshall Islands',
				'MQ' => 'Martinique',
				'MR' => 'Mauritania',
				'MU' => 'Mauritius',
				'YT' => 'Mayotte',
				'MX' => 'Mexico',
				'FM' => 'Micronesia',
				'MD' => 'Moldova',
				'MC' => 'Monaco',
				'MN' => 'Mongolia',
				'ME' => 'Montenegro',
				'MS' => 'Montserrat',
				'MA' => 'Morocco',
				'MZ' => 'Mozambique',
				'MM' => 'Myanmar',
				'NA' => 'Namibia',
				'NR' => 'Nauru',
				'NP' => 'Nepal',
				'NL' => 'Netherlands',
				'AN' => 'Netherlands Antilles',
				'NC' => 'New Caledonia',
				'NZ' => 'New Zealand',
				'NI' => 'Nicaragua',
				'NE' => 'Niger',
				'NG' => 'Nigeria',
				'NU' => 'Niue',
				'NF' => 'Norfolk Island',
				'KP' => 'North Korea',
				'NO' => 'Norway',
				'OM' => 'Oman',
				'PK' => 'Pakistan',
				'PS' => 'Palestinian Territory',
				'PA' => 'Panama',
				'PG' => 'Papua New Guinea',
				'PY' => 'Paraguay',
				'PE' => 'Peru',
				'PH' => 'Philippines',
				'PN' => 'Pitcairn',
				'PL' => 'Poland',
				'PT' => 'Portugal',
				'QA' => 'Qatar',
				'IE' => 'Republic of Ireland',
				'RE' => 'Reunion',
				'RO' => 'Romania',
				'RU' => 'Russia',
				'RW' => 'Rwanda',
				'ST' => 'São Tomé and Príncipe',
				'BL' => 'Saint Barthélemy',
				'SH' => 'Saint Helena',
				'KN' => 'Saint Kitts and Nevis',
				'LC' => 'Saint Lucia',
				'SX' => 'Saint Martin (Dutch part)',
				'MF' => 'Saint Martin (French part)',
				'PM' => 'Saint Pierre and Miquelon',
				'VC' => 'Saint Vincent and the Grenadines',
				'SM' => 'San Marino',
				'SA' => 'Saudi Arabia',
				'SN' => 'Senegal',
				'RS' => 'Serbia',
				'SC' => 'Seychelles',
				'SL' => 'Sierra Leone',
				'SG' => 'Singapore',
				'SK' => 'Slovakia',
				'SI' => 'Slovenia',
				'SB' => 'Solomon Islands',
				'SO' => 'Somalia',
				'ZA' => 'South Africa',
				'GS' => 'South Georgia/Sandwich Islands',
				'KR' => 'South Korea',
				'SS' => 'South Sudan',
				'ES' => 'Spain',
				'LK' => 'Sri Lanka',
				'SD' => 'Sudan',
				'SR' => 'Suriname',
				'SJ' => 'Svalbard and Jan Mayen',
				'SZ' => 'Swaziland',
				'SE' => 'Sweden',
				'CH' => 'Switzerland',
				'SY' => 'Syria',
				'TW' => 'Taiwan',
				'TJ' => 'Tajikistan',
				'TZ' => 'Tanzania',
				'TH' => 'Thailand',
				'TL' => 'Timor-Leste',
				'TG' => 'Togo',
				'TK' => 'Tokelau',
				'TO' => 'Tonga',
				'TT' => 'Trinidad and Tobago',
				'TN' => 'Tunisia',
				'TR' => 'Turkey',
				'TM' => 'Turkmenistan',
				'TC' => 'Turks and Caicos Islands',
				'TV' => 'Tuvalu',
				'UG' => 'Uganda',
				'UA' => 'Ukraine',
				'AE' => 'United Arab Emirates',
				'GB' => 'United Kingdom (UK)',
				'US' => 'United States (US)',
				'UY' => 'Uruguay',
				'UZ' => 'Uzbekistan',
				'VU' => 'Vanuatu',
				'VA' => 'Vatican',
				'VE' => 'Venezuela',
				'VN' => 'Vietnam',
				'WF' => 'Wallis and Futuna',
				'EH' => 'Western Sahara',
				'WS' => 'Western Samoa',
				'YE' => 'Yemen',
				'ZM' => 'Zambia',
				'ZW' => 'Zimbabwe',
			);

		// utilizzata per Ricerca testuale..
		public static $listResultClasses = array(
			0 => 'zona globo',
			1 => 'stato',
			2 => 'zona stato',
			3 => 'regione',
			4 => 'zona regione',
			5 => 'città',
			6 => 'zona città',
			7 => 'poi',
			8 => 'categoria evento',
			9 => 'categoria merchant',
			10 => 'categoria gruppo di risorsa',
			11 => 'categoria risorsa',
			12 => 'tag evento',
			13 => 'tag merchant',
			14 => 'tag gruppo di risorsa',
			15 => 'tag risorsa',
			16 => 'evento',
			17 => 'merchant',
			18 => 'gruppo di risorsa',
			19 => 'risorsa',
			20 => 'pacchetto',
		);

		public static $listNameAnalytics = array(
			0 => 'Direct access',
			1 => 'Merchants Group List',
			2 => 'Resources Group List',
			3 => 'Resources Search List',
			4 => 'Merchants List',
			5 => 'Resources List',
			6 => 'Offers List',
			7 => 'Sales Resources List',
			8 => 'Sales Resources Search List',
			9 => 'Search Group List',
			11 => 'Event List',
			12 => 'Poi List',
			13 => 'Cart List',
			14 => 'Highlight',
			15 => 'Packages Search List',
		);
		private static $image_paths = array(
			'merchant' => '/merchants/',
			'resources' => '/products/unita/',
			'offers' => '/packages/',
			'services' => '/servizi/',
			'merchantgroup' => '/merchantgroups/',
			'tag' => '/tags/',
			'onsellunits' => '/products/unitavendita/',
			'resourcegroup' => '/products/condominio/',
			'variationplans' => '/variationplans/',
			'prices' => '/prices/',
			'events' => '/events/',
			'eventbanners' => '/eventbanners/',
			'poi' => '/poi/',
			'rating' => '/merchantcategories/',
			'mapsell' => '/mapsell/',
			'attachment' => 'images/attachments/',
			'routes' => '/routes/',
			'packages' => '/packages/',

		);

		private static $image_path_resized = array(
			'merchant_list'						=> '148x148',
			'merchant_list_default'				=> '148x148',
			'resource_list_default'				=> '148x148',
			'onsellunit_list_default'			=> '148x148',
			'resource_list_default_logo'		=> '148x148',
			'resource_list_merchant_logo'		=> '200x70',
			'merchant_logo'						=> '200x70',
			'merchant_logo_small'				=> '65x65',
			'merchant_logo_small_top'			=> '250x90',
			'merchant_logo_small_rapidview'		=> '200x70',
			'resourcegroup_list_default'		=> '148x148',
			'offer_list_default'				=> '148x148',
			'resource_service'					=> '24x24',
			'resource_planimetry'				=> '400x250',
			'merchant_gallery_full'				=> '500x375',
			'merchant_mono_full'				=> '770x545',
			'merchant_gallery_thumb'			=> '85x85',
			'resource_gallery_full'				=> '692x450',
			'resource_mono_full'				=> '640x450',
			'resource_gallery_thumb'			=> '85x85',
			'resource_gallery_full_rapidview'	=> '416x290',
			'resource_gallery_thumb_rapidview'	=> '80x53',
			'resource_mono_full_rapidview'		=> '416x290',
			'resource_gallery_default_logo'		=> '100x100',
			'onsellunit_gallery_full'			=> '550x300',
			'onsellunit_mono_full'				=> '550x300',
			'onsellunit_default_logo'			=> '250x250',
			'onsellunit_gallery_thumb'			=> '85x85',
			'onsellunit_map_default'			=> '85x85',
			'onsellunit_showcase'				=> '180x180',
			'onsellunit_gallery'				=> '106x67',
			'resourcegroup_map_default'			=> '85x85',
			'merchant_merchantgroup'			=> '40x40',
			'resource_search_grid'			=> '380x215',
			'merchant_resource_grid' => '380x215',
			'small' => '201x113',
			'medium' => '380x215',
			'big' => '820x460',
			'logomedium' => '148x148',
			'logobig' => '170x95',
			'img40' => '40x40',
			'img24' => '24x24',
			'tag24' => '24x24',
			'banner' => '790x90'
		);

		private static $image_resizes = array(
			'merchant_list' => 'width=100&bgcolor=FFFFFF',
			'merchant_logo' => 'width=200&bgcolor=FFFFFF',
			'merchant_logo_small' => 'width=65&height=65&bgcolor=FFFFFF',
			'merchant_logo_small_top' => 'width=250&height=90&bgcolor=FFFFFF',
			'merchant_logo_small_rapidview' => 'width=180&height=65&bgcolor=FFFFFF',
			'resource_list_default' => 'width=148&height=148&mode=crop&anchor=middlecente&bgcolor=FFFFFF',
			'onsellunit_list_default' => 'width=148&height=148&mode=crop&anchor=middlecenter&bgcolor=FFFFFF',
			'resource_list_default_logo' => 'width=148&height=148&bgcolor=FFFFFF',
			'resource_list_merchant_logo' =>  'width=200&height=70&bgcolor=FFFFFF',
			'merchant_list_default' => 'width=148&height=148&bgcolor=FFFFFF',
			'resourcegroup_list_default' => 'width=148&height=148&bgcolor=FFFFFF',
			'offer_list_default' => 'width=148&height=148&bgcolor=FFFFFF',
			'resource_service' => 'width=24&height=24',
			'resource_planimetry' => 'width=400&height=250&mode=pad&anchor=middlecenter',
			'merchant_gallery_full' => 'width=500&height=375&mode=pad&anchor=middlecenter',
			'merchant_mono_full' => 'width=770&height=545&mode=crop&anchor=middlecenter&scale=both',
			'merchant_gallery_thumb' => 'width=85&height=85&mode=crop&anchor=middlecenter',
			'resource_gallery_full' => 'width=692&height=450&mode=pad&anchor=middlecenter&ext=.jpg',
			'resource_mono_full' => 'width=640&height=450&mode=pad&anchor=middlecenter&scale=both',
			'resource_gallery_thumb' => 'width=85&height=85&mode=crop&anchor=middlecenter',
			'resource_gallery_full_rapidview' => 'w=416&h=290&mode=crop&anchor=middlecenter&ext=.jpg',
			'resource_gallery_thumb_rapidview' => 'width=80&height=53&mode=crop&anchor=middlecenter',
			'resource_mono_full_rapidview' => 'w=416&h=290&mode=crop&anchor=middlecenter&ext=.jpg',
			'resource_gallery_default_logo' => 'w=100&h=100&mode=pad&anchor=middlecenter&ext=.jpg',
			'onsellunit_gallery_full' => 'w=550&h=300&bgcolor=EDEDED&mode=pad&anchor=middlecenter&ext=.jpg',
			'onsellunit_mono_full' => 'width=550&height=300&mode=crop&anchor=middlecenter&scale=both',
			'onsellunit_default_logo' => 'width=250&height=250&bgcolor=FFFFFF',
			'onsellunit_gallery_thumb' => 'width=85&height=85&mode=crop&anchor=middlecenter',
			'onsellunit_map_default' => 'width=85&height=85&bgcolor=FFFFFF',
			'onsellunit_showcase' => 'width=180&height=180&bgcolor=FFFFFF&mode=crop&anchor=middlecenter',
			'onsellunit_gallery' => 'width=106&height=67&bgcolor=FFFFFF',
			'resourcegroup_map_default' => 'width=85&height=85&bgcolor=FFFFFF',
			'merchant_merchantgroup' => 'width=40&height=40',
			'small' => 'width=201&height=113&mode=crop&anchor=middlecenter',
			'medium' => 'width=380&height=215&mode=crop&anchor=middlecenter',
			'big' => 'width=820&height=460&mode=crop&anchor=middlecenter',
			'logomedium' => 'width=148&height=148&anchor=middlecenter&bgcolor=FFFFFF',
			'logobig' => 'width=170&height=95&anchor=middlecenter&bgcolor=FFFFFF',
			'tag24' => 'width=24&height=24'
		);
		//public static $typologiesMerchantResults = array(1,6);
		public static function isUnderHTTPS() {
			return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' );
		}

		public static function isMerchantBehaviour() {
			if (defined('COM_BOOKINGFORCONNECTOR_MERCHANTBEHAVIOUR')) {
				if (COM_BOOKINGFORCONNECTOR_MERCHANTBEHAVIOUR) {
					return true;
				}
			}
			return false;
		}

		public static function getItem($xml, $itemName, $itemContext = null) {
			if ($xml==null || $itemName == null) return '';
				$currErrLev = error_reporting();
				error_reporting(0);
			try {
				$xdoc = new SimpleXmlElement($xml);
				if (isset($itemContext)) $xdoc= $xdoc->$itemContext;
				$item = $xdoc->$itemName;

			} catch (Exception $e) {
				// maybe it's not a well formed XML?
				return $itemName;
			}
				error_reporting($currErrLev);
			return $item;
		}

		public static function getSubscriptionInfos() {
			$model = new BookingForConnectorModelPortal;
			return $model->getSubscriptionInfos();
		}

		public static function getMerchantFromServicebyId($merchantId) {
			$model = new BookingForConnectorModelMerchantDetails;
			return $model->getMerchantFromServicebyId($merchantId);
		}
		public static function getMerchantbyId($merchantId) {
			$model = new BookingForConnectorModelMerchantDetails;
			return $model->getMerchantFromServicebyId($merchantId);
		}


		public static function getResourcegroupFromServicebyId($resourceId) {
			$model = new BookingForConnectorModelResourcegroup;
			return $model->getResourcegroupFromService($resourceId);
		}



		public static function getEventById($id='', $language='') {
			$model = new BookingForConnectorModelEvent;
			return $model->getDetails($id, $language);
		}

		public static function GetResourcesById($id,$language='') {
		  $model = new BookingForConnectorModelResource;
			return $model->getItem($id);
		}
		public static function GetExperienceById($id,$language='') {
		  $model = new BookingForConnectorModelExperience;
			return $model->getItem($id);
		}

		public static function getTags($language='',$categoryIds='') {
			$model = new BookingForConnectorModelTags;
			return $model->getTags($language,$categoryIds,null,null);
		}

		public static function getSlug($string) {
			$s = array();
			$r = array();
			$s[0] = "/\&/";
			$r[0] = "and";
			$s[1] = '/[^a-z0-9-]/';
			$r[1] = '-';
			$s[2] = '/-+/';
			$r[2] = '-';
			$string = preg_replace( $s, $r, strtolower( trim( $string ) ) );
			return $string;
		}

		public static function containsUrl($string) {
			$re = '/^[a-zA-Z0-9\-\.\:\\\\]+\.(com|org|net|mil|edu|COM|ORG|NET|MIL|EDU)$/';
			$re1 = '/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/';
			$trimmed = trim( $string );
			if( preg_match($re,$trimmed) || preg_match($re1,$trimmed)){
				return true;
			}
			return false;

		}

		public static function getLanguage($xml, $langCode, $fallbackCode = 'en-gb', $opts = array() ) {
			if (!isset($xml)) {
				return '';
			}
			$retVal = $xml;

			if (isset($opts) && count($opts) > 0) {
				foreach ($opts as $key => $opt) {
					switch (strtolower($key)) {
						case 'ln2br':
							$retVal = nl2br($retVal, true);
							break;
						case 'htmlencode':
							$retVal = htmlentities($retVal, ENT_COMPAT);
							break;
						case 'striptags':
							$retVal = strip_tags($retVal, "<br><br/>");
							break;
						case 'nomore1br':
							$retVal = preg_replace("/\n+/", "\n", $retVal);
							break;
						case 'nobr':
							$retVal = preg_replace("/\n+/", " ", $retVal);
							break;
						case 'bbcode':
							$search = array (
								'~\[b\](.*?)\[/b\]~s',
								'~\[i\](.*?)\[/i\]~s',
								'~\[u\](.*?)\[/u\]~s',
								'~\[s\](.*?)\[/s\]~s',
								'~\[ul\](.*?)\[/ul\]~s',
								'~\[li\](.*?)\[/li\]~s',
								'~\[ol\](.*?)\[/ol\]~s',
								'~\[size=(.*?)\](.*?)\[/size\]~s',
								'~\[color=([^"><]*?)\](.*?)\[/color\]~s',
								'~\[url=(.*?)(\])(.*?)\[\/url\]~s',
								'~\[img\](https?://[^"><]*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]~s',
								'~\[img=(.*?)x(.*?)\](https?://[^"><]*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]~s',
								'~\[center\](.*?)\[/center\]~s',
								'~\[td\](.*?)\[/td\]~s',
								'~\[tr\](.*?)\[/tr\]~s',
								'~\[table\](.*?)\[/table\]~s',
								'~\[sup\](.*?)\[/sup\]~s',
								'~\[sub\](.*?)\[/sub\]~s',
								'~\[right\](.*?)\[/right\]~s',
								'~\[justify\](.*?)\[/justify\]~s',
								'/(?<=<ul>|<\/li>)\s*?(?=<\/ul>|<li>)/is'
							);
							$replace = array (
								'<b>$1</b>',
								'<i>$1</i>',
								'<u>$1</u>',
								'<s>$1</s>',
								'<ul>$1</ul>',
								'<li>$1</li>',
								'<ol>$1</ol>',
								'<font size="$1">$2</font>',
								'<span style="color:$1;">$2</span>',
								'<a href="$1" target="_blank" style="text-transform:none;">$3</a>',
								'<img src="$1" alt="" />',
								'<img width="$1" height="$2" src="$3" alt="" />',
								'<center>$1</center>',
								'<td>$1</td>',
								'<tr>$1</tr>',
								'<table>$1</table>',
								'<sup>$1</sup>',
								'<sub>$1</sub>',
								'<span style="display: block; text-align: right;">$1</span>',
								'<span style="display: block; text-align: justify;">$1</span>',
								''
							);
							$retVal = preg_replace($search, $replace, $retVal); // cleen for br

							break;
						default:
							break;
					}
				}
			}

			return $retVal;
		}
	/**
	 * Removes invalid XML
	 *
	 * @access public
	 * @param string $value
	 * @return string
	 */
		public static function stripInvalidXml($value)
	{
		$ret = "";
		$current;
		if (empty($value))
		{
			return $ret;
		}

		$length = strlen($value);
		for ($i=0; $i < $length; $i++)
		{
			$current = ord($value[$i]);
			if (($current == 0x9) ||
				($current == 0xA) ||
				($current == 0xD) ||
				(($current >= 0x20) && ($current <= 0xD7FF)) ||
				(($current >= 0xE000) && ($current <= 0xFFFD)) ||
				(($current >= 0x10000) && ($current <= 0x10FFFF)))
			{
				$ret .= chr($current);
			}
			else
			{
				$ret .= " ";
			}
		}
		return $ret;
	}
		public static function getQuotedString($str){
			if (isset($str) && $str!=null){
				return '\'' . $str . '\'';
				//return '\'' . str_replace('%27', '\'', $str) . '\'';
			}
			return null;
		}

		public static function getJsonEncodeString($str){
			if (isset($str) && $str!=null){
				return json_encode($str);
			}
			return null;

		}

		public static function parseJsonDate($date, $format = 'd/m/Y') {
			date_default_timezone_set('UTC');
			//preg_match( '/([\d]{13})/', $date, $matches);
			preg_match( '/(\-?)([\d]{9,})/', $date, $matches);
			// Match the time stamp (microtime) and the timezone offset (may be + or -)
			$formatDate = 'd/m/Y';
			if (isset($format) && $format!=""){
				$formatDate = $format;
			}
						
			 if (count($matches)<2) {
				 return $date;
			 }
			$date = date($formatDate, $matches[1].$matches[2]/1000 ); // convert to seconds from microseconds
			return $date;
		}

		public static function parseJsonDateTime($date, $format = 'd/m/Y') {
			date_default_timezone_set('UTC');
			return DateTime::createFromFormat($format, BFCHelper::parseJsonDate($date,$format),new DateTimeZone('UTC'));
		}
		public static function parseStringDateTime($date, $format = 'Y-m-d\TH:i:s') {
			date_default_timezone_set('UTC');
			return DateTime::createFromFormat($format, $date,new DateTimeZone('UTC'));
		}

		public static function parseArrayList($stringList, $fistDelimiter = ';', $secondDelimiter = '|'){
			$a = array();
			if(!empty($stringList)){
			foreach (explode($fistDelimiter, $stringList) as $aa) {
				list ($cKey, $cValue) = explode($secondDelimiter, $aa, 2);
				$a[$cKey] = $cValue;
			}
			}
			return $a;
		}

		public static function getImagePath($type) {
			return self::$image_paths[$type];
		}

		public static function getImageUrlResized($type, $path = null, $resizedpath = null ) {
			if ($path == '' || $path===null)
				return '';
			$finalPath = self::$image_basePathCDN . COM_BOOKINGFORCONNECTOR_IMGURL;
			if (isset($type) && isset(self::$image_paths[$type])) {
				$finalPath .= self::$image_paths[$type] ;
				if (!empty($resizedpath)) {
						$pathfilename = basename($path);
						if (isset(self::$image_path_resized[$resizedpath])) {
							$path = str_replace($pathfilename, self::$image_path_resized[$resizedpath] . "/".$pathfilename ,$path);
						} else {
							$path = str_replace($pathfilename, $resizedpath . "/".$pathfilename ,$path);
						}
				}
				$finalPath .= $path;
			}

			return $finalPath;
		}

		public static function formatDistanceUnits($meters)
		{
			if ($meters >= 1000)
			{
				//round to .1
				$meters = number_format(floor($meters / 1000),1) . ' Km';
			}
			elseif ($meters > 0)
			{
				// round under 50m
				$meters =  $meters + (50 -  $meters % 50);
				$meters = number_format(floor($meters),0)  . ' m';
			}
			else
			{
				$meters = '';
			}
			return $meters;
		}

		public static function getImageUrl($type, $path = null, $resizepars = null ) {
			if ($path == '' || $path===null)
				return '';
			$finalPath = self::$image_basePath;
			if (isset($type) && isset(self::$image_paths[$type])) {
				$finalPath .= self::$image_paths[$type] . $path;
				if (isset($resizepars)) {
					// resize params manually added
					if (is_array($resizepars)) {
						$params = '';
						foreach ($resizepars as $param) {
							if ($params=='')
								$params .= '?';
							else
								$params .= '&';
							$params .= $param;
						}
						if ($params!='') {
							$finalPath .= $params;
						}
					} else { // resize params as predefined configuration
						if (isset(self::$image_resizes[$resizepars])) {
							$finalPath .= '?' . self::$image_resizes[$resizepars];
						}
					}
				}
			}

			return $finalPath;
		}

		public static function setState($stateObj, $key, $namespace = null) {
			if (isset($namespace)) {
				$key = $namespace . '.' . $key;
			}
			self::$currentState[$key] = $stateObj;
		}

		public static function getState($key, $namespace = null) {
			if (isset($namespace)) {
				$key = $namespace . '.' . $key;
			}
			if (isset(self::$currentState[$key])) {
				return self::$currentState[$key];
			}
			return null;
		}

		public static function orderBy($a, $b, $ordering, $direction) {
			return ($a->$ordering < $b->$ordering) ?
			(
				($direction == 'desc')
					? 1
					: -1
			) :
			(
				($a->$ordering > $b->$ordering)
					?	(
							($direction == 'desc')
								? -1
								: 1
						)
					: 0
			);
		}

		public static function shorten_string($string, $amount)
		{
			 if(strlen($string) > $amount)
			{
				if ( function_exists( 'mb_substr' ) ){
                     $string = trim(mb_substr($string, 0, $amount)).'...';
                }else{
                    $string = trim(substr($string, 0, $amount))."...";
                }
			}
			return $string;
		}

		public static function getVar($string, $defaultValue=null) {			
//			return isset($_REQUEST[$string]) ?htmlspecialchars($_REQUEST[$string], ENT_QUOTES, 'UTF-8') : $defaultValue;
			$currVal= isset($_REQUEST[$string]) ? $_REQUEST[$string] : $defaultValue;
			if (!is_array($currVal) ) {
			    $currVal = htmlspecialchars($currVal, ENT_QUOTES, 'UTF-8');
			}else{
				foreach ($currVal as $key=>$val  ) {
				    $val=htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
				}
			}
			return $currVal;
		}
		public static function getFloat($string, $defaultValue=null) {

			$jinput = isset($_REQUEST[$string]) ? str_replace(",", ".", $_REQUEST[$string]) : $defaultValue;

			return floatval($jinput);
		}

		public static function getSession($string, $defaultValue=null, $prefix ='') {
			return isset($_SESSION[COM_BOOKINGFORCONNECTOR_SUBSCRIPTION_KEY.$prefix.$string]) ? $_SESSION[COM_BOOKINGFORCONNECTOR_SUBSCRIPTION_KEY.$prefix.$string] : $defaultValue;
		}
		public static function setSession($string, $value=null, $prefix ='') {
			$_SESSION[COM_BOOKINGFORCONNECTOR_SUBSCRIPTION_KEY.$prefix.$string] = $value;
		}



		public static function buildAddress($Address) {
			$indirizzo = isset($Address->Address)?$Address->Address:"";
			$cap = isset($Address->ZipCode)?$Address->ZipCode:""; 
			$comune = isset($Address->CityName)?$Address->CityName:"";
			$stato = isset($Address->StateName)?$Address->StateName:"";
			$strAddress = "";
			if (!empty($indirizzo)) {
				$strAddress = '<span class="street-address">' . $indirizzo .'</span>';
			}
			if (!empty($cap) || !empty($comune)) {
				if(!empty( $strAddress )){
					$strAddress .=", ";
				}
				$strAddress .= '<span class="postal-code">' . $cap .'</span> ';
				$strAddress .= '<span class="locality">' . $comune .'</span>';
			}
			if (!empty($stato)) {
				if(!empty( $strAddress )){
					$strAddress .=", ";
				}
				$strAddress .= '<span class="region">' . $stato .'</span>';
			}
			return $strAddress;
		}

		public static function string_sanitize($s) {
			$result = preg_replace("/[^a-zA-Z0-9\s]+/", "", html_entity_decode($s, ENT_QUOTES));
			return $result;
		}
		public static function escapeJavaScriptText($string)
		{
			return str_replace("\n", '\n', str_replace('"', '\"', addcslashes(str_replace("\r", '', (string)$string), "\0..\37'\\")));
		}
		public static function bfi_get_clientdata() {
			$ipClient = BFCHelper::bfi_get_client_ip();
			$ipServer = $_SERVER['SERVER_ADDR'];
			$uaClient = $_SERVER['HTTP_USER_AGENT'];
			$RequestTime = $_SERVER['REQUEST_TIME'];
			$Referer = $_SERVER['HTTP_REFERER'];
			$clientdata =
				"ipClient:" . str_replace( ":", "_", $ipClient) ."|".
				"ipServer:" . str_replace( ":", "_", $ipServer) ."|".
				"uaClient:" . str_replace( "|", "_", str_replace( ":", "_", $uaClient)) ."|".
				"Referer:" . str_replace( "|", "_", str_replace( ":", "_", $Referer)) ."|".
				"RequestTime:" . $RequestTime;
			return $clientdata;
		}
		public static function bfi_get_client_ip() {
			$ipaddress = '';
			if (isset($_SERVER['HTTP_CLIENT_IP']))
				$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
			else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
				$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
			else if(isset($_SERVER['HTTP_X_FORWARDED']))
				$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
			else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
				$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
			else if(isset($_SERVER['HTTP_FORWARDED']))
				$ipaddress = $_SERVER['HTTP_FORWARDED'];
			else if(isset($_SERVER['REMOTE_ADDR']))
				$ipaddress = $_SERVER['REMOTE_ADDR'];
			else
				$ipaddress = 'UNKNOWN';

			return $ipaddress;
		}
		public static function bfi_get_curr_url() {
			return (isset($_SERVER['HTTPS']) ? "https" : "http") . ':' ."//" .$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		}

		public static function bfi_utf8ize($d) {
			if (is_array($d))
				foreach ($d as $k => $v)
					$d[$k] = self::bfi_utf8ize($v);
			 else if(is_object($d))
				foreach ($d as $k => $v)
					$d->$k = self::bfi_utf8ize($v);
			 else
				return utf8_encode($d);
			return $d;
		}

		public static function getResourcesbyIdMerchant($start, $limit, $merchantId = NULL, $parentId = NULL) {
		  $model = new BookingForConnectorModelMerchantDetails;
			return $model->getMerchantResourcesFromSearch($start, $limit, $merchantId, $parentId);
		}


//----------------------------------
//	country functions
//----------------------------------
		public static function bfi_get_country_name_by_code( $country_code ) {
			if (empty($country_code) && strlen ($country_code) < 2) {
			    return false;
			}
			if (strlen ($country_code) > 2) {
				$country_code = substr($country_code,0,2);
			}
			$country_code = strtoupper($country_code);
			return ( isset( self::$listCountries[$country_code] ) ? self::$listCountries[$country_code] : false );
		}

		public static function bfi_get_country_code_by_name( $country_name ) {
			if (empty($country_name) ) {
			    return false;
			}
			return ( in_array ( $country_name , self::$listCountries ) ? array_search($country_name,  self::$listCountries) : false );
		}

//----------------------------------
//	END country functions
//----------------------------------

		public static function GetSettingValue($array, $data){
			if(!empty( $array )){
				foreach($array as $currObj) {
				   if( isset($currObj->SettingKey) && $currObj->SettingKey == $data) {
					   return strtolower($currObj->SettingValue)==='true';
					   }
				}
			}
			return false;
		}

		public static function getPageId($refpage){
			$currPage = get_post( bfi_get_page_id( $refpage ) );
			if (!empty($currPage )) {
				return $currPage->ID;
			}
			return 0;
		}

		public static function getPageUrl($refpage){
			$currPageId = self::getPageId( $refpage );
			return self::getPageUrlbyId($currPageId);
		}

		public static function getPageUrlbyId($currPageId){
			if (!empty($currPageId )) {
				$currUrl =  get_permalink( $currPageId);
				if(substr($currUrl , -1)!=='/'){
					$currUrl .= '/';
				}
				return $currUrl;
			}
			return '';
		}

		public static function getPageUrlbyIdtranslated($currPageId){
			if( isset($currPageId) && defined( 'POLYLANG_VERSION' ) ) {
				$currPageId = pll_get_post( $currPageId );				
			}
			
			$currPage = get_post( $currPageId );
			if (!empty($currPage )) {
				$currUrl =  get_permalink( $currPage->ID);
				if(substr($currUrl , -1)!=='/'){
					$currUrl .= '/';
				}
				return $currUrl;
			}
			return '';
		}

		public static function is_dir_empty($dir) {
			if (!is_readable($dir)) return NULL; 
			return (count(scandir($dir)) == 2);
		}


		/**--------per widgets  ---**/
		public static function getMerchantCategories($language='') {
		  $model = new BookingForConnectorModelPortal;
			return $model->getMerchantCategoriesFromService($language);
		}
		public static function GetProductCategoryForSearch($language='', $typeId = 1,$merchantid=0) {
			$model = new BookingForConnectorModelPortal;
			return $model->getProductCategoryForSearch($language, $typeId,$merchantid);
		}


	}
	class MyDateTime extends \DateTime implements \JsonSerializable
	{
		public function __construct(DateTime $dt) {
			parent::__construct($dt->format("r"));
		}
		
		public function jsonSerialize()
		{
			return $this->format("Y-m-d\Th:m:s");
		}
	}
}
