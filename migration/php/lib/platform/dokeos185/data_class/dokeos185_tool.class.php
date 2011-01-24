<?php
namespace migration;

use common\libraries\Translation;
use repository\RepositoryDataManager;
use common\libraries\Utilities;
use application\weblcms\WeblcmsDataManager;

/**
 * $Id: dokeos185_tool.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Tool
 *
 * @author Van Wayenbergh David
 */
class Dokeos185Tool extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'tool';
    /**
     * Migration data manager
     */
    private $convert = array('course_description' => 'description', 'calendar_event' => 'calendar', 'document' => 'document', 'learnpath' => 'learning_path',
        'link' => 'link', 'announcement' => 'announcement', 'forum' => 'forum', 'dropbox' => 'dropbox', 'user' => 'user', 'group' => 'course_group',
        'chat' => 'chat', 'tracking' => 'reporting', 'course_setting' => 'course_settings', 'survey' => 'survey', 'course_maintenance' => 'maintenance', 'wiki' => 'wiki');

    /**
     * Announcement properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_VISIBILITY = 'visibility';

    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new dokeos185 Tool object
     * @param array $defaultProperties The default properties
     */
    function __construct($defaultProperties = array())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_VISIBILITY);
    }

    /**
     * Sets a default property by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the id of this tool.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the name of this tool.
     * @return string the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the visibility of this tool.
     * @return int the visibility.
     */
    function get_visibility()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBILITY);
    }

    /**
     * Checks if tool is valid
     */
    function is_valid()
    {
        if (!$this->get_name())
        {
            $this->create_failed_element($this->get_id());
//
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'tool', 'ID' => $this->get_id())));

            return false;
        }
        return true;
    }

    /**
     * migrate to new tool
     * @param String $course
     * @return dokeos185tool
     */
    function convert_data()
    {
        $value = $this->convert[$this->get_name()];
        $new_course_id = $this->get_id_reference($this->get_course()->get_code(), 'main_database.course');
        $db = WeblcmsDataManager :: get_instance();
        $db->set_module_visible($new_course_id, $value, $this->get_visibility());
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'tool', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $new_course_id)));

        return $this;
    }

    static function get_table_name()
    {
                return Utilities :: camelcase_to_underscores(substr(Utilities :: get_classname_from_namespace(__CLASS__), 9));  ;
    }

    static function get_class_name()
    {
        return self :: CLASS_NAME;
    }

}

?>