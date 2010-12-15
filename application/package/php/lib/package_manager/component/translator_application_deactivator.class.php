<?php

namespace application\package;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Utilities;
/**
 * @package application.package.package.component
 */
/**
 * Component to delete language_packs objects
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerTranslatorApplicationDeactivatorComponent extends PackageManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[PackageManager :: PARAM_TRANSLATOR_APPLICATION];
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
				$can_deactivate = PackageRights :: is_allowed_in_languages_subtree(PackageRights :: EDIT_RIGHT, $translator_application->get_destination_language_id(), 'package_language');
				
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

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_TRANSLATOR_APPLICATIONS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectsSelected', null, Utilities :: COMMON_LIBRARIES)));
		}
	}
	
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_package_languages_url(), Translation :: get('PackageManagerPackageLanguagesBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_translator_applications_link(), Translation :: get('PackageManagerTranslatorApplicationBrowserComponent')));
    	$breadcrumbtrail->add_help('package_languages_application_deactivator');
    }
    
    function get_additional_parameters()
    {
    	return array(PackageManager :: PARAM_TRANSLATOR_APPLICATION);
    }
}
?>