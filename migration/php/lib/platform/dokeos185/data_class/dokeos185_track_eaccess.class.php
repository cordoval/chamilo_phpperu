<?php
namespace migration;

use application\weblcms\CourseModuleLastAccess;
use common\libraries\Utilities;


/**
 * $Id: dokeos185_track_eaccess.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 track_e_access
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackEAccess extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'track_e_access';
    const DATABASE_NAME = 'statistics_database';

    /**
     * Dokeos185TrackEAccess properties
     */
    const PROPERTY_ACCESS_ID = 'access_id';
    const PROPERTY_ACCESS_USER_ID = 'access_user_id';
    const PROPERTY_ACCESS_DATE = 'access_date';
    const PROPERTY_ACCESS_COURS_CODE = 'access_cours_code';
    const PROPERTY_ACCESS_TOOL = 'access_tool';

    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;
    private $convert = array('course_description' => 'description', 'calendar_event' => 'calendar', 'document' => 'document', 'learnpath' => 'learning_path', 'link' => 'link', 'announcement' => 'announcement', 'forum' => 'forum', 'dropbox' => 'dropbox', 'user' => 'user', 'group' => 'group', 'chat' => 'chat', 'tracking' => 'statics', 'course_setting' => 'course_settings', 'survey' => 'learning_style_survey', 'course_maintenance' => 'maintenance');

    /**
     * Creates a new Dokeos185TrackEAccess object
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
        return array(self :: PROPERTY_ACCESS_ID, self :: PROPERTY_ACCESS_USER_ID, self :: PROPERTY_ACCESS_DATE, self :: PROPERTY_ACCESS_COURS_CODE, self :: PROPERTY_ACCESS_TOOL);
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
     * Returns the access_id of this Dokeos185TrackEAccess.
     * @return the access_id.
     */
    function get_access_id()
    {
        return $this->get_default_property(self :: PROPERTY_ACCESS_ID);
    }

    /**
     * Returns the access_user_id of this Dokeos185TrackEAccess.
     * @return the access_user_id.
     */
    function get_access_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_ACCESS_USER_ID);
    }

    /**
     * Returns the access_date of this Dokeos185TrackEAccess.
     * @return the access_date.
     */
    function get_access_date()
    {
        return $this->get_default_property(self :: PROPERTY_ACCESS_DATE);
    }

    /**
     * Returns the access_cours_code of this Dokeos185TrackEAccess.
     * @return the access_cours_code.
     */
    function get_access_course_code()
    {
        return $this->get_default_property(self :: PROPERTY_ACCESS_COURS_CODE);
    }

    /**
     * Returns the access_tool of this Dokeos185TrackEAccess.
     * @return the access_tool.
     */
    function get_access_tool()
    {
        return $this->get_default_property(self :: PROPERTY_ACCESS_TOOL);
    }

    /**
     * Validation checks
     * @param Array $array
     * @todo
     */
    function is_valid()
    {

        $new_user_id = $this->get_id_reference($this->get_access_user_id(), 'main_database.user');
        $new_course_id = $this->get_id_reference($this->get_access_course_code(), 'main_database.course');

        if (!$new_user_id || !$new_course_id || !$this->convert[$this->get_access_tool()]) //if the user id doesn't exist anymore, the data can be ignored
        {
            $this->create_failed_element($this->get_id());
            return false;
        }
        return true;
    }

    /**
     * Convertion
     * @param Array $array
     */
    function convert_data()
    {
        //$visit_tracker = new VisitTracker();
        $new_course_id = $this->get_id_reference($this->get_access_course_code(), 'main_database.course');
        $new_user_id = $this->get_id_reference($this->get_access_user_id(), 'main_database.user');
        $tool = $this->get_access_tool();

//        if ($tool)
//            $url = "/hg/run.php?go=courseviewer&course=$new_course_id&application=weblcms&tool=$tool";
//        else
//            $url="/hg/run.php?go=courseviewer&course=$new_course_id&application=weblcms";
//
//require_once dirname(__FILE__) . '/../../../../../application/lib/weblcms/course/course_module_last_access.class.php';
//
//        $visit_tracker->create();

        $value = $this->convert[$tool];
        if ($value)
        {
            $course_module_last_access = new CourseModuleLastAccess();
            $course_module_last_access->set_course_code($new_course_id);
            $course_module_last_access->set_user_id($new_user_id);
            $course_module_last_access->set_module_name($value);
            $course_module_last_access->set_category_id(0);
            $course_module_last_access->set_access_date(strtotime($this->get_access_date()));

            return $course_module_last_access->create();
        }
    }

    static function get_table_name()
    {
                return Utilities :: camelcase_to_underscores(substr(Utilities :: get_classname_from_namespace(__CLASS__), 9));  ;
    }

    static function get_class_name()
    {
        return self :: CLASS_NAME;
    }

    function get_database_name()
    {
        return self :: DATABASE_NAME;
    }

}

?>
