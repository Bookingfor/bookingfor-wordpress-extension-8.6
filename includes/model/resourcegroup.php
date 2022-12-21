<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelMerchants Model
 */
if ( ! class_exists( 'BookingForConnectorModelResourcegroup' ) ) :

class BookingForConnectorModelResourcegroup
{
	private $urlResourcegroup = null;
	private $urlResourcegroupbyids = null;
	private $urlResourcegroupCount = null;
	private $urlResourcegroupbyid = null;
	private $resourcegroupsCount = 0;
	private $urlSearch = null;
		
	private $helper = null;
	
	public function __construct($config = array())
	{
      $ws_url = COM_BOOKINGFORCONNECTOR_WSURL;
		$api_key = COM_BOOKINGFORCONNECTOR_API_KEY;
		$this->helper = new wsQueryHelper($ws_url, $api_key);
		$this->urlResourcegroup = '/GetCondominiums';
		$this->urlResourcegroupbyids = '/GetCondominiumsByIds';
		$this->urlResourcegroupCount = '/GetCondominiumsCount';
		$this->urlResourcegroupbyid = '/GetCondominiumById';
		$this->urlSearch = '/SearchResources'; //'/SearchAllLiteNew';
	}
	
	public function applyDefaultFilter(&$options) {

	}
	
	public function getResourceGroupsByTagIds($tagids, $start = null, $limit = null) {
		$cultureCode = $GLOBALS['bfi_lang'];
		$options = array(
				'path' =>  $this->urlSearch,
				'data' => array(
					'top' => 10,
					'skip' => 0,
					'lite' => 1,
					'getAllResults' => 1,
					'calculate' => 0,
					'checkAvailability' => 0,  
					'checkStays' => 0,  
//					'itemTypeIds' => BFCHelper::getQuotedString('2'),  
					'tagids' => BFCHelper::getQuotedString($tagids),
					'getSingleResultOnly' => 0,
					'getFilters' => 0,  // per recuperare i filtri
					'groupResultType' => 2,  // per recuperare i filtri
					'domainLabel' => BFCHelper::getQuotedString(COM_BOOKINGFORCONNECTOR_FORM_KEY),
					'$format' => 'json',
					'culturecode' => BFCHelper::getQuotedString($cultureCode),
					'viewContextType' => 1, // contesto per la visualizzazione dei tag....
						
				)
			);
		$options['data']['searchid'] = '\'' . uniqid('', true). '\'';
		if (isset($start) && $start >= 0) {
			$options['data']['skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
		}	

		$url = $this->helper->getQuery($options);

		$results = null;

		$r = $this->helper->executeQuery($url,null,null,false);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->SearchResources)){
				$results = $res->d->SearchResources;
			}elseif(!empty($res->d)){
				$results = $res->d;
			}
		}
		$resultsItems = null;
		if(isset($results->ItemsCount)){
			$resultsItems = json_decode($results->ItemsString);
		}
		return $resultsItems;

	}

//	public function getResourcegroupsFromService($start, $limit, $ordering, $direction) {
	public function getResourcegroupsFromService($start, $limit) {// with randor order is not possible to otrder by another field
		$options = array(
				'path' => $this->urlResourcegroup,
				'data' => array(
					'$format' => 'json'
				)
			);

		if (isset($start) && $start >= 0) {
			$options['data']['skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
		}
		
		$this->applyDefaultFilter($options);

		$url = $this->helper->getQuery($options);
		
		$resourcegroups = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$resourcegroups = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$resourcegroups = $res->d->results;
			}elseif(!empty($res->d)){
				$resourcegroups = $res->d;
			}

		}

		return $resourcegroups;
	}
	
	public function getTotal()
	{
		//$typeId = $this->getTypeId();
		$options = array(
				'path' => $this->urlResourcegroupCount,
				'data' => array(
					'$format' => 'json'
				)
			);
		
		$this->applyDefaultFilter($options);
				
		$url = $this->helper->getQuery($options);
		
		$count = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$count = $res->d->GetCondominiumsCount;
		}

		return $count;
	}
	
	public function getResourcegroupByIds($listsId,$language='') {// with randor order is not possible to otrder by another field
		if ($language==null) {
			$language = $GLOBALS['bfi_lang'];

		}
		$options = array(
				'path' => $this->urlResourcegroupbyids,
				'data' => array(
					'$format' => 'json',
					'ids' =>  '\'' .$listsId. '\'',
					'cultureCode' => BFCHelper::getQuotedString($language)
				)
			);
 
		$url = $this->helper->getQuery($options);
		
		$resourcegroups = null;
		
		$r = $this->helper->executeQuery($url,"POST");
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$resourcegroups = json_encode($res->d->results);
			}elseif(!empty($res->d)){
				$resourcegroups = json_encode($res->d);
			}
		}

		return $resourcegroups;
	}

	public function getResourcegroupFromService($resourcegroupId,$language='')
	{
//		$params = $this->getState('params');
		if ($language==null) {
			$language = $GLOBALS['bfi_lang'];

		}
	
		$data = array(
				'$format' => 'json',
				'id' =>  $resourcegroupId,
				'cultureCode' => BFCHelper::getQuotedString($language)
		);
				
		$options = array(
				'path' => $this->urlResourcegroupbyid,
				'data' => $data
		);
		
		$url = $this->helper->getQuery($options);
		
		$resourcegroup= null;
		
//		$r = $this->helper->executeQuery($url,null,null,false);
		$r = $this->helper->executeQuery($url,null,null,false,"","",bfi_TagsScope::Resource,$resourcegroupId );
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$resourcegroup = $res->d->results->GetCondominiumById;
			}elseif(!empty($res->d)){
				$resourcegroup = $res->d->GetCondominiumById;
			}

		}
		if (!empty($resourcegroup)){
			$resourcegroup->Tags = json_decode($resourcegroup->TagsString);
			if (empty($resourcegroup->XPos) && !empty($resourcegroup->XGooglePos)) {
				$resourcegroup->XPos = $resourcegroup->XGooglePos;
			}
			if (empty($resourcegroup->YPos) && !empty($resourcegroup->YGooglePos)) {
				$resourcegroup->YPos = $resourcegroup->YGooglePos;
			}
			
			if (!empty($resourcegroup->YPos) && !empty($resourcegroup->YPos)){
				$resourceLat = $resourcegroup->XPos;
				$resourceLon = $resourcegroup->YPos;
				$currPoint = "0|" . $resourceLat . " " . $resourceLon . " 10000";
//				$resourcegroup->Poi = BFCHelper::GetProximityPoi($currPoint);					
			}
				
			$resourcegroup->Merchant=BFCHelper::getMerchantFromServicebyId($resourcegroup->MerchantId);
		}
		
		
		return $resourcegroup;
	}
		
	protected function populateState($ordering = NULL, $direction = NULL) {
		$filter_order = BFCHelper::getCmd('filter_order','Order');
		$filter_order_Dir = BFCHelper::getCmd('filter_order_Dir','asc');		
//		return parent::populateState($filter_order, $filter_order_Dir);
	}
	
	
	public function getItem($resource_id) {
		$item = $this->getResourcegroupFromService($resource_id);
		return $item;
	}
	
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		$items = $this->getResourcegroupsFromService(
			$this->getStart(), 
			$this->getState('list.limit'), 
			$this->getState('list.ordering', 'Order'), 
			$this->getState('list.direction', 'asc')
		);

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}
}
endif;