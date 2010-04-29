<?php
/**
 * $Id: redirect.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common
 */
class Redirect
{
    // Different redirect types
    const TYPE_LINK = 'link';
    const TYPE_URL = 'url';

    // Different link types
    const TYPE_CORE = 'core';
    const TYPE_APPLICATION = 'application';

    const ARGUMENT_SEPARATOR = '&';

    static function link($application, $parameters = array (), $filter = array(), $encode_entities = false, $type = self :: TYPE_APPLICATION)
    {
        $link = self :: get_link($application, $parameters, $filter, $encode_entities, $type);
        self :: write_header($link);
    }

    static function get_link($application, $parameters = array (), $filter = array(), $encode_entities = false, $type = self :: TYPE_APPLICATION)
    {
        switch ($type)
        {
            case self :: TYPE_CORE :
                //$link = 'index_' . $application;
                $link = 'core';
                $parameters['application'] = $application;
                break;
            case self :: TYPE_APPLICATION :
                $link = 'run';
                $parameters['application'] = $application;
                break;
            default :
                $link = 'index';
                break;
        }

        $link .= '.php';

        if (count($filter) > 0)
        {
            foreach ($parameters as $key => $value)
            {
                if (! in_array($key, $filter))
                {
                    $url_parameters[$key] = $value;
                }
            }

            $parameters = $url_parameters;
        }

       return self :: web_link($link, $parameters, $encode_entities);
    }

    static function url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        $url = self :: get_url($parameters, $filter, $encode_entities);
        self :: write_header($url);
    }

    static function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
    	$url = $_SERVER['PHP_SELF'];

        if (count($filter) > 0)
        {
            foreach ($parameters as $key => $value)
            {
                if (! in_array($key, $filter))
                {
                    $url_parameters[$key] = $value;
                }
            }

            $parameters = $url_parameters;
        }

        return self :: web_link($url, $parameters, $encode_entities);
    }
    
    static function web_link($url, $parameters = array (), $encode_entities = false)
    {
    	if (count($parameters))
        {
            // Because the argument separator can be defined in the php.ini
            // file, we explicitly add it as a parameter here to avoid
            // trouble when parsing the resulting urls
            $url .= '?' . http_build_query($parameters, '', self :: ARGUMENT_SEPARATOR);
        }

        if ($encode_entities)
        {
            $url = htmlentities($url);
        }

        return $url;
    }

    static function write_header($url)
    {  
    	if(headers_sent($filename, $line))
    	{
    		throw new Exception('headers already sent in ' . $filename . ' on line ' . $line);
    	}
        header('Location: ' . $url);
        exit();
    }
}
?>