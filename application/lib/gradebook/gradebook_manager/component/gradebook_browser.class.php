<?php
//require_once dirname(__FILE__).'/gradebook_browser/gradebook_browser_table.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class GradebookManagerGradebookBrowserComponent extends GradebookManagerComponent
{
	private $ab;

	function run()
	{
		if (!GradebookRights :: is_allowed(GradebookRights :: VIEW_RIGHT, GradebookRights :: LOCATION_BROWSER, 'gradebook_component'))
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME)), Translation :: get('GradeBook')));
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION=> GradebookManager :: ACTION_BROWSE_GRADEBOOK)), Translation :: get('BrowseGradeBook')));

		$this->display_header($trail);
		$this->ab = $this->get_action_bar();
		echo $this->get_browser_html();
		$this->display_footer();
	}

	function get_browser_html(){
		$html = array();
		//$html[] = GradebookUtilities :: get_gradebook_admin_menu($this);
		$html[] = '<div id="tool_browser_right">';
		$html[] = '<div>';
		$html[] = $this->ab->as_html() . '<br />';
		//$html[] = $this->get_table_html();
		$html[] = '<div style="clear: both;"></div>';
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n", $html);
	}

	function get_table_html()
	{

		$parameters = $this->get_parameters();
		$parameters[GradebookManager :: PARAM_ACTION]=  GradebookManager :: ACTION_BROWSE_GRADEBOOK;

		$table = new GradebookBrowserTable($this, $parameters, $this->get_condition());


		$html = array();
		//$html[] = '<div style="float: right; width: 80%;">';
		$html[] = $table->as_html();
		//$html[] = '</div>';

		return implode($html, "\n");
	}


	function get_condition()
	{

		$condition = new EqualityCondition(Gradebook :: PROPERTY_OWNER_ID, $this->get_user_id());

		$query = $this->ab->get_query();
		if(isset($query) && $query != '')
		{
			$or_conditions = array();
			$or_conditions[] = new PatternMatchCondition(Gradebook :: PROPERTY_NAME, '*' . $query . '*');
			$or_conditions[] = new PatternMatchCondition(Gradebook :: PROPERTY_DESCRIPTION, '*' . $query . '*');
			$or_condition = new OrCondition($or_conditions);

			$and_conditions = array();
			$and_conditions[] = $condition;
			$and_conditions[] = $or_condition;
			$condition = new AndCondition($and_conditions);
		}

		return $condition;
	}

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		$action_bar->set_search_url($this->get_url());
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path().'action_add.png', $this->get_create_gradebook_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		return $action_bar;
	}
}
?>