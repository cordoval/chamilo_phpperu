<?php
//require_once Path :: get_application_path() . 'internship_organizer/php/agreement_manager/component/browser/browser_table.class.php';
//require_once Path :: get_application_path() . 'internship_organizer/php/agreement_manager/component/moment_browser/browser_table.class.php';
//require_once Path :: get_application_path() . 'internship_organizer/php/agreement.class.php';


class InternshipOrganizerAgreementManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    const PARAM_AGREEMENT_ID = 'agreement_id';
    const PARAM_LOCATION_ID = 'location_id';
    const PARAM_STUDENT_ID = 'user_id';
    const PARAM_AGREEMENT_REL_LOCATION_ID = 'agreement_rel_location_id';
    const PARAM_DELETE_SELECTED_AGREEMENTS = 'delete_agreements';
    const PARAM_MOVE_AGREEMENT_REL_LOCATION_DIRECTION = 'move_direction';
    const PARAM_APPROVE_AGREEMENT_REL_LOCATION_TYPE = 'approve_type';
    const SUB_MANAGER_NAME = 'agreement';
    
    const PARAM_MOMENT_ID = 'moment_id';
    const PARAM_SUBSCRIBE_SELECTED = 'subscribe_selected';
    const PARAM_PUBLICATION_ID = 'publication_id';
    
    const ACTION_CREATE_AGREEMENT = 'creator';
    const ACTION_BROWSE_AGREEMENT = 'browser';
    const ACTION_UPDATE_AGREEMENT = 'editor';
    const ACTION_EDIT_AGREEMENT_RIGHTS = 'rights_editor';
    const ACTION_DELETE_AGREEMENT = 'deleter';
    const ACTION_VIEW_AGREEMENT = 'viewer';
    const ACTION_PUBLISH_AGREEMENT = 'publisher';
    const ACTION_VIEW_PUBLICATION = 'publication_viewer';
    const ACTION_DELETE_PUBLICATION = 'publication_deleter';
    const ACTION_SUBSCRIBE_LOCATION_TO_AGREEMENT = 'subscriber';
    const ACTION_UNSUBSCRIBE_LOCATION_FROM_AGREEMENT = 'unsubscriber';
    
    const ACTION_MOVE_AGREEMENT_REL_LOCATION = 'mover';
    const ACTION_APPROVE_AGREEMENT_REL_LOCATION = 'approve_location';
    
    const ACTION_CREATE_MOMENT = 'moment_creator';
    const ACTION_BROWSE_MOMENTS = 'moment_browser';
    const ACTION_EDIT_MOMENT = 'moment_editor';
    const ACTION_EDIT_MOMENT_RIGHTS = 'moment_rights_editor';
    const ACTION_DELETE_MOMENT = 'moment_deleter';
    const ACTION_VIEW_MOMENT = 'moment_viewer';
    const ACTION_PUBLISH_MOMENT = 'moment_publisher';
    const ACTION_REPORTING = 'reporting';
    
    const ACTION_EDIT_PUBLICATION_RIGHTS = 'publication_rights_editor';
    
    const ACTION_SUBSCRIBE_LOCATION = 'subscribe_location_browser';
    const ACTION_SUBSCRIBE_MENTOR = 'subscribe_mentor';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE_AGREEMENT;

    function InternshipOrganizerAgreementManager($internship_manager)
    {
        parent :: __construct($internship_manager);
    }

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'internship_organizer/php/agreement_manager/component/';
    }

    //agreements
    

    function count_agreements($condition)
    {
        return InternshipOrganizerDataManager :: get_instance()->count_agreements($condition);
    }

    function retrieve_agreements($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_agreements($condition, $offset, $count, $order_property);
    }

    function retrieve_agreement($id)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_agreement($id);
    }

    function retrieve_agreement_rel_locations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_agreement_rel_locations($condition, $offset, $count, $order_property);
    }

    function count_agreement_rel_locations($condition = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->count_agreement_rel_locations($condition);
    }

    function retrieve_agreement_rel_location($location_id, $agreement_id)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_agreement_rel_location($location_id, $agreement_id);
    }

    //moments
    function count_moments($condition)
    {
        return InternshipOrganizerDataManager :: get_instance()->count_moments($condition);
    }

    function retrieve_moments($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_moments($condition, $offset, $count, $order_property);
    }

    function retrieve_moment($id)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_moment($id);
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

    function get_view_moment_url($moment)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_MOMENT, self :: PARAM_MOMENT_ID => $moment->get_id()));
    }

    function get_subscribe_location_url($agreement)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_LOCATION, self :: PARAM_AGREEMENT_ID => $agreement->get_id()));
    }

    function get_agreement_rel_location_subscribing_url($agreement, $categoryrellocation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_LOCATION_TO_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement->get_id(), self :: PARAM_LOCATION_ID => $categoryrellocation->get_location_id()));
    }

    function get_agreement_rel_location_unsubscribing_url($agreementrellocation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_LOCATION_FROM_AGREEMENT, self :: PARAM_AGREEMENT_REL_LOCATION_ID => $agreementrellocation->get_agreement_id() . '|' . $agreementrellocation->get_location_id()));
    }

    function get_agreement_rel_location_move_up_url($agreementrellocation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_AGREEMENT_REL_LOCATION, self :: PARAM_AGREEMENT_ID => $agreementrellocation->get_agreement_id(), self :: PARAM_LOCATION_ID => $agreementrellocation->get_location_id(), self :: PARAM_MOVE_AGREEMENT_REL_LOCATION_DIRECTION => - 1));
    }

    function get_agreement_rel_location_move_down_url($agreementrellocation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_AGREEMENT_REL_LOCATION, self :: PARAM_AGREEMENT_ID => $agreementrellocation->get_agreement_id(), self :: PARAM_LOCATION_ID => $agreementrellocation->get_location_id(), self :: PARAM_MOVE_AGREEMENT_REL_LOCATION_DIRECTION => + 1));
    }

    function get_agreement_rel_location_approve_url($agreementrellocation)
    {
        if ($agreementrellocation->get_location_type() == 1)
        {
            $type = 2;
        }
        else 
            if ($agreementrellocation->get_location_type() == 2)
            {
                $type = 1;
            
            }
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_APPROVE_AGREEMENT_REL_LOCATION, self :: PARAM_AGREEMENT_ID => $agreementrellocation->get_agreement_id(), self :: PARAM_LOCATION_ID => $agreementrellocation->get_location_id(), self :: PARAM_APPROVE_AGREEMENT_REL_LOCATION_TYPE => $type));
    }

    function get_agreement_subscribe_mentor_url($agreement)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_MENTOR, self :: PARAM_AGREEMENT_ID => $agreement->get_id()));
    }

    function get_agreement_publish_url($agreement)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_PUBLISH_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement->get_id()));
    }

    function get_agreements_publish_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_PUBLISH_AGREEMENT));
    }

    function get_moments_publish_url($agreement)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_PUBLISH_MOMENT, self :: PARAM_AGREEMENT_ID => $agreement->get_id()));
    }

    function get_moment_publish_url($moment)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_PUBLISH_MOMENT, self :: PARAM_MOMENT_ID => $moment->get_id()));
    }

    function get_view_publication_url($publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_PUBLICATION, self :: PARAM_PUBLICATION_ID => $publication->get_id()));
    }

    function get_delete_publication_url($publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_PUBLICATION, self :: PARAM_PUBLICATION_ID => $publication->get_id()));
    }

    function get_agreement_reporting_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING));
    }

    function get_rights_editor_url($agreement)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_AGREEMENT_RIGHTS, self :: PARAM_AGREEMENT_ID => $agreement->get_id()));
    }

    function get_moment_rights_editor_url($moment)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_MOMENT_RIGHTS, self :: PARAM_MOMENT_ID => $moment->get_id()));
    }

    function get_publication_rights_editor_url($publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_PUBLICATION_RIGHTS, self :: PARAM_PUBLICATION_ID => $publication->get_id()));
    }

    function get_take_evaluation_url($publication)
    {
        return $this->get_url(array(Application :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_EVALUATION, InternshipOrganizerEvaluationManager :: PARAM_ACTION => InternshipOrganizerEvaluationManager :: ACTION_TAKE_EVALUATION, InternshipOrganizerEvaluationManager :: PARAM_PUBLICATION_ID => $publication->get_id(), InternshipOrganizerEvaluationManager :: PARAM_SURVEY_ID => $publication->get_content_object_id(), InternshipOrganizerEvaluationManager :: PARAM_INVITEE_ID => $this->get_user_id()));
    }

    private function set_agreement_action($action)
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