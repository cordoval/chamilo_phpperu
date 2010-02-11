<?php
require_once dirname(__FILE__).'/../gradebook_manager.class.php';
require_once dirname(__FILE__).'/../gradebook_manager_component.class.php';
require_once dirname(__FILE__).'/../../gradebook_utilities.class.php';
require_once dirname(__FILE__).'/gradebook_browser/gradebook_browser_table.class.php';

class GradebookManagerHomeComponent extends GradebookManagerComponent
{
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('GradeBook')));

		if (!GradebookRights :: is_allowed(GradebookRights :: VIEW_RIGHT, 'home', 'gradebook_component'))
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}

		$this->display_header($trail);
		echo $this->get_gradebook_home();
		$this->display_footer();
	}

	function get_gradebook_home()
	{
		$is_admin = GradebookRights :: is_allowed(GradebookRights :: VIEW_RIGHT, 'browser', 'gradebook_component');
		$is_user = GradebookRights :: is_allowed(GradebookRights :: VIEW_RIGHT, 'viewer', 'gradebook_component');

		if ($is_admin)
		{
			echo $this->get_gradebook_home_admin();
		}
		elseif($is_user)
		{
			echo $this->get_gradebook_home_user();
		}
		else
		{
			$this->display_error_message(Translation :: get('NotAllowed'));
		}
	}

	function get_gradebook_home_user()
	{
		$html = array();
		$user = $this->get_user();
	

		return implode("\n", $html);
	}

	function get_gradebook_home_admin()
	{
		$component = GradebookManagerComponent :: factory('GradebookBrowser', $this);
		return $component->run(); 

	}
	
}
?>