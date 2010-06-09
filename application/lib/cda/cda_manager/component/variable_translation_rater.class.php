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
		
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('Cda')));
		$trail->add(new Breadcrumb($this->get_browse_language_packs_url($language_id), $language->get_original_name()));
		$trail->add(new Breadcrumb($this->get_browse_variable_translations_url($language_id, $variable->get_language_pack_id()), $language_pack->get_branch_name() . ' - ' . $language_pack->get_name()));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_CDA_LANGUAGE => $language_id,
														CdaManager :: PARAM_VARIABLE => $variable_translation->get_variable_id())), Translation :: get('RateVariableTranslation')));
		
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
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>