<?php

namespace application\package;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\WebApplication;
/**
 * @package application.package.package.component
 */
require_once WebApplication :: get_application_class_lib_path('package') . 'forms/rate_form.class.php';

/**
 * Component to edit an existing variable_translation object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerVariableTranslationRaterComponent extends PackageManager
{
	private $variable_translation;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$variable_translation_id = Request :: get(PackageManager :: PARAM_VARIABLE_TRANSLATION);
		$variable_translation = $this->retrieve_variable_translation($variable_translation_id);
		
		$language_id = $variable_translation->get_language_id();
		$variable_id = $variable_translation->get_variable_id();
		$variable = $this->retrieve_variable($variable_id);
		
		$language = $this->retrieve_package_language($language_id);
		$language_pack = $this->retrieve_language_pack($variable->get_language_pack_id());
		
		$form = new RateForm($variable_translation, $variable, $this->get_url(array(PackageManager :: PARAM_VARIABLE_TRANSLATION => $variable_translation_id)));

		if($form->validate())
		{
			$success = $form->rate();
			$this->redirect($success ? Translation :: get('VariableTranslationRated') : Translation :: get('VariableTranslationNotRated'), 
						!$success, array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS,
										 PackageManager :: PARAM_PACKAGE_LANGUAGE => $language_id, PackageManager :: PARAM_LANGUAGE_PACK => $variable->get_language_pack_id()));
		}
		else
		{
			$this->display_header();
			$form->display();
			$this->display_footer();
		}
	}
	
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_package_languages_url(), Translation :: get('PackageManagerPackageLanguagesBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_LANGUAGE_PACKS, PackageManager :: PARAM_PACKAGE_LANGUAGE => Request :: get(self :: PARAM_PACKAGE_LANGUAGE))), Translation :: get('PackageManagerLanguagePacksBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS, PackageManager :: PARAM_LANGUAGE_PACK => Request :: get(self :: PARAM_LANGUAGE_PACK), PackageManager :: PARAM_PACKAGE_LANGUAGE => Request :: get(self :: PARAM_PACKAGE_LANGUAGE))), Translation :: get('PackageManagerVariableTranslationsBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_VIEW_VARIABLE_TRANSLATION, PackageManager :: PARAM_VARIABLE_TRANSLATION => Request :: get(self :: PARAM_VARIABLE_TRANSLATION))), Translation :: get('PackageManagerVariableTranslationViewerComponent')));
    	$breadcrumbtrail->add_help('package_variable_translations_viewer');
    }
    
	function get_additional_parameters()
    {
    	return array(PackageManager :: PARAM_VARIABLE_TRANSLATION, self :: PARAM_PACKAGE_LANGUAGE, self :: PARAM_VARIABLE_TRANSLATION);
    }
}
?>