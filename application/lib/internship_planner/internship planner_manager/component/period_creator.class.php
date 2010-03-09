<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/period_form.class.php';

/**
 * Component to create a new period object
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerPeriodCreatorComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_PERIODS)), Translation :: get('BrowsePeriods')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreatePeriod')));

		$period = new Period();
		$form = new PeriodForm(PeriodForm :: TYPE_CREATE, $period, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_period();
			$this->redirect($success ? Translation :: get('PeriodCreated') : Translation :: get('PeriodNotCreated'), !$success, array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_PERIODS));
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