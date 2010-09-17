<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../../forms/rate_form.class.php';

/**
 * Component to edit an existing variable_translation object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerVariableTranslationRaterComponent extends CdaManager
{
	private $variable_translation;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$variable_translation_id = Request :: get(CdaManager :: PARAM_VARIABLE_TRANSLATION);
		$variable_translation = $this->retrieve_variable_translation($variable_translation_id);
		
		$language_id = $variable_translation->get_language_id();
		$variable_id = $variable_translation->get_variable_id();
		$variable = $this->retrieve_variable($variable_id);
		
		$language = $this->retrieve_cda_language($language_id);
		$language_pack = $this->retrieve_language_pack($variable->get_language_pack_id());
		
		$form = new RateForm($variable_translation, $variable, $this->get_url(array(CdaManager :: PARAM_VARIABLE_TRANSLATION => $variable_translation_id)));

		if($form->validate())
		{
			$success = $form->rate();
			$this->redirect($success ? Translation :: get('VariableTranslationRated') : Translation :: get('VariableTranslationNotRated'), 
						!$success, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS,
										 CdaManager :: PARAM_CDA_LANGUAGE => $language_id, CdaManager :: PARAM_LANGUAGE_PACK => $variable->get_language_pack_id()));
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
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('CdaManagerCdaLanguagesBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_LANGUAGE_PACKS, CdaManager :: PARAM_CDA_LANGUAGE => Request :: get(self :: PARAM_CDA_LANGUAGE))), Translation :: get('CdaManagerLanguagePacksBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS, CdaManager :: PARAM_LANGUAGE_PACK => Request :: get(self :: PARAM_LANGUAGE_PACK), CdaManager :: PARAM_CDA_LANGUAGE => Request :: get(self :: PARAM_CDA_LANGUAGE))), Translation :: get('CdaManagerVariableTranslationsBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_VIEW_VARIABLE_TRANSLATION, CdaManager :: PARAM_VARIABLE_TRANSLATION => Request :: get(self :: PARAM_VARIABLE_TRANSLATION))), Translation :: get('CdaManagerVariableTranslationViewerComponent')));
    	$breadcrumbtrail->add_help('cda_variable_translations_viewer');
    }
    
	function get_additional_parameters()
    {
    	return array(CdaManager :: PARAM_VARIABLE_TRANSLATION, self :: PARAM_CDA_LANGUAGE, self :: PARAM_VARIABLE_TRANSLATION);
    }
}
?>