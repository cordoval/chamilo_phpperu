<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager_component.class.php';

class InternshipOrganizerOrganisationManagerLocationDeleterComponent extends InternshipOrganizerOrganisationManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[InternshipOrganizerOrganisationManager :: PARAM_LOCATION_ID];
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
					$message = 'SelectedInternshipOrganizerLocationNotDeleted';
				}
				else
				{
					$message = 'Selected{InternshipOrganizerLocationsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedInternshipOrganizerLocationDeleted';
				}
				else
				{
					$message = 'SelectedInternshipOrganizerLocationsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerLocationsSelected')));
		}
	}
}
?>