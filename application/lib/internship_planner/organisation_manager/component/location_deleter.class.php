<?php

require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';

class InternshipPlannerOrganisationManagerLocationDeleterComponent extends InternshipPlannerOrganisationManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[InternshipPlannerOrganisationManager :: PARAM_LOCATION_ID];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$location = $this->retrieve_location($id);
				$organisation_id = $location->get_organisation_id();
				
				if (!$location->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedInternshipPlannerLocationNotDeleted';
				}
				else
				{
					$message = 'Selected{InternshipPlannerLocationsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedInternshipPlannerLocationDeleted';
				}
				else
				{
					$message = 'SelectedInternshipPlannerLocationsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipPlannerOrganisationManager :: PARAM_ACTION => InternshipPlannerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipPlannerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoInternshipPlannerLocationsSelected')));
		}
	}
}
?>