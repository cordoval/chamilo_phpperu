<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once dirname(__FILE__).'/../internship_planner_manager.class.php';
require_once dirname(__FILE__).'/../internship_planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/moment_form.class.php';

/**
 * Component to create a new moment object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerManagerMomentCreatorComponent extends InternshipPlannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternshipPlanner')));
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_MOMENTS)), Translation :: get('BrowseMoments')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateMoment')));

		$moment = new Moment();
		$form = new MomentForm(MomentForm :: TYPE_CREATE, $moment, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_moment();
			$this->redirect($success ? Translation :: get('MomentCreated') : Translation :: get('MomentNotCreated'), !$success, array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_MOMENTS));
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