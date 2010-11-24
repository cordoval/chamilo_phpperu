<?php
namespace application\weblcms;

use common\libraries\Filesystem;
use common\libraries\EqualityCondition;
use common\libraries\DataManagerInterface;
use common\libraries\Configuration;
use DOMDocument;

/**
 * $Id: weblcms_data_manager.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms
 */
/**
==============================================================================
 * This is a skeleton for a data manager for the Weblcms application. Data
 * managers must extend this class.
 *
 * @author Tim De Pauw
==============================================================================
 */

class WeblcmsDataManager implements DataManagerInterface
{
    /**
     * Instance of the class, for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor. Initializes the data manager.
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Creates the shared instance of the configured data manager if
     * necessary and returns it. Uses a factory pattern.
     * @return WeblcmsDataManagerInterface The instance.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_weblcms_data_manager.class.php';
            $class = __NAMESPACE__ . '\\' . $type . 'WeblcmsDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    /*
	 * Gets the tool of a section
	 */
    static function get_tools($requested_section = 'all')
    {
        $course_modules = Array();

        $options = array('forceEnum' => array('properties'));

        $dir = dirname(__FILE__) . '/../../tool/';
        $tools = Filesystem :: get_directory_content($dir, Filesystem :: LIST_DIRECTORIES, false);
        foreach ($tools as $tool)
        {
            $properties_file = $dir . $tool . '/php/properties.xml';
            if (! file_exists($properties_file))
            {
                continue;
            }

            $doc = new DOMDocument();

            $doc->load($properties_file);
            $xml_properties = $doc->getElementsByTagname('property');
            $properties = array();

            foreach ($xml_properties as $index => $property)
            {
                if ($property->getAttribute('name') == 'section')
                {
                    $section = $property->getAttribute('value');
                    break;
                }
            }

            if ($section == $requested_section || $requested_section == 'all')
            {
                $course_modules[] = $tool;
            }
        }

        return $course_modules;
    }

    /**
     * Checks whether subscription to a specific course is allowed.
     * @param Course $course
     * @param User $user
     * @return boolean
     */
    static function course_subscription_allowed($course, $user)
    {
        $already_subscribed = self :: get_instance()->is_subscribed($course, $user);

        $subscription_allowed = ($course->get_access() == 1 ? true : false);

        if (! $already_subscribed && $subscription_allowed)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Checks whether unsubscription from a specific course is allowed.
     * @param Course $course
     * @param User $user
     * @return boolean
     */
    static function course_unsubscription_allowed($course, $user)
    {
        if ($course->is_course_admin($user))
        {
            return false;
        }

        $already_subscribed = self :: get_instance()->is_subscribed($course, $user);
        $unsubscription_allowed = ($course->get_unsubscribe_allowed() == 1 ? true : false);
        if ($already_subscribed && $unsubscription_allowed)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    static function get_user_course_groups($user, $course = null)
    {
        $course_groups = self :: get_instance()->retrieve_course_groups_from_user($user, $course)->as_array();

        $course_groups_recursive = array();

        foreach ($course_groups as $course_group)
        {
            if (! array_key_exists($course_group->get_id(), $course_groups_recursive))
            {
                $course_groups_recursive[$course_group->get_id()] = $course_group;
            }

            $parents = $course_group->get_parents(false);

            while ($parent = $parents->next_result())
            {
                if (! array_key_exists($parent->get_id(), $course_groups_recursive))
                {
                    $course_groups_recursive[$parent->get_id()] = $parent;
                }
            }
        }

        return $course_groups_recursive;
    }

    static function is_course_code_available($code)
    {
        $condition = new EqualityCondition(Course :: PROPERTY_VISUAL, $code);
        return (self :: get_instance()->count_courses($condition) == 0);
    }

    private static $new_publications;

    /**
     * Determines if a tool has new publications  since the last time the
     * current user visited the tool.
     * @param string $tool
     * @param Course $course
     */
    static function tool_has_new_publications($tool, User $user, Course $course = null)
    {
        if (! $course || $course->get_id() == 0)
        {
            return false;
        }

        if (is_null(self :: $new_publications[$course->get_id()]))
        {
            self :: $new_publications[$course->get_id()] = self :: get_instance()->count_new_publications_from_course($course, $user);
        }

        if (self :: $new_publications[$course->get_id()][$tool] && self :: $new_publications[$course->get_id()][$tool] > 0)
        {
            return true;
        }

        return false;
    }

}
?>