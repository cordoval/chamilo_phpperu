<?php
require_once dirname(__FILE__) . '/../../external_repository_object_display.class.php';

abstract class StreamingMediaExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{

    function as_html()
    {
        $object = $this->get_object();
        
        $html = array();
        $html[] = '<h3>' . $object->get_title() . ' (' . Utilities :: format_seconds_to_minutes($object->get_duration()) . ')</h3>';
        $html[] = $this->get_video_player_as_html() . '<br/>';
        $html[] = $this->get_properties_table() . '<br/>';
        
        return implode("\n", $html);
    }

    abstract function get_video_player_as_html();

    function get_properties_table()
    {
        $html = array();
        $html[] = '<table class="data_table data_table_no_header">';
        $html[] = '<tr><td class="header">' . Translation :: get('Description') . '</td><td>' . $this->get_object()->get_description() . '</td></tr>';
        $html[] = $this->get_additional_properties();
        $html[] = '<tr><td class="header">' . Translation :: get('Status') . '</td><td>' . $this->get_object()->get_status_text() . '</td></tr>';
        $html[] = '</table>';
        return implode("\n", $html);
    }

    abstract function get_additional_properties();
}
?>