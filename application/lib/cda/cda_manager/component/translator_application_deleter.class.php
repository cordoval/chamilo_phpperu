<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';

/**
 * Component to delete language_packs objects
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerTranslatorApplicationDeleterComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[CdaManager :: PARAM_TRANSLATOR_APPLICATION];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$translator_application = $this->retrieve_translator_application($id);
				$can_delete = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, $translator_application->get_destination_language_id(), 'cda_language');
				
				if (!$can_delete)
				{
					$failures++;
				}
				elseif (!$translator_application->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedTranslatorApplicationNotDeleted';
				}
				else
				{
					$message = 'SelectedTranslatorApplicationsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedTranslatorApplicationDeleted';
				}
				else
				{
					$message = 'SelectedTranslatorApplicationsNotDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_TRANSLATOR_APPLICATIONS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoTranslatorApplicationsSelected')));
		}
	}
}
?>