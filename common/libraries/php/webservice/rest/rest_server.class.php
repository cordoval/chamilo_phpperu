<?php
class RestServer
{
    const ACCEPTED_FORMAT_PLAIN = 'text/plain';
    const ACCEPTED_FORMAT_HTML = 'text/html; charset=UTF-8';
    const ACCEPTED_FORMAT_JSON = 'application/json';
    const ACCEPTED_FORMAT_XML = 'application/xml';

    const FORMAT_PLAIN = 'plain';
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    public $url;
    public $method;
    public $format;
    public $data;

    function RestServer()
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
                        $this->format = self :: FORMAT_HTML;
                        return;
                        break;
                    case self :: ACCEPTED_FORMAT_JSON :
                        $this->format = self :: FORMAT_JSON;
                        return;
                        break;
                    case self :: ACCEPTED_FORMAT_XML :
                        $this->format = self :: FORMAT_XML;
                        return;
                        break;
                    case self :: ACCEPTED_FORMAT_PLAIN :
                        $this->format = self :: FORMAT_PLAIN;
                        return;
                        break;
                }
            }
        }

        $extension_method = explode('.', $this->get_url());
        if (count($extension_method) == 2 && in_array($extension_method[1], $this->get_formats()))
        {
            $this->format = $extension_method[1];
        }
        else
        {
            $this->format = self :: FORMAT_PLAIN;
        }
    }

    public function determine_data()
    {
        switch ($this->get_method())
        {
            case self :: METHOD_GET :
                $this->data = $_GET;
                break;
            case self :: METHOD_POST :
                $this->data = $_POST;
                break;
            case self :: METHOD_PUT :
                parse_str(file_get_contents('php://input'), $_PUT);
                $this->data = $_PUT;
            case self :: METHOD_DELETE :
                parse_str(file_get_contents('php://input'), $_DELETE);
                $this->data = $_DELETE;
                break;
        }
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

    public function get_formats()
    {
        return array(self :: FORMAT_JSON, self :: FORMAT_XML, self :: FORMAT_HTML, self :: FORMAT_PLAIN);
    }

}

// TEST SCRIPT
include_once ('../../global.inc.php');

$rest_server = new RestServer();
$rest_server->handle();
dump($rest_server);
?>