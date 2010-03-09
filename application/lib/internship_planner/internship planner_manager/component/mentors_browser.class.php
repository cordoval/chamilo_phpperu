<?php
/**
 * @package application.internship planner.internship planner.component
 */

require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * internship planner component which allows the user to browse his mentors
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerMentorsBrowserComponent extends Internship plannerManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseMentors')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_mentor_url() . '">' . Translation :: get('CreateMentor') . '</a>';
		echo '<br /><br />';

		$mentors = $this->retrieve_mentors();
		while($mentor = $mentors->next_result())
		{
			echo '<div style="border: 1px solid grey; padding: 5px;">';
			dump($mentor);
			echo '<br /><a href="' . $this->get_update_mentor_url($mentor). '">' . Translation :: get('UpdateMentor') . '</a>';
			echo ' | <a href="' . $this->get_delete_mentor_url($mentor) . '">' . Translation :: get('DeleteMentor') . '</a>';
			echo '</div><br /><br />';
		}

		$this->display_footer();
	}

}
?>