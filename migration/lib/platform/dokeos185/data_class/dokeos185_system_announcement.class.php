<?php
/**
 * $Id: dokeos185_system_announcement.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';
require_once dirname(__FILE__) . '/../dokeos185_data_manager.class.php';

/**
 * This class represents an old Dokeos 1.8.5 system announcement
 *
 * @author David Van Wayenberghµ
 * @author Sven Vanpoucke
 */

class Dokeos185SystemAnnouncement extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
	const TABLE_NAME = 'sys_announcement';   
	const DATABASE_NAME = 'main_database';
	
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
     * Get the default properties of all system annoucement.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_CONTENT, self :: PROPERTY_DATE_START, self :: PROPERTY_DATE_END, self :: PROPERTY_VISIBLE_TEACHER, self :: PROPERTY_VISIBLE_STUDENT, self :: PROPERTY_VISIBLE_GUEST, self :: PROPERTY_LANG);
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
    function is_valid()
    {
        
        if (! ($this->get_title() || $this->get_content()))
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'system announcement', 'ID' => $this->get_id())));
            return false;
        }
        
        return true;
    }

    /**
     * migrate system announcement, set category, make publication
     * @param String $admin_id
     * @return Announcement
     */
    function convert_data()
    {
		$admin_id = $this->get_id_reference($this->get_data_manager()->get_admin_id(), 'main_database.user');
		
    	$chamilo_system_announcement = new SystemAnnouncement();
        $chamilo_system_announcement->set_owner_id($admin_id);
        $chamilo_system_announcement->set_icon(SystemAnnouncement :: ICON_CONFIRMATION);
        
        if (! $this->get_title())
        {
            $chamilo_system_announcement->set_title(Utilities :: truncate_string($this->get_content(), 50));
        }
        else 
        {
            $chamilo_system_announcement->set_title($this->get_title());
        }
        
        if (! $this->get_content())
        {
            $chamilo_system_announcement->set_description($this->get_title());
        }
        else
        {
            $chamilo_system_announcement->set_description($this->get_content());
        }
            
        //Create category in admin repository and create system announcement    
            
        $chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($admin_id, Translation :: get('SystemAnnouncements')); 
		$chamilo_system_announcement->set_parent_id($chamilo_category_id);
        $chamilo_system_announcement->create();
        
        //Make System Announcement publication
        $chamilo_system_announcement_publication = new SystemAnnouncementPublication();
        $chamilo_system_announcement_publication->set_content_object_id($chamilo_system_announcement->get_id());
        $chamilo_system_announcement_publication->set_publisher($admin_id);
        $chamilo_system_announcement_publication->set_published(strtotime($this->get_date_start()));
        $chamilo_system_announcement_publication->create();
        
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'system announcement', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $chamilo_system_announcement->get_id())));
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
    
    static function get_class_name()
    {
    	return self :: CLASS_NAME;
    }
    
    static function get_database_name()
    {
    	return self :: DATABASE_NAME;
    }
}
?>