<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelMerchantDetails Model
 */
if ( ! class_exists( 'BookingForConnectorModelMerchantDetails' ) ) {

	class BookingForConnectorModelMerchantDetails
	{
		private $urlMerchant = null;
		private $helper = null;

		public function __construct($config = array())
		{
			$ws_url = COM_BOOKINGFORCONNECTOR_WSURL;
			$api_key = COM_BOOKINGFORCONNECTOR_API_KEY;
			$this->helper = new wsQueryHelper($ws_url, $api_key);
			$this->urlMerchant = '/GetMerchantsById';
			$this->urlSearch = '/SearchResources'; //'/SearchAllLiteNew';
		}

		public function getMerchantFromServicebyId($merchantId) {

			return $this->getMerchantFromService($merchantId);
		}

		public function getMerchantFromService($merchantId='') {

			if(empty($merchantId)){
				return null;
			}

			$cultureCode = $GLOBALS['bfi_lang'];

			$sessionkey = 'merchant.' . $merchantId . $cultureCode ;			
			$merchant = null;
			$options = array(
					'path' => $this->urlMerchant,
					'data' => array(
						'id' => $merchantId,
						'cultureCode' => BFCHelper::getQuotedString($cultureCode),
						'$format' => 'json'
					)
			);
			
			$url = $this->helper->getQuery($options);

			$r = $this->helper->executeQuery($url,null,null,false,"","",bfi_TagsScope::Merchant,$merchantId );
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->GetMerchantsById)){
					$merchant = $res->d->GetMerchantsById;
				}elseif(!empty($res->d)){
					$merchant = $res->d;
				}
			}
			return $merchant;
		}

		public function getItem($merchantId = '') {
			$item = null;
			if($merchantId != '') {
			  $item = $this->getMerchantFromService($merchantId);
			}
			return $item;
		}

		public function getMerchantResourcesFromSearch($start, $limit, $merchantId = NULL, $parentId = NULL) {
			$resultsItems = null;
			$merchantId = $merchantId != NULL ? $merchantId : $params['merchantId'];
			
			if ($merchantId != NULL) {
				$language = $GLOBALS['bfi_lang'];
				$options = array(
		//				'path' =>  $this->urlGetRelatedResourceStays,
						'path' =>  $this->urlSearch,
						'data' => array(
							'getAllResults' => 1,
							'lite' => 1,
							'calculate' => 0,
							'top' => 5,
							'skip' => 0,
		//					'checkin' => '\'' . (new DateTime('UTC'))->format('Ymd') . '\'',
							'getSingleResultOnly' => 0,
							'getFilters' => 0,  // per recuperare i filtri
							'simpleResult' => 0,  // per recuperare i filtri
							'checkAvailability' => 0,  // per recuperare i filtri
							'getBestGroupResult' => 0,  // per recuperare i filtri
							'groupResultType' => 0,  // per recuperare i filtri
							'checkStays' => 0,  // per recuperare i filtri
							'getUpSellproducts' => 0, // per recuperare i prodotti in upsell
							'domainLabel' => BFCHelper::getQuotedString(COM_BOOKINGFORCONNECTOR_FORM_KEY),
							'$format' => 'json',
							'viewContextType' => 1, // contesto per la visualizzazione dei tag....
								
						)
					);

					//orderby
					$orderby = 'priority';
					$ordertype = 'asc';
					if(isset( $relatedProductid ) && !empty($relatedProductid)){
						$orderby = 'resourceid:' . $relatedProductid ;
						$options['data']['orderby'] = '\'' . $orderby . '\'';
					}else{
						$options['data']['orderby'] = '\'' . $orderby . ';' . $ordertype.  '\'';
		//				$options['data']['ordertype'] = '\'' . $ordertype . '\'';
					}

				if (!empty($parentId)) {
					$options['data']['resourceGroupId'] = $parentId;
					$options['data']['condominiumId'] = $parentId;
				}
				if (!empty($merchantId)) {
					$options['data']['merchantId'] = $merchantId;
				}
				if (!empty($language)) {
					$options['data']['cultureCode'] = '\'' . $language . '\'';
				}
					$options['data']['searchid'] = '\'' . uniqid('', true). '\'';
						$url = $this->helper->getQuery($options);

				$results = null;

				$r = $this->helper->executeQuery($url);
				if (isset($r)) {
					$res = json_decode($r);
					if (!empty($res->d->SearchResources)){
						$results = $res->d->SearchResources;
					}elseif(!empty($res->d)){
						$results = $res->d;
					}
				}

				if(isset($results->ItemsCount)){
		//			$this->count = $results->ItemsCount;
		//			$this->availableCount = $results->AvailableItemsCount;
					$resultsItems = json_decode($results->ItemsString);
				}
			}
			return $resultsItems;

		}

	}
}