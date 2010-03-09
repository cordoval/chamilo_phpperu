<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once dirname(__FILE__).'/../internship_planner_manager.class.php';
require_once dirname(__FILE__).'/../internship_planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/place_form.class.php';

/**
 * Component to edit an existing place object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerManagerPlaceUpdaterComponent extends InternshipPlannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternshipPlanner')));
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_PLACES)), Translation :: get('BrowsePlaces')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdatePlace')));

		$place = $this->retrieve_place(Request :: get(InternshipPlannerManager :: PARAM_PLACE));
		$form = new PlaceForm(PlaceForm :: TYPE_EDIT, $place, $this->get_url(array(InternshipPlannerManager :: PARAM_PLACE => $place->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_place();
			$this->redirect($success ? Translation :: get('PlaceUpdated') : Translation :: get('PlaceNotUpdated'), !$success, array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_PLACES));
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