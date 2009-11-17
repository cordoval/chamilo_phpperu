<?php
class RestResult 
{
    /**
	* the http method used for the request
	*
	* @var string
	*/ 
	private $request_http_method;
	
	/**
	* the request connexion mode
	*
	* @var string
	*/ 
	private $request_connexion_mode;
	
	/**
	* the data sent with the request
	*
	* @var string
	*/ 
	private $request_sent_data;
	
	/**
	* the request URL
	*
	* @var string
	*/ 
	private $request_url;
	
	/**
	* the request port number
	*
	* @var integer
	*/ 
	private $request_port;
	
    /**
	* HTTP code returned
	*
	* @var integer
	*/ 
	private $response_http_code;
    
	/**
	* MIME type returned
	*
	* @var string
	*/ 
	private $response_mime_type;
	
	/**
	* the response content
	*
	* @var string
	*/ 
	private $response_content;
	
	/**
	* the response error
	*
	* @var string
	*/ 
	private $response_error;
	
	
	/****************************************************************************************/
    
	function RestResult() 
	{
		;
	}
	
	
	/****************************************************************************************/
	
	/**
	* Get HTTP code returned
	*
	* @return integer
	*/
	public function get_response_http_code()
	{
		return $this->response_http_code;
	}
	
	/**
	* Set HTTP code returned
	*
	* @var $http_code_returned integer
	* @return void
	*/
	public function set_response_http_code($response_http_code)
	{
		$this->response_http_code = $response_http_code;
	}
	
	/****************************************************************************************/
	
	/**
	* Get MIME type returned
	*
	* @return string
	*/
	public function get_response_mime_type()
	{
		return $this->response_mime_type;
	}
	
	/**
	* Set MIME type returned
	*
	* @var $returned_mime_type string
	* @return void
	*/
	public function set_response_mime_type($response_mime_type)
	{
		$this->response_mime_type = $response_mime_type;
	}
	
	
	/****************************************************************************************/
	
	/**
	* Get the response content
	*
	* @return string
	*/
	public function get_response_content()
	{
		return $this->response_content;
	}
	
	/**
	* Set the response content
	*
	* @var $response_content string
	* @return void
	*/
	public function set_response_content($response_content)
	{
		$this->response_content = $response_content;
	}
	
	
	/****************************************************************************************/
	
	/**
	* Get the response error
	*
	* @return string
	*/
	public function get_response_error()
	{
		return $this->response_error;
	}
	
	/**
	* Set the response error
	*
	* @var $response_error string
	* @return void
	*/
	public function set_response_error($response_error)
	{
		$this->response_error = $response_error;
	}
	
	
	/**
	 * @return boolean Indicates if this response object contains an error
	 */
	public function has_error()
	{
	    $error = $this->get_response_error();
	    return isset($error) && strlen($error) > 0;
	}
	
	/****************************************************************************************/
	
	/**
	* Get the http method used for the request
	*
	* @return string
	*/
	public function get_request_http_method()
	{
		return $this->request_http_method;
	}
	
	/**
	* Set the http method used for the request
	*
	* @var $request_http_method string
	* @return void
	*/
	public function set_request_http_method($request_http_method)
	{
		$this->request_http_method = $request_http_method;
	}
	
	
	/****************************************************************************************/
	
	/**
	* Get the request connexion mode
	*
	* @return string
	*/
	public function get_request_connexion_mode()
	{
		return $this->request_connexion_mode;
	}
	
	/**
	* Set the request connexion mode
	*
	* @var $request_connexion_mode string
	* @return void
	*/
	public function set_request_connexion_mode($request_connexion_mode)
	{
		$this->request_connexion_mode = $request_connexion_mode;
	}
	
	
	/****************************************************************************************/
	
	/**
	* Get the data sent with the request
	*
	* @return string
	*/
	public function get_request_sent_data()
	{
		return $this->request_sent_data;
	}
	
	/**
	* Set the data sent with the request
	*
	* @var $request_sent_data string
	* @return void
	*/
	public function set_request_sent_data($request_sent_data)
	{
		$this->request_sent_data = $request_sent_data;
	}
	
	
	/****************************************************************************************/
	
	/**
	* Get the request URL
	*
	* @return string
	*/
	public function get_request_url()
	{
		return $this->request_url;
	}
	
	/**
	* Set the request URL
	*
	* @var $request_url string
	* @return void
	*/
	public function set_request_url($request_url)
	{
		$this->request_url = $request_url;
	}
	
	
	/****************************************************************************************/
	
	/**
	* Get the request port number
	*
	* @return integer
	*/
	public function get_request_port()
	{
		return $this->request_port;
	}
	
	/**
	* Set the request port number
	*
	* @var $request_port integer
	* @return void
	*/
	public function set_request_port($request_port)
	{
		$this->request_port = $request_port;
	}
	
	
	/****************************************************************************************/
	
	
}
?>