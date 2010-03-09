<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/place_form.class.php';

/**
 * Component to edit an existing place object
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerPlaceUpdaterComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_PLACES)), Translation :: get('BrowsePlaces')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdatePlace')));

		$place = $this->retrieve_place(Request :: get(Internship plannerManager :: PARAM_PLACE));
		$form = new PlaceForm(PlaceForm :: TYPE_EDIT, $place, $this->get_url(array(Internship plannerManager :: PARAM_PLACE => $place->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_place();
			$this->redirect($success ? Translation :: get('PlaceUpdated') : Translation :: get('PlaceNotUpdated'), !$success, array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_PLACES));
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