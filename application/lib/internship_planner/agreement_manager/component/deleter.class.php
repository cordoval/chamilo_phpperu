<?php

require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';

class InternshipPlannerAgreementManagerDeleterComponent extends InternshipPlannerAgreementManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[InternshipPlannerAgreementManager :: PARAM_AGREEMENT_ID];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$agreement = $this->retrieve_agreement($id);

				if (!$agreement->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedInternshipPlannerAgreementNotDeleted';
				}
				else
				{
					$message = 'Selected{InternshipPlannerAgreementsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedInternshipPlannerAgreementDeleted';
				}
				else
				{
					$message = 'SelectedInternshipPlannerAgreementsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipPlannerAgreementManager :: PARAM_ACTION => InternshipPlannerAgreementManager :: ACTION_BROWSE_AGREEMENT));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoInternshipPlannerAgreementsSelected')));
		}
	}
}
?>