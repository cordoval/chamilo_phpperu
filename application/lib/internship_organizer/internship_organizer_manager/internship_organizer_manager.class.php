<?php
/**
 * @package application.lib.internship_organizer.internship_organizer_manager
 */

require_once dirname(__FILE__) . '/../internship_organizer_data_manager.class.php';
require_once dirname(__FILE__) . '/../internship_organizer_utilities.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/organisation_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/category_manager/category_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/agreement_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/region_manager/region_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/period_manager/period_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/appointment_manager/appointment_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/trackers/internship_organizer_changes_tracker.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/evaluation_manager/evaluation_manager.class.php';


require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_rights.class.php';

class InternshipOrganizerManager extends WebApplication
{
    const APPLICATION_NAME = 'internship_organizer';
    
    const PARAM_COMPONENT_ID = 'component';
    
    const ACTION_ORGANISATION = 'organisation';
    const ACTION_AGREEMENT = 'agreement';
    const ACTION_CATEGORY = 'category';
    const ACTION_APPLICATION_CHOOSER = 'application_chooser';
    const ACTION_REGION = 'region';
    const ACTION_PERIOD = 'period';
    const ACTION_APPOINTMENT = 'appointment';
    const ACTION_ADMINISTRATION = 'rights_editor';
    const ACTION_EVALUATION = 'evaluation';
    
    const DEFAULT_ACTION = self :: ACTION_APPLICATION_CHOOSER;

    /**
     * Constructor
     * @param User $user The current user
     */
    function InternshipOrganizerManager($user = null)
    {
        parent :: __construct($user);
    }

    function get_organisation_application_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ORGANISATION));
    
    }

    function get_agreement_application_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_AGREEMENT));
    
    }

    function get_category_application_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CATEGORY));
    
    }

    function get_application_chooser_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_APPLICATION_CHOOSER));
    
    }

    function get_region_application_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REGION));
    
    }

    function get_period_application_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_PERIOD));
    
    }

    function get_appointment_application_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_APPOINTMENT));
    
    }

    function get_administration_url($component_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ADMINISTRATION, self :: PARAM_COMPONENT_ID => $component_id));
    
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    //publications: interaction with the repository
    

    static function content_object_is_published($object_id)
    {
        return InternshipOrganizerDataManager :: get_instance()->content_object_is_published($object_id);
    }

    static function any_content_object_is_published($object_ids)
    {
        return InternshipOrganizerDataManager :: get_instance()->any_content_object_is_published($object_ids);
    }

    static function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->get_content_object_publication_attributes($object_id, $type, $offset, $count, $order_property);
    }

    static function get_content_object_publication_attribute($publication_id)
    {
        return InternshipOrganizerDataManager :: get_instance()->get_content_object_publication_attribute($publication_id);
    }

    static function count_publication_attributes($type = null, $condition = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->count_publication_attributes($type, $condition);
    }

    static function delete_content_object_publications($object_id)
    {
        return InternshipOrganizerDataManager :: get_instance()->delete_content_object_publications($object_id);
    }

    static function delete_content_object_publication($publication_id)
    {
        return InternshipOrganizerDataManager :: get_instance()->delete_content_object_publication($publication_id);
    }

    static function update_content_object_publication_id($publication_attr)
    {
        return InternshipOrganizerDataManager :: get_instance()->update_content_object_publication_id($publication_attr);
    }

    static function add_publication_attributes_elements($form)
    {
    
    }

    static function get_content_object_publication_locations($content_object)
    {
        $allowed_types = array();
        
        $type = $content_object->get_type();
        if (in_array($type, $allowed_types))
        {
            
            $locations = array();
            
            return $locations;
        }
        
        return array();
    }

    static function publish_content_object($content_object, $location, $attributes)
    {
    
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

}
?>