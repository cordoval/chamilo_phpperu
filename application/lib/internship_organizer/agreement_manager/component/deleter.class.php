<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager_component.class.php';

class InternshipOrganizerAgreementManagerDeleterComponent extends InternshipOrganizerAgreementManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID];
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
					$message = 'SelectedInternshipOrganizerAgreementNotDeleted';
				}
				else
				{
					$message = 'Selected{InternshipOrganizerAgreementsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedInternshipOrganizerAgreementDeleted';
				}
				else
				{
					$message = 'SelectedInternshipOrganizerAgreementsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_BROWSE_AGREEMENT));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerAgreementsSelected')));
		}
	}
}
?>