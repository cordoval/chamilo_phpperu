<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';

/**
 * Component to delete locations objects
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipOrganisationManagerDeleterComponent extends InternshipOrganisationManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[InternshipOrganisationManager :: PARAM_ORGANISATION_ID];
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
					$message = 'SelectedInternshipOrganisationNotDeleted';
				}
				else
				{
					$message = 'Selected{InternshipOrganisationsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedInternshipOrganisationDeleted';
				}
				else
				{
					$message = 'SelectedInternshipOrganisationsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipOrganisationManager :: PARAM_ACTION => InternshipOrganisationManager :: ACTION_BROWSE_ORGANISATION));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganisationsSelected')));
		}
	}
}
?>