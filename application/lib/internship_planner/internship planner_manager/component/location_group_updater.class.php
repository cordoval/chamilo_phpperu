<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/location_group_form.class.php';

/**
 * Component to edit an existing location_group object
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerLocationGroupUpdaterComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATION_GROUPS)), Translation :: get('BrowseLocationGroups')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateLocationGroup')));

		$location_group = $this->retrieve_location_group(Request :: get(Internship plannerManager :: PARAM_LOCATION_GROUP));
		$form = new LocationGroupForm(LocationGroupForm :: TYPE_EDIT, $location_group, $this->get_url(array(Internship plannerManager :: PARAM_LOCATION_GROUP => $location_group->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_location_group();
			$this->redirect($success ? Translation :: get('LocationGroupUpdated') : Translation :: get('LocationGroupNotUpdated'), !$success, array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATION_GROUPS));
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