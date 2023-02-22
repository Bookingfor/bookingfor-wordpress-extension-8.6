<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$nthumb = 6;
$images = array();

if(!empty($bfiImageData)) {
	$imageData = preg_replace('/\s+/', '', $bfiImageData);
	foreach(explode(',', $imageData) as $image) {
		if (!empty($image)){
			$images[] = array('type' => 'image', 'data' => $image, 'index' => count ($images));
		}
	}
}
$firstVideo = 0;

if(!empty($bfiVideoData) && empty(COM_BOOKINGFORCONNECTOR_ISMOBILE)) {	
	$videoData = preg_replace('/\s+/', '', $bfiVideoData);
	foreach(explode(',', $videoData) as $image) {
		if (!empty($image)){
			if ($firstVideo == 0) {
			    $firstVideo = count ($images);
			}
			$images[] =  array('type' => 'video', 'data' => $image, 'index' =>count ($images));
		}
	}
}
?>


<?php if (count ($images)>0){ ?>
<table class="bfi-table bfi-imgsmallgallery" > 
		<tr>
<?php
	foreach($images as $sub_img) {
		$srcImage = "";
		if($sub_img['type'] == 'image' || $sub_img['type'] == 'planimetry') {
			$srcImage = BFCHelper::getImageUrlResized($bfiSourceData, $sub_img['data'],'small');
		}else{
			$url = $sub_img["data"];
			if (strpos($url,'www.google.com/maps') !== false) {			    
				$srcImage = BFI()->plugin_url() . "/assets/images/street-view.jpg";
			}else{
				parse_str( parse_url( $url, PHP_URL_QUERY ), $arrUrl );
				if (array_key_exists('v',$arrUrl)) {
					$idyoutube = $arrUrl['v'];
					$srcImage = "//img.youtube.com/vi/" . $idyoutube ."/mqdefault.jpg";
				}
			}
		}
?>
			<td >
				<img src="<?php echo $srcImage?>" alt="">
			</td>
<?php } ?>
		</tr>
	</table>


<div class="bfi-clearfix"><br /></div>
		<?php 
	
} elseif (isset($merchant) && $merchant!= null && $merchant->LogoUrl != '') { ?>
	<img src="<?php echo BFCHelper::getImageUrlResized('merchant', $merchant->LogoUrl , 'resource_mono_full')?>" onerror="this.onerror=null;this.src='<?php echo BFCHelper::getImageUrl('merchant', $merchant->LogoUrl, 'resource_mono_full')?>'" />
<?php } ?>