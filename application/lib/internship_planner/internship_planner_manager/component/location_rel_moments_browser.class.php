<?php
/**
 * @package application.internship_planner.internship_planner.component
 */

require_once dirname(__FILE__).'/../internship_planner_manager.class.php';
require_once dirname(__FILE__).'/../internship_planner_manager_component.class.php';

/**
 * internship_planner component which allows the user to browse his location_rel_moments
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerManagerLocationRelMomentsBrowserComponent extends InternshipPlannerManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternshipPlanner')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseLocationRelMoments')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_location_rel_moment_url() . '">' . Translation :: get('CreateLocationRelMoment') . '</a>';
		echo '<br /><br />';

		$location_rel_moments = $this->retrieve_location_rel_moments();
		while($location_rel_moment = $location_rel_moments->next_result())
		{
			echo '<div style="border: 1px solid grey; padding: 5px;">';
			dump($location_rel_moment);
			echo '<br /><a href="' . $this->get_update_location_rel_moment_url($location_rel_moment). '">' . Translation :: get('UpdateLocationRelMoment') . '</a>';
			echo ' | <a href="' . $this->get_delete_location_rel_moment_url($location_rel_moment) . '">' . Translation :: get('DeleteLocationRelMoment') . '</a>';
			echo '</div><br /><br />';
		}

		$this->display_footer();
	}

}
?>