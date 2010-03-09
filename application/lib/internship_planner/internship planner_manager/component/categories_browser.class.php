<?php
/**
 * @package application.internship planner.internship planner.component
 */

require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * internship planner component which allows the user to browse his categories
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerCategoriesBrowserComponent extends Internship plannerManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseCategories')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_category_url() . '">' . Translation :: get('CreateCategory') . '</a>';
		echo '<br /><br />';

		$categories = $this->retrieve_categories();
		while($category = $categories->next_result())
		{
			echo '<div style="border: 1px solid grey; padding: 5px;">';
			dump($category);
			echo '<br /><a href="' . $this->get_update_category_url($category). '">' . Translation :: get('UpdateCategory') . '</a>';
			echo ' | <a href="' . $this->get_delete_category_url($category) . '">' . Translation :: get('DeleteCategory') . '</a>';
			echo '</div><br /><br />';
		}

		$this->display_footer();
	}

}
?>