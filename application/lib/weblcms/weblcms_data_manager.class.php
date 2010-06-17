<?php
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
    protected function WeblcmsDataManager()
    {
        $this->initialize();
    }

    /**
     * Creates the shared instance of the configured data manager if
     * necessary and returns it. Uses a factory pattern.
     * @return WeblcmsDataManager The instance.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_weblcms_data_manager.class.php';
            $class = $type . 'WeblcmsDataManager';
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
        
        $dir = dirname(__FILE__) . '/tool/';
        $tools = FileSystem :: get_directory_content($dir, FileSystem :: LIST_DIRECTORIES, false);
        foreach($tools as $tool)
        {
        	$properties_file = $dir . $tool . '/properties.xml';
        	if(!file_exists($properties_file))
        	{
        		continue;
        	}
        	
        	$doc = new DOMDocument();

	        $doc->load($properties_file);
	        $xml_properties = $doc->getElementsByTagname('property');
	        $properties = array();
	
	        foreach ($xml_properties as $index => $property)
	        {
	            if($property->getAttribute('name') == 'section')
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
     * @param int $user_id
     * @return boolean
     */
    static function course_subscription_allowed($course, $user_id)
    {
        $already_subscribed = self :: get_instance()->is_subscribed($course, $user_id);

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
     * @param int $user_id
     * @return boolean
     */
    static function course_unsubscription_allowed($course, $user)
    {
        if ($course->is_course_admin($user))
        {
            return false;
        }

        $already_subscribed = self :: get_instance()->is_subscribed($course, $user->get_id());
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

            foreach ($parents as $parent)
            {
                if (! array_key_exists($parent->get_id(), $course_groups_recursive))
                {
                    $course_groups_recursive[$parent->get_id()] = $parent;
                }
            }
        }

        return $course_groups_recursive;
    }

}
?>