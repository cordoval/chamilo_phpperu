<?php
/**
 * @package application.internship planner.internship planner.component
 */

require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * internship planner component which allows the user to browse his location_groups
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerLocationGroupsBrowserComponent extends Internship plannerManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseLocationGroups')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_location_group_url() . '">' . Translation :: get('CreateLocationGroup') . '</a>';
		echo '<br /><br />';

		$location_groups = $this->retrieve_location_groups();
		while($location_group = $location_groups->next_result())
		{
			echo '<div style="border: 1px solid grey; padding: 5px;">';
			dump($location_group);
			echo '<br /><a href="' . $this->get_update_location_group_url($location_group). '">' . Translation :: get('UpdateLocationGroup') . '</a>';
			echo ' | <a href="' . $this->get_delete_location_group_url($location_group) . '">' . Translation :: get('DeleteLocationGroup') . '</a>';
			echo '</div><br /><br />';
		}

		$this->display_footer();
	}

}
?>