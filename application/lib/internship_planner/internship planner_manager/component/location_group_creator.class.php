<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/location_group_form.class.php';

/**
 * Component to create a new location_group object
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerLocationGroupCreatorComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATION_GROUPS)), Translation :: get('BrowseLocationGroups')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateLocationGroup')));

		$location_group = new LocationGroup();
		$form = new LocationGroupForm(LocationGroupForm :: TYPE_CREATE, $location_group, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_location_group();
			$this->redirect($success ? Translation :: get('LocationGroupCreated') : Translation :: get('LocationGroupNotCreated'), !$success, array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATION_GROUPS));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>