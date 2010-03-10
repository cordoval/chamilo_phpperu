<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once dirname(__FILE__).'/../internship_planner_manager.class.php';
require_once dirname(__FILE__).'/../internship_planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/period_form.class.php';

/**
 * Component to edit an existing period object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerManagerPeriodUpdaterComponent extends InternshipPlannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternshipPlanner')));
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_PERIODS)), Translation :: get('BrowsePeriods')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdatePeriod')));

		$period = $this->retrieve_period(Request :: get(InternshipPlannerManager :: PARAM_PERIOD));
		$form = new PeriodForm(PeriodForm :: TYPE_EDIT, $period, $this->get_url(array(InternshipPlannerManager :: PARAM_PERIOD => $period->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_period();
			$this->redirect($success ? Translation :: get('PeriodUpdated') : Translation :: get('PeriodNotUpdated'), !$success, array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_PERIODS));
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