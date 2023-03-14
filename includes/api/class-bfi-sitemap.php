<?php
/**
 * Contains the query functions for Bookingfor which alter the front-end post queries and loops
 *
 * @class 		BFI_Controller
 * @version             2.0.5
 * @package		
 * @category	        Class
 * @author 		Bookingfor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BFI_Sitemap' ) ) {
	/**
	 * BFI_Controller Class.
	 */
	class BFI_Sitemap {

		/**
		 * Constructor for the query class. Hooks in methods.
		 *
		 * @access public
		 */
		private $formlabel = null;
		protected $stylesheet_index = '';
		public function __construct() {
			$this->formlabel = COM_BOOKINGFORCONNECTOR_FORM_KEY;
			$stylesheet_url = $this->get_sitemap_stylesheet_url();

			if ( $stylesheet_url ) {
				$this->stylesheet = '<?xml-stylesheet type="text/xsl" href="' . esc_url( $stylesheet_url ) . '" ?>';
			}

			$stylesheet_index_url = $this->get_sitemap_index_stylesheet_url();

			if ( $stylesheet_index_url ) {
				$this->stylesheet_index = '<?xml-stylesheet type="text/xsl" href="' . esc_url( $stylesheet_index_url ) . '" ?>';
			}
		}
		
	public function sitemaps_enabled() {
		$is_enabled = (bool) get_option( 'bfi_enable_xml_sitemap_key',0 );
		return $is_enabled;
//		/**
//		 * Filters whether XML Sitemaps are enabled or not.
//		 *
//		 * When XML Sitemaps are disabled via this filter, rewrite rules are still
//		 * in place to ensure a 404 is returned.
//		 *
//		 * @see WP_Sitemaps::register_rewrites()
//		 *
//		 * @since 5.5.0
//		 *
//		 * @param bool $is_enabled Whether XML Sitemaps are enabled or not. Defaults
//		 * to true for public sites.
//		 */
//		return (bool) apply_filters( 'wp_sitemaps_enabled', $is_enabled );
	}
	/**
	 * Gets the URL for the sitemap index stylesheet.
	 *
	 * @since 5.5.0
	 *
	 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
	 *
	 * @return string The sitemap index stylesheet URL.
	 */
	public function get_sitemap_stylesheet_url() {
		global $wp_rewrite;

		$sitemap_url = home_url( '/sitemap_bfi.xsl' );

		if ( ! $wp_rewrite->using_permalinks() ) {
			$sitemap_url = home_url( '/?sitemap-stylesheetbfi=2' );
		}
		return $sitemap_url;
		/**
		 * Filters the URL for the sitemap index stylesheet.
		 *
		 * If a falsey value is returned, no stylesheet will be used and
		 * the "raw" XML of the sitemap index will be displayed.
		 *
		 * @since 5.5.0
		 *
		 * @param string $sitemap_url Full URL for the sitemaps index XSL file.
		 */
//		return apply_filters( 'wp_sitemaps_stylesheet_index_url', $sitemap_url );
	}
	/**
	 * Gets the URL for the sitemap index stylesheet.
	 *
	 * @since 5.5.0
	 *
	 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
	 *
	 * @return string The sitemap index stylesheet URL.
	 */
	public function get_sitemap_index_stylesheet_url() {
		global $wp_rewrite;

		$sitemap_url = home_url( '/sitemap_bfi_index.xsl' );

		if ( ! $wp_rewrite->using_permalinks() ) {
			$sitemap_url = home_url( '/?sitemap-stylesheet=1' );
		}
		return $sitemap_url;

		/**
		 * Filters the URL for the sitemap index stylesheet.
		 *
		 * If a falsey value is returned, no stylesheet will be used and
		 * the "raw" XML of the sitemap index will be displayed.
		 *
		 * @since 5.5.0
		 *
		 * @param string $sitemap_url Full URL for the sitemaps index XSL file.
		 */
//		return apply_filters( 'wp_sitemaps_stylesheet_index_url', $sitemap_url );
	}

	public function render_sitemaps() {
		global $wp; 
		global $wp_query;

		$sitemap         = sanitize_text_field( $wp->query_vars['sitemapbfi'] );
		$object_subtype  = sanitize_text_field(  $wp->query_vars[ 'sitemap-subtype' ] );
		$paged  = sanitize_text_field(  $wp->query_vars[ 'paged' ] );
		$stylesheet_type = sanitize_text_field( $wp->query_vars['sitemap-stylesheet' ] );
		
//		$stylesheet_type = sanitize_text_field( get_query_var( 'sitemap-stylesheet' ) );
//		$paged           = absint( get_query_var( 'paged' ) );

//		// Bail early if this isn't a sitemap or stylesheet route.
		if ( ! ( $sitemap || $stylesheet_type ) ) {
			return;
		}
//		if ( ! ( $sitemap ) ) {
//			return;
//		}

		if ( ! $this->sitemaps_enabled() ) {
			$wp_query->set_404();
			status_header( 404 );
			return;
		}

		// Render stylesheet if this is stylesheet route.
		if ( $stylesheet_type ) {
			$stylesheet = new BFI_Sitemaps_Stylesheet();

			$stylesheet->render_stylesheet( $stylesheet_type );
			exit;
		}

		// Render the index.
		if ( '1' == $sitemap ) {
			$sitemap_list = $this->get_sitemap_entries();
			$this->render_index( $sitemap_list );
			exit;
		}

//
//		if ( empty( $paged ) ) {
//			$paged = 1;
//		}
//
		$url_list = $this->get_url_list( $paged, $object_subtype );
//
//		// Force a 404 and bail early if no URLs are present.
		if ( empty( $url_list ) ) {
			$wp_query->set_404();
			status_header( 404 );
			return;
		}

		$this->render_sitemap( $url_list );
		exit;
	}

	public function render_index( $sitemaps ) {
		header("Cache-Control: no-cache");
		header( 'Content-type: application/xml; charset=UTF-8' );

		$this->check_for_simple_xml_availability();

		$index_xml = $this->get_sitemap_index_xml( $sitemaps );

		if ( ! empty( $index_xml ) ) {
			// All output is escaped within get_sitemap_index_xml().
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $index_xml;
		}
	}
	public function render_sitemap( $url_list ) {
		header("Cache-Control: no-cache");
		header( 'Content-type: application/xml; charset=UTF-8' );

		$this->check_for_simple_xml_availability();

		$sitemap_xml = $this->get_sitemap_xml( $url_list );

		if ( ! empty( $sitemap_xml ) ) {
			// All output is escaped within get_sitemap_xml().
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $sitemap_xml;
		}
	}

	public function get_sitemap_entries() {
		$portalinfo =  BFCHelper::getSubscriptionInfos();
		$languages = BFCHelper::get_active_languages();

		$sitemaps = array();

		$sitemap_types = ["products","productgroups","onsells"];
		$isportal = (bool) get_option( 'bfi_isportal_key',1 );

		// solo se è un portale allora creo la lista dei merchants
		if ($isportal ) {
			    $sitemap_types[] =  "merchants";
		    
		}
		// controllo quali Plugin sono attivati e nel caso li aggiungo
		if ($portalinfo!= null && !empty($portalinfo->PluginsString) ) {
			$portalinfo->Plugins = json_decode($portalinfo->PluginsString);
			
			// se ha gli eventi allora ha anche i POI
			if (in_array("events",$portalinfo->Plugins)) {
			    $sitemap_types[] =  "events";
			    $sitemap_types[] =  "poi";
			}
			
			if (in_array("packages",$portalinfo->Plugins)) {
			    $sitemap_types[] =  "packages";
			}
		}
		foreach ($languages as $currKey=>$page){
			foreach ( $sitemap_types as $type ) {
					$sitemap_entry = array(
						'loc' => $this->get_sitemap_url( $type, $page ),
					);
					$sitemaps[] = $sitemap_entry;
			}
		}

		return $sitemaps;
	}

	public function get_sitemap_url( $name, $page ) {
		global $wp_rewrite;

		// Accounts for cases where name is not included, ex: sitemaps-users-1.xml.
		$params = array_filter(
			array(
//				'sitemap'         => $this->name,
				'sitemap-subtype' => $name,
				'paged'           => $page,
			)
		);

		$basename = sprintf(
			'/sitemap_bfi-%1$s.xml',
			implode( '-', $params )
		);

		if ( ! $wp_rewrite->using_permalinks() ) {
			$basename = '/?' . http_build_query( $params, '', '&' );
		}

		return home_url( $basename );
	}

	public function get_url_list( $page, $object_subtype = '' ) {
		$url_list = array();
		$languages = BFCHelper::get_active_languages();
		$url_merchant_page = [];
		$url_resource_page = [];
		$url_resource_page_experience = [];
		$url_event_page = [];
		$url_pointsofinterestdetails_page = [];
		$url_package_page = [];
		$url_resourcegroup_page = [];
		$url_onsell_page = [];

		foreach ($languages as $currKey=>$language){
			$url_merchant_page[$language] = BFCHelper::getPageUrlByLang('merchantdetails',$language );
			$url_resource_page[$language] = BFCHelper::getPageUrlByLang('accommodationdetails',$language );
			$url_resource_page_experience[$language] = BFCHelper::getPageUrlByLang('experiencedetails',$language);
			$url_event_page[$language] = BFCHelper::getPageUrlByLang('eventdetails',$language );
			$url_pointsofinterestdetails_page[$language] = BFCHelper::getPageUrlByLang('pointsofinterestdetails',$language );
			$url_package_page[$language] = BFCHelper::getPageUrlByLang('packagesdetails',$language );
			$url_resourcegroup_page[$language] = BFCHelper::getPageUrlByLang('resourcegroupdetails',$language );
			$url_onsell_page[$language] = BFCHelper::getPageUrlByLang('onselldetails',$language );
		}
		if ($object_subtype == 'merchants') {
			$listMerchants = BFCHelper::getMerchantsList(implode($languages,","));
				if(!empty( $listMerchants )){
					foreach ($listMerchants as $currKey=>$merchant){
						if (empty($page) || $merchant->CultureCode == $page) {
							$currUriMerchant = $url_merchant_page[$merchant->CultureCode] . $merchant->MerchantId.'-'.BFI()->seoUrl((!empty($merchant->SEOSlugName)?$merchant->SEOSlugName:$merchant->Name));
							$sitemap_entry = array(
								'loc' => $currUriMerchant,
							);
							$url_list[]	= $sitemap_entry;
						}
					}
				}
		}
		if ($object_subtype == 'productgroups') {
			$listProductGroups = BFCHelper::getProductGroupList(implode($languages,","));
				if(!empty( $listProductGroups )){
					foreach ($listProductGroups as $currKey=>$productGroup){
						if (empty($page) || $productGroup->CultureCode == $page) {
							$currUriResourceGroup = $url_resourcegroup_page[$productGroup->CultureCode] . $productGroup->ProductGroupId.'-'.BFI()->seoUrl((!empty($productGroup->SEOSlugName)?$productGroup->SEOSlugName:$productGroup->Name));
							$sitemap_entry = array(
								'loc' => $currUriResourceGroup,
							);
							$url_list[]	= $sitemap_entry;
						}
					}
				}
		}
		if ($object_subtype == 'onsells') {
			$listOnSells = BFCHelper::getResourcesOnSellList(implode($languages,","));
				if(!empty( $listOnSells )){
					foreach ($listOnSells as $currKey=>$onSell){
						if (empty($page) || $onSell->CultureCode == $page) {
							$currUriResourceGroup = $url_onsell_page[$onSell->CultureCode] . $onSell->ResourceId.'-'.BFI()->seoUrl((!empty($onSell->SEOSlugName)?$onSell->SEOSlugName:$onSell->Name));
							$sitemap_entry = array(
								'loc' => $currUriResourceGroup,
							);
							$url_list[]	= $sitemap_entry;
						}
					}
				}
		}
		if ($object_subtype == 'products') {
				$listResources = BFCHelper::getResourcesList(implode($languages,","));
				if(!empty( $listResources )){
					foreach ($listResources as $currKey=>$resource){
						if (empty($page) || $resource->CultureCode == $page) {
							switch ($resource->ItemTypeId ) {
								case bfi_ItemType::Experience :
									$currUriresource  = $url_resource_page_experience[$resource->CultureCode].$resource->ResourceId.'-'.BFI()->seoUrl((!empty($resource->SEOSlugName)?$resource->SEOSlugName:$resource->Name));
								break;
								default:      
									$currUriresource  = $url_resource_page[$resource->CultureCode].$resource->ResourceId.'-'.BFI()->seoUrl((!empty($resource->SEOSlugName)?$resource->SEOSlugName:$resource->Name));
								break;
							}
							$sitemap_entry = array(
								'loc' => $currUriresource,
							);
							$url_list[]	= $sitemap_entry;
						}
					}
				}	
			}
		if ($object_subtype == 'events') {
				$listEvents = BFCHelper::getEventsList(implode($languages,","));
				if( !empty( $listEvents )){
					foreach ($listEvents as $currKey=>$event){
						if (empty($page) || $event->CultureCode == $page) {
							$currUriEvent  = $url_event_page[$event->CultureCode].$event->EventId.'-'.BFI()->seoUrl((!empty($event->SEOSlugName)?$event->SEOSlugName:$event->Name));
							$sitemap_entry = array(
								'loc' => $currUriEvent,
							);
							$url_list[]	= $sitemap_entry;
						}
					}
				}	
			}

		if ($object_subtype == 'poi') {
				$listpois = BFCHelper::getPOIList(implode($languages,","));
				if( !empty( $listpois )){
					foreach ($listpois as $currKey=>$poi){
						if (empty($page) || $poi->CultureCode == $page) {
							$currUriPoi  = $url_pointsofinterestdetails_page[$poi->CultureCode].$poi->PointOfInterestId.'-'.BFI()->seoUrl((!empty($poi->SEOSlugName)?$poi->SEOSlugName:$poi->Name));
							$sitemap_entry = array(
								'loc' => $currUriPoi,
							);
							$url_list[]	= $sitemap_entry;
						}
					}
				}	
			}

		if ($object_subtype == 'packages') {
				$listPackages = BFCHelper::getPackagesList(implode($languages,","));
				if(!empty( $listPackages )){
					foreach ($listPackages as $currKey=>$package){
						if (empty($page) || $package->CultureCode == $page) {
							$currUriPackage  = $url_package_page[$package->CultureCode].$package->PackageId.'-'.BFI()->seoUrl((!empty($package->SEOSlugName)?$package->SEOSlugName:$package->Name));
							$sitemap_entry = array(
								'loc' => $currUriPackage,
							);
							$url_list[]	= $sitemap_entry;
						}
					}
				}
			}

		return $url_list;
	}

	/**
	 * Checks for the availability of the SimpleXML extension and errors if missing.
	 *
	 * @since 5.5.0
	 */
	private function check_for_simple_xml_availability() {
		if ( ! class_exists( 'SimpleXMLElement' ) ) {
			add_filter(
				'wp_die_handler',
				static function () {
					return '_xml_wp_die_handler';
				}
			);

			wp_die(
				sprintf(
					/* translators: %s: SimpleXML */
					esc_xml( __( 'Could not generate XML sitemap due to missing %s extension' ) ),
					'SimpleXML'
				),
				esc_xml( __( 'WordPress &rsaquo; Error' ) ),
				array(
					'response' => 501, // "Not implemented".
				)
			);
		}
	}
	/**
	 * Gets XML for a sitemap index.
	 *
	 * @since 5.5.0
	 *
	 * @param array $sitemaps Array of sitemap URLs.
	 * @return string|false A well-formed XML string for a sitemap index. False on error.
	 */
	public function get_sitemap_index_xml( $sitemaps ) {
		$sitemap_index = new SimpleXMLElement(
			sprintf(
				'%1$s%2$s%3$s',
				'<?xml version="1.0" encoding="UTF-8" ?>',
				$this->stylesheet_index,
				'<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" />'
			)
		);

		foreach ( $sitemaps as $entry ) {
			$sitemap = $sitemap_index->addChild( 'sitemap' );

			// Add each element as a child node to the <sitemap> entry.
			foreach ( $entry as $name => $value ) {
				if ( 'loc' === $name ) {
					$sitemap->addChild( $name, esc_url( $value ) );
				} elseif ( 'lastmod' === $name ) {
					$sitemap->addChild( $name, esc_xml( $value ) );
				} else {
					_doing_it_wrong(
						__METHOD__,
						sprintf(
							/* translators: %s: List of element names. */
							__( 'Fields other than %s are not currently supported for the sitemap index.' ),
							implode( ',', array( 'loc', 'lastmod' ) )
						),
						'5.5.0'
					);
				}
			}
		}

		return $sitemap_index->asXML();
	}

	/**
	 * Gets XML for a sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param array $url_list Array of URLs for a sitemap.
	 * @return string|false A well-formed XML string for a sitemap index. False on error.
	 */
	public function get_sitemap_xml( $url_list ) {
		$urlset = new SimpleXMLElement(
			sprintf(
				'%1$s%2$s%3$s',
				'<?xml version="1.0" encoding="UTF-8" ?>',
				$this->stylesheet,
				'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" />'
			)
		);

		foreach ( $url_list as $url_item ) {
			$url = $urlset->addChild( 'url' );

			// Add each element as a child node to the <url> entry.
			foreach ( $url_item as $name => $value ) {
				if ( 'loc' === $name ) {
					$url->addChild( $name, esc_url( $value ) );
				} elseif ( in_array( $name, array( 'lastmod', 'changefreq', 'priority' ), true ) ) {
					$url->addChild( $name, esc_xml( $value ) );
				} else {
					_doing_it_wrong(
						__METHOD__,
						sprintf(
							/* translators: %s: List of element names. */
							__( 'Fields other than %s are not currently supported for sitemaps.' ),
							implode( ',', array( 'loc', 'lastmod', 'changefreq', 'priority' ) )
						),
						'5.5.0'
					);
				}
			}
		}

		return $urlset->asXML();
	}

	/**
	 * Notify Google of the updated sitemap.
	 */
	public function ping_search_engines() {

		$url = rawurlencode(BFI()->get_site_url(). '/sitemap_bfi_index.xml' );


		// Ping Google about our sitemap change.
		wp_remote_get( 'https://www.google.com/ping?sitemap=' . $url, [ 'blocking' => false ] );
		wp_remote_get( 'https://www.bing.com/ping?sitemap=' . $url, [ 'blocking' => false ] );
	}

	public function DeleteCacheSitemap(){
			$currScope = bfi_TagsScope::Sitemap;
			if (file_exists (COM_BOOKINGFORCONNECTOR_CACHEDIR) && !BFCHelper::is_dir_empty(COM_BOOKINGFORCONNECTOR_CACHEDIR)) {
				$mask = 'bfi_' . $currScope . '__*.cache';
				array_map('unlink', glob(COM_BOOKINGFORCONNECTOR_CACHEDIR . '/' . $mask));
			}
			if (file_exists (COM_BOOKINGFORCONNECTOR_CACHEDIRBOT) && !BFCHelper::is_dir_empty(COM_BOOKINGFORCONNECTOR_CACHEDIRBOT)) {
				$mask = 'bfi_' . $currScope . '__*.cache';
				array_map('unlink', glob(COM_BOOKINGFORCONNECTOR_CACHEDIRBOT . '/' . $mask));
			}
	}


	} //end class
}