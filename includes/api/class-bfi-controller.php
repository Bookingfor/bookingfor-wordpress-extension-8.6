<?php
/**
 * Contains the query functions for Bookingfor which alter the front-end post queries and loops
 *
 * @class 		BFI_Controller
 * @version             2.0.5
 * @package		
 * @category	        Class
 * @author 		Bookingfor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BFI_Controller' ) ) {
	/**
	 * BFI_Controller Class.
	 */
	class BFI_Controller {

		/**
		 * Constructor for the query class. Hooks in methods.
		 *
		 * @access public
		 */
		private $formlabel = null;
		public function __construct() {
			$this->formlabel = COM_BOOKINGFORCONNECTOR_FORM_KEY;
		}
		
		public function handle_request(){ 
			global $wp; 
			
			$task = isset($_REQUEST['task']) ? $_REQUEST['task'] :null ;
			$search = $_SERVER['REQUEST_URI'] ;

			if(!empty($task)){
				BFI()->define( "DONOTCACHEPAGE", true ); // Do not cache this page
				if (method_exists($this, $task)){
					$message = $this->$task();
					$simple = isset($_REQUEST['simple']) ? $_REQUEST['simple'] :null ;
					if(!empty($simple)){
						$this->send_text_response($message);  
					}else{
						$this->send_json_response($message);  
					}

				}
			}else{
				if (str_contains($search,"bfi-api/v1/search") ) {
					$this->send_response('test search'); 
					// inpostare una ricerca di default
				}
				$this->send_response('Method not allowed'); 
			}
		} 

		protected function send_response($msg){ 
			$response['message'] = $msg; 
	// 		header('content-type: application/json; charset=utf-8'); 
			echo json_encode($response)."\n"; 
			die();
	//		exit; 
		} 
		protected function send_json_response($msg){ 
	// 		header('content-type: application/json; charset=utf-8'); 
			echo $msg."\n"; 
			die();
	//		exit; 
		} 
		protected function send_text_response($msg){ 
	// 		header('content-type: text/plain; charset=utf-8'); 
			echo $msg."\n"; 
			die();
	//		exit; 
		} 
		function DeleteCacheByIds(){
			$scope = BFCHelper::getVar('scope');
			$currScope = 0;
			switch ($scope) {
			    case "0":
					$currScope = bfi_TagsScope::Merchant;
					break;
			    case "1":
					$currScope = bfi_TagsScope::Resource;
					break;
			    case "2":
					$currScope = bfi_TagsScope::Offert;
					break;
			    case "3":
					$currScope = bfi_TagsScope::Event;
					break;
			    case "4":
					$currScope = bfi_TagsScope::Poi;
					break;
			    case "6":
					$currScope = bfi_TagsScope::Package;
					break;
				default:      
					 $currScope = 0;
			}
			$ids = BFCHelper::getVar('ids');
			if (file_exists (COM_BOOKINGFORCONNECTOR_CACHEDIR) && !BFCHelper::is_dir_empty(COM_BOOKINGFORCONNECTOR_CACHEDIR)) {
				if (!empty($currScope) && !empty($ids)) {
					$ids = str_replace(" ", "",$ids);
				    $aIds = explode(",",$ids);
					foreach ($aIds as $id ) {
					    $mask = 'bfi_' . $currScope . '_' . $id.'_*.cache';
						array_map('unlink', glob(COM_BOOKINGFORCONNECTOR_CACHEDIR . '/' . $mask));
					}
				}
			}
			echo "1";      
		}

	} //end class
}