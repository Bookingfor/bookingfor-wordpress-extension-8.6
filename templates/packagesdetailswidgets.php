<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
 ?>
<?php

BFI()->define( "DONOTCACHEPAGE", true ); // Do not cache this page
	$resource_id = get_query_var( 'resource_id', 0 );
	$language = $GLOBALS['bfi_lang'];
	$layout = get_query_var( 'bfi_layout', '' );
	$sitename = sanitize_text_field( get_bloginfo( 'name' ) );

	$model = new BookingForConnectorModelPackage;
	$resource = $model->getItem($resource_id);	 
	if (empty($resource)) {
		  global $wp_query;
		  $wp_query->set_404();
		  status_header( 404 );
		  get_template_part( 404 );
		  exit();		
	}
	
	/*---------------IMPOSTAZIONI SEO----------------------*/
		$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('nobr'=>'nobr', 'striptags'=>'striptags')); 
		$seoDescr = !empty($resource->SEODescription)?$resource->SEODescription:$resource->Description;
		$resourceDescriptionSeo = BFCHelper::getLanguage($seoDescr, $language, null, array( 'nobr'=>'nobr', 'bbcode'=>'bbcode', 'striptags'=>'striptags')) ;
		if (!empty($resourceDescriptionSeo) && strlen($resourceDescriptionSeo) > 160) {
			$resourceDescriptionSeo = substr($resourceDescriptionSeo,0,160);
		}
		$url_resource_page = BFCHelper::GetPageUrl('packagesdetails');
		$routeResource = $url_resource_page.$resource->PackageId.'-'.BFI()->seoUrl((!empty($resource->SEOSlugName)?$resource->SEOSlugName:$resourceName));
		$canonicalUrl = $routeResource;

		$indirizzo = "";
		$cap = "";
		$comune = "";
		$stato = "";
		$provincia = "";
		$resourceLat = "";
		$resourceLon = "";
		$titleHead = !empty($resource->SEOTitle)?$resource->SEOTitle:"$resourceName - $resource->CategoryNames - $sitename";
		$keywordsHead = !empty($resource->SEOkeywords)?$resource->SEOkeywords:"$resourceName, $resource->CategoryNames";

		if(!empty( $resource->Address )){
			$indirizzo = $resource->Address->Address;
			$cap = $resource->Address->ZipCode;
			$comune = $resource->Address->CityName;
			$provincia = $resource->Address->RegionName;
			$stato = !empty($resource->Address->StateName)?$resource->Address->StateName:"";
			if(!empty($resource->Address->XPos)){
				$resourceLat = $resource->Address->XPos;
			}
			if(!empty($resource->Address->YPos)){
				$resourceLon = $resource->Address->YPos;
			}
			$titleHead = !empty($resource->SEOTitle)?$resource->SEOTitle:"$resourceName ($comune, $stato) - $resource->CategoryNames - $sitename";
			$keywordsHead = !empty($resource->SEOkeywords)?$resource->SEOkeywords:"$resourceName, $comune, $stato, $resource->CategoryNames";
		}
		$resourceRoute = $routeResource ;

	/* microformat */

	$payloadaddress["@type"] = "PostalAddress";
	$payloadaddress["streetAddress"] = $indirizzo;
	$payloadaddress["addressLocality"] = $comune;
	$payloadaddress["postalCode"] = $cap;
	$payloadaddress["addressRegion"] = $provincia;
	$payloadaddress["addressCountry"] =  BFCHelper::bfi_get_country_code_by_name($stato);

	// SEO
	$payloadresource["@context"] = "http://schema.org";
	$payloadresource["@type"] = "TouristAttraction";
	$payloadresource["location"] = $payloadaddress;
	$payloadresource["name"] = $resourceName;
	$payloadresource["description"] = $resourceDescriptionSeo;
	$payloadresource["url"] = $resourceRoute; 
	if (!empty($resource->ImageUrl)){
		$payloadresource["image"] = "https:".BFCHelper::getImageUrlResized('packages',$resource->DefaultImg, 'logobig');
	}
	if (!empty($resourceLat) && !empty($resourceLon)) {
		$payloadgeo["@type"] = "GeoCoordinates";
		$payloadgeo["latitude"] = $resourceLat;
		$payloadgeo["longitude"] = $resourceLon;
		$payloadresource["geo"] = $payloadgeo; 
	}

	/* end microformat */

		if ( defined('WPSEO_VERSION') ) {
					add_filter( 'wpseo_title', function() use ($titleHead) {return	$titleHead;} , 10, 1 );
					add_filter( 'wpseo_metakey', function() use ($keywordsHead) {return $keywordsHead; } , 10, 1  );
					add_filter( 'wpseo_metadesc', function() use ($resourceDescriptionSeo) {return $resourceDescriptionSeo; } , 10, 1 );
					add_filter( 'wpseo_robots', function() {return "index,follow"; } , 10, 1 );
					add_filter( 'wpseo_canonical', function() use ($canonicalUrl) {
						if(substr($canonicalUrl , -1)!= '/'){
							$canonicalUrl .= '/';
						}
						return $canonicalUrl; 
					} , 10, 1 );
					// OpenGraph for Social
					add_filter( 'wpseo_opengraph_url', function() use ($canonicalUrl) {
						if(substr($canonicalUrl , -1)!= '/'){
							$canonicalUrl .= '/';
						}
						return $canonicalUrl; 
					} , 10, 1 );		
					add_filter( 'wpseo_opengraph_title', function() use ($titleHead) {return	$titleHead;});
					add_filter( 'wpseo_opengraph_desc', function() use ($resourceDescriptionSeo) {return	$resourceDescriptionSeo;});
										
					if (!empty($resource->DefaultImg)){
						add_action( 'wpseo_add_opengraph_images', 'add_images' );
						function add_images( $object ) {
						  $object->add_image( COM_BOOKINGFORCONNECTOR_DEFAULTIMAGE );
						}
						add_filter( 'wpseo_opengraph_image', function() use ($resource) {return	"https:".BFCHelper::getImageUrlResized('packages',$resource->DefaultImg, 'medium');} );
					}
					/* microformat */
					add_action( 'wpseo_head', function() use ($payloadresource) { bfi_add_json_ld( $payloadresource ); } , 30);
		}else{
			add_filter( 'wp_title', function() use ($titleHead) {return	$titleHead;} , 10, 1 );
			add_action( 'wp_head', function() use ($keywordsHead) {return bfi_add_meta_keywords($keywordsHead); }, 10, 1);
			add_action( 'wp_head', function() use ($resourceDescriptionSeo) {return bfi_add_meta_description($resourceDescriptionSeo); } , 10, 1 );
			add_action( 'wp_head', 'bfi_add_meta_robots', 10, 1);
			remove_action('wp_head', 'rel_canonical');
			add_action( 'wp_head', function() use ($canonicalUrl) {return bfi_add_canonicalurl($canonicalUrl); }, 10, 1);
					/* microformat */
			add_action( 'wp_head', function() use ($payloadresource) { bfi_add_json_ld( $payloadresource ); } , 10, 1 );
			// OpenGraph for Social
			add_action( 'wp_head', function() use ($titleHead) {return bfi_add_opengraph_title($titleHead); }, 10, 1);
			add_action( 'wp_head', function() use ($resourceDescriptionSeo) {return bfi_add_opengraph_desc($resourceDescriptionSeo); } , 10, 1 );
			add_action( 'wp_head', function() use ($canonicalUrl) {return bfi_add_opengraph_url($canonicalUrl); }, 10, 1);
			if (!empty($resource->DefaultImg)){
				add_action( 'wp_head', function() use ($resource) {return bfi_add_opengraph_image("https:".BFCHelper::getImageUrlResized('packages',$resource->DefaultImg, 'medium')); }, 10, 1);
			}
		}
	/*--------------- END IMPOSTAZIONI SEO----------------------*/
	get_header( 'packagesdetails' );
	do_action( 'bookingfor_before_main_content' );
?>
<bfi-page class="bfi-page-container bfi-packagesdetails-page ">
	<div class="bfi_page_container">
		<div class="bookingforwidget" path="package" 
			data-Id="<?php echo $resource_id ?>"
			data-languages="<?php echo substr($language,0,2) ?>">
			<div id="bficontainer" class="bfi-loader"></div>
		</div>
	</div>
</bfi-page>
<?php
	do_action( 'bookingfor_after_main_content' );	
	get_footer( 'packagesdetails' );
?>
