<?php
/**
 * @package application.internship_planner.internship_planner.component
 */

require_once dirname(__FILE__).'/../internship_planner_manager.class.php';
require_once dirname(__FILE__).'/../internship_planner_manager_component.class.php';

/**
 * internship_planner component which allows the user to browse his moments
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerManagerMomentsBrowserComponent extends InternshipPlannerManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternshipPlanner')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseMoments')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_moment_url() . '">' . Translation :: get('CreateMoment') . '</a>';
		echo '<br /><br />';

		$moments = $this->retrieve_moments();
		while($moment = $moments->next_result())
		{
			echo '<div style="border: 1px solid grey; padding: 5px;">';
			dump($moment);
			echo '<br /><a href="' . $this->get_update_moment_url($moment). '">' . Translation :: get('UpdateMoment') . '</a>';
			echo ' | <a href="' . $this->get_delete_moment_url($moment) . '">' . Translation :: get('DeleteMoment') . '</a>';
			echo '</div><br /><br />';
		}

		$this->display_footer();
	}

}
?>