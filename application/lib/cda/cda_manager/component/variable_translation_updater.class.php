<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/variable_translation_form.class.php';

/**
 * Component to edit an existing variable_translation object
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerVariableTranslationUpdaterComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$language_id = Request :: get(CdaManager :: PARAM_CDA_LANGUAGE);
		$variable_id = Request :: get(CdaManager :: PARAM_VARIABLE);
		
		$variable = $this->retrieve_variable($variable_id);
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('BrowseLanguages')));
		$trail->add(new Breadcrumb($this->get_browse_language_packs_url($language_id), Translation :: get('BrowseLanguagePacks')));
		$trail->add(new Breadcrumb($this->get_browse_variable_translations_url($language_id, $variable->get_language_pack_id()), 
								   Translation :: get('BrowseVariableTranslations')));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_CDA_LANGUAGE => $language_id,
														CdaManager :: PARAM_VARIABLE => $variable_id)), Translation :: get('UpdateVariableTranslation')));
		
		$variable_translation = $this->retrieve_variable_translation($language_id, $variable_id);
		
		$form = new VariableTranslationForm($variable_translation, $variable, 
				$this->get_url(array(CdaManager :: PARAM_CDA_LANGUAGE => $language_id, 
									 CdaManager :: PARAM_VARIABLE => $variable_id)), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_variable_translation();
			$this->redirect($success ? Translation :: get('VariableTranslationUpdated') : Translation :: get('VariableTranslationNotUpdated'), 
						!$success, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS,
										 CdaManager :: PARAM_CDA_LANGUAGE => $language_id, CdaManager :: PARAM_LANGUAGE_PACK => $variable->get_language_pack_id()));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>