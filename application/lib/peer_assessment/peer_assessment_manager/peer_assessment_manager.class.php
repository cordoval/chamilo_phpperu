<?php
require_once dirname(__FILE__) . '/peer_assessment_manager_component.class.php';
require_once dirname(__FILE__) . '/../peer_assessment_data_manager.class.php';
/**
 * A peer_assessment manager
 * @author Nick Van Loocke
 */

class PeerAssessmentManager extends WebApplication
{
    const APPLICATION_NAME = 'peer_assessment';
    
    const PARAM_ACTION = 'go';
    const PARAM_DELETE_SELECTED = 'delete_selected';
    const PARAM_PEER_ASSESSMENT_PUBLICATION = 'peer_assessment_publication';
    const PARAM_PUBLICATION_ID = 'pid';
    const PARAM_MOVE = 'move'; 
    
    const ACTION_DELETE = 'delete_peer_assessment_publication';
    const ACTION_EDIT = 'edit_peer_assessment_publication';
    const ACTION_CREATE = 'create_peer_assessment_publication';
    const ACTION_VIEW = 'view_peer_assessment_publications';
    const ACTION_PUBLISH = 'publish';
    const ACTION_BROWSE = 'browse';
    const ACTION_TOGGLE_VISIBILITY = 'toggle_visibility';
    const ACTION_MOVE = 'move';
    const ACTION_MANAGE_CATEGORIES = 'manage_categories';
      
    
    private $parameters;
    private $user;
    private $rights;

    /**
     * Constructor
     * @param User $user The current user
     */
    function PeerAssessmentManager($user = null)
    {
        $this->user = $user;
        $this->parameters = array();
        $this->load_rights();
        $this->set_action(Request :: get(self :: PARAM_ACTION));
    }

    /**
     * Run this peer_assessment manager
     */
    function run()
    {
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_DELETE :
                $component = PeerAssessmentManagerComponent :: factory('Deleter', $this);
                break;
            case self :: ACTION_CREATE :
                $component = PeerAssessmentManagerComponent :: factory('Creator', $this);
                break;
            case self :: ACTION_BROWSE :
                $component = PeerAssessmentManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_VIEW :
                $component = PeerAssessmentManagerComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_EDIT :
                $component = PeerAssessmentManagerComponent :: factory('Editor', $this);
                break;
            case self :: ACTION_MOVE :
                $component = PeerAssessmentManagerComponent :: factory('Mover', $this);
                break;
            case self :: ACTION_TOGGLE_VISIBILITY :
                $component = PeerAssessmentManagerComponent :: factory('ToggleVisibility', $this);
                break;
            case self :: ACTION_MANAGE_CATEGORIES :
                $component = PeerAssessmentManagerComponent :: factory('CategoryManager', $this);
                break;
            default :
                $this->set_action(self :: ACTION_BROWSE);
                $component = PeerAssessmentManagerComponent :: factory('Browser', $this);
        
        }
        $component->run();
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    // Data Retrieving
    

    function count_peer_assessment_publications($condition)
    {
        return PeerAssessmentDataManager :: get_instance()->count_peer_assessment_publications($condition);
    }

    function retrieve_peer_assessment_publications($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publications($condition, $offset, $count, $order_property);
    }

    function retrieve_peer_assessment_publication($id)
    {
        return PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication($id);
    }

    // Url Creation
    

    function get_create_peer_assessment_publication_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_PEER_ASSESSMENT_PUBLICATION));
    }

    function get_update_peer_assessment_publication_url($peer_assessment_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT, self :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id()));
    }

    function get_delete_peer_assessment_publication_url($peer_assessment_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE, self :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id()));
    }

    function get_browse_peer_assessment_publications_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS));
    }

    function get_browse_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }

    function get_category_manager_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_CATEGORIES));
    }

    // Dummy Methods which are needed because we don't work with learning objects
    function content_object_is_published($object_id)
    {
    }

    function any_content_object_is_published($object_ids)
    {
    }

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
    }

    function get_content_object_publication_attribute($object_id)
    {
    
    }

	function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        return PeerAssessmentDataManager :: get_instance()->count_publication_attributes($user, $object_id, $condition);
    }

    function delete_content_object_publications($object_id)
    {
    
    }
    
	function delete_content_object_publication($publication_id)
    {
    
    }

    function update_content_object_publication_id($publication_attr)
    {
    
    }

    function get_content_object_publication_locations($content_object)
    {
    
    }

    function publish_content_object($content_object, $location)
    {
    
    }

    function get_user_id()
    {
        return $this->user->get_id();
    }

    function is_allowed($right)
    {
        return $this->rights[$right];
    }

    function get_user()
    {
        return $this->user;
    }

    /**
     * Load the rights for the current user in this tool
     */
    private function load_rights()
    {
        $this->rights[VIEW_RIGHT] = true;
        $this->rights[EDIT_RIGHT] = true;
        $this->rights[ADD_RIGHT] = true;
        $this->rights[DELETE_RIGHT] = true;
        return;
    }
}
?>