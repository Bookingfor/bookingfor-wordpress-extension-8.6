<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelResource Model
 */
if ( ! class_exists( 'BookingForConnectorModelResource' ) ) {
	class BookingForConnectorModelExperience
	{
		private $urlResource = null;
		private $helper = null;
		public function __construct($config = array())
		{
			$ws_url = COM_BOOKINGFORCONNECTOR_WSURL;
			$api_key = COM_BOOKINGFORCONNECTOR_API_KEY;
			$this->helper = new wsQueryHelper($ws_url, $api_key);
			$this->urlResource = '/GetExperienceById';// 
		}


		public function getResourceFromService($resource_id) {
			$resourceId = $resource_id;
			$resourceIdRef = $resource_id;
				$language = $GLOBALS['bfi_lang'];
	//		}
			$options = array(
					'path' => $this->urlResource, // sprintf($this->urlResource, $resourceId),
					'data' => array(
						'$format' => 'json',
						//'expand' => 'Merchant',
						'id' => $resourceId,
						'cultureCode' => BFCHelper::getQuotedString($language)
					)
				);

			$url = $this->helper->getQuery($options);

			$resource = null;

			$r = $this->helper->executeQuery($url,null,null,false,"","",bfi_TagsScope::Resource,$resourceId );
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->GetExperienceById)){
					$resource = $res->d->GetExperienceById;
				}elseif(!empty($res->d)){
					$resource = $res->d;
				}
			}
			if(!empty($resource)){
				$resource->Merchant = BFCHelper::getMerchantFromServicebyId($resource->MerchantId);
				$resource->Tags = json_decode($resource->TagsString);
			}
			return $resource;
		}
		public function getItem($resource_id) {
			$item = $this->getResourceFromService($resource_id);
			return $item;
		}
}
}