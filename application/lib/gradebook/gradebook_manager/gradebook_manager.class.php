<?php
//require_once dirname(__FILE__).'/../../web_application.class.php';
require_once dirname(__FILE__).'/gradebook_manager_component.class.php';
require_once dirname(__FILE__).'/../gradebook_data_manager.class.php';
require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';
require_once Path :: get_application_path() . 'lib/gradebook/gradebook_rights.class.php';
/*require_once dirname(__FILE__).'/component/gradebook_browser/gradebook_browser_table.class.php';
require_once dirname(__FILE__).'/component/gradebook_subscribe_user_browser/gradebook_subscribe_user_browser_table.class.php';
require_once dirname(__FILE__).'/component/gradebook_rel_user_browser/gradebook_rel_user_browser_table.class.php'*/;

//require_once dirname(__FILE__).'/../gradebook_utilities.class.php';

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
	/*
	 * Gradebook parameters
	 */
	const PARAM_ACTIVATE_SELECTED_EVALUATION_FORMAT = 'activate_selected_evaluation_format';
	const PARAM_DEACTIVATE_SELECTED_EVALUATION_FORMAT = 'deactivate_selected_evaluation_format';
	const PARAM_EVALUATION_FORMAT = 'evaluation_format';
	
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
				$component = GradebookManagerComponent :: factory('AdminEvaluationFormatsBrowser', $this);
				break;	
			case self :: ACTION_CHANGE_FORMAT_ACTIVE_PROPERTY :
				$component = GradebookManagerComponent :: factory('AdminActiveChanger', $this);
				break;	
			case self :: ACTION_EDIT_EVALUATION_FORMAT :
				$component = GradebookManagerComponent :: factory('AdminEditEvaluationFormat', $this);
				break;		
			default :
				$this->set_action(self :: ACTION_VIEW_HOME);
				$component = GradebookManagerComponent :: factory('GradebookBrowser', $this);
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
			if(isset($_POST[GradebookBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX])){ 
				$selected_ids = $_POST[GradebookBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];
			}
			
			if(isset($_POST[GradebookSubscribeUserBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX])){
				$selected_ids = $_POST[GradebookSubscribeUserBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];
			}
			if(isset($_POST[GradebookRelUserBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX])){
				$selected_ids = $_POST[GradebookRelUserBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];
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
				case self :: PARAM_REMOVE_SELECTED :
					$this->set_action(self :: ACTION_DELETE_GRADEBOOK);
					$_GET[self :: PARAM_GRADEBOOK_ID] = $selected_ids;
					break;
				case self :: PARAM_UNSUBSCRIBE_SELECTED :
					$this->set_action(self :: ACTION_UNSUBSCRIBE_USER_FROM_GRADEBOOK);
					$_GET[self :: PARAM_GRADEBOOK_REL_USER_ID] = $selected_ids;
					break;
				case self :: PARAM_SUBSCRIBE_SELECTED :
					$this->set_action(self :: ACTION_SUBSCRIBE_USER_TO_GRADEBOOK);
					$_GET[self :: PARAM_USER_ID] = $selected_ids;
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
	
	function retrieve_evaluation_formats()
	{
		return GradebookDataManager :: get_instance()->retrieve_evaluation_formats();
	}
    
	function retrieve_evaluation_format($id)
	{
		return GradebookDataManager :: get_instance()->retrieve_evaluation_format($id);
	}
// internal items
	function retrieve_internal_item_by_publication($application, $publication_id)
	{
		return GradebookDataManager :: get_instance()->retrieve_internal_item_by_publication($application, $publication_id);
	}
// URL creation
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
}
?>