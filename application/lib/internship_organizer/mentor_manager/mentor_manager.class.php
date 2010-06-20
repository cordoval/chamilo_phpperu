<?php
require_once Path :: get_application_path() . 'lib/internship_organizer/mentor_manager/component/browser/browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/mentor.class.php';

class InternshipOrganizerMentorManager extends SubManager
{
    const PARAM_ACTION = 'action';
    const PARAM_MENTOR_ID = 'mentor_id';
    const PARAM_DELETE_SELECTED_MENTORS = 'delete_mentors';
    
    const ACTION_CREATE_MENTOR = 'create';
    const ACTION_BROWSE_MENTOR = 'browse';
    const ACTION_UPDATE_MENTOR = 'update';
    const ACTION_DELETE_MENTOR = 'delete';
    const ACTION_VIEW_MENTOR = 'view';

    function InternshipOrganizerMentorManager($internship_manager)
    {
        parent :: __construct($internship_manager);
        $action = Request :: get(self :: PARAM_ACTION);
        if ($action)
        {
            $this->set_parameter(self :: PARAM_ACTION, $action);
        }
        $this->parse_input_from_table();
    
    }

    function run()
    {
        $action = $this->get_parameter(self :: PARAM_ACTION);
        
        switch ($action)
        {
            
            case self :: ACTION_UPDATE_MENTOR :
                $component = $this->create_component('Updater');
                break;
            case self :: ACTION_DELETE_MENTOR :
                $component = $this->create_component('Deleter');
                break;
            case self :: ACTION_CREATE_MENTOR :
                $component = $this->create_component('Creator');
                break;
            case self :: ACTION_VIEW_MENTOR :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_BROWSE_MENTOR :
                $component = $this->create_component('Browser');
                break;
            default :
                $component = $this->create_component('Browser');
                break;
        }
        
        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'lib/internship_organizer/mentor_manager/component/';
    }

    //mentors
    

    function count_mentors($condition)
    {
        return InternshipOrganizerDataManager :: get_instance()->count_mentors($condition);
    }

    function retrieve_mentors($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_mentors($condition, $offset, $count, $order_property);
    }

    function retrieve_mentor($id)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_mentor($id);
    }

    //url creation
    function get_create_mentor_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_MENTOR));
    }

    function get_update_mentor_url($mentor)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_MENTOR, self :: PARAM_MENTOR_ID => $mentor->get_id()));
    }

    function get_delete_mentor_url($mentor)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_MENTOR, self :: PARAM_MENTOR_ID => $mentor->get_id()));
    }

    function get_browse_mentors_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_MENTOR));
    }

    function get_view_mentor_url($mentor)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_MENTOR, self :: PARAM_MENTOR_ID => $mentor->get_id()));
    }

    private function parse_input_from_table()
    {
        
        if (isset($_POST['action']))
        {
            
            if (isset($_POST[InternshipOrganizerMentorBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX]))
            {
                $selected_ids = $_POST[InternshipOrganizerMentorBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
            }
            
            if (empty($selected_ids))
            {
                $selected_ids = array();
            }
            elseif (! is_array($selected_ids))
            {
                $selected_ids = array($selected_ids);
            }
            switch ($_POST['action'])
            {
                case self :: PARAM_DELETE_SELECTED_MENTORS :
                    $this->set_mentor_action(self :: ACTION_DELETE_MENTOR);
                    $_GET[self :: PARAM_MENTOR_ID] = $selected_ids;
                    break;
            }
        }
    }

    private function set_mentor_action($action)
    {
        $this->set_parameter(self :: PARAM_ACTION, $action);
    }
}

?>