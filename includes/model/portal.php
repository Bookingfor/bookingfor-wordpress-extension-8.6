<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * BookingForConnectorModelOrders Model
 */
if ( ! class_exists( 'BookingForConnectorModelPortal' ) ) {

	class BookingForConnectorModelPortal
	{
			
		private $helper = null;
		private $urlGetSubscriptionInfos = null;
		private $urlMerchantCategories = null;
		private $urlGetProductCategoryForSearch = null;


		public function __construct($config = array())
		{
		  $ws_url = COM_BOOKINGFORCONNECTOR_WSURL;
			$api_key = COM_BOOKINGFORCONNECTOR_API_KEY;
			$this->helper = new wsQueryHelper($ws_url, $api_key);
			$this->urlGetSubscriptionInfos = '/GetSubscriptionInfos';
			$this->urlMerchantCategories = '/GetMerchantsCategory';
			$this->urlGetProductCategoryForSearch = '/GetProductCategoryForSearch';
			$this->urlGetResourcesList = '/GetResourcesList';
			$this->urlGetEventsList = '/GetEventsList';
			$this->urlGetMerchantsList = '/GetMerchantList';
			$this->urlGetPackagesList = '/GetPackagesList';
			$this->urlGetPOIList = '/GetPOIList';
			$this->urlGeProductGroupList = '/GeProductGroupList';
			$this->urlGeResourcesOnSellList = '/GetResourcesOnsellList';
		}

		public function getSubscriptionInfos($language='') {		
			$options = array(
					'path' => $this->urlGetSubscriptionInfos,
					'data' => array(
	//					'cultureCode' => BFCHelper::getQuotedString($language),
						'$format' => 'json'
					)
			);
			
			$url = $this->helper->getQuery($options);
			
			$return = null;
			
			$r = $this->helper->executeQuery($url,null,null,false);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->results)){
					$return = $res->d->results->GetSubscriptionInfos;
				}elseif(!empty($res->d)){
					$return = $res->d->GetSubscriptionInfos;
				}
				if (!empty($return) && isset($return->SettingsString)) {
					$return->Settings = json_decode($return->SettingsString);
				}
			}

			return $return;
		}
		
		public function getResourcesList($language='', $itemtype=null) {

			$options = array(
					'path' => $this->urlGetResourcesList,
					'data' => array(
							'$format' => 'json',
							'cultureCodes' => BFCHelper::getQuotedString($language),
					)
			);
			if (isset($itemtype)) {
				$options['data']['itemTypeId'] = $itemtype;
			}

			$url = $this->helper->getQuery($options);

			$list = null;

			$r = $this->helper->executeQuery($url,null,null,false,"","",bfi_TagsScope::Sitemap);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->results)){
					$list = $res->d->results;
				}elseif(!empty($res->d)){
					$list = $res->d;
				}
			}

			return $list;
		}
		public function getProductGroupList($language='') {

			$options = array(
					'path' => $this->urlGeProductGroupList,
					'data' => array(
							'$format' => 'json',
							'cultureCodes' => BFCHelper::getQuotedString($language),
					)
			);
			$url = $this->helper->getQuery($options);

			$list = null;

			$r = $this->helper->executeQuery($url,null,null,false,"","",bfi_TagsScope::Sitemap);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->results)){
					$list = $res->d->results;
				}elseif(!empty($res->d)){
					$list = $res->d;
				}
			}

			return $list;
		}

		public function getResourcesOnSellList($language='') {

			$options = array(
					'path' => $this->urlGeResourcesOnSellList,
					'data' => array(
							'$format' => 'json',
							'cultureCodes' => BFCHelper::getQuotedString($language),
					)
			);
			$url = $this->helper->getQuery($options);

			$list = null;

			$r = $this->helper->executeQuery($url,null,null,false,"","",bfi_TagsScope::Sitemap);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->results)){
					$list = $res->d->results;
				}elseif(!empty($res->d)){
					$list = $res->d;
				}
			}

			return $list;
		}

		public function getEventsList($language='') {

			$options = array(
					'path' => $this->urlGetEventsList,
					'data' => array(
							'$format' => 'json',
							'cultureCodes' => BFCHelper::getQuotedString($language),
					)
			);
			$url = $this->helper->getQuery($options);

			$list = null;

			$r = $this->helper->executeQuery($url,null,null,false,"","",bfi_TagsScope::Sitemap);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->results)){
					$list = $res->d->results;
				}elseif(!empty($res->d)){
					$list = $res->d;
				}
			}

			return $list;
		}
		public function getPOIList($language='') {

			$options = array(
					'path' => $this->urlGetPOIList,
					'data' => array(
							'$format' => 'json',
							'cultureCodes' => BFCHelper::getQuotedString($language),
					)
			);
			$url = $this->helper->getQuery($options);

			$list = null;

			$r = $this->helper->executeQuery($url,null,null,false,"","",bfi_TagsScope::Sitemap);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->results)){
					$list = $res->d->results;
				}elseif(!empty($res->d)){
					$list = $res->d;
				}
			}

			return $list;
		}
		public function getPackagesList($language='') {

			$options = array(
					'path' => $this->urlGetPackagesList,
					'data' => array(
							'$format' => 'json',
							'cultureCodes' => BFCHelper::getQuotedString($language),
					)
			);
			$url = $this->helper->getQuery($options);

			$list = null;

			$r = $this->helper->executeQuery($url,null,null,false,"","",bfi_TagsScope::Sitemap);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->results)){
					$list = $res->d->results;
				}elseif(!empty($res->d)){
					$list = $res->d;
				}
			}

			return $list;
		}
		public function getMerchantsList($language='') {

			$options = array(
					'path' => $this->urlGetMerchantsList,
					'data' => array(
							'$format' => 'json',
							'cultureCodes' => BFCHelper::getQuotedString($language),
					)
			);
			$url = $this->helper->getQuery($options);

			$list = null;

			$r = $this->helper->executeQuery($url,null,null,false,"","",bfi_TagsScope::Sitemap);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->results)){
					$list = $res->d->results;
				}elseif(!empty($res->d)){
					$list = $res->d;
				}
			}

			return $list;
		}

		public function getMerchantCategoriesFromService($language='') {

			$options = array(
					'path' => $this->urlMerchantCategories,
					'data' => array(
							'$format' => 'json',
							'cultureCode' => BFCHelper::getQuotedString($language),
					)
			);
			$url = $this->helper->getQuery($options);

			$categoriesFromService = null;

			$r = $this->helper->executeQuery($url,null,null,false);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->results)){
					$categoriesFromService = $res->d->results;
				}elseif(!empty($res->d)){
					$categoriesFromService = $res->d;
				}
			}

			return $categoriesFromService;
		}

	public function getProductCategoryForSearch($language='', $typeId = 1,$merchantid=0) {
		$options = array(
				'path' => $this->urlGetProductCategoryForSearch,
				'data' => array(
					'typeId' => $typeId,
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
			);		
		$url = $this->helper->getQuery($options);
		
		$return = null;
		
		$r = $this->helper->executeQuery($url,null,null,false);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$return = $res->d->results;
			}elseif(!empty($res->d)){

				$return = $res->d;
			}
		}
		return $return;
	}


	}
}