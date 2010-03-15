<?php
/**
 * $Id: web_application.class.php 237 2009-11-16 13:04:53Z vanpouckesven $
 * @package application
 */

abstract class WebApplication extends Application
{

    /**
     * Determines whether the given learning object has been published in this
     * application.
     * @param int $object_id The ID of the learning object.
     * @return boolean True if the object is currently published, false
     *                 otherwise.
     */
    function content_object_is_published($object_id)
    {
        return false;
    }

    /**
     * Determines whether any of the given learning objects has been published
     * in this application.
     * @param array $object_ids The Id's of the learning objects
     * @return boolean True if at least one of the given objects is published in
     * this application, false otherwise
     */
    function any_content_object_is_published($object_ids)
    {
        return false;
    }

    /**
     * Determines where in this application the given learning object has been
     * published.
     * @param int $object_id The ID of the learning object.
     * @return array An array of ContentObjectPublicationAttributes objects;
     *               empty if the object has not been published anywhere.
     */
    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return array();
    }

    /**
     * Determines where in this application the given learning object
     * publication is published.
     * @param int $publication_id The ID of the learning object publication.
     * @return ContentObjectPublicationAttributes
     */
    function get_content_object_publication_attribute($publication_id)
    {
        return null;
    }

    /**
     * Counts the number of publications
     * @param string $type
     * @param Condition $condition
     * @return int
     */
    function count_publication_attributes($user = null, $object_id, $condition = null)
    {
        return 0;
    }

    /**
     * Deletes all publications of a given learning object
     * @param int $object_id The id of the learning object
     */
    function delete_content_object_publications($object_id)
    {
        return true;
    }
    
    function delete_content_object_publication($publication_id)
    {
    	return true;
    }

    function get_content_object_publication_locations($content_object)
    {
        return array();
    }

    function publish_content_object($content_object, $location, $attributes)
    {
        return null;
    }

    function add_publication_attributes_elements($form)
    {
    }

    /**
     *
     */
    function update_content_object_publication_id($publication_attr)
    {
        return true;
    }

    /**
     * Loads the applications installed on the system. Applications are classes
     * in the /application/lib subdirectory. Each application is a directory,
     * which in its turn contains a class file named after the application. For
     * instance, the weblcms application is the class Weblcms, defined in
     * /application/lib/weblcms/weblcms.class.php. Applications must extend the
     * Application class.
     */
    public static function load_all_from_filesystem($include_application_classes = true, $only_registered_applications = false)
    {
        $applications = array();
        $path = dirname(__FILE__) . '/../lib/';
        $directories = Filesystem :: get_directory_content($path, Filesystem :: LIST_DIRECTORIES, false);
        
        if ($only_registered_applications)
        {
        	$adm = AdminDataManager :: get_instance();
        }
        
        foreach ($directories as $directory)
        {
            $application_name = basename($directory);
            
            if($only_registered_applications && !$adm->is_registered($application_name))
            {
            	continue;
            }
            
            if (Application :: is_application_name($application_name))
            {
                if (! in_array($application_name, $applications))
                {
                    if ($include_application_classes)
                    {
                        require_once ($path . $directory . '/' . $application_name . '_manager/' . $application_name . '_manager.class.php');
                    }
                    $applications[] = $application_name;
                }
            }
        }
        return $applications;
    }

    public static function load_all($include_application_classes = true)
    {
        $path = Path :: get_application_path() . 'lib';
        $adm = AdminDataManager :: get_instance();
        $condition = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_APPLICATION);
        
        $applications = $adm->retrieve_registrations($condition);
        $active_applications = array();
        
        while ($application = $applications->next_result())
        {
            if ($include_application_classes)
            {
                require_once $path . '/' . $application->get_name() . '/' . $application->get_name() . '_manager/' . $application->get_name() . '_manager.class.php';
            }
            $active_applications[] = $application->get_name();
        }
        
        return $active_applications;
    }

    /**
     * Determines if a given application exists
     * @param string $name
     * @return boolean
     */
    public static function is_application($name)
    {
        $path = dirname(__FILE__) . '/../lib/';
        $application_path = $path . '/' . $name;
        $application_manager_path = $path . '/' . $name . '/' . $name . '_manager' . '/' . $name . '_manager.class.php';
        if (file_exists($application_path) && is_dir($application_path) && file_exists($application_manager_path))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function is_active($application)
    {
        if (self :: is_application($application))
        {
            $adm = AdminDataManager :: get_instance();
            
            $conditions = array();
            $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, 'application');
            $conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, $application);
            $condition = new AndCondition($conditions);
            
            $registrations = $adm->retrieve_registrations($condition);
            if ($registrations->size() > 0)
            {
                $registration = $registrations->next_result();
                if ($registration->is_active())
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    public function get_application_path($application_name)
    {
        return Path :: get_application_path() . 'lib/' . $application_name . '/';
    }

    public function get_application_component_path()
    {
        $application_name = $this->get_application_name();
        return $this->get_application_path($application_name) . $application_name . '_manager/component/';
    }

    function factory($application, $user = null)
    {
        $manager_path = self :: get_application_path($application) . $application . '_manager/' . $application . '_manager.class.php';
        require_once $manager_path;
        return parent :: factory($application, $user);
    }
    
    function get_additional_user_information($user)
    {
    	return null;
    }
}
?>