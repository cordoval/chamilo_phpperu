<?php

require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';

class InternshipPlannerAgreementManagerMomentDeleterComponent extends InternshipPlannerAgreementManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[InternshipPlannerAgreementManager :: PARAM_MOMENT_ID];
		$failures = 0;
				
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$moment = $this->retrieve_moment($id);
				$agreement_id = $moment->get_agreement_id();	
				if (!$moment->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedInternshipPlannerMomentNotDeleted';
				}
				else
				{
					$message = 'Selected{InternshipPlannerMomentsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedInternshipPlannerMomentDeleted';
				}
				else
				{
					$message = 'SelectedInternshipPlannerMomentsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipPlannerAgreementManager :: PARAM_ACTION => InternshipPlannerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipPlannerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoInternshipPlannerMomentsSelected')));
		}
	}
}
?>