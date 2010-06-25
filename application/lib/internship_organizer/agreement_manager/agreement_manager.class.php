<?php
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/browser/browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/moment_browser/browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement.class.php';

class InternshipOrganizerAgreementManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    const PARAM_AGREEMENT_ID = 'agreement_id';
    const PARAM_LOCATION_ID = 'location_id';
    const PARAM_AGREEMENT_REL_LOCATION_ID = 'agreement_rel_location_id';
    const PARAM_DELETE_SELECTED_AGREEMENTS = 'delete_agreements';
    const PARAM_MOVE_AGREEMENT_REL_LOCATION_DIRECTION = 'move_direction';
    const PARAM_APPROVE_AGREEMENT_REL_LOCATION_TYPE = 'approve_type';
    
    const PARAM_MOMENT_ID = 'moment_id';
    const PARAM_DELETE_SELECTED_MOMENTS = 'delete_moments';
    const PARAM_SUBSCRIBE_SELECTED = 'subscribe_selected';
    const PARAM_PUBLICATION_ID = 'publication_id';
    
    const ACTION_CREATE_AGREEMENT = 'create';
    const ACTION_BROWSE_AGREEMENT = 'browse';
    const ACTION_UPDATE_AGREEMENT = 'update';
    const ACTION_DELETE_AGREEMENT = 'delete';
    const ACTION_VIEW_AGREEMENT = 'view';
    const ACTION_PUBLISH_AGREEMENT = 'publish';
    const ACTION_VIEW_PUBLICATION = 'view_publication';
    const ACTION_SUBSCRIBE_LOCATION_TO_AGREEMENT = 'subscribe';
    const ACTION_UNSUBSCRIBE_LOCATION_FROM_AGREEMENT = 'unsubscribe';
    
    const ACTION_MOVE_AGREEMENT_REL_LOCATION = 'move';
    const ACTION_APPROVE_AGREEMENT_REL_LOCATION = 'approve';
    
    const ACTION_CREATE_MOMENT = 'create_moment';
    const ACTION_BROWSE_MOMENTS = 'browse_moments';
    const ACTION_EDIT_MOMENT = 'edit_moment';
    const ACTION_DELETE_MOMENT = 'delete_moment';
    const ACTION_VIEW_MOMENT = 'view_moment';
    const ACTION_PUBLISH_MOMENT = 'publish_moment';
    
    const ACTION_SUBSCRIBE_LOCATION = 'subscribe_location';
    const ACTION_SUBSCRIBE_MENTOR = 'subscribe_mentor';

    function InternshipOrganizerAgreementManager($internship_manager)
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
                $component = $this->create_component('Updater');
                break;
            case self :: ACTION_DELETE_AGREEMENT :
                $component = $this->create_component('Deleter');
                break;
            case self :: ACTION_CREATE_AGREEMENT :
                $component = $this->create_component('Creator');
                break;
            case self :: ACTION_VIEW_AGREEMENT :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_BROWSE_AGREEMENT :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_PUBLISH_AGREEMENT :
                $component = $this->create_component('Publisher');
                break;
            case self :: ACTION_VIEW_PUBLICATION :
                $component = $this->create_component('PublicationViewer');
                break;    
            case self :: ACTION_EDIT_MOMENT :
                $component = $this->create_component('MomentUpdater');
                break;
            case self :: ACTION_DELETE_MOMENT :
                $component = $this->create_component('MomentDeleter');
                break;
            case self :: ACTION_CREATE_MOMENT :
                $component = $this->create_component('MomentCreator');
                break;
            case self :: ACTION_BROWSE_MOMENTS :
                $component = $this->create_component('MomentBrowser');
                break;
            case self :: ACTION_VIEW_MOMENT :
                $component = $this->create_component('MomentViewer');
                break;
            case self :: ACTION_PUBLISH_MOMENT :
                $component = $this->create_component('MomentPublisher');
                break;
            case self :: ACTION_SUBSCRIBE_LOCATION :
                $component = $this->create_component('SubscribeLocationBrowser');
                break;
            case self :: ACTION_UNSUBSCRIBE_LOCATION_FROM_AGREEMENT :
                $component = $this->create_component('Unsubscriber');
                break;
            case self :: ACTION_SUBSCRIBE_LOCATION_TO_AGREEMENT :
                $component = $this->create_component('Subscriber');
                break;
            case self :: ACTION_MOVE_AGREEMENT_REL_LOCATION :
                $component = $this->create_component('Mover');
                break;
            case self :: ACTION_APPROVE_AGREEMENT_REL_LOCATION :
                $component = $this->create_component('ApproveLocation');
                break;
            case self :: ACTION_SUBSCRIBE_MENTOR :
                $component = $this->create_component('SubscribeMentor');
                break;
            default :
                $component = $this->create_component('Browser');
                break;
        }
        
        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/';
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

    function get_agreement_rel_location_subscribing_url($agreement, $location)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_LOCATION_TO_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement->get_id(), self :: PARAM_LOCATION_ID => $location->get_id()));
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

    function get_agreement_publish_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_PUBLISH_AGREEMENT));
    }

    function get_moment_publish_url($agreement)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_PUBLISH_MOMENT, self :: PARAM_AGREEMENT_ID => $agreement->get_id()));
    }
	
	function get_view_publication_url($publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_PUBLICATION, self :: PARAM_PUBLICATION_ID => $publication->get_id()));
    }
    
    private function parse_input_from_table()
    {
        
        if (isset($_POST['action']))
        {
            if (isset($_POST[InternshipOrganizerAgreementBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX]))
            {
                $selected_ids = $_POST[InternshipOrganizerAgreementBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
            }
            
            if (isset($_POST[InternshipOrganizerMomentBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX]))
            {
                $selected_ids = $_POST[InternshipOrganizerMomentBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
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
                case self :: PARAM_DELETE_SELECTED_MOMENTS :
                    $this->set_agreement_action(self :: ACTION_DELETE_MOMENT);
                    $_GET[self :: PARAM_MOMENT_ID] = $selected_ids;
                    break;
                case self :: PARAM_DELETE_SELECTED_AGREEMENTS :
                    $this->set_agreement_action(self :: ACTION_DELETE_AGREEMENT);
                    $_GET[self :: PARAM_AGREEMENT_ID] = $selected_ids;
                    break;
            }
        }
    }

    private function set_agreement_action($action)
    {
        $this->set_parameter(self :: PARAM_ACTION, $action);
    }
}

?>