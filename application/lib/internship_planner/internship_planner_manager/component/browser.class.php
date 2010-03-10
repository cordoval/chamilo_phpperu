<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once dirname(__FILE__).'/../internship_planner_manager.class.php';
require_once dirname(__FILE__).'/../internship_planner_manager_component.class.php';

/**
 * InternshipPlanner component which allows the user to browse the internship_planner application
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerManagerBrowserComponent extends InternshipPlannerManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseInternshipPlanner')));

		$this->display_header($trail);

		echo '<br /><a href="' . $this->get_browse_categories_url() . '">' . Translation :: get('BrowseCategories') . '</a>';
		echo '<br /><a href="' . $this->get_browse_locations_url() . '">' . Translation :: get('BrowseLocations') . '</a>';
		echo '<br /><a href="' . $this->get_browse_location_groups_url() . '">' . Translation :: get('BrowseLocationGroups') . '</a>';
		echo '<br /><a href="' . $this->get_browse_location_rel_categories_url() . '">' . Translation :: get('BrowseLocationRelCategories') . '</a>';
		echo '<br /><a href="' . $this->get_browse_location_rel_mentors_url() . '">' . Translation :: get('BrowseLocationRelMentors') . '</a>';
		echo '<br /><a href="' . $this->get_browse_location_rel_moments_url() . '">' . Translation :: get('BrowseLocationRelMoments') . '</a>';
		echo '<br /><a href="' . $this->get_browse_location_rel_types_url() . '">' . Translation :: get('BrowseLocationRelTypes') . '</a>';
		echo '<br /><a href="' . $this->get_browse_mentors_url() . '">' . Translation :: get('BrowseMentors') . '</a>';
		echo '<br /><a href="' . $this->get_browse_moments_url() . '">' . Translation :: get('BrowseMoments') . '</a>';
		echo '<br /><a href="' . $this->get_browse_periods_url() . '">' . Translation :: get('BrowsePeriods') . '</a>';
		echo '<br /><a href="' . $this->get_browse_places_url() . '">' . Translation :: get('BrowsePlaces') . '</a>';

		$this->display_footer();
	}

}
?>