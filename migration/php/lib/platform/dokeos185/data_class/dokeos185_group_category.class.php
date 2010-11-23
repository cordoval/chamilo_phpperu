<?php
namespace migration;
use common\libraries\Translation;
use repository\RepositoryDataManager;
use common\libraries\Utilities;
use application\weblcms\CourseGroup;

/**
 * $Id: dokeos185_group_category.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Group category
 *
 * @author Sven Vanpoucke
 */
class Dokeos185GroupCategory extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'group_category';
    /**
     * Group properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_GROUPS_PER_USER = 'groups_per_user';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_MAX_STUDENT = 'max_student';
    const PROPERTY_DOC_STATE = 'doc_state';
    const PROPERTY_CALENDAR_STATE = 'calendar_state';
    const PROPERTY_WORK_STATE = 'work_state';
    const PROPERTY_ANNOUNCEMENTS_STATE = 'groups_state';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    const PROPERTY_self_REG_ALLOWED = 'self_reg_allowed';
    const PROPERTY_self_UNREG_ALLOWED = 'self_unreg_allowed';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_GROUPS_PER_USER, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_MAX_STUDENT, self :: PROPERTY_DOC_STATE, self :: PROPERTY_CALENDAR_STATE, self :: PROPERTY_WORK_STATE, self :: PROPERTY_ANNOUNCEMENTS_STATE, self :: PROPERTY_DISPLAY_ORDER, self :: PROPERTY_self_REG_ALLOWED, self :: PROPERTY_self_UNREG_ALLOWED);
    }
    
    /**
     * Returns the id of this group.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the title of this group.
     * @return string the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the groups_per_user of this group.
     * @return string the groups_per_user.
     */
    function get_groups_per_user()
    {
        return $this->get_default_property(self :: PROPERTY_GROUPS_PER_USER);
    }

    /**
     * Returns the description of this group.
     * @return date the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the max_student of this group.
     * @return int the max_student.
     */
    function get_max_student()
    {
        return $this->get_default_property(self :: PROPERTY_MAX_STUDENT);
    }

    /**
     * Returns the doc_state of this group.
     * @return int the doc_state.
     */
    function get_doc_state()
    {
        return $this->get_default_property(self :: PROPERTY_DOC_STATE);
    }

    /**
     * Returns the calendar_state of this groupcategory.
     * @return int The calendar_state.
     */
    function get_calendar_state()
    {
        return $this->get_default_property(self :: PROPERTY_CALENDAR_STATE);
    }

    /**
     * Returns the work_state of this groupcategory.
     * @return string the work_state.
     */
    function get_work_state()
    {
        return $this->get_default_property(self :: PROPERTY_WORK_STATE);
    }

    /**
     * Returns the groupcategorys_state of this groupcategory.
     * @return string the groupcategorys_state.
     */
    function get_groupcategorys_state()
    {
        return $this->get_default_property(self :: PROPERTY_ANNOUNCEMENTS_STATE);
    }

    /**
     * Returns the display_order of this groupcategory.
     * @return date the display_order.
     */
    function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns the self_reg_allowed of this groupcategory.
     * @return int the self_reg_allowed.
     */
    function get_self_reg_allowed()
    {
        return $this->get_default_property(self :: PROPERTY_self_REG_ALLOWED);
    }

    /**
     * Returns the self_unreg_allowed of this groupcategory.
     * @return int the self_unreg_allowed.
     */
    function get_self_unreg_allowed()
    {
        return $this->get_default_property(self :: PROPERTY_self_UNREG_ALLOWED);
    }

    /**
     * Check if the group category is valid
     * @param Course $course the course
     * @return true if the group category is valid 
     */
    function is_valid()
    {
        $course = $this->get_course();
        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');
        
        if (!$this->get_title() || !$new_course_code)
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'group_category', 'ID' => $this->get_id())));
            
            return false;
        }
        
        return true;
    }

    /**
     * Convert to new group category
     * @param Course $course the course
     * @return the new group category
     */
    function convert_data()
    {
        $course = $this->get_course();
        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');
        
        $chamilo_lcms_group = new CourseGroup();
        $chamilo_lcms_group->set_course_code($new_course_code);
        $chamilo_lcms_group->set_name($this->get_title());
        $chamilo_lcms_group->set_max_number_of_members($this->get_max_student());
        
        if ($this->get_description())
        {
            $chamilo_lcms_group->set_description($this->get_description());
        }
        else
        {
            $chamilo_lcms_group->set_description($this->get_title());
        }
        
        $chamilo_lcms_group->set_self_registration_allowed($this->get_self_reg_allowed());
        $chamilo_lcms_group->set_self_unregistration_allowed($this->get_self_unreg_allowed());
        $chamilo_lcms_group->create();
        
        $this->create_id_reference($this->get_id(), $chamilo_lcms_group->get_id());
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'group_category', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $chamilo_lcms_group->get_id())));
        
        return $chamilo_lcms_group;
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