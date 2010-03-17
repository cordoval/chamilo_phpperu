<?php
/**
 * $Id: dokeos185_system_announcement.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_system_announcement.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/system_announcement/system_announcement.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/category_manager/repository_category.class.php';

/**
 * This class represents an old Dokeos 1.8.5 system announcement
 *
 * @author David Van Wayenberghµ
 * @author Sven Vanpoucke
 */

class Dokeos185SystemAnnouncement extends Import
{
    
    /**
     * course relation user properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_CONTENT = 'content';
    const PROPERTY_DATE_START = 'date_start';
    const PROPERTY_DATE_END = 'date_end';
    const PROPERTY_VISIBLE_TEACHER = 'visible_teacher';
    const PROPERTY_VISIBLE_STUDENT = 'visible_student';
    const PROPERTY_VISIBLE_GUEST = 'visible_guest';
    const PROPERTY_LANG = 'lang';
    
    /**
     * Default properties of the system annoucement object, stored in an associative
     * array.
     */
    private $defaultProperties;

    /**
     * Creates a new system annoucement object.
     * @param array $defaultProperties The default properties of the system annoucement
     *                                 object. Associative array.
     */
    function Dokeos185SystemAnnouncement($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property of this system annoucement object by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties of this system annoucement.
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties of all system annoucement.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_CONTENT, self :: PROPERTY_DATE_START, self :: PROPERTY_DATE_END, self :: PROPERTY_VISIBLE_TEACHER, self :: PROPERTY_VISIBLE_STUDENT, self :: PROPERTY_VISIBLE_GUEST, self :: PROPERTY_LANG);
    }

    /**
     * Sets a default property of this system annoucement by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Checks if the given identifier is the name of a default system annoucement
     * property.
     * @param string $name The identifier.
     * @return boolean True if the identifier is a property name, false
     *                 otherwise.
     */
    static function is_default_property_name($name)
    {
        return in_array($name, self :: get_default_property_names());
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the id of this system announcement.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the title of this system announcement.
     * @return String The title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the content of this system announcement.
     * @return String The content.
     */
    function get_content()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT);
    }

    /**
     * Returns the date_start of this system announcement.
     * @return String The date_start.
     */
    function get_date_start()
    {
        return $this->get_default_property(self :: PROPERTY_DATE_START);
    }

    /**
     * Returns the date_end of this system announcement.
     * @return String The date_end.
     */
    function get_date_end()
    {
        return $this->get_default_property(self :: PROPERTY_DATE_END);
    }

    /**
     * Returns the visible_teacher of this system announcement.
     * @return int The visible_teacher.
     */
    function get_visible_teacher()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBLE_TEACHER);
    }

    /**
     * Returns the visible_student of this system announcement.
     * @return int The visible_student.
     */
    function get_visible_student()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBLE_STUDENT);
    }

    /**
     * Returns the visible_guest of this system announcement.
     * @return int The visible_guest.
     */
    function get_visible_guest()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBLE_GUEST);
    }

    /**
     * Returns the lang of this system announcement.
     * @return String The lang.
     */
    function get_lang()
    {
        return $this->get_default_property(self :: PROPERTY_LANG);
    }

    /**
     * Checks if valid system announcement()
     * @return Boolean
     */
    function is_valid($parameters)
    {
        
        if (! ($this->get_title() || $this->get_content()))
        {
            $mgdm = MigrationDataManager :: get_instance();
            $mgdm->add_failed_element($this->get_id(), 'dokeos_main.sys_announcement');
            return false;
        }
        
        return true;
    }

    /**
     * migrate system announcement, set category, make publication
     * @param String $admin_id
     * @return Announcement
     */
    function convert_to_lcms($parameters)
    {
        $admin_id = $parameters['admin_id'];
        
        $new_mgdm = MigrationDataManager :: get_instance();
        $lcms_system_announcement = new SystemAnnouncement();
        $lcms_system_announcement->set_owner_id($admin_id);
        $lcms_system_announcement->set_icon('6');
        
        if (! $this->get_title())
            $lcms_system_announcement->set_title(substr($this->get_content(), 0, 20));
        else
            $lcms_system_announcement->set_title($this->get_title());
        
        if (! $this->get_content())
            $lcms_system_announcement->set_description($this->get_title());
        else
            $lcms_system_announcement->set_description($this->get_content());
            
        //Create category in admin repository and create system announcement    
            
        $lcms_category_id = $mgdm->get_repository_category_by_name($admin_id,Translation :: get('system_announcements')); 
		$lcms_system_announcement->set_parent_id($lcms_category_id);
        $lcms_system_announcement->create();
        
        //Make System Announcement publication
        $lcms_system_announcement_publication = new SystemAnnouncementPublication();
        $lcms_system_announcement_publication->set_content_object_id($lcms_system_announcement->get_id());
        $lcms_system_announcement_publication->set_publisher($admin_id);
        $lcms_system_announcement_publication->set_published($mgdm->make_unix_time($this->get_date()));
        $lcms_system_announcement_publication->create_all();
        
        return $lcms_system_announcement;
    }

    /**
     * Gets all the system announcement
     * @param Array $parameters
     * @return Array of dokeos185systemannouncements
     */
    static function get_all($parameters)
    {
        $mgdm = $parameters['old_mgdm'];
        
        $db = 'main_database';
        $tablename = 'sys_announcement';
        $classname = 'Dokeos185SystemAnnouncement';
        
        return $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'main_database';
        $array['table'] = 'sys_announcement';
        return $array;
    }
}
?>