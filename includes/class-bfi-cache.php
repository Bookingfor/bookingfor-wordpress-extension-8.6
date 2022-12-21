<?php
/**
 * Contains the query functions for BookingFor which alter the front-end post queries and loops
 *
 * @class 		BFI_Cache
 * @version     8.2.0
 * @package		BookingFor/
 * @category	Class
 * @author 		BookingFor
 */
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BFI_Cache' ) ) {

/**
 * BFI_Cache Class.
 */
	class BFI_Cache {

		public function __construct()
		{
			$this->cachetime = COM_BOOKINGFORCONNECTOR_CACHETIMEBOT; // 20 days default for bot
			$this->cachedir = COM_BOOKINGFORCONNECTOR_CACHEDIRBOT;
		}


		public static function getCachedContent($key, $skip_cache = false)
		{
			if (isset($key)) {


				if ( ! is_dir(COM_BOOKINGFORCONNECTOR_CACHEDIRBOT)) {
					mkdir(COM_BOOKINGFORCONNECTOR_CACHEDIRBOT, 0755, true);
				}
				$cacheFileName = $key;

//				$hash = md5($cacheFileName);

				$bfifile = COM_BOOKINGFORCONNECTOR_CACHEDIRBOT ."/bfi_$cacheFileName.html";

				$mtime = 0;
				if (file_exists($bfifile)) {
					$mtime = filemtime($bfifile);
				}
				$bfifiletimemod = $mtime + COM_BOOKINGFORCONNECTOR_CACHETIMEBOT;

				if ($bfifiletimemod < time() || $skip_cache) {
					return null;
				} else {
					$r = file_get_contents($bfifile);
				}
				return $r;
			}
			return null;

		}
		public static function setCachedContent($key,$content)
		{
			if (isset($key) && isset($content)) {
				if ( ! is_dir(COM_BOOKINGFORCONNECTOR_CACHEDIRBOT)) {
					mkdir(COM_BOOKINGFORCONNECTOR_CACHEDIRBOT, 0755, true);
				}
				$cacheFileName = $key;

				$bfifile = COM_BOOKINGFORCONNECTOR_CACHEDIRBOT ."/bfi_$cacheFileName.html";

				file_put_contents($bfifile, $content);
			}
		}
	}
}