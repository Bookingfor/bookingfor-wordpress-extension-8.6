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

	$model = new BookingForConnectorModelResourcegroup;
	$resource = $model->getResourcegroupFromService($resource_id,$language);	 

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
	$resourceDescription = BFCHelper::getLanguage($seoDescr, $language, null, array('ln2br'=>'ln2br', 'bbcode'=>'bbcode', 'striptags'=>'striptags'));
	
	$url_resource_page =  BFCHelper::getPageUrl("resourcegroupdetails");
	$routeResource = $url_resource_page.$resource->CondominiumId.'-'.BFI()->seoUrl((!empty($resource->SEOSlugName)?$resource->SEOSlugName:$resourceName));
	$canonicalUrl = $routeResource;
	$routeMerchant = BFCHelper::getPageUrl("merchantdetails") . $merchant->MerchantId.'-'.BFI()->seoUrl((!empty($merchant->SEOSlugName)?$merchant->SEOSlugName:$merchant->Name));

	$indirizzo = isset($resource->Address)?$resource->Address:"";
	$cap = isset($resource->ZipCode)?$resource->ZipCode:""; 
	$comune = isset($resource->CityName)?$resource->CityName:"";
	$stato = isset($resource->StateName)?$resource->StateName:"";

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
		$payloadresource["image"] = "https:".BFCHelper::getImageUrlResized('resourcegroup',$resource->DefaultImg, 'logobig');
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
				if (!empty($resource->DefaultImg)){
					add_action( 'wpseo_add_opengraph_images', 'add_images' );
					function add_images( $object ) {
					  $object->add_image( COM_BOOKINGFORCONNECTOR_DEFAULTIMAGE );
					}
					add_filter( 'wpseo_opengraph_image', function() use ($resource) { return	BFCHelper::getImageUrlResized('resourcegroup',$resource->DefaultImg, 'big');});
				}
				add_filter( 'wpseo_schema_webpage', function( $data) use ($titleHead,$canonicalUrl) {
									 $data["name"] = $titleHead;
									 $data["url"] = $canonicalUrl;
									 $data["@id"] = $canonicalUrl;
									return	$data;
							} );
	}else{
		add_filter( 'wp_title', function() use ($titleHead) {return	$titleHead;} , 10, 1 );
		add_action( 'wp_head', function() use ($keywordsHead) {return bfi_add_meta_keywords($keywordsHead); }, 10, 1);
		add_action( 'wp_head', function() use ($resourceDescriptionSeo) {return bfi_add_meta_description($resourceDescriptionSeo); } , 10, 1 );
		remove_action('wp_head', 'rel_canonical');
		add_action( 'wp_head', function() use ($canonicalUrl) {return bfi_add_canonicalurl($canonicalUrl); }, 10, 1);
		add_action( 'wp_head', 'bfi_add_meta_robots', 10, 1);
		/* microformat */
		add_action( 'wp_head',function() use ($payloadresource) { bfi_add_json_ld($payloadresource);} , 10, 1 );
		add_action( 'wp_head',function() use ($payload) { bfi_add_json_ld($payload);} , 10, 1 );
		// OpenGraph for Social
		add_action( 'wp_head', function() use ($titleHead) {return bfi_add_opengraph_title($titleHead); }, 10, 1);
		add_action( 'wp_head', function() use ($resourceDescriptionSeo) {return bfi_add_opengraph_desc($resourceDescriptionSeo); } , 10, 1 );
		add_action( 'wp_head', function() use ($canonicalUrl) {return bfi_add_opengraph_url($canonicalUrl); }, 10, 1);
		if (!empty($resource->ImageUrl)){
			add_action( 'wp_head', function() use ($resource) {return bfi_add_opengraph_image("https:".BFCHelper::getImageUrlResized('resourcegroup',$resource->ImageUrl, 'big')); }, 10, 1);
		}
	}


	get_header('resourcegroupdetails' );
	do_action( 'bookingfor_before_main_content' );
if (COM_BOOKINGFORCONNECTOR_ISBOT) {
$url_resource_page = BFCHelper::getPageUrl('accommodationdetails');
$url_resource_page_experience = BFCHelper::getPageUrl('experiencedetails');
?>
<h1><?php echo $resourceName ?></h1> 
<h2><?php echo  $merchantName?></h2>
<span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span>, <span class="region"><?php echo  $stato ?></span>
<p><?php echo $resourceDescriptionBot ?></p>
<?php  
	$bfiSourceData = 'resourcegroup';
	$bfiImageData = null;
	$bfiVideoData = null;
	if(!empty($resource->ImagesData)) {
		$bfiImageData = $resource->ImagesData;
	}
	if(!empty($resource->VideoData)) {
		$bfiVideoData = $resource->VideoData;
	}
	bfi_get_template("shared/gallery_type2.php",array("merchant"=>$merchant,"bfiSourceData"=>$bfiSourceData,"bfiImageData"=>$bfiImageData,"bfiVideoData"=>$bfiVideoData));	
	
	$resources = BFCHelper::getResourcesbyIdMerchant(0, 5, $merchant->MerchantId, $resource->CondominiumId );

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
	<div class="bfi_page_container bfi-resourcegroupdetails-page">
		<div class="bookingforwidget" path="resourcegroupdetails" 
			data-Id="<?php echo $resource_id ?>"
			data-languages="<?php echo substr($language,0,2) ?>">
			<div id="bficontainer" class="bfi-loader"></div>
		</div>
	</div>
<?php
	do_action( 'bookingfor_after_main_content' );
	get_footer( 'resourcegroupdetails' ); 
?>
