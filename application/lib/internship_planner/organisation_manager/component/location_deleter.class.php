<?php

require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';

class InternshipOrganisationManagerLocationDeleterComponent extends InternshipOrganisationManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[InternshipOrganisationManager :: PARAM_LOCATION_ID];
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

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipOrganisationManager :: PARAM_ACTION => InternshipOrganisationManager :: ACTION_VIEW_ORGANISATION));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoInternshipPlannerLocationsSelected')));
		}
	}
}
?>