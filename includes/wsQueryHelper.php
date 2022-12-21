<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'wsQueryHelper' ) ) {
class wsQueryHelper {

	private $serviceUri = null;
	private $apikey = null;
	private $urlproxy = null;
	private $useproxy = 0;
	private $usegzip = 1;
	private $formlabel = null;
	public $errmsg = "";
	public $infomsg = "";
	protected $handle;

	public function __construct($serviceUri, $apikey)
	{
		$this->serviceUri = COM_BOOKINGFORCONNECTOR_WSURL;
		$this->apikey =  COM_BOOKINGFORCONNECTOR_API_KEY;
		$this->formlabel = '';
		$this->useproxy = COM_BOOKINGFORCONNECTOR_USEPROXY;
		$this->urlproxy = COM_BOOKINGFORCONNECTOR_URLPROXY;
		$this->usegzip = 1;
		$this->cachetime = COM_BOOKINGFORCONNECTOR_ISBOT ? COM_BOOKINGFORCONNECTOR_CACHETIMEBOT : COM_BOOKINGFORCONNECTOR_CACHETIME; // 1 day default
		$this->cachedir = COM_BOOKINGFORCONNECTOR_ISBOT ? COM_BOOKINGFORCONNECTOR_CACHEDIRBOT : COM_BOOKINGFORCONNECTOR_CACHEDIR;
		$this->handle = curl_init();
	}

	public function __destruct() {
		if (is_resource($this->handle)) {
			curl_close($this->handle);
		}
	}

	public function addFilter(&$filterbase, $filter, $operator) {
		if (isset($filter)) {
			if ($filterbase !== '')
				$filterbase .= ' ' . $operator . ' ';
			$filterbase .= $filter;
		}
		return $this;
	}

	public function executeQuery($url, $method = 'GET', $setApiKey = true, $skip_cache=TRUE, $userAgent ="", $skipUrlData ="", $scope ="", $id ="") {

		if (isset($url)) {
			
			if ( ! is_dir($this->cachedir)) {
				mkdir($this->cachedir, 0755, true);
			}
			$cacheFileName = $url;
			if (!empty($skipUrlData) ) {
			    $cacheFileName = remove_query_arg( explode(",",$skipUrlData), $url );					
			}
//			$hash = md5($url);
			$hash = $scope."_".$id."_".md5($cacheFileName);
			$bfifile = $this->cachedir ."/bfi_$hash.cache";
			
			$mtime = 0;
			if (file_exists($bfifile)) {
				$mtime = filemtime($bfifile);
			}
			$bfifiletimemod = $mtime + $this->cachetime;			
			
			if ($bfifiletimemod < time() || $skip_cache || empty(COM_BOOKINGFORCONNECTOR_ENABLECACHE)) {
				$body = array();
				$isInPost = false;
				if (isset($method) && strtoupper($method) === "POST" ) {
					$isInPost = true;
					$urlParsed = explode("?",$url);
					$url = $urlParsed[0];
					if ($setApiKey) {
						$url .='?s=' . uniqid('', true) . '&apikey='.$this->apikey;
					}
					if (isset($urlParsed[1])) {
						$body = $urlParsed[1];
					}
				}

            // Copy it and fill it with your parameters
            $ch = curl_copy_handle($this->handle);

				if($this->useproxy ==1 && !empty($this->urlproxy)){
					curl_setopt($ch, CURLOPT_PROXY, $this->urlproxy);
				}
				if($this->usegzip ==1){
					curl_setopt($ch,CURLOPT_ENCODING,'gzip');
				}
				if (!empty($userAgent)) {
					curl_setopt($ch,CURLOPT_USERAGENT,$userAgent);
				}
				curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_HTTPGET, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				if ($isInPost){
					curl_setopt ($ch, CURLOPT_POST, true);
					curl_setopt ($ch, CURLOPT_POSTFIELDS, $body);
				}
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
				curl_setopt($ch,CURLOPT_TIMEOUT,360);
				curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);

				curl_setopt($ch, CURLOPT_REFERER, site_url());
				$ipClient = BFCHelper::bfi_get_client_ip();
				$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				$uaClient = "";
				if(!empty( $_SERVER['HTTP_USER_AGENT'] )){
				$uaClient = $_SERVER['HTTP_USER_AGENT']; // user agent client
					
				}
				
				$label = COM_BOOKINGFORCONNECTOR_FORM_KEY;

				curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
					"REMOTE_ADDR: $ipClient",
					"X_FORWARDED_FOR: $ipClient",
					"HTTP_REFERER: $actual_link",
					"X_FORWARDED_USERAGENT: $uaClient",
					"X_REQUEST_LABEL: $label", //label impostata nel plugin
				));	

				$http_codes = parse_ini_file("httpcode.ini");
				try{
					$r = curl_exec($ch);
					if(!curl_errno($ch)) {
						$info = curl_getinfo($ch);
						$this->infomsg = 'Took ' . $info['total_time'] . ' seconds to send a request <!-- to ' . $info['url'] . ' -->' ;
						if ($info['http_code'] >= 500 && $info['http_code'] <600){
							$this->errmsg = $http_codes[$info['http_code']];
						}
						if ($info['http_code'] >= 400 && $info['http_code'] <500){
							$this->errmsg = $http_codes[$info['http_code']];
						}
		//				echo "<pre>";
		//				echo print_r($info);
		//				echo "</pre>";
						
		//				 echo '<!--Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'] . '-->';
						if ($r && !$skip_cache & $info['http_code'] < 400) {
							file_put_contents($bfifile, $r);
						}
					}else {
	//				echo '<!--Curl error: ' . curl_error($ch) . '-->';
					$this->errmsg = curl_error($ch);
						if($this->useproxy ==1 && !empty($this->urlproxy)){
							$this->errmsg .= " ;proxy enabled: check proxy ";
						 }
					 }
            //curl_clone($ch);
					 
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, null);
		curl_setopt($ch, CURLOPT_WRITEFUNCTION, null);

				 }catch (Exception $e) {
					$this->errmsg("Error in Request");
				}
			} else {
				$r = file_get_contents($bfifile);
			}
			return $r;
		}
		return null;
	}

	public function getQuery($options = array()) {
		$url = $this->serviceUri;

		if (isset($options['path']))
		{
			$url .= $options['path'];
		} else {
			return null;
		}

		if (!isset($options['data'])) {
			return $url;
		} else {
			$options['data']['apikey'] = $this->apikey;
		}

		$options['data'] = $this->sanitizeData($options['data']);
		$query = http_build_query($options['data']);

		// http_build_query has urlencoded the query and char '$' has been replaced by '%24'. This restores the '$' char
		$query = str_ireplace('%24', '$', $query);
		$query = str_ireplace('%27', '\'', $query);
		$query = str_ireplace('__27__', '\'\'', $query);
		$query = str_ireplace('__1013__', '%0D%0A', $query);
		$query = str_ireplace('%26quot%3B', '"', $query);

		if (stripos($url,'?') === false) {
			$url .= '?';
		} else {
			$url .= '&';
		}

		$url .= $query;

		return $url;
	}

	public function sanitizeData($data) {
		$newData = array();
		$matches = array();
		foreach ($data as $key=>$elem) {
			if(!empty($elem)){
				$elem = str_replace("\n\r", "__1013__", $elem);
				$elem = str_replace("\n", "__1013__", $elem);
				$elem = str_replace("\r", "__1013__", $elem);
			}
			if (preg_match("/^\'(.*?)\'$/i", $elem, $matches) > 0) {
				$newData[$key] = "'" . str_ireplace('\'','__27__',$matches[1]) . "'";
			} else {
				$newData[$key] = $elem;
			}
		}
		return $newData;
	}
	public function url_exists($url = "") {
		if(empty($url)){
			$url = $this->serviceUri;
		}
		if (!$fp = curl_init($url)) return false;
		return true;
	}		

}
}