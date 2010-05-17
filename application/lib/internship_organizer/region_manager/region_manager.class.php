<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/region_manager/component/browser/browser_table.class.php';

require_once dirname(__FILE__) . '/../region_menu.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/region.class.php';

class InternshipOrganizerRegionManager extends SubManager
{

	const PARAM_ACTION = 'action';

	const PARAM_REGION_ID = 'region_id';
	const PARAM_PARENT_REGION_ID = 'parent_id';
	const PARAM_REMOVE_SELECTED = 'delete';
	const PARAM_TRUNCATE_SELECTED = 'truncate';

	const ACTION_CREATE_REGION = 'create';
	const ACTION_BROWSE_REGIONS = 'browse';
	const ACTION_EDIT_REGION = 'edit';
	const ACTION_DELETE_REGION = 'delete';
	const ACTION_VIEW_REGION = 'view';

	function InternshipOrganizerRegionManager($internship_manager)
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

			case self :: ACTION_CREATE_REGION :
				$component = $this->create_component('Creator');
				break;
			case self :: ACTION_EDIT_REGION :
				$component = $this->create_component('Editor');
				break;
			case self :: ACTION_DELETE_REGION :
				$component = $this->create_component('Deleter');
				break;
			case self :: ACTION_VIEW_REGION :
				$component = $this->create_component('Viewer');
				break;
			case self :: ACTION_BROWSE_REGIONS :
				$component = $this->create_component('Browser');
				break;
			default :
				$this->set_region_action(self :: ACTION_BROWSE_REGIONS);
				$component = $this->create_component('Browser');
		}

		$component->run();
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


	function get_browse_regions_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_REGIONS));
				
		//return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_REGIONS));
	}

	function get_region_editing_url($region)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_REGION, self :: PARAM_REGION_ID => $region->get_id()));
	}


	function get_region_create_url($parent_id = null)
	{
		if($parent_id != null){
			return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_REGION, self :: PARAM_PARENT_REGION_ID => $parent_id));
		}else{
			return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_REGION));
		}
	}

	function get_region_emptying_url($region)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_TRUNCATE_REGION, self :: PARAM_REGION_ID => $region->get_id()));
	}

	function get_region_viewing_url($region)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_REGION, self :: PARAM_REGION_ID => $region->get_id(), self :: PARAM_PARENT_REGION_ID => $region->get_parent_id()));
	}

	function get_region_delete_url($region)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_REGION, self :: PARAM_REGION_ID => $region->get_id(), self :: PARAM_PARENT_REGION_ID => $region->get_parent_id()));
	}

	private function parse_input_from_table()
	{
		if (isset($_POST[InternshipOrganizerRegionBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX]))
		{
			$selected_ids = $_POST[InternshipOrganizerRegionBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
		}
		 
		 
		if (isset($_POST['action']))
		{
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
				case self :: PARAM_REMOVE_SELECTED :
					$this->set_region_action(self :: ACTION_DELETE_REGION);
					Request :: set_get(self :: PARAM_REGION_ID, $selected_ids);
					break;
			}
		}
	}

	private function set_region_action($action)
	{
		$this->set_parameter(self :: PARAM_ACTION, $action);
	}
}

?>