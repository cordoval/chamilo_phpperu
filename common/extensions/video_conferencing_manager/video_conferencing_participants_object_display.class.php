<?php
abstract class VideoConferencingParticipantsObjectDisplay
{
    private $object;

    function VideoConferencingParticipantsObjectDisplay($object)
    {
        $this->object = $object;
    }

    static function factory($object)
    {
        $type = $object->get_object_type();
        $class = Utilities :: underscores_to_camelcase($type) . 'VideoConferencingParticipantsObjectDisplay';
        require_once dirname(__FILE__) . '/type/' . $type . '/' . $type . '_video_conferencing_participants_object_display.class.php';
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
        $html[] = $this->get_id();
        $html[] = $this->get_type() . '<br/>';
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
        $properties[Translation :: get('Id')] = $object->get_id();
        $properties[Translation :: get('Type')] = $object->get_type();

        return $properties;
    }

    /**
     * @return string
     */
    function get_id()
    {
        $object = $this->get_object();
        return '<h3>' . $object->get_id() . '</h3>';
    }
}
?>