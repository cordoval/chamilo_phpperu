<?php
abstract class VideoConferencingMeetingObjectDisplay
{
    private $object;

    function VideoConferencingMeetingObjectDisplay($object)
    {
        $this->object = $object;
    }

    static function factory($object)
    {
        $type = $object->get_object_type();
        $class = Utilities :: underscores_to_camelcase($type) . 'VideoConferencingMeetingObjectDisplay';
        require_once dirname(__FILE__) . '/type/' . $type . '/' . $type . '_video_conferencing_meeting_object_display.class.php';
        return new $class($object);
    }

    function get_object()
    {
        return $this->object;
    }

    /**
     * @return string
     */
    function as_html()
    {
        $html = array();
        $html[] = $this->get_title();
        $html[] = $this->get_preview() . '<br/>';
        $html[] = $this->get_properties_table();

        return implode("\n", $html);
    }

    /**
     * @return string
     */
    function get_properties_table()
    {
        $object = $this->get_object();
        $properties = $this->get_display_properties();

        $table = new PropertiesTable($properties);
        $table->setAttribute('style', 'margin-top: 1em; margin-bottom: 0;');
        return $table->toHtml();
    }

    /**
     * @return array
     */
    function get_display_properties()
    {
        $object = $this->get_object();

        $properties = array();
        $properties[Translation :: get('Title')] = $object->get_title();

        if ($object->get_description())
        {
            $properties[Translation :: get('Description')] = $object->get_description();
        }
        $properties[Translation :: get('StartTime')] = DatetimeUtilities :: format_locale_date(null, $object->get_start_time());
        $properties[Translation :: get('StartDate')] = DatetimeUtilities :: format_locale_date(null, $object->get_start_date());
        $properties[Translation :: get('EndTime')] = DatetimeUtilities :: format_locale_date(null, $object->get_end_time());
        $properties[Translation :: get('EndDate')] = DatetimeUtilities :: format_locale_date(null, $object->get_end_date());
        
        $properties[Translation :: get('Participants')] = $object->get_participants();

        return $properties;
    }

    /**
     * @return string
     */
    function get_title()
    {
        $object = $this->get_object();
        return '<h3>' . $object->get_title() . '</h3>';
    }
}
?>