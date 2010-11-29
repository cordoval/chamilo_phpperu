<?php

namespace common\libraries;

require_once dirname(__FILE__) . '/rest_message_renderer.class.php';
require_once dirname(__FILE__) . '/success_rest_message.class.php';

class RestServer
{
    const ACCEPTED_FORMAT_PLAIN = 'text/plain';
    const ACCEPTED_FORMAT_HTML = 'text/html; charset=UTF-8';
    const ACCEPTED_FORMAT_JSON = 'application/json';
    const ACCEPTED_FORMAT_XML = 'application/xml';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    const PARAM_ID = 'id';
    const PARAM_APPLICATION = 'application';
    const PARAM_OBJECT = 'object';

    public $url;
    public $method;
    public $format;
    public $data;
    public $webservice_handler;

    function __construct()
    {

    }

    function handle()
    {
        $this->process_request();
    }

    public function process_request()
    {
        $this->determine_path();
        $this->determine_method();
        $this->determine_format();
        $this->determine_data();
        $this->call_webservice_handler();
    }

    public function determine_path()
    {
        $path = substr(preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']), 1);
        if ($path[strlen($path) - 1] == '/')
        {
            $path = substr($path, 0, - 1);
        }
        $this->url = $path;
    }

    public function determine_method()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function determine_format()
    {
        $accepted_formats = explode(',', $_SERVER['HTTP_ACCEPT']);

        foreach ($this->get_accepted_formats() as $format)
        {
            if (in_array($format, $accepted_formats))
            {
                switch ($format)
                {
                    case self :: ACCEPTED_FORMAT_HTML :
                        $this->format = RestMessageRenderer :: FORMAT_HTML;
                        return;
                        break;
                    case self :: ACCEPTED_FORMAT_JSON :
                        $this->format = RestMessageRenderer :: FORMAT_JSON;
                        return;
                        break;
                    case self :: ACCEPTED_FORMAT_XML :
                        $this->format = RestMessageRenderer :: FORMAT_XML;
                        return;
                        break;
                    case self :: ACCEPTED_FORMAT_PLAIN :
                        $this->format = RestMessageRenderer :: FORMAT_PLAIN;
                        return;
                        break;
                }
            }
        }

        $extension_method = explode('.', $this->get_url());
        if (count($extension_method) == 2 && in_array($extension_method[1], RestMessageRenderer :: get_formats()))
        {
            $this->format = $extension_method[1];
        }
        else
        {
            $this->format = RestMessageRenderer :: FORMAT_HTML;
        }
    }

    public function determine_data()
    {
        switch ($this->get_method())
        {
            case self :: METHOD_GET :
                $this->data = $_GET;
                unset($this->data[self :: PARAM_APPLICATION]);
                unset($this->data[self :: PARAM_OBJECT]);
                unset($this->data[self :: PARAM_ID]);
                break;
            case self :: METHOD_POST :
                $this->data = $_POST;
                break;
            case self :: METHOD_PUT :
                parse_str(file_get_contents('php://input'), $_PUT);
                $this->data = $_PUT;
                break;
            case self :: METHOD_DELETE :
                parse_str(file_get_contents('php://input'), $_DELETE);
                $this->data = $_DELETE;
                break;
        }
    }

    public function call_webservice_handler()
    {
        $application = Request :: get(self :: PARAM_APPLICATION);
        $object = Request :: get(self :: PARAM_OBJECT);
        $id = Request :: get(self :: PARAM_ID);

        $type = Application :: get_type($application);
        $path = $type :: get_application_path($application) . 'php/webservices/' . $object . '/webservice_handler.class.php';
        require_once($path);
        $class = Application :: determine_namespace($application) . '\\' . Utilities :: underscores_to_camelcase($object) . 'WebserviceHandler';

        $this->webservice_handler = new $class();

        switch ($this->get_method())
        {
            case self :: METHOD_GET :
                if ($id)
                {
                    if (method_exists($this->webservice_handler, 'get'))
                    {
                        $object = call_user_func(array($this->webservice_handler, 'get'), array($id));
                    }
                }
                else
                {
                    if (method_exists($this->webservice_handler, 'get_list'))
                    {
                        $object = call_user_func(array($this->webservice_handler, 'get_list'));
                    }
                }
                break;
            case self :: METHOD_POST :
                if (method_exists($this->webservice_handler, 'create'))
                {
                    $object = call_user_func(array($this->webservice_handler, 'create'), array($this->data));
                }
                break;
            case self :: METHOD_PUT :
                if (method_exists($this->webservice_handler, 'update'))
                {
                    $object = call_user_func(array($this->webservice_handler, 'update'), array($id, $this->data));
                }
                break;
            case self :: METHOD_DELETE :
                if (method_exists($this->webservice_handler, 'delete'))
                {
                    $object = call_user_func(array($this->webservice_handler, 'delete'), array($id));
                }
                break;
        }

        $renderer = RestMessageRenderer :: factory($this->format);
        $renderer->render($object);
    }

    /**
     * @return the $url
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     * @return the $method
     */
    public function get_method()
    {
        return $this->method;
    }

    /**
     * @return the $format
     */
    public function get_format()
    {
        return $this->format;
    }

    /**
     * @return the $data
     */
    public function get_data()
    {
        return $this->data;
    }

    /**
     * @param $url the $url to set
     */
    public function set_url($url)
    {
        $this->url = $url;
    }

    /**
     * @param $method the $method to set
     */
    public function set_method($method)
    {
        $this->method = $method;
    }

    /**
     * @param $format the $format to set
     */
    public function set_format($format)
    {
        $this->format = $format;
    }

    /**
     * @param $data the $data to set
     */
    public function set_data($data)
    {
        $this->data = $data;
    }

    public function get_accepted_formats()
    {
        return array(self :: ACCEPTED_FORMAT_JSON, self :: ACCEPTED_FORMAT_XML, self :: ACCEPTED_FORMAT_HTML, self :: ACCEPTED_FORMAT_PLAIN);
    }

    

}
?>