<?php
/**
 * @package application.internship planner.internship planner.component
 */

require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * internship planner component which allows the user to browse his location_rel_categories
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerLocationRelCategoriesBrowserComponent extends Internship plannerManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseLocationRelCategories')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_location_rel_category_url() . '">' . Translation :: get('CreateLocationRelCategory') . '</a>';
		echo '<br /><br />';

		$location_rel_categories = $this->retrieve_location_rel_categories();
		while($location_rel_category = $location_rel_categories->next_result())
		{
			echo '<div style="border: 1px solid grey; padding: 5px;">';
			dump($location_rel_category);
			echo '<br /><a href="' . $this->get_update_location_rel_category_url($location_rel_category). '">' . Translation :: get('UpdateLocationRelCategory') . '</a>';
			echo ' | <a href="' . $this->get_delete_location_rel_category_url($location_rel_category) . '">' . Translation :: get('DeleteLocationRelCategory') . '</a>';
			echo '</div><br /><br />';
		}

		$this->display_footer();
	}

}
?>