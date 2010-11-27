<?php

namespace common\libraries;

/**
 * A REST message that will be returned when a REST server has been called.
 */
abstract class RestMessage
{
    const FORMAT_PLAIN = 'plain';
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';

    const TYPE_SUCCESS = 'success';
    const TYPE_OBJECT = 'object';
    const TYPE_OBJECTS = 'objects';
    
    function render($format)
    {
        switch($format)
        {
            case self :: FORMAT_PLAIN:
                return $this->render_as_plain();
            case self :: FORMAT_HTML:
                return $this->render_as_html();
            case self :: FORMAT_JSON:
                return $this->render_as_json();
            case self :: FORMAT_XML:
                return $this->render_as_xml();
        }
    }

    abstract function render_as_xml();
    abstract function render_as_html();
    abstract function render_as_json();
    abstract function render_as_plain();

    // Helper functions
    function render_xml_header()
    {
        echo '<?xml version="1.0" encoding="UTF-8"?>';
    }

    function render_html_header($column_names)
    {
        Display :: header();

        $html = array();
        $html[] = '<br />';
        $html[] = '<table class="data_table">';
        $html[] = '<thead>';
        $html[] = '<tr>';

        foreach($column_names as $column_name)
        {
            $html[] = '<th>' . $column_name . '</th>';
        }

        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        echo implode("\n", $html);
    }

    function render_html_footer()
    {
        $html[] = '</tbody>';
        $html[] = '</table>';

        echo implode("\n", $html);
        
        Display :: footer();
    }

    static function factory($type)
    {
        $path = dirname(__FILE__) . '/messages/' . $type . '_rest_message.class.php';
        if(!file_exists($path))
        {
            throw new Exception(Translation :: get('CouldNotCreateRestMessageType', array('TYPE' => $type)));
        }

        require_once($path);

        $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'RestMessage';
        return new $class();
    }
}

?>
