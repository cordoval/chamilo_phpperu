<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/place_form.class.php';

/**
 * Component to create a new place object
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerPlaceCreatorComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_PLACES)), Translation :: get('BrowsePlaces')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreatePlace')));

		$place = new Place();
		$form = new PlaceForm(PlaceForm :: TYPE_CREATE, $place, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_place();
			$this->redirect($success ? Translation :: get('PlaceCreated') : Translation :: get('PlaceNotCreated'), !$success, array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_PLACES));
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