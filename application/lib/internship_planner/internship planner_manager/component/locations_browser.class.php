<?php
/**
 * @package application.internship planner.internship planner.component
 */

require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * internship planner component which allows the user to browse his locations
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerLocationsBrowserComponent extends Internship plannerManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseLocations')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_location_url() . '">' . Translation :: get('CreateLocation') . '</a>';
		echo '<br /><br />';

		$locations = $this->retrieve_locations();
		while($location = $locations->next_result())
		{
			echo '<div style="border: 1px solid grey; padding: 5px;">';
			dump($location);
			echo '<br /><a href="' . $this->get_update_location_url($location). '">' . Translation :: get('UpdateLocation') . '</a>';
			echo ' | <a href="' . $this->get_delete_location_url($location) . '">' . Translation :: get('DeleteLocation') . '</a>';
			echo '</div><br /><br />';
		}

		$this->display_footer();
	}

}
?>