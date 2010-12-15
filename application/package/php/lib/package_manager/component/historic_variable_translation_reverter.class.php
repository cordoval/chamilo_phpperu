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
 * Component to revert historic variable translations objects
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerHistoricVariableTranslationReverterComponent extends PackageManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[PackageManager :: PARAM_HISTORIC_VARIABLE_TRANSLATION];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$historic_variable_translation = $this->retrieve_historic_variable_translation($id);
				$can_delete = PackageRights :: is_allowed_in_languages_subtree(PackageRights :: EDIT_RIGHT, $historic_variable_translation->get_variable_translation()->get_language_id(), 'package_language');

				if (!$can_delete)
				{
					$failures++;
				}
				elseif (!$historic_variable_translation->revert())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedHistoricVariableTranslationNotReverted';
				}
				else
				{
					$message = 'SelectedHistoricVariableTranslationsNotReverted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedHistoricVariableTranslationReverted';
				}
				else
				{
					$message = 'SelectedHistoricVariableTranslationsNotReverted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_VIEW_VARIABLE_TRANSLATION, PackageManager :: PARAM_VARIABLE_TRANSLATION => $historic_variable_translation->get_variable_translation_id()));
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
    	$breadcrumbtrail->add_help('package_variable_translations_historic_reverter');
    }
    
    function get_additional_parameters()
    {
    	return array(PackageManager :: PARAM_HISTORIC_VARIABLE_TRANSLATION, self :: PARAM_VARIABLE_TRANSLATION, self :: PARAM_PACKAGE_LANGUAGE, self :: PARAM_VARIABLE_TRANSLATION);
    }
}
?>