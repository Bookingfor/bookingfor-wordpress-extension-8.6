<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	$merchant_id = get_query_var( 'merchant_id', 0 );
	$parent_id = get_query_var( 'parent_id', 0 );
	$language = $GLOBALS['bfi_lang'];
	$sitename = sanitize_text_field( get_bloginfo( 'name' ) );
	$layout = get_query_var( 'bfi_layout', '' );
	$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;

	$model = new BookingForConnectorModelMerchantDetails;
	$merchant = $model->getItem($merchant_id);	 
	if (empty($merchant) && !isset($_GET['task'])) {
		  global $wp_query;
		  $wp_query->set_404();
		  status_header( 404 );
		  get_template_part( 404 );
		  exit();		
	}
	$indirizzo = isset($merchant->AddressData->Address)?$merchant->AddressData->Address:"";
	$cap = isset($merchant->AddressData->ZipCode)?$merchant->AddressData->ZipCode:""; 
	$comune = isset($merchant->AddressData->CityName)?$merchant->AddressData->CityName:"";
	$stato = isset($merchant->AddressData->StateName)?$merchant->AddressData->StateName:"";
	$merchantName = BFCHelper::getLanguage($merchant->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

	$url_merchant_page = BFCHelper::getPageUrl("merchantdetails");
	$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl((!empty($merchant->SEOSlugName)?$merchant->SEOSlugName:$merchant->Name));
	$canonicalUrl = $routeMerchant;

/*---------------IMPOSTAZIONI SEO----------------------*/
	$seoDescr = !empty($merchant->SEODescription)?$merchant->SEODescription:$merchant->Description;
	$merchantDescriptionSeo = BFCHelper::getLanguage($seoDescr, $language, null, array( 'nobr'=>'nobr', 'bbcode'=>'bbcode', 'striptags'=>'striptags')) ;
	$merchantDescriptionBot = BFCHelper::getLanguage($merchant->Description, $language, null, array( 'striptags'=>'striptags', 'bbcode'=>'bbcode','ln2br'=>'ln2br')) ;
	if (!empty($merchantDescriptionSeo) && strlen($merchantDescriptionSeo) > 160) {
	    $merchantDescriptionSeo = substr($merchantDescriptionSeo,0,160);
	}

//	$titleHead = "$merchantName ($comune, $stato) - $merchant->MainCategoryName - $sitename";
//	$keywordsHead = "$merchantName, $comune, $stato, $merchant->MainCategoryName";
	$titleHead = !empty($merchant->SEOTitle)?$merchant->SEOTitle:"$merchantName ($comune, $stato) - $merchant->MainCategoryName - $sitename";
	$keywordsHead = !empty($merchant->SEOkeywords)?$merchant->SEOkeywords:"$merchantName, $comune, $stato, $merchant->MainCategoryName";

	$merchantNameTrack =  BFCHelper::string_sanitize($merchantName);
	$merchantCategoryNameTrack =  BFCHelper::string_sanitize($merchant->MainCategoryName);
	$payload["@type"] = "LocalBusiness";
	$payload["@context"] = "http://schema.org";
	$payload["name"] = $merchantName;
	$payload["description"] = $merchantDescriptionSeo;
	$payload["url"] = ($isportal)? $routeMerchant: $base_url; 
	if (!empty($merchant->LogoUrl)){
		$payload["logo"] = "https:".BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig');
	}
	
	if (!empty($merchant->Avg && $merchant->Avg->Average>0)){
		$aggregateRating["@type"] = "AggregateRating";
		$aggregateRating["ratingValue"] = number_format($merchant->Avg->Average, 1) ."";
		$aggregateRating["reviewCount"] = $merchant->Avg->Count."";
		$aggregateRating["bestRating"] = "10";
		$aggregateRating["worstRating"] = "1";
		$payload["aggregateRating"] = $aggregateRating;
	}
		
	switch ( $layout) {
		case _x( 'resources', 'Page slug', 'bfi' ):
//			$titleHead = "$merchantName ($comune, $stato) - " . _x( 'resources', 'Page slug', 'bfi' ) . " - $sitename";
//			$keywordsHead = "$merchantName, $comune, $stato, $merchant->MainCategoryName, " . _x( 'resources', 'Page slug', 'bfi' ) ;
			$titleHead .= " - "._x( 'resources', 'Page slug', 'bfi' );
			$keywordsHead .= ","._x( 'resources', 'Page slug', 'bfi' );
			$canonicalUrl = $routeMerchant .'/'._x( 'resources', 'Page slug', 'bfi' );

			break;
		case _x( 'events', 'Page slug', 'bfi' ):
//			$titleHead = "$merchantName ($comune, $stato) - " . _x( 'events', 'Page slug', 'bfi' ) . " - $sitename";
//			$keywordsHead = "$merchantName, $comune, $stato, $merchant->MainCategoryName, " . _x( 'events', 'Page slug', 'bfi' ) ;
			$titleHead .= " - "._x( 'events', 'Page slug', 'bfi' );
			$keywordsHead .= ","._x( 'events', 'Page slug', 'bfi' );
			$canonicalUrl = $routeMerchant .'/'._x('events', 'Page slug', 'bfi' );	

			break;
		case _x('offers', 'Page slug', 'bfi' ):
//			$titleHead = "$merchantName ($comune, $stato) - " . _x('offers', 'Page slug', 'bfi' ) . " - $sitename";
//			$keywordsHead = "$merchantName, $comune, $stato, $merchant->MainCategoryName, " . _x('offers', 'Page slug', 'bfi' ) ;
			$titleHead .= " - "._x( 'offers', 'Page slug', 'bfi' );
			$keywordsHead .= ","._x( 'offers', 'Page slug', 'bfi' );
			$canonicalUrl = $routeMerchant .'/'._x('offers', 'Page slug', 'bfi' );

			break;
		case _x( 'onsellunits', 'Page slug', 'bfi' ):
//			$titleHead = "$merchantName ($comune, $stato) - " . _x( 'onsellunits', 'Page slug', 'bfi' ) . " - $sitename";
//			$keywordsHead = "$merchantName, $comune, $stato, $merchant->MainCategoryName, " . _x( 'onsellunits', 'Page slug', 'bfi' ) ;
			$titleHead .= " - "._x( 'onsellunits', 'Page slug', 'bfi' );
			$keywordsHead .= ","._x( 'onsellunits', 'Page slug', 'bfi' );
			$canonicalUrl = $routeMerchant .'/'._x( 'onsellunits', 'Page slug', 'bfi' );

			break;
		case _x('offer', 'Page slug', 'bfi' ):
			break;
		case _x('thanks', 'Page slug', 'bfi' ):
		case 'thanks':
			break;
		case _x('errors', 'Page slug', 'bfi' ):
		case 'errors':
			break;
		case _x('reviews', 'Page slug', 'bfi' ):
//			$titleHead = "$merchantName ($comune, $stato) - " . _x('reviews', 'Page slug', 'bfi' ) . " - $sitename";
//			$keywordsHead = "$merchantName, $comune, $stato, $merchant->MainCategoryName, " . _x('reviews', 'Page slug', 'bfi' ) ;
			$titleHead .= " - "._x( 'reviews', 'Page slug', 'bfi' );
			$keywordsHead .= ","._x( 'reviews', 'Page slug', 'bfi' );
			$canonicalUrl = $routeMerchant .'/'._x('reviews', 'Page slug', 'bfi' );
			break;
		case _x('review', 'Page slug', 'bfi' ):
		break;
		case _x('redirect', 'Page slug', 'bfi' ):
		break;		
		default:

	}
	
	if ( defined('WPSEO_VERSION') ) {
				add_filter( 'wpseo_title', function() use ($titleHead) {return	$titleHead;} , 10, 1 );
				add_filter( 'wpseo_metakey', function() use ($keywordsHead) {return $keywordsHead; } , 10, 1  );
				add_filter( 'wpseo_metadesc', function() use ($merchantDescriptionSeo) {return $merchantDescriptionSeo; } , 10, 1 );
				add_filter( 'wpseo_robots', function() {return "index,follow"; } , 10, 1 );
				add_filter( 'wpseo_canonical', function() use ($canonicalUrl) {
					if(substr($canonicalUrl , -1)!= '/'){
						$canonicalUrl .= '/';
					}
					return $canonicalUrl; 
				} , 10, 1 );
				/* microformat */
				add_filter( 'wpseo_head', function() use ($payload) { bfi_add_json_ld( $payload ); } , 30);
				// OpenGraph for Social
				add_filter( 'wpseo_opengraph_url', function() use ($canonicalUrl) {
					if(substr($canonicalUrl , -1)!= '/'){
						$canonicalUrl .= '/';
					}
					return $canonicalUrl; 
				} , 10, 1 );		
				add_filter( 'wpseo_opengraph_title', function() use ($titleHead) {return	$titleHead;} , 10, 1);
				add_filter( 'wpseo_opengraph_desc', function() use ($merchantDescriptionSeo) {return	$merchantDescriptionSeo;} , 10, 1);
				if (!empty($merchant->LogoUrl)){
					add_action( 'wpseo_add_opengraph_images', 'add_images' );
					function add_images( $object ) {
					  $object->add_image( COM_BOOKINGFORCONNECTOR_DEFAULTIMAGE );
					}
					add_filter( 'wpseo_opengraph_image', function() use ($merchant) {return	BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig');} );
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
		add_action( 'wp_head', function() use ($merchantDescriptionSeo) {return bfi_add_meta_description($merchantDescriptionSeo); } , 10, 1 );
		remove_action('wp_head', 'rel_canonical');
		add_action( 'wp_head', function() use ($canonicalUrl) {return bfi_add_canonicalurl($canonicalUrl); }, 10, 1);
		add_action( 'wp_head', 'bfi_add_meta_robots', 10, 1);
		/* microformat */
		add_action( 'wp_head', function() use ($payload) { bfi_add_json_ld( $payload ); } , 10, 1 );
		// OpenGraph for Social
		add_action( 'wp_head', function() use ($titleHead) {return bfi_add_opengraph_title($titleHead); }, 10, 1);
		add_action( 'wp_head', function() use ($merchantDescriptionSeo) {return bfi_add_opengraph_desc($merchantDescriptionSeo); } , 10, 1 );
		add_action( 'wp_head', function() use ($canonicalUrl) {return bfi_add_opengraph_url($canonicalUrl); }, 10, 1);
		if (!empty($merchant->LogoUrl)){
			add_action( 'wp_head', function() use ($merchant) {return bfi_add_opengraph_image(BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig')); }, 10, 1);
		}
	}

	get_header( 'merchantdetails' );
	do_action( 'bookingfor_before_main_content' );

if (COM_BOOKINGFORCONNECTOR_ISBOT) {
$url_resource_page = BFCHelper::getPageUrl('accommodationdetails');
$url_resource_page_experience = BFCHelper::getPageUrl('experiencedetails');

?>
<h1><?php echo $merchantName ?></h1>
<span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span>, <span class="region"><?php echo  $stato ?></span>
<p><?php echo $merchantDescriptionBot ?></p>
<?php  
	$bfiSourceData = 'merchant';
	$bfiImageData = null;
	$bfiVideoData = null;
	if(!empty($merchant->ImageData)) {
		$bfiImageData = $merchant->ImageData;
	}
	if(!empty($merchant->VideoData)) {
		$bfiVideoData = $merchant->VideoData;
	}
	bfi_get_template("shared/gallery_type2.php",array("merchant"=>$merchant,"bfiSourceData"=>$bfiSourceData,"bfiImageData"=>$bfiImageData,"bfiVideoData"=>$bfiVideoData));	

	$resources = BFCHelper::getResourcesbyIdMerchant(0, 5, $merchant->MerchantId, null );

	if (!empty($resources )) {
?>
		<table class="bfi-table bfi-table-bordered bfi-table-resources-list" style="margin-top: 20px;">
			<thead>
				<tr>
					<th><div>&nbsp;</div></th>
					<th><div><?php _e('Information', 'bfi') ?></div></th>
				</tr>
			</thead>

	    <?php 
	    	    
		foreach ($resources as $currKey=>$resource){
			foreach ($resource->Results as $resourceId=>$currResource ) {
				    
				$resourceNameTrack =  BFCHelper::string_sanitize($currResource->Name);
				$merchantNameTrack =  BFCHelper::string_sanitize($merchant->Name);
				$merchantCategoryNameTrack =  BFCHelper::string_sanitize($merchant->MainCategoryName);
				switch ($currResource->ItemTypeId ) {
					case bfi_ItemType::Experience :
						$currUriresource  = $url_resource_page_experience.$currResource->ResourceId.'-'.BFI()->seoUrl($currResource->Name);
					break;
					default:      
						$currUriresource  = $url_resource_page.$currResource->ResourceId.'-'.BFI()->seoUrl($currResource->Name);
					break;
				} // end switch
			?>
			<tr>
				<td>
					<?php 
						if ($resource->MaxCapacityPaxes>0) {?>
						<div class="bfi-icon-paxes">
							<i class="fa fa-user"></i> 
							<?php if ($resource->MaxCapacityPaxes==2){?>
							<i class="fa fa-user"></i> 
							<?php }?>
							<?php if ($resource->MaxCapacityPaxes>2){?>
								<?php echo ($resource->MinCapacityPaxes != $resource->MaxCapacityPaxes)? $resource->MinCapacityPaxes . "-" : "" ?><?php echo  $resource->MaxCapacityPaxes ?>
							<?php }?>
						</div>
					<?php
						}
					?>
				</td>
				<td>
					<a class="bfi-resname eectrack bfi-resname-list" href="<?php echo $currUriresource ?>" data-type="Resource" data-id="<?php echo $currResource->ResourceId?>" data-index="0" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><i class="fas fa-caret-right"></i> <?php echo $currResource->Name; ?></a>
					<div>
<?php 
								$mainDetails = array();
								if (isset($currResource->MainBedsConfiguration) && !empty($currResource->MainBedsConfiguration)) {
									$listBeds = array();
									foreach ($currResource->MainBedsConfiguration as $bedrooms) {
										$currBeds = $bedrooms->Beds;
										foreach ($currBeds as $beds) {
											if (isset($listBeds[$beds->Type])) {
												$listBeds[$beds->Type]->Quantity += $beds->Quantity;
											} else {
												$listBeds[$beds->Type] = $beds;
											}
										}
									}
									
									foreach ($listBeds as $beds) {
										array_push($mainDetails, ($beds->Quantity . " " . ($beds->Quantity > 1 ? $bedtypes_text[$beds->Type] : $bedtype_text[$beds->Type]) . ' <i class="bfi-bedtypes bfi-bedtypes'. $beds->Type .'"></i>'));
									}
									
								}
								foreach ($mainDetails as $det) {
								?>
									<span class="bfi-comma"><?php echo $det ?></span>
								<?php } ?>
					</div>
				</td>
			</tr>
	<?php 
				}
		}
		?>
		</table>
		<?php 
		
	}
?>

<?php 
}
?>
	<div class="bfi_page_container">
		<div class="bookingforwidget" path="merchantdetails" 
			data-Id="<?php echo $merchant_id ?>"
			data-languages="<?php echo substr($language,0,2) ?>"
			data-layout="<?php echo $layout ?>"
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
	get_footer( 'merchantdetails' ); 
?>
