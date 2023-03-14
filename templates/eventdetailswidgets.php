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

		$model = new BookingForConnectorModelEvent;
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
		$seoDescr = !empty($resource->SEODescription)?$resource->SEODescription:$resource->ShortDescription;
		$resourceDescriptionSeo = BFCHelper::getLanguage($seoDescr, $language, null, array( 'nobr'=>'nobr', 'bbcode'=>'bbcode', 'striptags'=>'striptags')) ;
		$resourceDescriptionBot = BFCHelper::getLanguage($resource->Description, $language, null, array( 'striptags'=>'striptags', 'bbcode'=>'bbcode','ln2br'=>'ln2br')) ;
		if(empty( $resourceDescriptionSeo )){
			$resourceDescriptionSeo = BFCHelper::getLanguage($resource->Description, $language, null, array( 'nobr'=>'nobr', 'bbcode'=>'bbcode', 'striptags'=>'striptags')) ;
		}
		if (!empty($resourceDescriptionSeo) && strlen($resourceDescriptionSeo) > 160) {
			$resourceDescriptionSeo = substr($resourceDescriptionSeo,0,160);
		}

		$details_page = get_post( bfi_get_page_id( 'eventdetails' ) );
		$url_resource_page = get_permalink( $details_page->ID );
		$routeResource = $url_resource_page.$resource->EventId.'-'.BFI()->seoUrl((!empty($resource->SEOSlugName)?$resource->SEOSlugName:$resourceName));
		$canonicalUrl = $routeResource;

		$indirizzo = "";
		$cap = "";
		$comune = "";
		$provincia = "";

		if(!empty( $resource->Address )){
			$indirizzo = $resource->Address->Address;
			$cap = $resource->Address->ZipCode;
			$comune = $resource->Address->CityName;
			$provincia = $resource->Address->RegionName;
			$stato = !empty($resource->Address->StateName)?$resource->Address->StateName:"";
		}
		$titleHead = !empty($resource->SEOTitle)?$resource->SEOTitle:"$resourceName ($comune, $stato) - $resource->CategoryNames - $sitename";
		$keywordsHead = !empty($resource->SEOkeywords)?$resource->SEOkeywords:"$resourceName, $comune, $stato, $resource->CategoryNames";

	$startDate = BFCHelper::parseJsonDate($resource->StartDate,'Y-m-d\TH:i:s');
	$endDate = BFCHelper::parseJsonDate($resource->EndDate,'Y-m-d\TH:i:s');
	$startDate  = new DateTime($startDate,new DateTimeZone('UTC'));
	$endDate  = new DateTime($endDate,new DateTimeZone('UTC'));
	$details_page = get_post( bfi_get_page_id( 'eventdetails' ) );
	$url_resource_page = get_permalink( $details_page->ID );
	$uri = $url_resource_page.$resource->EventId.'-'.BFI()->seoUrl($resourceName);
	$resourceRoute = $uri;
	$resourceLat = "";
	$resourceLon = "";
	if(!empty($resource->Address->XPos)){
		$resourceLat = $resource->Address->XPos;
	}
	if(!empty($resource->Address->YPos)){
		$resourceLon = $resource->Address->YPos;
	}

	/* microformat */

	$payloadaddress["@type"] = "PostalAddress";
	$payloadaddress["streetAddress"] = $indirizzo;
	$payloadaddress["addressLocality"] = $comune;
	$payloadaddress["postalCode"] = $cap;
	$payloadaddress["addressRegion"] = $provincia;
	$payloadaddress["addressCountry"] =  BFCHelper::bfi_get_country_code_by_name($stato);

	$payloadlocation["@type"] = "Place";
	$payloadlocation["address"] = $payloadaddress;
	$payloadlocation["name"] = $comune . " - " . $provincia . " - " . $stato;
	
	// SEO
	$payloadresource["@context"] = "http://schema.org";
	$payloadresource["@type"] = "Event";
	$payloadresource["location"] = $payloadlocation;
	$payloadresource["name"] = $resourceName;
	$payloadresource["description"] = $resourceDescriptionSeo;
	$payloadresource["startDate"] = $startDate->format("Y-m-d");
	$payloadresource["endDate"] = $endDate->format("Y-m-d");
	$payloadresource["url"] = $resourceRoute; 
	if (!empty($resource->DefaultImg)){
		$payloadresource["image"] = BFCHelper::getImageUrlResized('events',$resource->DefaultImg, 'big');
	}
	if (!empty($resourceLat) && !empty($resourceLon)) {
		$payloadgeo["@type"] = "GeoCoordinates";
		$payloadgeo["latitude"] = $resourceLat;
		$payloadgeo["longitude"] = $resourceLon;
		$payloadresource["geo"] = $payloadgeo; 
	}

	$organizer = new stdClass;
	if(!empty($resource->Organizer) && $resource->Organizer->InformationType != -1) { 
		if(!empty($resource->Organizer->MerchantId)) {
			$organizer = BFCHelper::getMerchantFromServicebyId($resource->Organizer->MerchantId);
			$routeorganizer = $url_merchant_page . $organizer->MerchantId.'-'.BFI()->seoUrl($organizer->Name);

		} else {
		$merchant = new stdClass;
		$organizer->LogoUrl = $resource->Organizer->LogoUrl;
		$organizer->Name = $resource->Organizer->Name;
		$organizer->Rating = $resource->Organizer->Rating;
		$organizer->RatingSubValue = $resource->Organizer->RatingSubValue;
		$organizer->Address = $resource->Organizer->Address;
		$organizer->ZipCode = $resource->Organizer->ZipCode;
		$organizer->CityName = $resource->Organizer->CityName;
		$organizer->StateName = $resource->Organizer->StateName;
		$organizer->SiteUrl = $resource->Organizer->SiteUrl;
		$organizer->Phone = $resource->Organizer->Phone;
		$routeorganizer = $organizer->SiteUrl;
		} 
			$payloadorganizer["@type"] = "Organization";
			$payloadorganizer["name"] = $organizer->Name ;
			$payloadorganizer["url"] = $routeorganizer;
			$payloadresource["organizer"] = $payloadorganizer; 
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
					/* microformat */
					add_filter( 'wpseo_head', function() use ($payloadresource) { bfi_add_json_ld( $payloadresource ); } , 30);
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
						add_filter( 'wpseo_opengraph_image', function() use ($resource) {return	BFCHelper::getImageUrlResized('events',$resource->DefaultImg, 'big');} );
						add_action( 'wp_head', function() use ($resource) {
							$image['secure_url'] = BFCHelper::getImageUrlResized('events',$resource->DefaultImg, 'big');
							$image['mime-type'] = 'image/jpeg';
							$image['width'] = 820;
							$image['height'] = 460;
							return bfi_add_opengraph_image_size($image);
						}, 1);

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
				add_action( 'wp_head', function() use ($resource) {return bfi_add_opengraph_image(BFCHelper::getImageUrlResized('events',$resource->DefaultImg, 'big')); }, 10, 1);
			}
		}
	/*--------------- END IMPOSTAZIONI SEO----------------------*/
			$paramRef = array(
				"resource"=>$resource,
				"resource_id"=>$resource_id,
				);

	get_header( 'eventdetails' );
	do_action( 'bookingfor_before_main_content' );
if (COM_BOOKINGFORCONNECTOR_ISBOT) {
?>
<h1><?php echo $resourceName ?></h1> 
<span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span>, <span class="region"><?php echo  $stato ?></span>
<p><?php echo $resourceDescriptionBot ?></p>
<?php  
			$bfiSourceData = 'events';
			$bfiImageData = null;
			$bfiVideoData = null;
			if(!empty($resource->ImageData)) {
				$bfiImageData = $resource->ImageData;
			}
			if(!empty($resource->VideoData)) {
				$bfiVideoData = $resource->VideoData;
			}
			bfi_get_template("shared/gallery_type2.php",array("merchant"=>null,"bfiSourceData"=>$bfiSourceData,"bfiImageData"=>$bfiImageData,"bfiVideoData"=>$bfiVideoData));	
?>

<?php 
}
?>

	<div class="bfi-page-container bfi-eventdetails-page ">
		<div class="bookingforwidget" path="eventdetails" 
			data-Id="<?php echo $resource_id ?>"
			data-languages="<?php echo substr($language,0,2) ?>">
			<div id="bficontainer" class="bfi-loader"></div>
			<div id="divlistresource"></div>
		</div>
	</div>

<?php
	do_action( 'bookingfor_after_main_content' );
?>	
<?php get_footer( 'eventdetails' ); ?>
