<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelMerchants Model
 */
if ( ! class_exists( 'BookingForConnectorModelResource' ) ) {

	class BookingForConnectorModelResource
	{
		private $urlResource = null;
		private $helper = null;

		private $urlSearch = null;

		public function __construct($config = array())
		{
			$ws_url = COM_BOOKINGFORCONNECTOR_WSURL;
			$api_key = COM_BOOKINGFORCONNECTOR_API_KEY;
			$this->helper = new wsQueryHelper($ws_url, $api_key);
			$this->urlResource = '/GetResourceById';// '/Resources(%d)';
		}

		public function getResourceFromService($resource_id) {
			$resourceId = $resource_id;
			$resourceIdRef = $resource_id;
			$language = $GLOBALS['bfi_lang'];
			$options = array(
					'path' => $this->urlResource, // sprintf($this->urlResource, $resourceId),
					'data' => array(
						'$format' => 'json',
						'id' => $resourceId,
						'cultureCode' => BFCHelper::getQuotedString($language)
					)
				);

			$url = $this->helper->getQuery($options);

			$resource = null;

			$r = $this->helper->executeQuery($url,null,null,false,"","",bfi_TagsScope::Resource,$resourceId );
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->GetResourceById)){
					$resource = $res->d->GetResourceById;
				}elseif(!empty($res->d)){
					$resource = $res->d;
				}
			}
			if(!empty($resource)){
				$resource->Merchant=BFCHelper::getMerchantFromServicebyId($resource->MerchantId);
			}
			return $resource;
		}

		public function getItem($resource_id) {
			$item = $this->getResourceFromService($resource_id);
			return $item;
		}
	}
}