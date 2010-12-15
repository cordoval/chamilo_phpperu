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
 * Component to delete historic variable translations objects
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerVariableTranslationVerifierComponent extends PackageManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = Request :: get(PackageManager :: PARAM_VARIABLE_TRANSLATION);
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
        		$variable_translation = $this->retrieve_variable_translation($id);

        		$language_id = $variable_translation->get_language_id();
        		$variable_id = $variable_translation->get_variable_id();
        		$variable = $this->retrieve_variable($variable_id);

        		$can_translate = PackageRights :: is_allowed_in_languages_subtree(PackageRights :: VIEW_RIGHT, $language_id, 'package_language');
        		$can_lock = PackageRights :: is_allowed_in_languages_subtree(PackageRights :: EDIT_RIGHT, $language_id, 'package_language');

				if (!($can_translate && !$variable_translation->is_locked()) && !$can_lock)
				{
					$failures++;
				}
				elseif (!$variable_translation->verify())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedVariableTranslationNotVerified';
				}
				else
				{
					$message = 'SelectedVariableTranslationsNotVerified';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedVariableTranslationVerified';
				}
				else
				{
					$message = 'SelectedVariableTranslationsNotVerified';
				}
			}

			$parameters = array();
			$parameters[PackageManager :: PARAM_ACTION] = PackageManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS;
			$parameters[PackageManager :: PARAM_PACKAGE_LANGUAGE] = $language_id;
			$parameters[PackageManager :: PARAM_LANGUAGE_PACK] = $variable->get_language_pack_id();

			$this->redirect(Translation :: get($message), ($failures ? true : false), $parameters);
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectsSelected', null, Utilities :: COMMON_LIBRARIES)));
		}
	}
	
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_package_languages_url(), Translation :: get('PackageManagerPackageLanguagesBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_LANGUAGE_PACKS, PackageManager :: PARAM_PACKAGE_LANGUAGE => Request :: get(self :: PARAM_PACKAGE_LANGUAGE))), Translation :: get('PackageManagerLanguagePacksBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS, PackageManager :: PARAM_LANGUAGE_PACK => Request :: get(self :: PARAM_LANGUAGE_PACK), PackageManager :: PARAM_PACKAGE_LANGUAGE => Request :: get(self :: PARAM_PACKAGE_LANGUAGE))), Translation :: get('PackageManagerVariableTranslationsBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_VIEW_VARIABLE_TRANSLATION, PackageManager :: PARAM_VARIABLE_TRANSLATION => Request :: get(self :: PARAM_VARIABLE_TRANSLATION))), Translation :: get('PackageManagerVariableTranslationViewerComponent')));
    	$breadcrumbtrail->add_help('package_variable_translations_verifier');
    }
    
	function get_additional_parameters()
    {
    	return array(PackageManager :: PARAM_VARIABLE_TRANSLATION, self :: PARAM_PACKAGE_LANGUAGE, self :: PARAM_VARIABLE_TRANSLATION);
    }
}
?>