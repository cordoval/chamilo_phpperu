<?php
/**
 * @package application.internship planner.internship planner.component
 */

require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * internship planner component which allows the user to browse his periods
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerPeriodsBrowserComponent extends Internship plannerManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowsePeriods')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_period_url() . '">' . Translation :: get('CreatePeriod') . '</a>';
		echo '<br /><br />';

		$periods = $this->retrieve_periods();
		while($period = $periods->next_result())
		{
			echo '<div style="border: 1px solid grey; padding: 5px;">';
			dump($period);
			echo '<br /><a href="' . $this->get_update_period_url($period). '">' . Translation :: get('UpdatePeriod') . '</a>';
			echo ' | <a href="' . $this->get_delete_period_url($period) . '">' . Translation :: get('DeletePeriod') . '</a>';
			echo '</div><br /><br />';
		}

		$this->display_footer();
	}

}
?>