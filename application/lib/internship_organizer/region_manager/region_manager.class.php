<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/region_manager/component/browser/browser_table.class.php';

require_once dirname(__FILE__) . '/../region_menu.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/region.class.php';

class InternshipOrganizerRegionManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    
    const PARAM_REGION_ID = 'region_id';
    const PARAM_REMOVE_SELECTED = 'delete';
    
    const ACTION_CREATE_REGION = 'creator';
    const ACTION_BROWSE_REGIONS = 'browser';
    const ACTION_EDIT_REGION = 'editor';
    const ACTION_DELETE_REGION = 'deleter';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE_REGIONS;

    function InternshipOrganizerRegionManager($internship_manager)
    {
        parent :: __construct($internship_manager);
    }

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'lib/internship_organizer/region_manager/component/';
    }

    //regions
    function retrieve_regions($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_regions($condition, $offset, $count, $order_property);
    }

    function retrieve_region($id)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_internship_organizer_region($id);
    }

    function retrieve_root_region()
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_root_region();
    }

    function count_regions($conditions = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->count_regions($conditions);
    }

    //url
    

    function get_browse_regions_url($region)
    {
        if ($region)
        {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_REGIONS, self :: PARAM_REGION_ID => $region->get_id()));
        }
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_REGIONS));
    }

    function get_region_editing_url($region)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_REGION, self :: PARAM_REGION_ID => $region->get_id()));
    }

    function get_region_create_url($parent_id = null)
    {
        if ($parent_id != null)
        {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_REGION, self :: PARAM_REGION_ID => $parent_id));
        }
        else
        {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_REGION));
        }
    }

    function get_region_delete_url($region)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_REGION, self :: PARAM_REGION_ID => $region->get_id()));
    }

    private function set_region_action($action)
    {
        $this->set_parameter(self :: PARAM_ACTION, $action);
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_ACTION;
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        parent :: launch(__CLASS__, $application);
    }
}

?>