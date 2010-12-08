<?php

namespace common\libraries;

/**
 * A REST message that will be returned when a REST server has been called.
 */
abstract class RestMessageRenderer
{
    const FORMAT_PLAIN = 'plain';
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';

    abstract function render_object(DataClass $object);
    abstract function render_multiple_objects(array $objects);

    function render($object)
    {
        if($object instanceof DataClass)
        {
            $this->render_object($object);
        }
        else
        {
            if(is_array($object) && count($object) > 0 && $object[0] instanceof DataClass)
            {
               $this->render_multiple_objects($object);
            }
        }
    }

    static function get_formats()
    {
        return array(self :: FORMAT_JSON, self :: FORMAT_XML, self :: FORMAT_HTML, self :: FORMAT_PLAIN);
    }

    static function factory($type)
    {
        $path = dirname(__FILE__) . '/rest_message_renderer/' . $type . '_rest_message_renderer.class.php';
        if(!file_exists($path))
        {
            throw new Exception(Translation :: get('CouldNotCreateRestMessageRendererType', array('TYPE' => $type)), null, WebserviceManager :: APPLICATION_NAME);
        }

        require_once($path);

        $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'RestMessageRenderer';
        return new $class();
    }
}

?>