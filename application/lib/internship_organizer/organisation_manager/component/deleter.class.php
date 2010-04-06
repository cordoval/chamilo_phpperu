<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager_component.class.php';

class InternshipOrganizerOrganisationManagerDeleterComponent extends InternshipOrganizerOrganisationManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID];
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
					$message = 'SelectedInternshipOrganizerOrganisationNotDeleted';
				}
				else
				{
					$message = 'Selected{InternshipOrganizerOrganisationsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedInternshipOrganizerOrganisationDeleted';
				}
				else
				{
					$message = 'SelectedInternshipOrganizerOrganisationsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_BROWSE_ORGANISATION));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerOrganisationsSelected')));
		}
	}
}
?>