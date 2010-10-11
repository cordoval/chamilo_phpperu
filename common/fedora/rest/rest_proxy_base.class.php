<?php

require_once dirname(__FILE__) . '/rest_config.class.php';
require_once dirname(__FILE__) . '/rest_client.class.php';

/**
 * Base class for proxies calling REST web methods.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch, Nicolas Rod
 *
 */
abstract class RestProxyBase  {

	/**
	 * Offset in hours between the current timezone and GMT.
	 *
	 */
	public static function timezone_offset(){
		static $result = false;
		if($result !== false){
			return $result;
		}

		$timezone = date_default_timezone_get();
		$timezone = new DateTimeZone($timezone);
		$result = (int)timezone_offset_get($timezone, new DateTime());
		$result /=3600;
		return $result;
	}

	public static function parse_date($text){
		if(empty($text)){
			return 0;
		}
		$text = strtoupper($text);
		$text = str_replace('T', '-', $text);
		$text = str_replace(':', '-', $text);
		$text = str_replace('.', '-', $text);
		$pieces = explode('-', $text);
		$year = $pieces[0];
		$month = $pieces[1];
		$day = $pieces[2];
		$hour = is_numeric($pieces[3]) ? $pieces[3] : 0;
		$minute = is_numeric($pieces[4]) ? $pieces[4] : 0;
		$second = is_numeric($pieces[5]) ? $pieces[5] : 0;

		$offset = self::timezone_offset();
		$hour += $offset;
		return mktime($hour, $minute, $second, $month, $day, $year);
	}

	/*
	const GET_NEW_UID_NOT_IMPLEMENTED = 'GET_NEW_UID_NOT_IMPLEMENTED';
	const SESSION_MISSING_FIELDS      = 'SESSION_MISSING_FIELDS';
	const OBJECT_ID                = 'object_id';
	const OBJECT_TITLE             = 'object_title';
	const OBJECT_SYNC_STATE        = 'object_sync_state';
	const OBJECT_OWNER_ID          = 'object_owner_id';
	const OBJECT_CREATION_DATE     = 'object_creation_date';
	const OBJECT_MODIFICATION_DATE = 'object_modification_date';
	const OBJECT_DESCRIPTION       = 'object_description';

	const EXTERNAL_OBJECT_KEY      = 'external_object';
	const SYNC_INFO                = 'sync_info';

	const SYNC_STATE               = 'sync_state';
	const SYNC_NEVER_SYNCHRONIZED  = 'never_synchronized';
	const SYNC_IDENTICAL           = 'sync_synchronized';
	const SYNC_NEWER_IN_CHAMILO    = 'newer_in_chamilo';
	const SYNC_OLDER_IN_CHAMILO    = 'older_in_chamilo';
*/

	/**
	 * @var RestClient
	 */
	private $rest_client = null;

	/**
	 * @var RestConfig
	 */
	private $config = null;

	public function __construct($config = null){
		$this->config = empty($config) ? RestConfig::get_test_config() : $config;
	}

	/**
	 * @return RestConfig
	 */
	public function get_config(){
		return $this->config;
	}

	public function set_config($value){
		$this->config = $value;
	}

	/**
	 * @return RestClient
	 */
	protected function get_rest_client(){
		if(!empty($this->rest_client)){
			return $this->rest_client;
		}
		$result = new RestClient($this->get_config());
		/*
		 if(isset($target_ca_file) && strlen($target_ca_file) > 0)
		 {
			$target_ca_file = Path :: get_repository_path() . 'lib/export/external_export/ssl' . StringUtilities :: ensure_start_with($target_ca_file, '/');

			$result->set_target_ca_file($target_ca_file);
			}
			else
			{
			$result->set_check_target_certificate(false);
			}*/

		return $this->rest_client = $result;
	}


	public function execute($verb, $parameters = array(), $http_method='get', $data_to_send=null, $mime_type=''){
		$base = $this->get_config()->get_base_url();
		$base = rtrim($base, '/');
		$args = array();
		foreach($parameters as $key=>$value){
			if(is_bool($value)){
				$value = $value ? 'true' : 'false';
			}
			$args[] = $key. '=' . urlencode($value);
		}
		$args = implode('&', $args);
		$args = empty($args) ? '': "?$args";

		$url = "$base/{$verb}{$args}";

		try{
			$result =  $this->get_rest_xml_response($url, $http_method, $data_to_send, $mime_type);
			return $result;
		}catch(Exception $e){
			//echo htmlentities($parameters['query']) . "\n</br>". "\n</br>";//@todo:changethat
			//echo htmlentities($url) . "\n</br>". "\n</br>";//@todo:changethat
			throw $e;
		}
	}

	public function execute_post($verb, $post = array()){
		$base = $this->get_config()->get_base_url();
		$base = rtrim($base, '/');
		$url = "$base/{$verb}";
		try{
			$result =  $this->get_post_xml_response($url, $post);
			return $result;
		}catch(Exception $e){
			//echo htmlentities($parameters['query']) . "\n</br>". "\n</br>";//@todo:changethat
			throw $e;
		}
	}

	public function execute_raw($verb, $parameters, $http_method, $data_to_send=null, $mime_type=''){
		$base = $this->get_config()->get_base_url();
		$base = rtrim($base, '/');
		$args = array();
		foreach($parameters as $key=>$value){
			if(is_bool($value)){
				$value = $value ? 'true' : 'false';
			}
			$args[] = $key. '=' . urlencode($value);
		}
		$args = implode('&', $args);
		$url = "$base/$verb?$args";

		try{
			$result =  $this->get_rest_response($url, $http_method, $data_to_send, $mime_type);
			return $result;
		}catch(Exception $e){
			//debug(htmlentities($url));die;
			throw $e;
		}
	}

	public function execute_post_raw($verb, $post = array()){
		$base = $this->get_config()->get_base_url();
		$base = rtrim($base, '/');
		$url = "$base/{$verb}";
		try{
			$result =  $this->get_post_response($url, $post);
			return $result;
		}catch(Exception $e){
			//echo htmlentities($parameters['query']) . "\n</br>". "\n</br>";//@todo:changethat
			throw $e;
		}
	}

	/**
	 * Send a request to a REST service and parse the response as an XML Document
	 * @param $url string
	 * @param $http_method string
	 * @param $data_to_send string The content to send with the REST request
	 * @param $content_mimetype The mimetype of the content to send with the REST request
	 * @return DOMDocument or null if the response is not well formed XML
	 */
	protected function get_rest_xml_response($url, $http_method, $data_to_send = null, $content_mimetype = null){
		$client = $this->get_rest_client();
		$client->set_url($url);
		$client->set_http_method($http_method);
		$client->set_check_target_certificate(false);
		if(!empty($data_to_send)){
			if( !is_array($data_to_send) && file_exists($data_to_send) && empty($content_mimetype)){
				$content_mimetype = $this->get_file_mimetype($data_to_send);
			}else{
				$content_mimetype = null;
			}
			$client->set_data_to_send($data_to_send, $content_mimetype);
		}

		$result = $client->send_request();

		$response_content = $result->get_response_content();

		if(!$result->has_error() && stripos($response_content, 'Exception') === false){
			$document = new DOMDocument();
			if(!empty($response_content)){
				set_error_handler(array($this, 'handle_xml_error'));
				$document->loadXML($response_content);
				restore_error_handler();
			}

			return $document;
		}else{

			//echo htmlentities($url) . "\n</br>". "\n</br>";//@todo:changethat

			if(stripos($response_content, 'Exception') === false){
				throw new Exception(htmlentities($result->get_response_error()));
			}else{
				throw new Exception('<h3>REST response:</h3><p><strong>URL : </strong>' . $result->get_request_url() . '<p><strong>POST data : </strong>' . htmlentities($result->get_request_sent_data()) . '</p><p><strong>Response : </strong>' . $response_content . '</p>');
			}
		}
	}

	protected function get_post_xml_response($url, $post){
		$client = $this->get_rest_client();

		$client->set_url($url);
		$client->set_check_target_certificate(false);
		$client->set_http_method('post');
		$client->set_data_to_send(array('content' => $post));
		$result = $client->send_request();

		$response_content = $result->get_response_content();

		if(!$result->has_error() && stripos($response_content, 'Exception') === false){
			$document = new DOMDocument();
			if(!empty($response_content)){
				set_error_handler(array($this, 'handle_xml_error'));
				$document->loadXML($response_content);
				restore_error_handler();
			}

			return $document;
		}else{

			//echo htmlentities($url) . "\n</br>". "\n</br>";//@todo:changethat

			if(stripos($response_content, 'Exception') === false){
				throw new Exception(htmlentities($result->get_response_error()));
			}else{
				throw new Exception('<h3>REST response:</h3><p><strong>URL : </strong>' . $result->get_request_url() . '<p><strong>POST data : </strong>' . htmlentities($result->get_request_sent_data()) . '</p><p><strong>Response : </strong>' . $response_content . '</p>');
			}
		}
	}

	/**
	 * Send a request to a REST service and return the response
	 *
	 * @param $url string
	 * @param $http_method string
	 * @param $data_to_send string The content to send with the REST request
	 * @param $content_mimetype The mimetype of the content to send with the REST request
	 * @return mixed
	 */
	protected function get_rest_response($url, $http_method, $data_to_send = null, $content_mimetype = null)
	{

		$client = $this->get_rest_client();
		$client->set_url($url);
		$client->set_http_method($http_method);

		if(isset($data_to_send))
		{
			if(file_exists($data_to_send) && !isset($content_mimetype))
			{
				$content_mimetype = $this->get_file_mimetype($data_to_send);
			}

			$client->set_data_to_send($data_to_send, $content_mimetype);
		}

		$client->set_check_target_certificate(false);

		$result = $client->send_request();

		$response_content = $result->get_response_content();

		if(!$result->has_error() && stripos($response_content, 'Exception') === false)
		{
			return $response_content;
		}
		else
		{
			if(stripos($response_content, 'Exception') === false)
			{
				throw new Exception(htmlentities($result->get_response_error()));
			}
			else
			{
				throw new Exception('<h3>REST response:</h3><p><strong>URL : </strong>' . $result->get_request_url() . '<p><strong>POST data : </strong>' . htmlentities($result->get_request_sent_data()) . '</p><p><strong>Response : </strong>' . $response_content . '</p>');
			}
		}
	}

	protected function get_post_response($url, $post){
		$client = $this->get_rest_client();
		$client->set_url($url);
		$client->set_http_method('post');
		$client->set_data_to_send(array('content' => $post));


		$client->set_check_target_certificate(false);

		$result = $client->send_request();

		$response_content = $result->get_response_content();

		if(!$result->has_error() && stripos($response_content, 'Exception') === false)
		{
			return $response_content;
		}
		else
		{
			if(stripos($response_content, 'Exception') === false)
			{
				throw new Exception(htmlentities($result->get_response_error()));
			}
			else
			{
				throw new Exception('<h3>REST response:</h3><p><strong>URL : </strong>' . $result->get_request_url() . '<p><strong>POST data : </strong>' . htmlentities($result->get_request_sent_data()) . '</p><p><strong>Response : </strong>' . $response_content . '</p>');
			}
		}
	}

	public function handle_xml_error($error_no, $error_str, $error_file, $error_line){
		if($error_no == E_WARNING && substr_count($error_str,'DOMDocument') > 0){
			throw new DOMException($error_str);
		}else{
			return false;
		}
	}

	protected function get_file_mimetype($path_to_file){
		if(function_exists('finfo_open')){
			/*
			 * PHP >= 5.3 or PECL fileinfo installed
			 */
			$handle = finfo_open(FILEINFO_MIME);
			return finfo_file($handle, $file);
		}
		else{
			$path_info = pathinfo($path_to_file);
			return $this->get_mimetype_from_extension($path_info['extension']);
		}
	}

	protected function get_mimetype_from_extension($extension){
		$extension = strtolower($extension);
		switch($extension){
			case 'txt':
				return 'text';
			case 'xml':
				return 'text/xml';
			case 'html':
				return 'text/html';

			case 'pdf':
				return 'application/pdf';
			case 'doc':
				return 'application/word';
			case 'xls':
				return 'application/excel';
			case 'ppt':
			case 'pps':
				return 'application/powerpoint';

			case 'jpg':
			case 'jpe':
			case 'jpeg':
				return 'image/jpeg';
			case 'gif':
				return 'image/gif';
			case 'png':
				return 'image/png';
			case 'tiff':
				return 'image/tiff';
			case 'bmp':
				return 'image/bmp';

			default:
				return null;
		}
	}

}



?>