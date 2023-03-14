<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
 ?>
<?php
	$resource_id = get_query_var( 'resource_id', 0 );
	$language = $GLOBALS['bfi_lang'];
	$layout = get_query_var( 'bfi_layout', '' );
	$sitename = sanitize_text_field( get_bloginfo( 'name' ) );
	$base_url = get_site_url();
	$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;

	$model = new BookingForConnectorModelResource;
	$resource = $model->getItem($resource_id);	 
	if(empty($resource)){
	  global $wp_query;
	  $wp_query->set_404();
	  status_header( 404 );
	  get_template_part( 404 );
	  exit();		
	}

	$merchant = $resource->Merchant;
	$merchants = array();
	$merchants[] = $resource->MerchantId;

	$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('nobr'=>'nobr', 'striptags'=>'striptags')); 
	$merchantName = BFCHelper::getLanguage($merchant->Name, $language, null, array('nobr'=>'nobr', 'striptags'=>'striptags')); 
	$resourceDescription = BFCHelper::getLanguage($resource->Description, $language, null, array('ln2br'=>'ln2br', 'bbcode'=>'bbcode', 'striptags'=>'striptags'));

	$indirizzo = isset($resource->Address)?$resource->Address:"";
	$cap = isset($resource->ZipCode)?$resource->ZipCode:""; 
	$comune = isset($resource->CityName)?$resource->CityName:"";
	$stato = isset($resource->StateName)?$resource->StateName:"";

	$url_resource_page = BFCHelper::getPageUrl("accommodationdetails");
	$routeResource = $url_resource_page.$resource->ResourceId.'-'.BFI()->seoUrl((!empty($resource->SEOSlugName)?$resource->SEOSlugName:$resourceName));
	$canonicalUrl = $routeResource;
	$routeMerchant = BFCHelper::getPageUrl("merchantdetails") . $merchant->MerchantId.'-'.BFI()->seoUrl((!empty($merchant->SEOSlugName)?$merchant->SEOSlugName:$merchant->Name));

/*---------------IMPOSTAZIONI SEO----------------------*/
	$seoDescr = !empty($resource->SEODescription)?$resource->SEODescription:$resource->Description;
	$seoMerchantDescr = !empty($merchant->SEODescription)?$merchant->SEODescription:$merchant->Description;
	$resourceDescriptionSeo = BFCHelper::getLanguage($seoDescr, $language, null, array( 'nobr'=>'nobr', 'bbcode'=>'bbcode', 'striptags'=>'striptags')) ;
	$resourceDescriptionBot = BFCHelper::getLanguage($resource->Description, $language, null, array( 'striptags'=>'striptags', 'bbcode'=>'bbcode','ln2br'=>'ln2br')) ;
	$merchantDescriptionSeo = BFCHelper::getLanguage($seoMerchantDescr, $language, null, array( 'nobr'=>'nobr', 'bbcode'=>'bbcode', 'striptags'=>'striptags')) ;

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
		$payloadresource["image"] = BFCHelper::getImageUrlResized('resources',$resource->ImageUrl, 'big');
	}
	if (empty($resource->Avg )){
		$resource->Avg = new stdClass;
		$resource->Avg->Average = 8;
		$resource->Avg->Count = 1;
	}
	if (!empty($resource->Avg && $resource->Avg->Average>0)){
		$aggregateRating["@type"] = "AggregateRating";
		$aggregateRating["ratingValue"] = number_format($resource->Avg->Average, 1) ."";
		$aggregateRating["reviewCount"] = $resource->Avg->Count."";
		$aggregateRating["bestRating"] = "10";
		$aggregateRating["worstRating"] = "1";
		$payloadresource["aggregateRating"] = $aggregateRating;
	}

	$payload["@type"] = "LocalBusiness";
	$payload["@context"] = "http://schema.org";
	$payload["name"] = $merchantName;
	$payload["description"] = $merchantDescriptionSeo;
	$payload["url"] = ($isportal)? $routeMerchant: $base_url; 
	if (!empty($merchant->LogoUrl)){
		$payload["logo"] = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig');
	}
	if (!empty($merchant->Avg && $merchant->Avg->Average>0)){
		$aggregateRating["@type"] = "AggregateRating";
		$aggregateRating["ratingValue"] = number_format($merchant->Avg->Average, 1) ."";
		$aggregateRating["reviewCount"] = $merchant->Avg->Count."";
		$aggregateRating["bestRating"] = "10";
		$aggregateRating["worstRating"] = "1";
		$payload["aggregateRating"] = $aggregateRating;
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
					add_filter( 'wpseo_opengraph_image', function() use ($resource) {return	BFCHelper::getImageUrlResized('resources',$resource->ImageUrl, 'big');});
				}
				add_filter( 'wpseo_schema_webpage', function( $data) use ($titleHead,$canonicalUrl) {
									 $data["name"] = $titleHead;
									 $data["url"] = $canonicalUrl;
									 $data["@id"] = $canonicalUrl;
									return	$data;
							} );
				add_filter( 'wpseo_schema_graph_pieces', 'remove_breadcrumbs_from_schema', 11, 2 );
				add_filter( 'wpseo_schema_webpage', 'remove_breadcrumbs_property_from_webpage', 11, 1 );
				add_filter( 'wpseo_schema_webpage', 'remove_potentialaction_property_from_webpage', 11, 1 );
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
			add_action( 'wp_head', function() use ($resource) {return bfi_add_opengraph_image(BFCHelper::getImageUrlResized('resources',$resource->ImageUrl, 'big')); }, 10, 1);
		}
	}
		

	get_header( 'resourcedetails' );
	do_action( 'bookingfor_before_main_content' );
if (COM_BOOKINGFORCONNECTOR_ISBOT) {
?>
<h1><?php echo $resourceName ?></h1> 
<h2><?php echo  $merchantName?></h2>
<span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span>, <span class="region"><?php echo  $stato ?></span>
<p><?php echo $resourceDescriptionBot ?></p>
<?php  
		$bfiSourceData = 'resources';
		$bfiImageData = null;
		$bfiVideoData = null;
		if(!empty($resource->ImageData)) {
			$bfiImageData = $resource->ImageData;
		}
		if(!empty($resource->VideoData)) {
			$bfiVideoData = $resource->VideoData;
		}
		bfi_get_template("shared/gallery_type2.php",array("merchant"=>$merchant,"bfiSourceData"=>$bfiSourceData,"bfiImageData"=>$bfiImageData,"bfiVideoData"=>$bfiVideoData));	
?>

<?php 
}
?>
	<div class="bfi_page_container bfi-resourcedetails-page">
		<div class="bookingforwidget" path="resource" 
			data-Id="<?php echo $resource_id ?>"
			data-languages="<?php echo substr($language,0,2) ?>"
			>
			<div id="bficontainer" class="bfi-loader"></div>
		</div>
	</div>
	<?php 
	$date = new \DateTime('NOW');
	$tosub = new DateInterval('PT12H30M');
	$date->sub($tosub);
	$dateModified = $date->format('c');
	?>
	<div style="display:none">
		<span itemprop="dateModified" datetime="<?php echo $dateModified ?>"><?php echo $dateModified ?></span>
	</div>
<?php
	do_action( 'bookingfor_after_main_content' );
	get_footer( 'resourcedetails' ); 
?>
