<?php
/**
 * @package application.internship planner.internship planner.component
 */

require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * internship planner component which allows the user to browse his location_rel_mentors
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerLocationRelMentorsBrowserComponent extends Internship plannerManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseLocationRelMentors')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_location_rel_mentor_url() . '">' . Translation :: get('CreateLocationRelMentor') . '</a>';
		echo '<br /><br />';

		$location_rel_mentors = $this->retrieve_location_rel_mentors();
		while($location_rel_mentor = $location_rel_mentors->next_result())
		{
			echo '<div style="border: 1px solid grey; padding: 5px;">';
			dump($location_rel_mentor);
			echo '<br /><a href="' . $this->get_update_location_rel_mentor_url($location_rel_mentor). '">' . Translation :: get('UpdateLocationRelMentor') . '</a>';
			echo ' | <a href="' . $this->get_delete_location_rel_mentor_url($location_rel_mentor) . '">' . Translation :: get('DeleteLocationRelMentor') . '</a>';
			echo '</div><br /><br />';
		}

		$this->display_footer();
	}

}
?>