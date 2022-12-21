<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelMerchants Model
categoryId:
	1: Merchant
	2: Vendite
	4: Risorse
	8: Pacchetti
	16: Offerte 


 */
if ( ! class_exists( 'BookingForConnectorModelTags' ) ) :

class BookingForConnectorModelTags
{
	private $urlTags = null;
	private $urlTagForSearch = null;
	private $urlTagsCount = null;
	private $urlTagsbyids = null;
	private $urlTagbyid = null;

	private $urlMerchant = null;
	private $urlMerchantCount = null;
	private $urlResources = null;
	private $urlResourcesCount = null;
	private $urlOffers = null;
	private $urlOffersCount = null;
	private $urlPackages = null;
	private $urlPackagesCount = null;
	private $count = null;
	private $params = null;
	private $itemPerPage = null;
	private $ordering = null;
	private $direction = null;

		

	private $helper = null;
	
	public function __construct($config = array())
	{
//		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$this->helper = new wsQueryHelper(null, null);
		$this->urlTags = '/GetTags';
	}

	public function getTags($language='', $categoryIds='', $start, $limit,$viewContextType="")  {
		$results = $this->getTagsFromService($language, $categoryIds, $start, $limit,$viewContextType);
		return $results;
	}

	public function getTagsFromService($language='', $categoryIds='', $start, $limit, $viewContextType="") {
		if (empty($language)){
			$language = $GLOBALS['bfi_lang'];
		}
		$options = array(
				'path' => $this->urlTags,
				'data' => array(
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
			);

		if (!empty($categoryIds) ) {
			$options['data']['categoryIds'] = BFCHelper::getQuotedString($categoryIds);
		}

		if (isset($start) && $start >= 0) {
			$options['data']['skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
		}

		if (!empty($viewContextType) ) {
			$options['data']['viewContextType'] = $viewContextType;
		}
						
		$url = $this->helper->getQuery($options);
		
		$ret = null;
		
		$r = $this->helper->executeQuery($url,null,null,false);
//		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$ret = $res->d->results;
			}elseif(!empty($res->d)){
				$ret = $res->d;
			}
		}

		return $ret;
	}
}
endif;