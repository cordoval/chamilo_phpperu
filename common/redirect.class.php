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
        
        if (count($parameters))
        {
            $link .= '?' . http_build_query($parameters);
        }
        
        if ($encode_entities)
        {
            $link = htmlentities($link);
        }
        
        return $link;
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
        
        if (count($parameters))
        {
            $url .= '?' . http_build_query($parameters);
        }
        
        if ($encode_entities)
        {
            $url = htmlentities($url);
        }
        
        return $url;
    }

    static function write_header($url)
    {
        header('Location: ' . $url);
        exit();
    }
}
?>