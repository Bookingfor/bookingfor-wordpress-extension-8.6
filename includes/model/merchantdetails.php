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
	}
}