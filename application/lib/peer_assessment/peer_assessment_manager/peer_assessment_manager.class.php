<?php
require_once dirname(__FILE__) . '/../peer_assessment_data_manager.class.php';
require_once dirname(__FILE__) . '/component/peer_assessment_publication_browser/peer_assessment_publication_browser_table.class.php';

/**
 * A peer_assessment manager
 * @author Nick Van Loocke
 */
class PeerAssessmentManager extends WebApplication
{
    const APPLICATION_NAME = 'peer_assessment';

    const PARAM_PEER_ASSESSMENT_PUBLICATION = 'peer_assessment_publication';
    const PARAM_DELETE_SELECTED_PEER_ASSESSMENT_PUBLICATIONS = 'delete_selected_peer_assessment_publications';
    const PARAM_MOVE_SELECTED_PEER_ASSESSMENT_PUBLICATIONS = 'move_selected_peer_assessment_publications';

    const ACTION_DELETE_PEER_ASSESSMENT_PUBLICATION = 'delete_peer_assessment_publication';
    const ACTION_EDIT_PEER_ASSESSMENT_PUBLICATION = 'edit_peer_assessment_publication';
    const ACTION_CREATE_PEER_ASSESSMENT_PUBLICATION = 'create_peer_assessment_publication';
    const ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS = 'browse_peer_assessment_publications';
    const ACTION_VIEW_PEER_ASSESSMENT = 'view';
    const ACTION_EVALUATE_PEER_ASSESSMENT_PUBLICATION = 'evaluate_peer_assessment_publication';

    const ACTION_CHANGE_PEER_ASSESSMENT_PUBLICATION_VISIBILITY = 'change_publication_visibility';
    const ACTION_MOVE_PEER_ASSESSMENT_PUBLICATION = 'move_publication';
    const ACTION_TAKE_PEER_ASSESSMENT_PUBLICATION = 'take_publication';
    const ACTION_VIEW_PEER_ASSESSMENT_PUBLICATION_RESULTS = 'view_publication_results';
    const ACTION_BUILD_PEER_ASSESSMENT_PUBLICATION = 'build_publication';

    const ACTION_MANAGE_CATEGORIES = 'manage_categories';

    /**
     * Constructor
     * @param User $user The current user
     */
    function PeerAssessmentManager($user = null)
    {
        parent :: __construct($user);
        $this->parse_input_from_table();
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
        	case self :: ACTION_EVALUATE_PEER_ASSESSMENT_PUBLICATION :
        		$component = $this->create_component('PeerAssessmentEvaluation');
        		break;
            case self :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS :
            	$component = $this->create_component('Browser');
                break;
            case self :: ACTION_DELETE_PEER_ASSESSMENT_PUBLICATION :
            	$component = $this->create_component('Deleter');
                break;
            case self :: ACTION_EDIT_PEER_ASSESSMENT_PUBLICATION :
            	$component = $this->create_component('Updater');
                break;
            case self :: ACTION_CREATE_PEER_ASSESSMENT_PUBLICATION :
            	$component = $this->create_component('Creator');
                break;
            case self :: ACTION_VIEW_PEER_ASSESSMENT :
            	$component = $this->create_component('Viewer');
                break;
            case self :: ACTION_MANAGE_CATEGORIES :
            	$component = $this->create_component('CategoryManager');
                break;
            case self :: ACTION_TAKE_PEER_ASSESSMENT_PUBLICATION :
            	$component = $this->create_component('Take');
            	break;
            case self :: ACTION_VIEW_PEER_ASSESSMENT_PUBLICATION_RESULTS :
            	$component = $this->create_component('Results');
            	break;
            case self :: ACTION_MOVE_PEER_ASSESSMENT_PUBLICATION :
            	$component = $this->create_component('Mover');
            	break;
            case self :: ACTION_CHANGE_PEER_ASSESSMENT_PUBLICATION_VISIBILITY :
            	$component = $this->create_component('VisibilityChanger');
            	break;
            case self :: ACTION_BUILD_PEER_ASSESSMENT_PUBLICATION :
            	$component = $this->create_component('Builder');
            	break;
            default :
            	$this->set_action(self :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS);
            	$component = $this->create_component('Browser');

        }
        $component->run();
    }

    /*
     * Switch statement for the dropdown box function under the table (Remove selected/ Move selected)
     */
    private function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            switch ($_POST['action'])
            {
                case self :: PARAM_DELETE_SELECTED_PEER_ASSESSMENT_PUBLICATIONS :

                    $selected_ids = $_POST[PeerAssessmentPublicationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];

                    if (empty($selected_ids))
                    {
                        $selected_ids = array();
                    }
                    elseif (! is_array($selected_ids))
                    {
                        $selected_ids = array($selected_ids);
                    }

                    $this->set_action(self :: ACTION_DELETE_PEER_ASSESSMENT_PUBLICATION);
                    $_GET[self :: PARAM_PEER_ASSESSMENT_PUBLICATION] = $selected_ids;
                    break;

              	case self :: PARAM_MOVE_SELECTED_PEER_ASSESSMENT_PUBLICATIONS :

                    $selected_ids = $_POST[PeerAssessmentPublicationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];

                    if (empty($selected_ids))
                    {
                        $selected_ids = array();
                    }
                    elseif (! is_array($selected_ids))
                    {
                        $selected_ids = array($selected_ids);
                    }

                    $this->set_action(self :: ACTION_MOVE_PEER_ASSESSMENT_PUBLICATION);
                    $_GET[self :: PARAM_PEER_ASSESSMENT_PUBLICATION] = $selected_ids;
                    break;
            }

        }
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

	function retrieve_peer_assessment_publication_via_content_object($content_object_id)
    {
        return PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication_via_content_object($content_object_id);
    }

    // Url Creation


    function get_create_peer_assessment_publication_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_PEER_ASSESSMENT_PUBLICATION));
    }

    function get_evaluation_publication_url($peer_assessment_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EVALUATE_PEER_ASSESSMENT_PUBLICATION, self :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id()));
    }

    function get_update_peer_assessment_publication_url($peer_assessment_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_PEER_ASSESSMENT_PUBLICATION, self :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id()));
    }

    function get_delete_peer_assessment_publication_url($peer_assessment_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_PEER_ASSESSMENT_PUBLICATION, self :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id()));
    }

    function get_browse_peer_assessment_publications_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS));
    }

	function get_category_manager_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_CATEGORIES));
    }

    function is_allowed()
    {
        return true;
    }

    // Dummy Methods which are needed because we don't work with learning objects
    static function content_object_is_published($object_id)
    {
    	return PeerAssessmentDataManager :: get_instance()->content_object_is_publish($object_id);
    }

    static function any_content_object_is_published($object_ids)
    {
    	return PeerAssessmentDataManager :: get_instance()->any_content_object_is_published($object_ids);
    }

    static function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
    	return PeerAssessmentDataManager :: get_instance()->get_content_object_publication_attributes($object_id, $type, $offset, $count, $order_property);
    }

    static function get_content_object_publication_attribute($object_id)
    {
    	return PeerAssessmentDataManager :: get_instance()->get_content_object_publication_attribute($object_id);
    }

	static function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        return PeerAssessmentDataManager :: get_instance()->count_publication_attributes($user, $object_id, $condition);
    }

    static function delete_content_object_publications($object_id)
    {
    	return PeerAssessmentDataManager :: get_instance()->delete_content_object_publications($object_id);
    }

	static function delete_content_object_publication($publication_id)
    {
    	return PeerAssessmentDataManager :: get_instance()->delete_content_object_publication($publication_id);
    }

    static function update_content_object_publication_id($publication_attr)
    {
    	return PeerAssessmentDataManager :: get_instance()->update_content_object_publication_id($publication_attr);
    }

	function get_change_peer_assessment_publication_visibility_url($peer_assessment_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CHANGE_PEER_ASSESSMENT_PUBLICATION_VISIBILITY, self :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id()));
    }

    function get_move_peer_assessment_publication_url($peer_assessment_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_PEER_ASSESSMENT_PUBLICATION, self :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id()));
    }

	function get_take_peer_assessment_publication_url($peer_assessment_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_TAKE_PEER_ASSESSMENT_PUBLICATION, self :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id()));
    }

    function get_peer_assessment_results_viewer_url($peer_assessment_publication)
    {
        $id = $peer_assessment_publication ? $peer_assessment_publication->get_id() : null;
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_PEER_ASSESSMENT_PUBLICATION_RESULTS, self :: PARAM_PEER_ASSESSMENT_PUBLICATION => $id));
    }

    function get_build_peer_assessment_url($peer_assessment_publication)
    {
    	return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BUILD_PEER_ASSESSMENT_PUBLICATION, self :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id()));
    }

	static function get_content_object_publication_locations($content_object)
    {
        $allowed_types = array(PeerAssessment :: get_type_name());

        $type = $content_object->get_type();
        if (in_array($type, $allowed_types))
        {
            $locations = array(Translation :: get('PeerAssessment'));
            return $locations;
        }

        return array();
    }

    static function publish_content_object($content_object, $location)
    {
        $publication = new PeerAssessmentPublication();
        $publication->set_content_object($content_object->get_id());
        $publication->set_publisher(Session :: get_user_id());
        $publication->set_published(time());
        $publication->set_hidden(0);
        $publication->set_from_date(0);
        $publication->set_to_date(0);

        $publication->create();
        return Translation :: get('PublicationCreated');
    }
}
?>