<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelOnSellUnit Model
 */
if ( ! class_exists( 'BookingForConnectorModelOnSellUnit' ) ) {

	class BookingForConnectorModelOnSellUnit
	{
		private $urlResource = null;
		
		public function __construct($config = array())
		{
		  $ws_url = COM_BOOKINGFORCONNECTOR_WSURL;
			$api_key = COM_BOOKINGFORCONNECTOR_API_KEY;
			$this->helper = new wsQueryHelper($ws_url, $api_key);
			$this->urlResource = '/GetResourceOnSellByIdSimple';
		}
		

		public function getResourceFromService($resourceId) {
			$language = $GLOBALS['bfi_lang'];

			$resourceIdRef = $resourceId;
			$options = array(
					'path' => $this->urlResource,
					'data' => array(
						'$format' => 'json',
						'cultureCode' => BFCHelper::getQuotedString($language),
						'id' =>$resourceId
					)
				);
			
			$url = $this->helper->getQuery($options);
			
			$resource = null;
			
			$r = $this->helper->executeQuery($url);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->GetResourceOnSellByIdSimple)){
					$resource = $res->d->GetResourceOnSellByIdSimple;
				}elseif(!empty($res->d)){
					$resource = $res->d;
				}
				if (!empty($resource)) {
					$resource->Merchant=BFCHelper::getMerchantFromServicebyId($resource->MerchantId);
				}
			}
			return $resource;
		}	

		
		public function getItem($resourceId)
		{
			$item = $this->getResourceFromService($resourceId);
			return $item;
		}
		
	}
}