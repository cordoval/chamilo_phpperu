<?php
/**
 * $Id: course.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/course_group/course_group.class.php';

/**
 *	This class represents a course in the weblcms.
 *
 *	courses have a number of default properties:
 *	- id: the numeric ID of the course object;
 *	- visual: the visual code of the course;
 *	- name: the name of the course object;
 *	- path: the course's path;
 *	- titular: the titular of this course object;
 *  - language: the language of the course object;
 *	- extlink url: the URL department;
 *	- extlink name: the name of the department;
 *	- category code: the category code of the object;
 *	- category name: the name of the category;
 *
 * To access the values of the properties, this class and its subclasses
 * should provide accessor methods. The names of the properties should be
 * defined as class constants, for standardization purposes. It is recommended
 * that the names of these constants start with the string "PROPERTY_".
 *
 */

class Course extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_VISUAL = 'visual_code';
    const PROPERTY_NAME = 'title';
    const PROPERTY_TITULAR = 'titular_id';
    const PROPERTY_LANGUAGE = 'course_language';
    const PROPERTY_EXTLINK_URL = 'department_url';
    const PROPERTY_EXTLINK_NAME = 'department_name';
    const PROPERTY_CATEGORY = 'category_id';
    const PROPERTY_VISIBILITY = 'visibility';
    const PROPERTY_SUBSCRIBE_ALLOWED = 'subscribe';
    const PROPERTY_UNSUBSCRIBE_ALLOWED = 'unsubscribe';
    const PROPERTY_THEME = 'theme';
    const PROPERTY_LAYOUT = 'layout';
    const PROPERTY_TOOL_SHORTCUT = 'tool_shortcut';
    const PROPERTY_MENU = 'menu';
    const PROPERTY_BREADCRUMB = 'breadcrumb';
    const PROPERTY_ALLOW_FEEDBACK = 'allow_feedback';
    const PROPERTY_SHOW_SCORE = 'show_score';
    const PROPERTY_DISK_QUOTA = 'disk_quota';

    // Remnants from the old Chamilo system
    const PROPERTY_LAST_VISIT = 'last_visit';
    const PROPERTY_LAST_EDIT = 'last_edit';
    const PROPERTY_CREATION_DATE = 'creation_date';
    const PROPERTY_EXPIRATION_DATE = 'expiration_date';

    const LAYOUT_2_COLUMNS = 1;
    const LAYOUT_3_COLUMNS = 2;
    const LAYOUT_2_COLUMNS_GROUP_INACTIVE = 3;
    const LAYOUT_3_COLUMNS_GROUP_INACTIVE = 4;

    const TOOL_SHORTCUT_OFF = 1;
    const TOOL_SHORTCUT_ON = 2;

    const MENU_OFF = 1;
    const MENU_LEFT_ICON = 2;
    const MENU_LEFT_ICON_TEXT = 3;
    const MENU_LEFT_TEXT = 4;
    const MENU_RIGHT_ICON = 5;
    const MENU_RIGHT_ICON_TEXT = 6;
    const MENU_RIGHT_TEXT = 7;

    const BREADCRUMB_TITLE = 1;
    const BREADCRUMB_CODE = 2;
    const BREADCRUMB_COURSE_HOME = 3;

    static function get_layouts()
    {
        return array(self :: LAYOUT_2_COLUMNS => Translation :: get('TwoColumns'), self :: LAYOUT_3_COLUMNS => Translation :: get('ThreeColumns'), self :: LAYOUT_2_COLUMNS_GROUP_INACTIVE => Translation :: get('TwoColumnsGroupInactive'), self :: LAYOUT_3_COLUMNS_GROUP_INACTIVE => Translation :: get('ThreeColumnsGroupInactive'));
    }

    static function get_tool_shortcut_options()
    {
        return array(self :: TOOL_SHORTCUT_OFF => Translation :: get('Off'), self :: TOOL_SHORTCUT_ON => Translation :: get('On'));
    }

    static function get_menu_options()
    {
        return array(self :: MENU_OFF => Translation :: get('Off'), self :: MENU_LEFT_ICON => Translation :: get('LeftIcon'), self :: MENU_LEFT_ICON_TEXT => Translation :: get('LeftIconText'), self :: MENU_LEFT_TEXT => Translation :: get('LeftText'), self :: MENU_RIGHT_ICON => Translation :: get('RightIcon'), self :: MENU_RIGHT_ICON_TEXT => Translation :: get('RightIconText'), self :: MENU_RIGHT_TEXT => Translation :: get('RightText'));
    }

    static function get_breadcrumb_options()
    {
        return array(self :: BREADCRUMB_TITLE => Translation :: get('Title'), self :: BREADCRUMB_CODE => Translation :: get('Code'), self :: BREADCRUMB_COURSE_HOME => Translation :: get('CourseHome'));
    }

    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_LAYOUT, self :: PROPERTY_VISUAL, self :: PROPERTY_CATEGORY, self :: PROPERTY_NAME, self :: PROPERTY_SHOW_SCORE, self :: PROPERTY_TITULAR, self :: PROPERTY_LANGUAGE, self :: PROPERTY_EXTLINK_URL, self :: PROPERTY_EXTLINK_NAME, self :: PROPERTY_VISIBILITY, self :: PROPERTY_SUBSCRIBE_ALLOWED, self :: PROPERTY_UNSUBSCRIBE_ALLOWED, self :: PROPERTY_THEME, self :: PROPERTY_TOOL_SHORTCUT, self :: PROPERTY_MENU, self :: PROPERTY_BREADCRUMB, self :: PROPERTY_ALLOW_FEEDBACK, self :: PROPERTY_DISK_QUOTA, self :: PROPERTY_CREATION_DATE, self :: PROPERTY_EXPIRATION_DATE, self :: PROPERTY_LAST_EDIT, self :: PROPERTY_LAST_VISIT));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    /**
     * Returns the visual code of this course object
     * @return string the visual code
     */
    function get_visual()
    {
        return $this->get_default_property(self :: PROPERTY_VISUAL);
    }

    /**
     * Returns the category code of this course object
     * @return string the category code
     */
    function get_category()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

    /**
     * Returns the name (Title) of this course object
     * @return string The Name
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the titular of this course object
     * @return String The Titular
     */
    function get_titular()
    {
        return $this->get_default_property(self :: PROPERTY_TITULAR);
    }

    /**
     * Returns the titular as a string
     */
    function get_titular_string()
    {
        $titular_id = $this->get_titular();

        if (! is_null($titular_id))
        {
            $udm = UserDataManager :: get_instance();
            $user = $udm->retrieve_user($titular_id);
            return $user->get_lastname() . ' ' . $user->get_firstname();
        }
        else
        {
            return null;
        }
    }

    /**
     * Returns the language of this course object
     * @return String The Language
     */
    function get_language()
    {
        return $this->get_default_property(self :: PROPERTY_LANGUAGE);
    }

    /**
     * Returns the ext url of this course object
     * @return String The URL
     */
    function get_extlink_url()
    {
        return $this->get_default_property(self :: PROPERTY_EXTLINK_URL);
    }

    /**
     * Returns the ext link name of this course object
     * @return String The Name
     */
    function get_extlink_name()
    {
        return $this->get_default_property(self :: PROPERTY_EXTLINK_NAME);
    }

    /**
     * Returns the visibility code of this course object
     * @return String The Visibility Code
     */
    function get_visibility()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBILITY);
    }

    /**
     * Returns if you can subscribe to this course object
     * @return Int
     */
    function get_subscribe_allowed()
    {
        return $this->get_default_property(self :: PROPERTY_SUBSCRIBE_ALLOWED);
    }

    /**
     * Returns if you can unsubscribe to this course object
     * @return Int
     */
    function get_unsubscribe_allowed()
    {
        return $this->get_default_property(self :: PROPERTY_UNSUBSCRIBE_ALLOWED);
    }

    /**
     * Returns the course theme
     * @return string The theme
     */
    function get_theme()
    {
        return $this->get_default_property(self :: PROPERTY_THEME);
    }

    function get_creation_date()
    {
        return $this->get_default_property(self :: PROPERTY_CREATION_DATE);
    }

    function get_expiration_date()
    {
        return $this->get_default_property(self :: PROPERTY_EXPIRATION_DATE);
    }

    function get_last_edit()
    {
        return $this->get_default_property(self :: PROPERTY_LAST_EDIT);
    }

    function get_last_visit()
    {
        return $this->get_default_property(self :: PROPERTY_LAST_VISIT);
    }

    /**
     * Sets the visual code of this course object
     * @param String $visual The visual code
     */
    function set_visual($visual)
    {
        $this->set_default_property(self :: PROPERTY_VISUAL, $visual);
    }

    /**
     * Sets the category code of this course object
     * @param String $visual The category code
     */
    function set_category($category)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    }

    /**
     * Sets the course name of this course object
     * @param String $name The name of this course object
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Sets the titular of this course object
     * @param String $titular The titular of this course object
     */
    function set_titular($titular)
    {
        $this->set_default_property(self :: PROPERTY_TITULAR, $titular);
    }

    /**
     * Sets the language of this course object
     * @param String $language The language of this course object
     */
    function set_language($language)
    {
        $this->set_default_property(self :: PROPERTY_LANGUAGE, $language);
    }

    /**
     * Sets the extlink URL of this course object
     * @param String $url The URL if the extlink
     */
    function set_extlink_url($url)
    {
        $this->set_default_property(self :: PROPERTY_EXTLINK_URL, $url);
    }

    /**
     * Sets the extlink Name of this course object
     * @param String $name The name of the exlink
     */
    function set_extlink_name($name)
    {
        $this->set_default_property(self :: PROPERTY_EXTLINK_NAME, $name);
    }

    /**
     * Sets the visibility of this course object
     * @param String $visual The visual code
     */
    function set_visibility($visibility)
    {
        $this->set_default_property(self :: PROPERTY_VISIBILITY, $visibility);
    }

    /**
     * Sets if a user is allowed to subscribe on this course object
     * @param Int $subscribe
     */
    function set_subscribe_allowed($subscribe)
    {
        $this->set_default_property(self :: PROPERTY_SUBSCRIBE_ALLOWED, $subscribe);
    }

    /**
     * Sets if a user is allowed to unsubscribe on this course object
     * @param Int $subscribe
     */
    function set_unsubscribe_allowed($subscribe)
    {
        $this->set_default_property(self :: PROPERTY_UNSUBSCRIBE_ALLOWED, $subscribe);
    }

    function set_layout($layout)
    {
        $this->set_default_property(self :: PROPERTY_LAYOUT, $layout);
    }

    function get_layout()
    {
        return $this->get_default_property(self :: PROPERTY_LAYOUT);
    }

    function set_menu($menu)
    {
        $this->set_default_property(self :: PROPERTY_MENU, $menu);
    }

    function get_menu()
    {
        return $this->get_default_property(self :: PROPERTY_MENU);
    }

    function set_tool_shortcut($tool_shortcut)
    {
        $this->set_default_property(self :: PROPERTY_TOOL_SHORTCUT, $tool_shortcut);
    }

    function get_tool_shortcut()
    {
        return $this->get_default_property(self :: PROPERTY_TOOL_SHORTCUT);
    }

    function set_breadcrumb($breadcrumb)
    {
        $this->set_default_property(self :: PROPERTY_BREADCRUMB, $breadcrumb);
    }

    function get_breadcrumb()
    {
        return $this->get_default_property(self :: PROPERTY_BREADCRUMB);
    }

    function set_allow_feedback($allow_feedback)
    {
        $this->set_default_property(self :: PROPERTY_ALLOW_FEEDBACK, $allow_feedback);
    }

    function get_allow_feedback()
    {
        return $this->get_default_property(self :: PROPERTY_ALLOW_FEEDBACK);
    }

    function get_show_score()
    {
        return $this->get_default_property(self :: PROPERTY_SHOW_SCORE);
    }

    function set_show_score()
    {
        return $this->set_default_property(self :: PROPERTY_SHOW_SCORE);
    }

    /**
     * Sets the theme of this course object
     * @param String $theme The theme of this course object
     */
    function set_theme($theme)
    {
        $this->set_default_property(self :: PROPERTY_THEME, $theme);
    }

    function set_creation_date($creation_date)
    {
        $this->set_default_property(self :: PROPERTY_CREATION_DATE, $creation_date);
    }

    function set_expiration_date($expiration_date)
    {
        $this->set_default_property(self :: PROPERTY_EXPIRATION_DATE, $expiration_date);
    }

    function set_last_edit($last_edit)
    {
        $this->set_default_property(self :: PROPERTY_LAST_EDIT, $last_edit);
    }

    function set_last_visit($last_visit)
    {
        $this->set_default_property(self :: PROPERTY_LAST_VISIT, $last_visit);
    }

    /**
     * Creates the course object in persistent storage
     * @return boolean
     */
    function create()
    {
        $wdm = WeblcmsDataManager :: get_instance();

        if (! $wdm->create_course($this))
        {
            return false;
        }

        require_once (dirname(__FILE__) . '/../category_manager/content_object_publication_category.class.php');
        $dropbox = new ContentObjectPublicationCategory();
        $dropbox->create_dropbox($this->get_id());

        $location = new Location();
        $location->set_location($this->get_name());
        $location->set_application(WeblcmsManager :: APPLICATION_NAME);
        $location->set_type_from_object($this);
        $location->set_identifier($this->get_id());

        $parent = WeblcmsRights :: get_location_id_by_identifier('course_category', 1);
        //echo 'parent : ' . $parent;


        if ($parent)
        {
            $location->set_parent($parent);
        }
        else
        {
            $location->set_parent(0);
        }

        if (! $location->create())
        {
            return false;
        }

        if (! $this->initialize_course_sections())
        {
            return false;
        }

        return true;
    }

    function create_all()
    {
        $wdm = WeblcmsDataManager :: get_instance();
        return $wdm->create_course_all($this);
    }

    /**
     * Checks whether the given user is a course admin in this course
     * @param int $user_id
     * @return boolean
     */
    function is_course_admin($user)
    {
        if ($user->is_platform_admin())
        {
            return true;
        }
        $wdm = WeblcmsDataManager :: get_instance();
        return $wdm->is_course_admin($this, $user->get_id());
    }

    /**
     * Determines if this course has a theme
     * @return boolean
     */
    function has_theme()
    {
        return (! is_null($this->get_theme()) ? true : false);
    }

    /**
     * Gets the subscribed users of this course
     * @return array An array of CourseUserRelation objects
     */
    function get_subscribed_users()
    {
        $wdm = WeblcmsDataManager :: get_instance();

        $relation_conditions = array();
        $relation_conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $this->get_id());
        $relation_condition = new AndCondition($relation_conditions);

        return $wdm->retrieve_course_user_relations($relation_condition)->as_array();
    }

    /**
     * Gets the course_groups defined in this course
     * @return array An array of CourseGroup objects
     */
    function get_course_groups($as_array = true)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $condition = new EqualityCondition(CourseGroup :: PROPERTY_COURSE_CODE, $this->get_id());
        $result = $wdm->retrieve_course_groups($condition, null, null, array(new ObjectTableOrder(CourseGroup :: PROPERTY_NAME)));
        return ($as_array ? $result->as_array() : $result);
    }

    function is_layout_configurable()
    {
        $theme = PlatformSetting :: get('allow_course_theme_selection', WeblcmsManager :: APPLICATION_NAME);
        $layout = PlatformSetting :: get('allow_course_layout_selection', WeblcmsManager :: APPLICATION_NAME);
        $shortcut = PlatformSetting :: get('allow_course_tool_short_cut_selection', WeblcmsManager :: APPLICATION_NAME);
        $menu = PlatformSetting :: get('allow_course_menu_selection', WeblcmsManager :: APPLICATION_NAME);
        $breadcrumbs = PlatformSetting :: get('allow_course_breadcrumbs', WeblcmsManager :: APPLICATION_NAME);

        if (! $theme && ! $layout && ! $shortcut && ! $menu && ! $breadcrumbs)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function initialize_course_sections()
    {
        $sections = array();
        $sections[] = array('name' => Translation :: get('Tools'), 'type' => 1, 'order' => 1);
        $sections[] = array('name' => Translation :: get('Links'), 'type' => 2, 'order' => 2);
        $sections[] = array('name' => Translation :: get('Disabled'), 'type' => 0, 'order' => 3);
        $sections[] = array('name' => Translation :: get('CourseAdministration'), 'type' => 3, 'order' => 4);

        foreach ($sections as $section)
        {
            $course_section = new CourseSection();
            $course_section->set_course_code($this->get_id());
            $course_section->set_name($section['name']);
            $course_section->set_type($section['type']);
            $course_section->set_visible(true);
            if (! $course_section->create())
            {
                return false;
            }
        }

        return true;
    }

}
?>