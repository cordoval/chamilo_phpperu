<?php
/**
 * @package application.internship planner.internship planner.component
 */

require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * internship planner component which allows the user to browse his places
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerPlacesBrowserComponent extends Internship plannerManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowsePlaces')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_place_url() . '">' . Translation :: get('CreatePlace') . '</a>';
		echo '<br /><br />';

		$places = $this->retrieve_places();
		while($place = $places->next_result())
		{
			echo '<div style="border: 1px solid grey; padding: 5px;">';
			dump($place);
			echo '<br /><a href="' . $this->get_update_place_url($place). '">' . Translation :: get('UpdatePlace') . '</a>';
			echo ' | <a href="' . $this->get_delete_place_url($place) . '">' . Translation :: get('DeletePlace') . '</a>';
			echo '</div><br /><br />';
		}

		$this->display_footer();
	}

}
?>