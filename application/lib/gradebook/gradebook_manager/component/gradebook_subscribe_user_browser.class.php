<?php
require_once dirname(__FILE__).'/gradebook_subscribe_user_browser/gradebook_subscribe_user_browser_table.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';


class GradebookManagerGradebookSubscribeUserBrowserComponent extends GradebookManagerComponent
{

	private $ab;
	private $gradebook;

	function run()
	{
		if (!GradebookRights :: is_allowed(GradebookRights :: ADD_RIGHT, GradebookRights :: LOCATION_BROWSER, 'gradebook_component'))
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}
			
		$gradebook_id = $_GET[GradebookManager :: PARAM_GRADEBOOK_ID];
		$this->gradebook = $this->retrieve_gradebook($gradebook_id);

		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME)), Translation :: get('GradeBook')));
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_BROWSE_GRADEBOOK)), Translation :: get('BrowseGradeBook')));
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_GRADEBOOK, GradebookManager :: PARAM_GRADEBOOK_ID => $gradebook_id)), $this->gradebook->get_name()));
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_SUBSCRIBE_USER_BROWSER, GradebookManager :: PARAM_GRADEBOOK_ID => $gradebook_id)),Translation :: get('AddUsersTo').' '.$this->gradebook->get_name()));

		$this->ab = $this->get_action_bar();

		$this->display_header($trail, false);
		echo $this->ab->as_html();
		echo $this->get_table();
		$this->display_footer();

	}


	private function get_table(){
		$parameters = $this->get_parameters(true);
		$parameters[GradebookManager :: PARAM_ACTION]=  GradebookManager :: ACTION_SUBSCRIBE_USER_BROWSER;
		$parameters[GradebookManager ::  PARAM_GRADEBOOK_ID] = $this->gradebook->get_id();
		$table = new GradebookSubscribeUserBrowserTable($this, $parameters, $this->get_condition());
		$html = array();
		$html[] = '<div style="float: right; width: 100%;">';
		$html[] = $table->as_html();
		$html[] = '</div>';
		return implode("\n", $html);
	}


	function get_condition()
	{
		$query = $this->ab->get_query();
			
			
		if(isset($query) && $query != '')
		{
			$or_conditions[] = new LikeCondition(User :: PROPERTY_FIRSTNAME, $query);
			$or_conditions[] = new LikeCondition(User :: PROPERTY_LASTNAME, $query);
			$or_conditions[] = new LikeCondition(User :: PROPERTY_USERNAME, $query);
			$search_conditions = new OrCondition($or_conditions);
		}

		$condition = null;
		if (isset($this->firstletter))
		{
			$conditions = array();
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, $this->firstletter. '*');
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, chr(ord($this->firstletter)+1). '*');
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, chr(ord($this->firstletter)+2). '*');
			$condition = new OrCondition($conditions);
			if (count($search_conditions))
			{
				$condition = new AndCondition($condition, $search_conditions);
			}
		}
		else
		{
			if (count($search_conditions))
			{
				$condition = $search_conditions;
			}
		}

		return $condition;
	}

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		$action_bar->set_search_url($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_SUBSCRIBE_USER_BROWSER, GradebookManager :: PARAM_GRADEBOOK_ID => $this->gradebook->get_id())));

		return $action_bar;
	}
}
?>