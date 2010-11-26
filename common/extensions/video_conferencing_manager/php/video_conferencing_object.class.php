<?php
namespace common\extensions\video_conferencing_manager;

use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Session;
use common\libraries\Translation;
use common\libraries\Theme;

use repository\RepositoryDataManager;
use repository\ContentObject;
//use repository\VideoConferencingSync;

abstract class VideoConferencingObject
{
    /**
     * @var array
     */
    private $default_properties;

    /**
     * @var VideoConferencingSync
     */
    private $synchronization_data;

    const PROPERTY_ID = 'id';
    
    const PROPERTY_VIDEO_CONFERENCING_ID = 'video_conferencing_id';
    const PROPERTY_TITLE = 'title';
    
    /**
     * @param array $default_properties
     */
    function __construct($default_properties = array ())
    {
        $this->default_properties = $default_properties;
    }

    /**
     * Get the default properties of all data classes.
     * @return array The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_ID;
        $extended_property_names[] = self :: PROPERTY_VIDEO_CONFERENCING_ID;
        $extended_property_names[] = self :: PROPERTY_TITLE;
        return $extended_property_names;
    }

    /**
     * Gets a default property of this data class object by name.
     * @param string $name The name of the property.
     * @param mixed
     */
    function get_default_property($name)
    {
        return (isset($this->default_properties) && array_key_exists($name, $this->default_properties)) ? $this->default_properties[$name] : null;
    }

    /**
     * @param $default_properties the $default_properties to set
     */
    public function set_default_properties($default_properties)
    {
        $this->default_properties = $default_properties;
    }

    /**
     * Sets a default property of this data class by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->default_properties[$name] = $value;
    }

    function get_default_properties()
    {
        return $this->default_properties;
    }

    /**
     * @return string
     */
    public function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * @return int
     */
    public function get_video_conferencing_id()
    {
        return $this->get_default_property(self :: PROPERTY_VIDEO_CONFERENCING_ID);
    }
    
    /**
     * @return string
     */
    public function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * @param string $title
     */
    public function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    /**
     * @param string $id
     */
    public function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * @param int $video_conferencing_id
     */
    public function set_video_conferencing_id($video_conferencing_id)
    {
        $this->set_default_property(self :: PROPERTY_VIDEO_CONFERENCING_ID, $video_conferencing_id);
    }

//    /**
//     * @return VideoConferencingSync
//     */
//    function get_synchronization_data()
//    {
//        if (! isset($this->synchronization_data))
//        {
//            $sync_conditions = array();
//            $sync_conditions[] = new EqualityCondition(VideoConferencingSync :: PROPERTY_VIDEO_CONFERENCING_OBJECT_ID, $this->get_id());
//            $sync_conditions[] = new EqualityCondition(VideoConferencingSync :: PROPERTY_VIDEO_CONFERENCING_ID, $this->get_video_conferencing_id());            $sync_condition = new AndCondition($sync_conditions);
//
//            $this->synchronization_data = RepositoryDataManager :: get_instance()->retrieve_video_conferencing_sync($sync_condition);
//        }
//
//        return $this->synchronization_data;
//    }
//
//    /**
//     * @return int
//     */
//    function get_synchronization_status()
//    {
//        return $this->get_synchronization_data()->get_synchronization_status(null, $this->get_modified());
//    }
//
//    /**
//     * @return boolean
//     */
//    function is_importable()
//    {
//        return ! $this->get_synchronization_data() instanceof ExternalRepositorySync;
//    }
}
?>