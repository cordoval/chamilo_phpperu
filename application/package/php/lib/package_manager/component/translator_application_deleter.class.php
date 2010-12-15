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
class PackageManagerTranslatorApplicationDeleterComponent extends PackageManager
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
				$can_delete = PackageRights :: is_allowed_in_languages_subtree(PackageRights :: EDIT_RIGHT, $translator_application->get_destination_language_id(), 'package_language');
				
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
                                    $message = Translation :: get('ObjectNotDeleted', array('OBJECT' => Translation :: get('TranslatorApplication')), Utilities :: COMMON_LIBRARIES);
				}
				else
				{
                                    $message = Translation :: get('ObjectsNotDeleted', array('OBJECTS' => Translation :: get('TranslatorApplication')), Utilities :: COMMON_LIBRARIES);
				}
			}
			else
			{
				if (count($ids) == 1)
				{
                                    $message = Translation :: get('ObjectDeleted', array('OBJECT' => Translation :: get('TranslatorApplications')), Utilities :: COMMON_LIBRARIES);
				}
				else
				{
                                    $message = Translation :: get('ObjectsDeleted', array('OBJECTS' => Translation :: get('TranslatorApplications')), Utilities :: COMMON_LIBRARIES);
				}
			}

			$this->redirect($message, $failures, array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_TRANSLATOR_APPLICATIONS));
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
    	$breadcrumbtrail->add_help('package_languages_application_deleter');
    }
    
    function get_additional_parameters()
    {
    	return array(PackageManager :: PARAM_TRANSLATOR_APPLICATION);
    }
}
?>