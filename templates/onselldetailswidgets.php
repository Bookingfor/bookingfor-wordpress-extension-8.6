<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	$resource_id = get_query_var( 'resource_id', 0 );
	$language = $GLOBALS['bfi_lang'];
	$layout = get_query_var( 'bfi_layout', '' );
	$sitename = sanitize_text_field( get_bloginfo( 'name' ) );
	$base_url = get_site_url();
	$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;


		$model = new BookingForConnectorModelOnSellUnit;
		$resource = $model->getItem($resource_id);	 
		if (empty($resource)) {
			  global $wp_query;
			  $wp_query->set_404();
			  status_header( 404 );
			  get_template_part( 404 );
			  exit();		
		}


	$merchant = $resource->Merchant;
	$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('nobr'=>'nobr', 'striptags'=>'striptags')); 
	$merchantName = BFCHelper::getLanguage($merchant->Name, $language, null, array('nobr'=>'nobr', 'striptags'=>'striptags')); 
	$resourceDescription = BFCHelper::getLanguage($resource->Description, $language, null, array('ln2br'=>'ln2br', 'bbcode'=>'bbcode', 'striptags'=>'striptags'));

	$indirizzo = isset($resource->Address)?$resource->Address:"";
	$cap = isset($resource->ZipCode)?$resource->ZipCode:""; 
	$comune = isset($resource->CityName)?$resource->CityName:"";
	$stato = isset($resource->StateName)?$resource->StateName:"";


	$url_resource_page = BFCHelper::getPageUrl("onselldetails");
	$routeResource = $url_resource_page.$resource->ResourceId.'-'.BFI()->seoUrl((!empty($resource->SEOSlugName)?$resource->SEOSlugName:$resourceName));
	$canonicalUrl = $routeResource;
	$routeMerchant = BFCHelper::getPageUrl("merchantdetails") . $merchant->MerchantId.'-'.BFI()->seoUrl((!empty($merchant->SEOSlugName)?$merchant->SEOSlugName:$merchant->Name));

	/*---------------IMPOSTAZIONI SEO----------------------*/
	$seoDescr = !empty($resource->SEODescription)?$resource->SEODescription:$resource->Description;
	$seoMerchantDescr = !empty($merchant->SEODescription)?$merchant->SEODescription:$merchant->Description;
	$merchantDescriptionSeo = BFCHelper::getLanguage($seoDescr, $language, null, array( 'nobr'=>'nobr', 'bbcode'=>'bbcode', 'striptags'=>'striptags')) ;
	$resourceDescriptionSeo = BFCHelper::getLanguage($seoMerchantDescr, $language, null, array( 'nobr'=>'nobr', 'bbcode'=>'bbcode', 'striptags'=>'striptags')) ;
	if (!empty($merchantDescriptionSeo) && strlen($merchantDescriptionSeo) > 160) {
	    $merchantDescriptionSeo = substr($merchantDescriptionSeo,0,160);
	}
	if (!empty($resourceDescriptionSeo) && strlen($resourceDescriptionSeo) > 160) {
	    $resourceDescriptionSeo = substr($resourceDescriptionSeo,0,160);
	}

	$titleHead = !empty($resource->SEOTitle)?$resource->SEOTitle:"$merchantName: $resourceName ($comune, $stato) - $merchant->MainCategoryName - $sitename";
	$keywordsHead = !empty($resource->SEOkeywords)?$resource->SEOkeywords:"$merchantName, $resourceName, $comune, $stato, $merchant->MainCategoryName";

	$payloadresource["@type"] = "Product";
	$payloadresource["@context"] = "http://schema.org";
	$payloadresource["name"] = $resourceName;
	$payloadresource["description"] = $resourceDescriptionSeo;
	$payloadresource["url"] = $routeResource; 
	if (!empty($resource->ImageUrl)){
		$payloadresource["image"] = "https:".BFCHelper::getImageUrlResized('resources',$resource->ImageUrl, 'logomedium');
	}
	$imgPopup = COM_BOOKINGFORCONNECTOR_DEFAULTIMAGE;
	if (!empty($resource->ImageUrl)){
		$imgPopup =  BFCHelper::getImageUrlResized('resources',$resource->ImageUrl, 'logomedium');
	}
	$payload["@type"] = "LocalBusiness";
	$payload["@context"] = "http://schema.org";
	$payload["name"] = $merchantName;
	$payload["description"] = $merchantDescriptionSeo;
	$payload["url"] = ($isportal)? $routeMerchant: $base_url; 
	if (!empty($merchant->LogoUrl)){
		$payload["logo"] = "https:".BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig');
	}

/*--------------- FINE IMPOSTAZIONI SEO----------------------*/
	
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
				/* microformat */
				add_filter( 'wpseo_head', function() use ($payloadresource) { bfi_add_json_ld( $payloadresource ); } , 30);
				add_filter( 'wpseo_head', function() use ($payload) { bfi_add_json_ld( $payload ); } , 30);
				// OpenGraph for Social
				add_filter( 'wpseo_opengraph_url', function() use ($canonicalUrl) {
					if(substr($canonicalUrl , -1)!= '/'){
						$canonicalUrl .= '/';
					}
					return $canonicalUrl; 
				} , 10, 1 );		
				add_filter( 'wpseo_opengraph_title', function() use ($titleHead) {return	$titleHead;});
				add_filter( 'wpseo_opengraph_desc', function() use ($resourceDescriptionSeo) {return	$resourceDescriptionSeo;});
				if (!empty($resource->ImageUrl)){
					add_action( 'wpseo_add_opengraph_images', 'add_images' );
					function add_images( $object ) {
					  $object->add_image( COM_BOOKINGFORCONNECTOR_DEFAULTIMAGE );
					}
					add_filter( 'wpseo_opengraph_image', function() use ($resource) {return	"https:".BFCHelper::getImageUrlResized('resources',$resource->ImageUrl, 'logobig');});
				}
	}else{
		add_filter( 'wp_title', function() use ($titleHead) {return	$titleHead;} , 10, 1 );
		add_action( 'wp_head', function() use ($keywordsHead) {return bfi_add_meta_keywords($keywordsHead); }, 10, 1);
		add_action( 'wp_head', function() use ($resourceDescriptionSeo) {return bfi_add_meta_description($resourceDescriptionSeo); } , 10, 1 );
		remove_action('wp_head', 'rel_canonical');
		add_action( 'wp_head', function() use ($canonicalUrl) {return bfi_add_canonicalurl($canonicalUrl); }, 10, 1);
		add_action( 'wp_head', 'bfi_add_meta_robots', 10, 1);
		/* microformat */
		add_action( 'wp_head',function() use ($payloadresource) { bfi_add_json_ld($payloadresource);} , 10, 1 );
		add_action( 'wp_head',function() use ($payload) {  bfi_add_json_ld($payload);} , 10, 1 );
		// OpenGraph for Social
		add_action( 'wp_head', function() use ($titleHead) {return bfi_add_opengraph_title($titleHead); }, 10, 1);
		add_action( 'wp_head', function() use ($resourceDescriptionSeo) {return bfi_add_opengraph_desc($resourceDescriptionSeo); } , 10, 1 );
		add_action( 'wp_head', function() use ($canonicalUrl) {return bfi_add_opengraph_url($canonicalUrl); }, 10, 1);
		if (!empty($resource->ImageUrl)){
			add_action( 'wp_head', function() use ($resource) {return bfi_add_opengraph_image("https:".BFCHelper::getImageUrlResized('resources',$resource->ImageUrl, 'logobig')); }, 10, 1);
		}
	}

	get_header( 'onselldetails' );
	do_action( 'bookingfor_before_main_content' );
?>
<bfi-page  class="bfi-page-container bfi-onselldetails-page ">
	<div class="bfi_page_container">
		<div class="bookingforwidget" path="onselldetails" 
			data-Id="<?php echo $resource_id ?>"
			data-languages="<?php echo substr($language,0,2) ?>">
			<div id="bficontainer" class="bfi-loader"></div>
		</div>
	</div>
</bfi-page >

<?php
	do_action( 'bookingfor_after_main_content' );
	get_footer( 'onselldetails' ); 
?>
