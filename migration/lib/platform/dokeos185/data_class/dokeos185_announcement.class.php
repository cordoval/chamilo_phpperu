<?php

/**
 * $Id: dokeos185_announcement.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class represents an old Dokeos 1.8.5 announcement
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Announcement extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'announcement';
    
    /**
     * Announcement properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_CONTENT = 'content';
    const PROPERTY_END_DATE = 'end_date';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    const PROPERTY_EMAIL_SENT = 'email_sent';

    

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_CONTENT, self :: PROPERTY_END_DATE, self :: PROPERTY_DISPLAY_ORDER, self :: PROPERTY_EMAIL_SENT);
    }

    /**
     * Returns the id of this announcement.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the title of this announcement.
     * @return string the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the content of this announcement.
     * @return string the content.
     */
    function get_content()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT);
    }

    /**
     * Returns the end_date of this announcement.
     * @return date the end_date.
     */
    function get_end_date()
    {
        return $this->get_default_property(self :: PROPERTY_END_DATE);
    }

        /**
        * Returns the email_sent of this announcement.
        * @return int the email_sent.
        */
        function get_email_sent()
        {
            return $this->get_default_property(self :: PROPERTY_EMAIL_SENT);
        }

    /**
     * Returns the display_order of this announcement.
     * @return int the display_order.
     */
    function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Check if the announcement is valid
     * @param Course $course the course to which the announcement belongs
     * @return true if the announcement is valid 
     */
    function is_valid()
    {
        $this->set_item_property($this->get_data_manager()->get_item_property($this->get_course(), 'announcement', $this->get_id()));
        
        if (! $this->get_id() || ! ($this->get_title() || $this->get_content()) || ! $this->get_item_property() || ! $this->get_item_property()->get_lastedit_date() || ! $this->get_item_property()->get_insert_date())
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'announcement', 'ID' => $this->get_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new announcement 
     * Create announcement
     * Create publication
     * @param Course $course the course to which the announcement belongs
     * @return the new announcement
     */
    function convert_data()
    {
        $course = $this->get_course();
        
    	$new_user_id = $this->get_id_reference($this->get_item_property()->get_insert_user_id(), 'main_database.user');
        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');

        $new_to_group_id[] = $this->get_id_reference($this->get_item_property()->get_to_group_id(), 'main_database.user');
        $new_to_user_id[] = $this->get_id_reference($this->get_item_property()->get_to_user_id(), 'main_database.user');
        
        if (! $new_user_id)
        {
            $new_user_id = $this->get_data_manager()->get_owner_id($new_course_code);
        }
        
        //announcement parameters
        $chamilo_announcement = new Announcement();

        $chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($new_user_id, Translation :: get('Announcements'));
        
        if (! $this->get_title())
        {
            $chamilo_announcement->set_title(Utilities :: truncate_string($this->get_content(), 20));
        }
        else
        {
            $chamilo_announcement->set_title($this->get_title());
        }
        
        if (! $this->get_content())
        {
            $chamilo_announcement->set_description($this->get_title());
        }
        else
        {
            $chamilo_announcement->set_description($this->get_content());
        }
        
        $chamilo_announcement->set_owner_id($new_user_id);
        $chamilo_announcement->set_creation_date(strtotime($this->get_item_property()->get_insert_date()));
        $chamilo_announcement->set_modification_date(strtotime($this->get_item_property()->get_lastedit_date()));
        
        if ($this->get_item_property()->get_visibility() == 2)
        {
            $chamilo_announcement->set_state(1);
        }
            
        //create announcement in database
        $chamilo_announcement->create_all();
        
        $this->create_publication($chamilo_announcement, $new_course_code, $new_user_id, 'announcement', $new_to_user_id, $new_to_group_id);
        
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'annoucement', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $chamilo_announcement->get_id())));
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
    
    static function get_class_name()
    {
    	return self :: CLASS_NAME;
    }
}
?>