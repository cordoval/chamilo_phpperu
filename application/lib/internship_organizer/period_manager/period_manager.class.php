<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/period_manager/component/browser/browser_table.class.php';

require_once dirname(__FILE__) . '/../period_menu.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/period.class.php';

class InternshipOrganizerPeriodManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    
    const PARAM_PERIOD_ID = 'period_id';
    const PARAM_REMOVE_SELECTED = 'delete';
    const PARAM_TRUNCATE_SELECTED = 'truncate';
    const PARAM_PERIOD_REL_USER_ID = 'period_rel_user_id';
    const PARAM_PERIOD_REL_GROUP_ID = 'period_rel_group_id';
    const PARAM_PERIOD_REL_CATEGORY_ID = 'period_rel_category_id';
    const PARAM_USER_ID = 'user_id';
    const PARAM_USER_TYPE = 'user_type';
    const PARAM_AGREEMENT_ID = 'agreement_id';
    
    const ACTION_CREATE_PERIOD = 'create';
    const ACTION_BROWSE_PERIODS = 'browse';
    const ACTION_EDIT_PERIOD = 'edit';
    const ACTION_DELETE_PERIOD = 'delete';
    const ACTION_VIEW_PERIOD = 'view';
    const ACTION_PUBLISH_PERIOD = 'publish';
    
    const ACTION_CREATE_AGREEMENT = 'create_agreement';
    const ACTION_DELETE_AGREEMENT = 'delete_agreement';
    const ACTION_UPDATE_AGREEMENT = 'update_agreement';
    const ACTION_VIEW_AGREEMENT = 'view_agreement';
    
    const ACTION_SUBSCRIBE_USER = 'subscribe_user';
    const ACTION_SUBSCRIBE_GROUP = 'subscribe_group';
    const ACTION_SUBSCRIBE_CATEGORY = 'subscribe_category';
    const ACTION_SUBSCRIBE_AGREEMENT_REL_USER = 'subscribe_agreement_rel_user';
    
    const ACTION_UNSUBSCRIBE_USER = 'unsubscribe_user';
    const ACTION_UNSUBSCRIBE_GROUP = 'unsubscribe_group';
    const ACTION_UNSUBSCRIBE_CATEGORY = 'unsubscribe_category';
    const ACTION_UNSUBSCRIBE_AGREEMENT_REL_USER = 'unsubscribe_agreement_rel_user';
    
    const ACTION_REPORTING = 'reporting';

    function InternshipOrganizerPeriodManager($internship_manager)
    {
        parent :: __construct($internship_manager);
        $action = Request :: get(self :: PARAM_ACTION);
        if ($action)
        {
            $this->set_parameter(self :: PARAM_ACTION, $action);
        }
    
    }

    function run()
    {
        $action = $this->get_parameter(self :: PARAM_ACTION);
        
        switch ($action)
        {
            
            case self :: ACTION_CREATE_PERIOD :
                $component = $this->create_component('Creator');
                break;
            case self :: ACTION_EDIT_PERIOD :
                $component = $this->create_component('Editor');
                break;
            case self :: ACTION_DELETE_PERIOD :
                $component = $this->create_component('Deleter');
                break;
            case self :: ACTION_VIEW_PERIOD :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_BROWSE_PERIODS :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_REPORTING :
                $component = $this->create_component('Reporting');
                break;
            case self :: ACTION_PUBLISH_PERIOD :
                $component = $this->create_component('Publisher');
                break;
            case self :: ACTION_SUBSCRIBE_USER :
                $component = $this->create_component('SubscribeUser');
                break;
            case self :: ACTION_SUBSCRIBE_GROUP :
                $component = $this->create_component('SubscribeGroup');
                break;
            case self :: ACTION_SUBSCRIBE_CATEGORY :
                $component = $this->create_component('SubscribeCategory');
                break;
            case self :: ACTION_UNSUBSCRIBE_USER :
                $component = $this->create_component('UnsubscribeUser');
                break;
            case self :: ACTION_UNSUBSCRIBE_GROUP :
                $component = $this->create_component('UnsubscribeGroup');
                break;
            case self :: ACTION_UNSUBSCRIBE_CATEGORY :
                $component = $this->create_component('UnsubscribeCategory');
                break;
            case self :: ACTION_CREATE_AGREEMENT :
                $component = $this->create_component('AgreementCreator');
                break;
            case self :: ACTION_DELETE_AGREEMENT :
                $component = $this->create_component('AgreementDeleter');
                break;
            case self :: ACTION_UPDATE_AGREEMENT :
                $component = $this->create_component('AgreementUpdater');
                break;
            case self :: ACTION_VIEW_AGREEMENT :
                $component = $this->create_component('AgreementViewer');
                break;
            case self :: ACTION_UNSUBSCRIBE_AGREEMENT_REL_USER :
                $component = $this->create_component('UnsubscribeAgreementRelUser');
                break;
            case self :: ACTION_SUBSCRIBE_AGREEMENT_REL_USER :
                $component = $this->create_component('SubscribeAgreementRelUser');
                break;
            default :
                $this->set_parameter(self :: PARAM_ACTION, self :: ACTION_BROWSE_PERIODS);
                $component = $this->create_component('Browser');
        }
        
        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'lib/internship_organizer/period_manager/component/';
    }

    //periods
    function retrieve_periods($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_periods($condition, $offset, $count, $order_property);
    }

    function retrieve_period($id)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_internship_organizer_period($id);
    }

    function retrieve_root_period()
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_root_period();
    }

    function count_periods($conditions = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->count_periods($conditions);
    }

    function count_period_rel_users($conditions = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->count_period_rel_users($conditions);
    }

    function count_period_rel_groups($conditions = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->count_period_rel_groups($conditions);
    }

    function retrieve_groups($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_groups($condition, $offset, $count, $order_property);
    }

    //url
    

    function get_browse_periods_url($period = null)
    {
        if ($period != null)
        {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS, self :: PARAM_PERIOD_ID => $period->get_id()));
        
        }
        else
        {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS));
        
        }
    }

    function get_period_editing_url($period)
    {
        
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_PERIOD, self :: PARAM_PERIOD_ID => $period->get_id()));
    }

    function get_period_create_url($parent_period)
    {
        //        if ($parent_id != null)
        //        {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_PERIOD, self :: PARAM_PERIOD_ID => $parent_period->get_id()));
        //        }
    //        else
    //        {
    //            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_PERIOD));
    //        }
    }

    function get_period_emptying_url($period)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_TRUNCATE_PERIOD, self :: PARAM_PERIOD_ID => $period->get_id()));
    }

    function get_period_viewing_url($period)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_PERIOD, self :: PARAM_PERIOD_ID => $period->get_id()));
    }

    function get_period_delete_url($period)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_PERIOD, self :: PARAM_PERIOD_ID => $period->get_id()));
    }

    function get_period_reporting_url($period)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING, self :: PARAM_PERIOD_ID => $period->get_id()));
    }

    function get_period_publish_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_PUBLISH_PERIOD));
    }

    function get_period_subscribe_user_url($period)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_USER, self :: PARAM_PERIOD_ID => $period->get_id()));
    }

    function get_period_subscribe_group_url($period)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_GROUP, self :: PARAM_PERIOD_ID => $period->get_id()));
    }

    function get_period_subscribe_category_url($period)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_CATEGORY, self :: PARAM_PERIOD_ID => $period->get_id()));
    }

    function get_period_unsubscribe_user_url($period_rel_user)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_USER, self :: PARAM_PERIOD_REL_USER_ID => $period_rel_user->get_period_id() . '|' . $period_rel_user->get_user_id() . '|' . $period_rel_user->get_user_type()));
    
    }

    function get_period_unsubscribe_group_url($period_rel_group)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_GROUP, self :: PARAM_PERIOD_REL_GROUP_ID => $period_rel_group->get_period_id() . '|' . $period_rel_group->get_group_id() . '|' . $period_rel_group->get_user_type()));
    
    }

    function get_period_unsubscribe_category_url($category_rel_period)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_CATEGORY, self :: PARAM_PERIOD_REL_CATEGORY_ID => $category_rel_period->get_category_id() . '|' . $category_rel_period->get_period_id()));
    
    }

    function get_period_create_agreement_url($period, $user)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_AGREEMENT, self :: PARAM_USER_ID => $period->get_id() . '|' . $user->get_id()));
    
    }

    function get_update_agreement_url($agreement)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement->get_id()));
    }

    function get_delete_agreement_url($agreement)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement->get_id()));
    }

    function get_view_agreement_url($agreement)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement->get_id()));
    }

    function get_unsubscribe_agreement_rel_user_url($agreement, $user)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_AGREEMENT_REL_USER, self :: PARAM_USER_ID => $agreement->get_id() . '|' . $user->get_id()));
    
    }

    function get_subscribe_agreement_rel_user_url($agreement, $user_type)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_AGREEMENT_REL_USER, self :: PARAM_AGREEMENT_ID => $agreement->get_id()));
    
    }

}

?>