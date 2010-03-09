<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/period_form.class.php';

/**
 * Component to edit an existing period object
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerPeriodUpdaterComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_PERIODS)), Translation :: get('BrowsePeriods')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdatePeriod')));

		$period = $this->retrieve_period(Request :: get(Internship plannerManager :: PARAM_PERIOD));
		$form = new PeriodForm(PeriodForm :: TYPE_EDIT, $period, $this->get_url(array(Internship plannerManager :: PARAM_PERIOD => $period->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_period();
			$this->redirect($success ? Translation :: get('PeriodUpdated') : Translation :: get('PeriodNotUpdated'), !$success, array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_PERIODS));
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