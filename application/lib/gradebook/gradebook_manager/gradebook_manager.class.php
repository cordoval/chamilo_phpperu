<?php
require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';
require_once Path :: get_application_path() . 'lib/gradebook/gradebook_rights.class.php';

require_once dirname(__FILE__) . '/../gradebook_utilities.class.php';

require_once dirname(__FILE__) . '/../gradebook_data_manager.class.php';

require_once dirname(__FILE__) . '/component/evaluation_formats_browser/evaluation_formats_browser_table.class.php';

class GradebookManager extends WebApplication
{
	const APPLICATION_NAME = 'gradebook';
	const PARAM_ACTION = 'go';
//-------------IGNORE-----------------
	const PARAM_USER_ID = 'user';
	const PARAM_GRADEBOOK_ID = 'gradebook';
	const PARAM_GRADEBOOK_REL_USER_ID = 'gradebook_rel_user_id';
	const PARAM_REMOVE_SELECTED = 'remove_selected';
	const PARAM_UNSUBSCRIBE_SELECTED = 'unsubscribe_selected';
	const PARAM_SUBSCRIBE_SELECTED = 'subscribe_selected';
	const PARAM_TRUNCATE_SELECTED = 'truncate';

	const ACTION_VIEW_HOME = 'home';
	const ACTION_BROWSE_GRADEBOOK = 'browse';
	
	const ACTION_CREATE_GRADEBOOK = 'create_gradebook';
	const ACTION_EDIT_GRADEBOOK = 'edit_gradebook';
	const ACTION_DELETE_GRADEBOOK = 'delete_gradebook';
	const ACTION_MOVE_GRADEBOOK = 'move_gradebook';
	const ACTION_TRUNCATE_GRADEBOOK = 'truncate_gradebook';
	const ACTION_VIEW_GRADEBOOK = 'view_gradebook';
	
	const ACTION_SUBSCRIBE_USER_TO_GRADEBOOK = 'subscribe_user_to_gradebook';
	const ACTION_SUBSCRIBE_USER_BROWSER = 'subscribe_user_browser';
	const ACTION_UNSUBSCRIBE_USER_FROM_GRADEBOOK = 'unsubscribe_user_from_gradebook';
//--------------END IGNORE--------------
	/*
	 * Gradebook administration actions
	 */
	const ACTION_ADMIN_BROWSE_EVALUATION_FORMATS = 'admin_browse_evaluation_formats';
	const ACTION_EDIT_EVALUATION_FORMAT = 'edit_evaluation_format';
	const ACTION_CHANGE_FORMAT_ACTIVE_PROPERTY = 'change_evaluation_format_active_property';
	const ACTION_VIEW_EVALUATIONS_ON_PUBLICATION = 'view_evaluations_on_publication';
	/*
	 * Gradebook parameters
	 */
	const PARAM_ACTIVATE_SELECTED_EVALUATION_FORMAT = 'activate_selected_evaluation_format';
	const PARAM_DEACTIVATE_SELECTED_EVALUATION_FORMAT = 'deactivate_selected_evaluation_format';
	const PARAM_EVALUATION_FORMAT = 'evaluation_format';
	const PARAM_EVALUATION_FORMAT_ID = 'evaluation_format';
	const PARAM_ACTIVE = 'active';
	const PARAM_PUBLICATION_TYPE = 'publication_type';
	const PARAM_PUBLICATION_ID = 'publication_id';
	
	public function GradebookManager($user)
	{
	    parent :: __construct($user);
		$this->parse_input_from_table();
	}

	public function run()
	{
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
//			case self :: ACTION_BROWSE_GRADEBOOK :
//				$this->set_action(self :: ACTION_BROWSE_GRADEBOOK);
//				$component = GradebookManagerComponent :: factory('GradebookBrowser', $this);
//				break;
//			case self :: ACTION_CREATE_GRADEBOOK :
//				$this->set_action(self :: ACTION_CREATE_GRADEBOOK);
//				$component = GradebookManagerComponent :: factory('GradebookCreator', $this);
//				break;
//			case self :: ACTION_DELETE_GRADEBOOK :
//				$this->set_action(self :: ACTION_DELETE_GRADEBOOK);
//				$component = GradebookManagerComponent :: factory('GradebookDeleter', $this);
//				break;
//			case self :: ACTION_EDIT_GRADEBOOK :
//				$this->set_action(self :: ACTION_EDIT_GRADEBOOK);
//				$component = GradebookManagerComponent :: factory('GradebookEditor', $this);
//				break;
//			case self :: ACTION_VIEW_GRADEBOOK :
//				$this->set_action(self :: ACTION_VIEW_GRADEBOOK);
//				$component = GradebookManagerComponent :: factory('GradebookViewer', $this);
//				break;
//			case self :: ACTION_TRUNCATE_GRADEBOOK :
//				$component = GradebookManagerComponent :: factory('GradebookTruncater', $this);
//				break;	
//			case self :: ACTION_SUBSCRIBE_USER_BROWSER :
//				$this->set_action(self :: ACTION_SUBSCRIBE_USER_BROWSER);
//				$component = GradebookManagerComponent :: factory('GradebookSubscribeUserBrowser', $this);
//				break;
//			case self :: ACTION_SUBSCRIBE_USER_TO_GRADEBOOK :
//				$component = GradebookManagerComponent :: factory('GradebookSubscriber', $this);
//				break;
//			case self :: ACTION_UNSUBSCRIBE_USER_FROM_GRADEBOOK :
//				$component = GradebookManagerComponent :: factory('GradebookUnsubscriber', $this);
//				break;	
			case self :: ACTION_ADMIN_BROWSE_EVALUATION_FORMATS :
				$component = $this->create_component('AdminEvaluationFormatsBrowser', $this);
				break;	
			case self :: ACTION_CHANGE_FORMAT_ACTIVE_PROPERTY :
				$component = $this->create_component('AdminActiveChanger', $this);
				break;	
			case self :: ACTION_EDIT_EVALUATION_FORMAT :
				$component = $this->create_component('AdminEditEvaluationFormat', $this);
				break;		
			case self :: ACTION_VIEW_EVALUATIONS_ON_PUBLICATION :
				$component = $this->create_component('ViewEvaluationsOnPublication', $this);
				break;		
			default :
				$this->set_action(self :: ACTION_VIEW_HOME);
				$component = $this->create_component('GradebookBrowser', $this);
				break;
		}
		$component->run();
	}
	
  	public function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = array('name' => Translation :: get('EvaluationFormatTypeList'), 'description' => Translation :: get('EvaluationFormatTypeListDescription'), 'action' => 'list', 'url' => $this->get_admin_browse_evaluation_format_types_link());

        $info = parent :: get_application_platform_admin_links();
        $info['links'] = $links;
        return $info;
    }
    
	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}
//------------------IGNORE-------------------
//gradebook

	function retrieve_gradebook($id)
	{
		$dm = GradebookDataManager :: get_instance();
		return $dm->retrieve_gradebook($id);
	}
	function retrieve_gradebooks($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return GradebookDataManager :: get_instance()->retrieve_gradebooks($condition, $offset, $count, $order_property);
	}

	function count_gradebooks($condition = null)
	{
		return GradebookDataManager :: get_instance()->count_gradebooks($condition);
	}

	function get_gradebook_editing_url($gradebook)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_GRADEBOOK, self :: PARAM_GRADEBOOK_ID => $gradebook->get_id()));
	}

	function get_create_gradebook_url()
	{
		return $this->get_url(array ( self :: PARAM_ACTION => self :: ACTION_CREATE_GRADEBOOK));
	}

	function get_gradebook_emptying_url($gradebook)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_TRUNCATE_GRADEBOOK, self :: PARAM_GRADEBOOK_ID => $gradebook->get_id()));
	}

	function get_gradebook_viewing_url($gradebook)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_GRADEBOOK, self :: PARAM_GRADEBOOK_ID => $gradebook->get_id()));
	}

	function get_gradebook_subscribe_user_browser_url($gradebook)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_USER_BROWSER, self :: PARAM_GRADEBOOK_ID => $gradebook->get_id()));
	}

	function get_gradebook_delete_url($gradebook)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_GRADEBOOK, self :: PARAM_GRADEBOOK_ID => $gradebook->get_id()));
	}

	//gradebook rel users

	function retrieve_gradebook_rel_users($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return GradebookDataManager :: get_instance()->retrieve_gradebook_rel_users($condition, $offset, $count, $order_property);
	}

	function retrieve_gradebook_rel_user($user_id, $gradebook_id)
	{
		return GradebookDataManager :: get_instance()->retrieve_gradebook_rel_user($user_id, $gradebook_id);
	}

	function count_gradebook_rel_users($condition = null)
	{
		return GradebookDataManager :: get_instance()->count_gradebook_rel_users($condition);
	}

	function get_gradebook_rel_user_unsubscribing_url($gradebookreluser)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_USER_FROM_GRADEBOOK, self :: PARAM_GRADEBOOK_REL_USER_ID => $gradebookreluser->get_gradebook_id() . '|' . $gradebookreluser->get_user_id()));
	}

	function get_gradebook_rel_user_subscribing_url($gradebook, $user)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_USER_TO_GRADEBOOK, self :: PARAM_GRADEBOOK_ID => $gradebook->get_id(), self :: PARAM_USER_ID => $user->get_id()));
	}

	
	
	
	/**
	 * @see Application::content_object_is_published()
	 */
	public function content_object_is_published($object_id)
	{
		return false;
	}
	/**
	 * @see Application::any_content_object_is_published()
	 */
	public function any_content_object_is_published($object_ids)
	{
		return false;
	}
	/**
	 * @see Application::get_content_object_publication_attributes()
	 */
	public function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
	{
		return null;
	}
	/**
	 * @see Application::get_content_object_publication_attribute()
	 */
	public function get_content_object_publication_attribute($publication_id)
	{
		return null;
	}
	/**
	 * @see Application::count_publication_attributes()
	 */
	public function count_publication_attributes($type = null, $condition = null)
	{
		return 0;
	}
	/**
	 * @see Application::delete_content_object_publications()
	 */
	public function delete_content_object_publications($object_id)
	{
		return true;
	}
	/**
	 * @see Application::update_content_object_publication_id()
	 */
	public function update_content_object_publication_id($publication_attr)
	{
		return true;
	}

	/**
	 * Inherited
	 */
	function get_content_object_publication_locations($content_object)
	{
		return array();
	}

	function publish_content_object($content_object, $location)
	{
		return Translation :: get('PublicationCreated');
	}

	
	/**
	 * Parse the input from the sortable tables and process input accordingly
	 */
	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			if(isset($_POST[EvaluationFormatsBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX])){ 
				$selected_ids = $_POST[EvaluationFormatsBrowserTable  :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];
			}
			
			if (empty ($selected_ids))
			{
				$selected_ids = array ();
			}
			elseif (!is_array($selected_ids))
			{
				$selected_ids = array ($selected_ids);
			}
			switch ($_POST['action'])
			{
				case self :: PARAM_DEACTIVATE_SELECTED_EVALUATION_FORMAT :
					$this->set_action(self :: ACTION_CHANGE_FORMAT_ACTIVE_PROPERTY);
					Request :: set_get(self :: PARAM_EVALUATION_FORMAT_ID, $selected_ids);
					Request :: set_get(self :: PARAM_ACTIVE, 0);
					break;
				case self :: PARAM_ACTIVATE_SELECTED_EVALUATION_FORMAT :
					$this->set_action(self :: ACTION_CHANGE_FORMAT_ACTIVE_PROPERTY);
					Request :: set_get(self :: PARAM_EVALUATION_FORMAT_ID, $selected_ids);
					Request :: set_get(self :: PARAM_ACTIVE, 1);
					break;
			}
		}
	}
//--------------------------------------END IGNORE-------------------------------------------------------------
// Data retrieval
// **************
// evaluation formats
	function count_evaluation_formats()
	{
		return GradebookDataManager :: get_instance()->count_evaluation_formats();
	}
	
	function retrieve_evaluation_formats($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return GradebookDataManager :: get_instance()->retrieve_evaluation_formats($condition, $offset, $count, $order_property);
	}
    
	function retrieve_evaluation_format($id)
	{
		return GradebookDataManager :: get_instance()->retrieve_evaluation_format($id);
	}
// applications
	function retrieve_applications_with_evaluations()
	{
		return GradebookDataManager :: get_instance()->retrieve_applications_with_evaluations();
	}
	
	function retrieve_calculated_applications_with_evaluation($calculated_applications = array())
	{
		$internal_item_ids = GradebookDataManager :: get_instance()->retrieve_calculated_internal_items();
		foreach($internal_item_ids as $id)
		{
			$calculated_app[$id] = $this->retrieve_internal_item($id);
		}
		foreach($calculated_app as $id => $internal_item)
		{
			$application_manager = WebApplication :: factory($internal_item->get_application());
			$attributes = $application_manager->get_content_object_publication_attribute($internal_item->get_publication_id());
			$rdm = RepositoryDataManager :: get_instance();
			$content_object = $rdm->retrieve_content_object($attributes->get_publication_object_id());
			if($user = GradebookUtilities :: check_tracker_for_data($internal_item->get_application(), $internal_item->get_publication_id(), $content_object->get_type()))
			{
				$calculated_applications[] = $internal_item->get_application();
			}
		}
		return $calculated_applications;
	}
	
// content objects
	function retrieve_content_objects_by_ids($condition, $offset = null, $max_objects = null, $order_by = null)
	{
		return RepositoryDataManager :: get_instance()->retrieve_content_objects($condition, $offset, $count, $order_property);
	}
	
	function count_content_objects_by_ids($condition)
	{
		return RepositoryDataManager :: get_instance()->count_content_objects($condition);
	}
// internal items
	function retrieve_internal_items_by_application($condition, $offset = null, $max_objects = null, $order_by = null)
	{
		return GradebookDataManager :: get_instance()->retrieve_internal_items_by_application($condition, $offset, $count, $order_property);
	}
	
	function retrieve_internal_item($id)
	{
		return GradebookDataManager :: get_instance()->retrieve_internal_item($id);
	}
	
	function count_internal_items_by_application($condition)
	{
		return GradebookDataManager :: get_instance()->count_internal_items_by_application($condition);
	}
// evaluations
	function retrieve_all_evaluations_on_publication($application, $publication_id, $offset = null, $max_objects = null, $order_by = null)
	{
		return GradebookDataManager :: get_instance()->retrieve_all_evaluations_on_publication($application, $publication_id, $offset, $max_objects, $order_by);
	}
// URL creation
//***************
	function get_admin_browse_evaluation_format_types_link()
	{
		return $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_EVALUATION_FORMATS));
	}
	
	function get_evaluation_format_editing_url($evaluation_format)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_EVALUATION_FORMAT, self :: PARAM_EVALUATION_FORMAT => $evaluation_format->get_id()));
	}
	
	function get_change_evaluation_format_activation_url($evaluation_format)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CHANGE_FORMAT_ACTIVE_PROPERTY, self :: PARAM_EVALUATION_FORMAT => $evaluation_format->get_id()));
	}
	
	function get_evaluation_format_deleting_url()
	{
		return $this->get_url();
	}
	
	function get_evaluations_on_publications_viewer_url($internal_item)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_EVALUATIONS_ON_PUBLICATION, self :: PARAM_PUBLICATION_TYPE => $internal_item->get_application(), self :: PARAM_PUBLICATION_ID => $internal_item->get_publication_id()));
	}
	
	function get_publications_by_type_viewer_url($the_application)
	{
		return $this->get_url(array(GradebookManager :: PARAM_PUBLICATION_TYPE => $the_application));
	}

    function get_export_publication_url($publication_id)
    {
//        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXPORT_PUBLICATION, self :: PARAM_PUBLICATION_ID => $publication_id));
    }
}
?>