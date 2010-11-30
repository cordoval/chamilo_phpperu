<?php

namespace common\libraries;

/**
 * Class to provide authentication for webservices
 */
abstract class WebserviceAuthentication
{
    static function factory()
    {
        $type = 'digest';
        
        $path = dirname(__FILE__) . '/' . $type . '/' . $type . '_webservice_authentication.class.php';
        if(!file_exists($path))
        {
            throw new Exception(Translation :: get('CouldNotCreateWebserviceAuthenticationType', array('TYPE' => $type)));
        }

        require_once($path);

        $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'WebserviceAuthentication';
        return new $class();
    }

    abstract function is_valid();
}

?>
