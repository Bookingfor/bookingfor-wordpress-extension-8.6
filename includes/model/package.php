<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelPackage Model
 */
if ( ! class_exists( 'BookingForConnectorModelPackage' ) ) {

class BookingForConnectorModelPackage
{
	private $urlPackagebyid = null;
		
	private $helper = null;
	
	public function __construct($config = array())
	{
      $ws_url = COM_BOOKINGFORCONNECTOR_WSURL;
		$api_key = COM_BOOKINGFORCONNECTOR_API_KEY;
		$this->helper = new wsQueryHelper($ws_url, $api_key);
		$this->urlPackagebyid = '/GetPackageById';

	}
	

	public function getPackageFromService($packageId,$language='')
	{
		if ($language==null) {
			$language = $GLOBALS['bfi_lang'];

		}
	
		$data = array(
				'$format' => 'json',
				'id' =>  $packageId,
				'cultureCode' => BFCHelper::getQuotedString($language)
		);
				
		$options = array(
				'path' => $this->urlPackagebyid,
				'data' => $data
		);
		
		$url = $this->helper->getQuery($options);
		
		$package= null;
		
		$r = $this->helper->executeQuery($url,null,null,false,"","",bfi_TagsScope::Package,$packageId );
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$package = $res->d->results->GetPackageById;
			}elseif(!empty($res->d)){
				$package = $res->d->GetPackageById;
			}

		}		
		return $package;
	}

	public function getItem($resource_id) {
		$item = $this->getPackageFromService($resource_id);
		return $item;
	}
	
}
}