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