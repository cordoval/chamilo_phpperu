<?php
require_once Path :: get_application_path() . 'lib/internship_planner/agreement_manager/component/browser/browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/agreement.class.php';

class InternshipPlannerAgreementManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    const PARAM_AGREEMENT_ID = 'agreement_id';
    const PARAM_DELETE_SELECTED_AGREEMENTS = 'delete_agreements';
    
    const PARAM_MOMENT_ID = 'moment_id';
    const PARAM_DELETE_SELECTED_MOMENTS = 'delete_moments';
    
    const ACTION_CREATE_AGREEMENT = 'create';
    const ACTION_BROWSE_AGREEMENT = 'browse';
    const ACTION_UPDATE_AGREEMENT = 'update';
    const ACTION_DELETE_AGREEMENT = 'delete';
    const ACTION_VIEW_AGREEMENT = 'view';
    
    const ACTION_CREATE_MOMENT = 'create_moment';
    const ACTION_BROWSE_MOMENTS = 'browse_moments';
    const ACTION_EDIT_MOMENT = 'edit_moment';
    const ACTION_DELETE_MOMENT = 'delete_moment';

    function InternshipPlannerAgreementManager($internship_manager)
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
            
            case self :: ACTION_UPDATE_AGREEMENT :
                $component = InternshipPlannerAgreementManagerComponent :: factory('Updater', $this);
                break;
            case self :: ACTION_DELETE_AGREEMENT :
                $component = InternshipPlannerAgreementManagerComponent :: factory('Deleter', $this);
                break;
            case self :: ACTION_CREATE_AGREEMENT :
                $component = InternshipPlannerAgreementManagerComponent :: factory('Creator', $this);
                break;
            case self :: ACTION_VIEW_AGREEMENT :
                $component = InternshipPlannerAgreementManagerComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_BROWSE_AGREEMENT :
                $component = InternshipPlannerAgreementManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_EDIT_MOMENT :
                $component = InternshipPlannerAgreementManagerComponent :: factory('MomentUpdater', $this);
                break;
            case self :: ACTION_DELETE_MOMENT :
                $component = InternshipPlannerAgreementManagerComponent :: factory('MomentDeleter', $this);
                break;
            case self :: ACTION_CREATE_MOMENT :
               	$component = InternshipPlannerAgreementManagerComponent :: factory('MomentCreator', $this);
                break;
            case self :: ACTION_BROWSE_MOMENTS :
                $component = InternshipPlannerAgreementManagerComponent :: factory('MomentBrowser', $this);
                break;
            default :
                $component = InternshipPlannerAgreementManagerComponent :: factory('Browser', $this);
                break;
        }
        
        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'lib/internship_planner/agreement_manager/component/';
    }

    //agreements
    

    function count_agreements($condition)
    {
        return InternshipPlannerDataManager :: get_instance()->count_agreements($condition);
    }

    function retrieve_agreements($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipPlannerDataManager :: get_instance()->retrieve_agreements($condition, $offset, $count, $order_property);
    }

    function retrieve_agreement($id)
    {
        return InternshipPlannerDataManager :: get_instance()->retrieve_agreement($id);
    }

    //moments
    function count_moments($condition)
    {
        return InternshipPlannerDataManager :: get_instance()->count_moments($condition);
    }

    function retrieve_moments($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipPlannerDataManager :: get_instance()->retrieve_moments($condition, $offset, $count, $order_property);
    }

    function retrieve_moment($id)
    {
        return InternshipPlannerDataManager :: get_instance()->retrieve_moment($id);
    }

    //url creation
    function get_create_agreement_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_AGREEMENT));
    }

    function get_update_agreement_url($agreement)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement->get_id()));
    }

    function get_delete_agreement_url($agreement)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement->get_id()));
    }

    function get_browse_agreements_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_AGREEMENT));
    }

    function get_view_agreement_url($agreement)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement->get_id()));
    }

    function get_create_moment_url($agreement)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_MOMENT, self :: PARAM_AGREEMENT_ID => $agreement->get_id()));
    }

    function get_update_moment_url($moment)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_MOMENT, self :: PARAM_MOMENT_ID => $moment->get_id()));
    }

    function get_delete_moment_url($moment)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_MOMENT, self :: PARAM_MOMENT_ID => $moment->get_id()));
    }

    function get_browse_moments_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_MOMENTS));
    }

    private function parse_input_from_table()
    {
        
        if (isset($_POST['action']))
        {
            
            if (isset($_POST[InternshipPlannerAgreementBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX]))
            {
                $selected_ids = $_POST[InternshipPlannerAgreementBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
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
                //                case self :: PARAM_UNSUBSCRIBE_SELECTED :
                //                    $this->set_agreement_action(self :: ACTION_UNSUBSCRIBE_MOMENT_FROM_GROUP);
                //                    $_GET[self :: PARAM_GROUP_REL_STUDENT_ID] = $selected_ids;
                //                    break;
                //                case self :: PARAM_SUBSCRIBE_SELECTED :
                //                    $this->set_group_action(self :: ACTION_SUBSCRIBE_MOMENT_TO_GROUP);
                //                    $_GET[StsManager :: PARAM_MOMENT_ID] = $selected_ids;
                //                    break;
                case self :: PARAM_DELETE_SELECTED_AGREEMENTS :
                    $this->set_agreement_action(self :: ACTION_DELETE_AGREEMENT);
                    $_GET[self :: PARAM_AGREEMENT_ID] = $selected_ids;
                    break;
                //                case self :: PARAM_TRUNCATE_SELECTED :
            //                    $this->set_group_action(self :: ACTION_TRUNCATE_GROUP);
            //                    $_GET[self :: PARAM_GROUP_ID] = $selected_ids;
            //                    break;
            

            }
        }
    }

    private function set_agreement_action($action)
    {
        $this->set_parameter(self :: PARAM_ACTION, $action);
    }
}

?>