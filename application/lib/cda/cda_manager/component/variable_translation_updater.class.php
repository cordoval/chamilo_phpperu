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
 * @author Hans De Bisschop
 */
class CdaManagerVariableTranslationUpdaterComponent extends CdaManagerComponent
{
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
		
		$can_translate = CdaRights :: is_allowed(CdaRights :: VIEW_RIGHT, $language_id, 'cda_language');
		$can_lock = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, $language_id, 'cda_language');
		
		if (($can_translate && !$variable_translation->is_locked()) || $can_lock)
		{
			$language = $this->retrieve_cda_language($language_id);
			$language_pack = $this->retrieve_language_pack($variable->get_language_pack_id());
			
			$trail = new BreadcrumbTrail();
			$trail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('Cda')));
			$trail->add(new Breadcrumb($this->get_browse_language_packs_url($language_id), $language->get_original_name()));
			$trail->add(new Breadcrumb($this->get_browse_variable_translations_url($language_id, $variable->get_language_pack_id()), $language_pack->get_branch_name() . ' - ' . $language_pack->get_name()));
			$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_VARIABLE_TRANSLATION => $variable_translation_id)), Translation :: get('UpdateVariableTranslation')));
			
			$form = new VariableTranslationForm($variable_translation, $variable, 
					$this->get_url(array(CdaManager :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id())), $this->get_user());
	
			if($form->validate())
			{
				$type = $form->get_submit_type();
				
				if($type != VariableTranslationForm :: SUBMIT_NEXT_NO_SAVE)
					$success = $form->update_variable_translation();
				
				switch($type)
				{
					case VariableTranslationForm :: SUBMIT_NEXT_NO_SAVE:
						$_SESSION['skipped_variable_translations'][] = $variable_translation_id;
						$extra_condition = new NotCondition(new InCondition(VariableTranslation :: PROPERTY_ID, $_SESSION['skipped_variable_translations']));
						$success = true;
						$message = Translation :: get('TranslationSkipped');
					case VariableTranslationForm :: SUBMIT_NEXT :
						$parameters = array();
						
						$conditions = array();
						
						if($extra_condition)
							$conditions[] = $extra_condition;
						
						if (!$can_lock)
						{
							$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_STATUS, VariableTranslation :: STATUS_NORMAL);
						}
						
						$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
						$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_TRANSLATION, ' ');
						$conditions[] = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $variable->get_language_pack_id(), Variable :: get_table_name());
						$condition = new AndCondition($conditions);
						$next_variable = $this->retrieve_variable_translations($condition, 0, 1)->next_result();
						
						if(is_null($next_variable))
						{
							$conditions = array();
							
							if($extra_condition)
								$conditions[] = $extra_condition;
							
							$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
							$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_TRANSLATION, ' ');
							$condition = new AndCondition($conditions);
							$next_variable = $this->retrieve_variable_translations($condition, 0, 1)->next_result();
						}
						
						if (!is_null($next_variable))
						{
							if(!$message)
							{
								$message = $success ? Translation :: get('PreviousVariableTranslationUpdated') : Translation :: get('PreviousVariableTranslationNotUpdated');
								$message .= '<br /> ' . $variable->get_variable() . ' = ' . $variable_translation->get_translation(); 
							}
								
							$parameters[CdaManager :: PARAM_ACTION] = CdaManager :: ACTION_EDIT_VARIABLE_TRANSLATION;
							/*$parameters[CdaManager :: PARAM_CDA_LANGUAGE] = $language_id;
							$parameters[CdaManager :: PARAM_VARIABLE] = $next_variable->get_variable_id();*/
							$parameters[CdaManager :: PARAM_VARIABLE_TRANSLATION] = $next_variable->get_id();
						}
						else
						{
							$message = Translation :: get('LanguageCompletelyTranslated');
							$message .= '<br /> ' . $variable->get_variable() . ' = ' . $variable_translation->get_translation(); 
							$parameters[CdaManager :: PARAM_ACTION] = CdaManager :: ACTION_BROWSE_CDA_LANGUAGES;
							$success = true;
						}
						break;
					case VariableTranslationForm :: SUBMIT_SAVE :
						$message = $success ? Translation :: get('VariableTranslationUpdated') : Translation :: get('VariableTranslationNotUpdated');
						$message .= '<br /> ' . $variable->get_variable() . ' = ' . $variable_translation->get_translation(); 
						$parameters = array();
						$parameters[CdaManager :: PARAM_ACTION] = CdaManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS;
						$parameters[CdaManager :: PARAM_CDA_LANGUAGE] = $language_id;
						$parameters[CdaManager :: PARAM_LANGUAGE_PACK] = $variable->get_language_pack_id();
						break;
				}
				
				$this->redirect($message, !$success, $parameters);
			}
			else
			{
				$this->display_header($trail);

				echo ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/variable_translation_form.js');
				$form->display();
				
				$this->display_footer();
			}
		}
		else
		{
			Display :: not_allowed();
		}
	}
}
?>