<?php

require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';

class InternshipPlannerOrganisationManagerDeleterComponent extends InternshipPlannerOrganisationManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[InternshipPlannerOrganisationManager :: PARAM_ORGANISATION_ID];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$organisation = $this->retrieve_organisation($id);

				if (!$organisation->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedInternshipPlannerOrganisationNotDeleted';
				}
				else
				{
					$message = 'Selected{InternshipPlannerOrganisationsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedInternshipPlannerOrganisationDeleted';
				}
				else
				{
					$message = 'SelectedInternshipPlannerOrganisationsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipPlannerOrganisationManager :: PARAM_ACTION => InternshipPlannerOrganisationManager :: ACTION_BROWSE_ORGANISATION));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoInternshipPlannerOrganisationsSelected')));
		}
	}
}
?>