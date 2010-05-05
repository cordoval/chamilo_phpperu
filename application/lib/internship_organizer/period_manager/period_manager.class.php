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

	const ACTION_CREATE_PERIOD = 'create';
	const ACTION_BROWSE_PERIODS = 'browse';
	const ACTION_EDIT_PERIOD = 'edit';
	const ACTION_DELETE_PERIOD = 'delete';
	const ACTION_VIEW_PERIOD = 'view';

	function InternshipOrganizerPeriodManager($internship_manager)
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
			default :
				$this->set_period_action(self :: ACTION_BROWSE_PERIODS);
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
	//url


	function get_browse_periods_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS));
	}

	function get_period_editing_url($period)
	{

		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_PERIOD, self :: PARAM_PERIOD_ID => $period->get_id()));
	}


	function get_period_create_url($parent_id = null)
	{
		if($parent_id != null){
			return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_PERIOD, self :: PARAM_PERIOD_ID => $parent_id));
		}else{
			return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_PERIOD));
		}
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

	private function parse_input_from_table()
	{
		if (isset($_POST[InternshipOrganizerPeriodBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX]))
		{
			$selected_ids = $_POST[InternshipOrganizerPeriodBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
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
					$this->set_period_action(self :: ACTION_DELETE_PERIOD);
					Request :: set_get(self :: PARAM_PERIOD_ID, $selected_ids);
					break;
			}
		}
	}

	private function set_period_action($action)
	{
		$this->set_parameter(self :: PARAM_ACTION, $action);
	}
}

?>