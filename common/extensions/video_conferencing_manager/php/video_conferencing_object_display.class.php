<?php
namespace common\extensions\video_conferencing_manager;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\DatetimeUtilities;
use common\libraries\PropertiesTable;
use common\libraries\Theme;

abstract class VideoConferencingObjectDisplay
{
    /**
     * @var VideoConferencingObject
     */
    private $object;

    /**
     * @param VideoConferencingObject $object
     */
    function __construct($object)
    {
        $this->object = $object;
    }

    /**
     * @param VideoConferencingObject $object
     * @return VideoConferencingObjectDisplay
     */
    static function factory($object)
    {
        $type = $object->get_object_type();
        $class = 'common\extensions\video_conferencing_manager\implementation\\' . $type . '\\' . Utilities :: underscores_to_camelcase($type) . 'VideoConferencingObjectDisplay';
        require_once dirname(__FILE__) . '/../implementation/' . $type . '/php/' . $type . '_video_conferencing_object_display.class.php';
        return new $class($object);
    }

    /**
     * @return VideoConferencingObject
     */
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
        $html[] = $this->get_title(); '<br/>';
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