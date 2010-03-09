<?php
/**
 * @package application.lib.internship_planner.internship_planner_manager
 */
require_once dirname(__FILE__).'/internship_planner_manager_component.class.php';
require_once dirname(__FILE__).'/../internship_planner_data_manager.class.php';
require_once dirname(__FILE__).'/component/category_browser/category_browser_table.class.php';
require_once dirname(__FILE__).'/component/location_browser/location_browser_table.class.php';
require_once dirname(__FILE__).'/component/location_group_browser/location_group_browser_table.class.php';
require_once dirname(__FILE__).'/component/location_rel_category_browser/location_rel_category_browser_table.class.php';
require_once dirname(__FILE__).'/component/location_rel_mentor_browser/location_rel_mentor_browser_table.class.php';
require_once dirname(__FILE__).'/component/location_rel_moment_browser/location_rel_moment_browser_table.class.php';
require_once dirname(__FILE__).'/component/location_rel_type_browser/location_rel_type_browser_table.class.php';
require_once dirname(__FILE__).'/component/mentor_browser/mentor_browser_table.class.php';
require_once dirname(__FILE__).'/component/moment_browser/moment_browser_table.class.php';
require_once dirname(__FILE__).'/component/period_browser/period_browser_table.class.php';
require_once dirname(__FILE__).'/component/place_browser/place_browser_table.class.php';

/**
 * A internship_planner manager
 *
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
 class InternshipPlannerManager extends WebApplication
 {
 	const APPLICATION_NAME = 'internship_planner';

	const PARAM_CATEGORY = 'category';
	const PARAM_DELETE_SELECTED_CATEGORIES = 'delete_selected_categories';

	const ACTION_DELETE_CATEGORY = 'delete_category';
	const ACTION_EDIT_CATEGORY = 'edit_category';
	const ACTION_CREATE_CATEGORY = 'create_category';
	const ACTION_BROWSE_CATEGORIES = 'browse_categories';

	const PARAM_LOCATION = 'location';
	const PARAM_DELETE_SELECTED_LOCATIONS = 'delete_selected_locations';

	const ACTION_DELETE_LOCATION = 'delete_location';
	const ACTION_EDIT_LOCATION = 'edit_location';
	const ACTION_CREATE_LOCATION = 'create_location';
	const ACTION_BROWSE_LOCATIONS = 'browse_locations';

	const PARAM_LOCATION_GROUP = 'location_group';
	const PARAM_DELETE_SELECTED_LOCATION_GROUPS = 'delete_selected_location_groups';

	const ACTION_DELETE_LOCATION_GROUP = 'delete_location_group';
	const ACTION_EDIT_LOCATION_GROUP = 'edit_location_group';
	const ACTION_CREATE_LOCATION_GROUP = 'create_location_group';
	const ACTION_BROWSE_LOCATION_GROUPS = 'browse_location_groups';

	const PARAM_LOCATION_REL_CATEGORY = 'location_rel_category';
	const PARAM_DELETE_SELECTED_LOCATION_REL_CATEGORIES = 'delete_selected_location_rel_categories';

	const ACTION_DELETE_LOCATION_REL_CATEGORY = 'delete_location_rel_category';
	const ACTION_EDIT_LOCATION_REL_CATEGORY = 'edit_location_rel_category';
	const ACTION_CREATE_LOCATION_REL_CATEGORY = 'create_location_rel_category';
	const ACTION_BROWSE_LOCATION_REL_CATEGORIES = 'browse_location_rel_categories';

	const PARAM_LOCATION_REL_MENTOR = 'location_rel_mentor';
	const PARAM_DELETE_SELECTED_LOCATION_REL_MENTORS = 'delete_selected_location_rel_mentors';

	const ACTION_DELETE_LOCATION_REL_MENTOR = 'delete_location_rel_mentor';
	const ACTION_EDIT_LOCATION_REL_MENTOR = 'edit_location_rel_mentor';
	const ACTION_CREATE_LOCATION_REL_MENTOR = 'create_location_rel_mentor';
	const ACTION_BROWSE_LOCATION_REL_MENTORS = 'browse_location_rel_mentors';

	const PARAM_LOCATION_REL_MOMENT = 'location_rel_moment';
	const PARAM_DELETE_SELECTED_LOCATION_REL_MOMENTS = 'delete_selected_location_rel_moments';

	const ACTION_DELETE_LOCATION_REL_MOMENT = 'delete_location_rel_moment';
	const ACTION_EDIT_LOCATION_REL_MOMENT = 'edit_location_rel_moment';
	const ACTION_CREATE_LOCATION_REL_MOMENT = 'create_location_rel_moment';
	const ACTION_BROWSE_LOCATION_REL_MOMENTS = 'browse_location_rel_moments';

	const PARAM_LOCATION_REL_TYPE = 'location_rel_type';
	const PARAM_DELETE_SELECTED_LOCATION_REL_TYPES = 'delete_selected_location_rel_types';

	const ACTION_DELETE_LOCATION_REL_TYPE = 'delete_location_rel_type';
	const ACTION_EDIT_LOCATION_REL_TYPE = 'edit_location_rel_type';
	const ACTION_CREATE_LOCATION_REL_TYPE = 'create_location_rel_type';
	const ACTION_BROWSE_LOCATION_REL_TYPES = 'browse_location_rel_types';

	const PARAM_MENTOR = 'mentor';
	const PARAM_DELETE_SELECTED_MENTORS = 'delete_selected_mentors';

	const ACTION_DELETE_MENTOR = 'delete_mentor';
	const ACTION_EDIT_MENTOR = 'edit_mentor';
	const ACTION_CREATE_MENTOR = 'create_mentor';
	const ACTION_BROWSE_MENTORS = 'browse_mentors';

	const PARAM_MOMENT = 'moment';
	const PARAM_DELETE_SELECTED_MOMENTS = 'delete_selected_moments';

	const ACTION_DELETE_MOMENT = 'delete_moment';
	const ACTION_EDIT_MOMENT = 'edit_moment';
	const ACTION_CREATE_MOMENT = 'create_moment';
	const ACTION_BROWSE_MOMENTS = 'browse_moments';

	const PARAM_PERIOD = 'period';
	const PARAM_DELETE_SELECTED_PERIODS = 'delete_selected_periods';

	const ACTION_DELETE_PERIOD = 'delete_period';
	const ACTION_EDIT_PERIOD = 'edit_period';
	const ACTION_CREATE_PERIOD = 'create_period';
	const ACTION_BROWSE_PERIODS = 'browse_periods';

	const PARAM_PLACE = 'place';
	const PARAM_DELETE_SELECTED_PLACES = 'delete_selected_places';

	const ACTION_DELETE_PLACE = 'delete_place';
	const ACTION_EDIT_PLACE = 'edit_place';
	const ACTION_CREATE_PLACE = 'create_place';
	const ACTION_BROWSE_PLACES = 'browse_places';


	const ACTION_BROWSE = 'browse';

	/**
	 * Constructor
	 * @param User $user The current user
	 */
    function InternshipPlannerManager($user = null)
    {
    	parent :: __construct($user);
    	$this->parse_input_from_table();
    }

    /**
	 * Run this internship_planner manager
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_BROWSE_CATEGORIES :
				$component = InternshipPlannerManagerComponent :: factory('CategoriesBrowser', $this);
				break;
			case self :: ACTION_DELETE_CATEGORY :
				$component = InternshipPlannerManagerComponent :: factory('CategoryDeleter', $this);
				break;
			case self :: ACTION_EDIT_CATEGORY :
				$component = InternshipPlannerManagerComponent :: factory('CategoryUpdater', $this);
				break;
			case self :: ACTION_CREATE_CATEGORY :
				$component = InternshipPlannerManagerComponent :: factory('CategoryCreator', $this);
				break;
			case self :: ACTION_BROWSE_LOCATIONS :
				$component = InternshipPlannerManagerComponent :: factory('LocationsBrowser', $this);
				break;
			case self :: ACTION_DELETE_LOCATION :
				$component = InternshipPlannerManagerComponent :: factory('LocationDeleter', $this);
				break;
			case self :: ACTION_EDIT_LOCATION :
				$component = InternshipPlannerManagerComponent :: factory('LocationUpdater', $this);
				break;
			case self :: ACTION_CREATE_LOCATION :
				$component = InternshipPlannerManagerComponent :: factory('LocationCreator', $this);
				break;
			case self :: ACTION_BROWSE_LOCATION_GROUPS :
				$component = InternshipPlannerManagerComponent :: factory('LocationGroupsBrowser', $this);
				break;
			case self :: ACTION_DELETE_LOCATION_GROUP :
				$component = InternshipPlannerManagerComponent :: factory('LocationGroupDeleter', $this);
				break;
			case self :: ACTION_EDIT_LOCATION_GROUP :
				$component = InternshipPlannerManagerComponent :: factory('LocationGroupUpdater', $this);
				break;
			case self :: ACTION_CREATE_LOCATION_GROUP :
				$component = InternshipPlannerManagerComponent :: factory('LocationGroupCreator', $this);
				break;
			case self :: ACTION_BROWSE_LOCATION_REL_CATEGORIES :
				$component = InternshipPlannerManagerComponent :: factory('LocationRelCategoriesBrowser', $this);
				break;
			case self :: ACTION_DELETE_LOCATION_REL_CATEGORY :
				$component = InternshipPlannerManagerComponent :: factory('LocationRelCategoryDeleter', $this);
				break;
			case self :: ACTION_EDIT_LOCATION_REL_CATEGORY :
				$component = InternshipPlannerManagerComponent :: factory('LocationRelCategoryUpdater', $this);
				break;
			case self :: ACTION_CREATE_LOCATION_REL_CATEGORY :
				$component = InternshipPlannerManagerComponent :: factory('LocationRelCategoryCreator', $this);
				break;
			case self :: ACTION_BROWSE_LOCATION_REL_MENTORS :
				$component = InternshipPlannerManagerComponent :: factory('LocationRelMentorsBrowser', $this);
				break;
			case self :: ACTION_DELETE_LOCATION_REL_MENTOR :
				$component = InternshipPlannerManagerComponent :: factory('LocationRelMentorDeleter', $this);
				break;
			case self :: ACTION_EDIT_LOCATION_REL_MENTOR :
				$component = InternshipPlannerManagerComponent :: factory('LocationRelMentorUpdater', $this);
				break;
			case self :: ACTION_CREATE_LOCATION_REL_MENTOR :
				$component = InternshipPlannerManagerComponent :: factory('LocationRelMentorCreator', $this);
				break;
			case self :: ACTION_BROWSE_LOCATION_REL_MOMENTS :
				$component = InternshipPlannerManagerComponent :: factory('LocationRelMomentsBrowser', $this);
				break;
			case self :: ACTION_DELETE_LOCATION_REL_MOMENT :
				$component = InternshipPlannerManagerComponent :: factory('LocationRelMomentDeleter', $this);
				break;
			case self :: ACTION_EDIT_LOCATION_REL_MOMENT :
				$component = InternshipPlannerManagerComponent :: factory('LocationRelMomentUpdater', $this);
				break;
			case self :: ACTION_CREATE_LOCATION_REL_MOMENT :
				$component = InternshipPlannerManagerComponent :: factory('LocationRelMomentCreator', $this);
				break;
			case self :: ACTION_BROWSE_LOCATION_REL_TYPES :
				$component = InternshipPlannerManagerComponent :: factory('LocationRelTypesBrowser', $this);
				break;
			case self :: ACTION_DELETE_LOCATION_REL_TYPE :
				$component = InternshipPlannerManagerComponent :: factory('LocationRelTypeDeleter', $this);
				break;
			case self :: ACTION_EDIT_LOCATION_REL_TYPE :
				$component = InternshipPlannerManagerComponent :: factory('LocationRelTypeUpdater', $this);
				break;
			case self :: ACTION_CREATE_LOCATION_REL_TYPE :
				$component = InternshipPlannerManagerComponent :: factory('LocationRelTypeCreator', $this);
				break;
			case self :: ACTION_BROWSE_MENTORS :
				$component = InternshipPlannerManagerComponent :: factory('MentorsBrowser', $this);
				break;
			case self :: ACTION_DELETE_MENTOR :
				$component = InternshipPlannerManagerComponent :: factory('MentorDeleter', $this);
				break;
			case self :: ACTION_EDIT_MENTOR :
				$component = InternshipPlannerManagerComponent :: factory('MentorUpdater', $this);
				break;
			case self :: ACTION_CREATE_MENTOR :
				$component = InternshipPlannerManagerComponent :: factory('MentorCreator', $this);
				break;
			case self :: ACTION_BROWSE_MOMENTS :
				$component = InternshipPlannerManagerComponent :: factory('MomentsBrowser', $this);
				break;
			case self :: ACTION_DELETE_MOMENT :
				$component = InternshipPlannerManagerComponent :: factory('MomentDeleter', $this);
				break;
			case self :: ACTION_EDIT_MOMENT :
				$component = InternshipPlannerManagerComponent :: factory('MomentUpdater', $this);
				break;
			case self :: ACTION_CREATE_MOMENT :
				$component = InternshipPlannerManagerComponent :: factory('MomentCreator', $this);
				break;
			case self :: ACTION_BROWSE_PERIODS :
				$component = InternshipPlannerManagerComponent :: factory('PeriodsBrowser', $this);
				break;
			case self :: ACTION_DELETE_PERIOD :
				$component = InternshipPlannerManagerComponent :: factory('PeriodDeleter', $this);
				break;
			case self :: ACTION_EDIT_PERIOD :
				$component = InternshipPlannerManagerComponent :: factory('PeriodUpdater', $this);
				break;
			case self :: ACTION_CREATE_PERIOD :
				$component = InternshipPlannerManagerComponent :: factory('PeriodCreator', $this);
				break;
			case self :: ACTION_BROWSE_PLACES :
				$component = InternshipPlannerManagerComponent :: factory('PlacesBrowser', $this);
				break;
			case self :: ACTION_DELETE_PLACE :
				$component = InternshipPlannerManagerComponent :: factory('PlaceDeleter', $this);
				break;
			case self :: ACTION_EDIT_PLACE :
				$component = InternshipPlannerManagerComponent :: factory('PlaceUpdater', $this);
				break;
			case self :: ACTION_CREATE_PLACE :
				$component = InternshipPlannerManagerComponent :: factory('PlaceCreator', $this);
				break;
			case self :: ACTION_BROWSE:
				$component = InternshipPlannerManagerComponent :: factory('Browser', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE);
				$component = InternshipPlannerManagerComponent :: factory('Browser', $this);

		}
		$component->run();
	}

	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			switch ($_POST['action'])
			{
				case self :: PARAM_DELETE_SELECTED_CATEGORIES :

					$selected_ids = $_POST[CategoryBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_CATEGORY);
					$_GET[self :: PARAM_CATEGORY] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_LOCATIONS :

					$selected_ids = $_POST[LocationBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_LOCATION);
					$_GET[self :: PARAM_LOCATION] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_LOCATION_GROUPS :

					$selected_ids = $_POST[LocationGroupBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_LOCATION_GROUP);
					$_GET[self :: PARAM_LOCATION_GROUP] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_LOCATION_REL_CATEGORIES :

					$selected_ids = $_POST[LocationRelCategoryBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_LOCATION_REL_CATEGORY);
					$_GET[self :: PARAM_LOCATION_REL_CATEGORY] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_LOCATION_REL_MENTORS :

					$selected_ids = $_POST[LocationRelMentorBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_LOCATION_REL_MENTOR);
					$_GET[self :: PARAM_LOCATION_REL_MENTOR] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_LOCATION_REL_MOMENTS :

					$selected_ids = $_POST[LocationRelMomentBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_LOCATION_REL_MOMENT);
					$_GET[self :: PARAM_LOCATION_REL_MOMENT] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_LOCATION_REL_TYPES :

					$selected_ids = $_POST[LocationRelTypeBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_LOCATION_REL_TYPE);
					$_GET[self :: PARAM_LOCATION_REL_TYPE] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_MENTORS :

					$selected_ids = $_POST[MentorBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_MENTOR);
					$_GET[self :: PARAM_MENTOR] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_MOMENTS :

					$selected_ids = $_POST[MomentBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_MOMENT);
					$_GET[self :: PARAM_MOMENT] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_PERIODS :

					$selected_ids = $_POST[PeriodBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_PERIOD);
					$_GET[self :: PARAM_PERIOD] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_PLACES :

					$selected_ids = $_POST[PlaceBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_PLACE);
					$_GET[self :: PARAM_PLACE] = $selected_ids;
					break;
			}

		}
	}

	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}

	// Data Retrieving

	function count_categories($condition)
	{
		return InternshipPlannerDataManager :: get_instance()->count_categories($condition);
	}

	function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_categories($condition, $offset, $count, $order_property);
	}

 	function retrieve_category($id)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_category($id);
	}

	function count_locations($condition)
	{
		return InternshipPlannerDataManager :: get_instance()->count_locations($condition);
	}

	function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_locations($condition, $offset, $count, $order_property);
	}

 	function retrieve_location($id)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_location($id);
	}

	function count_location_groups($condition)
	{
		return InternshipPlannerDataManager :: get_instance()->count_location_groups($condition);
	}

	function retrieve_location_groups($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_location_groups($condition, $offset, $count, $order_property);
	}

 	function retrieve_location_group($id)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_location_group($id);
	}

	function count_location_rel_categories($condition)
	{
		return InternshipPlannerDataManager :: get_instance()->count_location_rel_categories($condition);
	}

	function retrieve_location_rel_categories($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_location_rel_categories($condition, $offset, $count, $order_property);
	}

 	function retrieve_location_rel_category($id)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_location_rel_category($id);
	}

	function count_location_rel_mentors($condition)
	{
		return InternshipPlannerDataManager :: get_instance()->count_location_rel_mentors($condition);
	}

	function retrieve_location_rel_mentors($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_location_rel_mentors($condition, $offset, $count, $order_property);
	}

 	function retrieve_location_rel_mentor($id)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_location_rel_mentor($id);
	}

	function count_location_rel_moments($condition)
	{
		return InternshipPlannerDataManager :: get_instance()->count_location_rel_moments($condition);
	}

	function retrieve_location_rel_moments($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_location_rel_moments($condition, $offset, $count, $order_property);
	}

 	function retrieve_location_rel_moment($id)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_location_rel_moment($id);
	}

	function count_location_rel_types($condition)
	{
		return InternshipPlannerDataManager :: get_instance()->count_location_rel_types($condition);
	}

	function retrieve_location_rel_types($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_location_rel_types($condition, $offset, $count, $order_property);
	}

 	function retrieve_location_rel_type($id)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_location_rel_type($id);
	}

	function count_mentors($condition)
	{
		return InternshipPlannerDataManager :: get_instance()->count_mentors($condition);
	}

	function retrieve_mentors($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_mentors($condition, $offset, $count, $order_property);
	}

 	function retrieve_mentor($id)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_mentor($id);
	}

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

	function count_periods($condition)
	{
		return InternshipPlannerDataManager :: get_instance()->count_periods($condition);
	}

	function retrieve_periods($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_periods($condition, $offset, $count, $order_property);
	}

 	function retrieve_period($id)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_period($id);
	}

	function count_places($condition)
	{
		return InternshipPlannerDataManager :: get_instance()->count_places($condition);
	}

	function retrieve_places($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_places($condition, $offset, $count, $order_property);
	}

 	function retrieve_place($id)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_place($id);
	}

	// Url Creation

	function get_create_category_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CATEGORY));
	}

	function get_update_category_url($category)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CATEGORY,
								    self :: PARAM_CATEGORY => $category->get_id()));
	}

 	function get_delete_category_url($category)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CATEGORY,
								    self :: PARAM_CATEGORY => $category->get_id()));
	}

	function get_browse_categories_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CATEGORIES));
	}

	function get_create_location_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_LOCATION));
	}

	function get_update_location_url($location)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_LOCATION,
								    self :: PARAM_LOCATION => $location->get_id()));
	}

 	function get_delete_location_url($location)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_LOCATION,
								    self :: PARAM_LOCATION => $location->get_id()));
	}

	function get_browse_locations_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_LOCATIONS));
	}

	function get_create_location_group_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_LOCATION_GROUP));
	}

	function get_update_location_group_url($location_group)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_LOCATION_GROUP,
								    self :: PARAM_LOCATION_GROUP => $location_group->get_id()));
	}

 	function get_delete_location_group_url($location_group)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_LOCATION_GROUP,
								    self :: PARAM_LOCATION_GROUP => $location_group->get_id()));
	}

	function get_browse_location_groups_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_LOCATION_GROUPS));
	}

	function get_create_location_rel_category_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_LOCATION_REL_CATEGORY));
	}

	function get_update_location_rel_category_url($location_rel_category)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_LOCATION_REL_CATEGORY,
								    self :: PARAM_LOCATION_REL_CATEGORY => $location_rel_category->get_id()));
	}

 	function get_delete_location_rel_category_url($location_rel_category)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_LOCATION_REL_CATEGORY,
								    self :: PARAM_LOCATION_REL_CATEGORY => $location_rel_category->get_id()));
	}

	function get_browse_location_rel_categories_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_LOCATION_REL_CATEGORIES));
	}

	function get_create_location_rel_mentor_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_LOCATION_REL_MENTOR));
	}

	function get_update_location_rel_mentor_url($location_rel_mentor)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_LOCATION_REL_MENTOR,
								    self :: PARAM_LOCATION_REL_MENTOR => $location_rel_mentor->get_id()));
	}

 	function get_delete_location_rel_mentor_url($location_rel_mentor)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_LOCATION_REL_MENTOR,
								    self :: PARAM_LOCATION_REL_MENTOR => $location_rel_mentor->get_id()));
	}

	function get_browse_location_rel_mentors_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_LOCATION_REL_MENTORS));
	}

	function get_create_location_rel_moment_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_LOCATION_REL_MOMENT));
	}

	function get_update_location_rel_moment_url($location_rel_moment)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_LOCATION_REL_MOMENT,
								    self :: PARAM_LOCATION_REL_MOMENT => $location_rel_moment->get_id()));
	}

 	function get_delete_location_rel_moment_url($location_rel_moment)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_LOCATION_REL_MOMENT,
								    self :: PARAM_LOCATION_REL_MOMENT => $location_rel_moment->get_id()));
	}

	function get_browse_location_rel_moments_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_LOCATION_REL_MOMENTS));
	}

	function get_create_location_rel_type_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_LOCATION_REL_TYPE));
	}

	function get_update_location_rel_type_url($location_rel_type)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_LOCATION_REL_TYPE,
								    self :: PARAM_LOCATION_REL_TYPE => $location_rel_type->get_id()));
	}

 	function get_delete_location_rel_type_url($location_rel_type)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_LOCATION_REL_TYPE,
								    self :: PARAM_LOCATION_REL_TYPE => $location_rel_type->get_id()));
	}

	function get_browse_location_rel_types_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_LOCATION_REL_TYPES));
	}

	function get_create_mentor_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_MENTOR));
	}

	function get_update_mentor_url($mentor)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_MENTOR,
								    self :: PARAM_MENTOR => $mentor->get_id()));
	}

 	function get_delete_mentor_url($mentor)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_MENTOR,
								    self :: PARAM_MENTOR => $mentor->get_id()));
	}

	function get_browse_mentors_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_MENTORS));
	}

	function get_create_moment_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_MOMENT));
	}

	function get_update_moment_url($moment)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_MOMENT,
								    self :: PARAM_MOMENT => $moment->get_id()));
	}

 	function get_delete_moment_url($moment)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_MOMENT,
								    self :: PARAM_MOMENT => $moment->get_id()));
	}

	function get_browse_moments_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_MOMENTS));
	}

	function get_create_period_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_PERIOD));
	}

	function get_update_period_url($period)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_PERIOD,
								    self :: PARAM_PERIOD => $period->get_id()));
	}

 	function get_delete_period_url($period)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_PERIOD,
								    self :: PARAM_PERIOD => $period->get_id()));
	}

	function get_browse_periods_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS));
	}

	function get_create_place_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_PLACE));
	}

	function get_update_place_url($place)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_PLACE,
								    self :: PARAM_PLACE => $place->get_id()));
	}

 	function get_delete_place_url($place)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_PLACE,
								    self :: PARAM_PLACE => $place->get_id()));
	}

	function get_browse_places_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PLACES));
	}

	function get_browse_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
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

	function count_publication_attributes($type = null, $condition = null)
	{

	}

	function delete_content_object_publications($object_id)
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
}
?>