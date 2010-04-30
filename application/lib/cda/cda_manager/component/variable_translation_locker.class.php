<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../../forms/variable_translation_form.class.php';

/**
 * Component to edit an existing variable_translation object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerVariableTranslationLockerComponent extends CdaManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$variable_translation_id = Request :: get(CdaManager :: PARAM_VARIABLE_TRANSLATION);
		$language_id = Request :: get(CdaManager :: PARAM_CDA_LANGUAGE);
		$language_pack_id = Request :: get(CdaManager :: PARAM_LANGUAGE_PACK);
		$status = Request :: get(CdaManager :: PARAM_VARIABLE_TRANSLATION_STATUS);
		
		$can_lock = CdaRights :: is_allowed_in_languages_subtree(CdaRights :: EDIT_RIGHT, $language_id, 'cda_language');
		
		if (!$can_lock)
		{
			Display :: not_allowed();
		}
		
		if($variable_translation_id)
		{
			$this->handle_translation($variable_translation_id);
		}
		
		if($language_pack_id)
		{
			$this->handle_language_pack($language_id, $language_pack_id, $status);
		}
		
		$this->handle_language($language_id, $status);
	}
	
	function handle_translation($variable_translation_id)
	{
		$translation = $this->retrieve_variable_translation($variable_translation_id);
		$variable = $this->retrieve_variable($translation->get_variable_id());
		$language_id = $translation->get_language_id();
		
		if($translation)
		{
			$translation->switch_lock();
			$succes = $translation->update();
		}
		
		if($succes)
		{
			if(!$translation->is_locked())
			{
				$message = 'TranslationVariableUnlocked';
			}
			else
			{
				$message = 'TranslationVariableLocked';
			}
		}
		else
		{
			if(!$translation->is_locked())
			{
				$message = 'TranslationVariableNotUnlocked';
			}
			else
			{
				$message = 'TranslationVariableNotLocked';
			}
		}
		
		$this->redirect(Translation :: get($message), !$succes, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS,
				CdaManager :: PARAM_CDA_LANGUAGE => $language_id, CdaManager :: PARAM_LANGUAGE_PACK => $variable->get_language_pack_id()));
		exit;
	}
	
	function handle_language_pack($language_id, $language_pack_id, $status)
	{
		$subcondition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack_id);
		$conditions[] = new SubSelectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID, Variable :: get_table_name(), $subcondition);
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
		$condition = new AndCondition($conditions);
		
		$succes = $this->update_variable_translations(array(VariableTranslation :: PROPERTY_STATUS => $status), $condition);
		
		if($succes)
		{
			if($status == VariableTranslation :: STATUS_NORMAL)
			{
				$message = 'LanguagePackUnlocked';
			}
			else
			{
				$message = 'LanguagePackLocked';
			}
		}
		else
		{
			if($status == VariableTranslation :: STATUS_NORMAL)
			{
				$message = 'LanguagePackNotUnlocked';
			}
			else
			{
				$message = 'LanguagePackNotLocked';
			}
		}
		
		$this->redirect(Translation :: get($message), !$succes, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_LANGUAGE_PACKS,
				CdaManager :: PARAM_CDA_LANGUAGE => $language_id));
		exit;
	}
	
	function handle_language($language_id, $status)
	{
		$condition = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
		
		$succes = $this->update_variable_translations(array(VariableTranslation :: PROPERTY_STATUS => $status), $condition);
		
		if($succes)
		{
			if($status == VariableTranslation :: STATUS_NORMAL)
			{
				$message = 'LanguageUnlocked';
			}
			else
			{
				$message = 'LanguageLocked';
			}
		}
		else
		{
			if($status == VariableTranslation :: STATUS_NORMAL)
			{
				$message = 'LanguageNotUnlocked';
			}
			else
			{
				$message = 'LanguageNotLocked';
			}
		}
		
		$this->redirect(Translation :: get($message), !$succes, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_CDA_LANGUAGES));
		exit;
	}
}
?>