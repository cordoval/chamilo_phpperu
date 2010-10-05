<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';

/**
 * Component to delete language_packs objects
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerTranslatorApplicationDeactivatorComponent extends CdaManager
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
				$can_deactivate = CdaRights :: is_allowed_in_languages_subtree(CdaRights :: EDIT_RIGHT, $translator_application->get_destination_language_id(), 'cda_language');
				
				if (!$can_deactivate)
				{
					$failures++;
				}
				elseif (!$translator_application->deactivate())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedTranslatorApplicationNotDeactivated';
				}
				else
				{
					$message = 'SelectedTranslatorApplicationsNotDeactivated';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedTranslatorApplicationDeactivated';
				}
				else
				{
					$message = 'SelectedTranslatorApplicationsNotDeactivated';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_TRANSLATOR_APPLICATIONS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoTranslatorApplicationsSelected')));
		}
	}
	
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('CdaManagerCdaLanguagesBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_translator_applications_link(), Translation :: get('CdaManagerTranslatorApplicationBrowserComponent')));
    	$breadcrumbtrail->add_help('cda_languages_application_deactivator');
    }
    
    function get_additional_parameters()
    {
    	return array(CdaManager :: PARAM_TRANSLATOR_APPLICATION);
    }
}
?>