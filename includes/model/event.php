<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelEvent Model
 */
if ( ! class_exists( 'BookingForConnectorModelEvent' ) ) {

	class BookingForConnectorModelEvent
	{
		private $urlDetails = null;
		private $helper = null;

		public function __construct($config = array())
		{
			$ws_url = COM_BOOKINGFORCONNECTOR_WSURL;
			$api_key = COM_BOOKINGFORCONNECTOR_API_KEY;
			$this->helper = new wsQueryHelper($ws_url, $api_key);
			$this->urlDetails = '/GetEventById';
		}

		public function getDetails($resourceId, $language='') {
			if (empty($resourceId)) {
				return null;
			}
			if (empty($language)){
				$language = $GLOBALS['bfi_lang'];
			}

			$resourceIdRef = $resourceId;
			$options = array(
					'path' => $this->urlDetails,
					'data' => array(
						'$format' => 'json',
						'cultureCode' => BFCHelper::getQuotedString($language),
						'id' =>$resourceId
					)
				);

			$url = $this->helper->getQuery($options);

			$resource = null;

			$r = $this->helper->executeQuery($url,null,null,false,"","",bfi_TagsScope::Event,$resourceId );
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->GetEventById)){
					$resource = $res->d->GetEventById;
				}elseif(!empty($res->d)){
					$resource = $res->d;
				}
				if(!empty($resource->Address->XPos) && !empty($resource->Address->YPos)){
					$resourceLat = $resource->Address->XPos;
					$resourceLon = $resource->Address->YPos;
					$currPoint = "0|" . $resourceLat . " " . $resourceLon . " 10000";
				}

			}
			return $resource;
		}

		protected function populateState() {
			$resourceId = JRequest::getInt('resourceId');
			$defaultRequest =  array(
				'resourceId' => JRequest::getInt('resourceId'),
				'state' => BFCHelper::getStayParam('state'),
			);

			//echo var_dump($defaultRequest);die();
			$this->setState('params', $defaultRequest);

//			return parent::populateState();
		}

		public function getItem($resourceId)
		{
			$item = $this->getDetails($resourceId);
			return $item;
		}
	}
}