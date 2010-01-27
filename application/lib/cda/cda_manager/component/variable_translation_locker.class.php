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
class CdaManagerVariableTranslationLockerComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$language_id = Request :: get(CdaManager :: PARAM_CDA_LANGUAGE);
		$variable_id = Request :: get(CdaManager :: PARAM_VARIABLE);
		$language_pack_id = Request :: get(CdaManager :: PARAM_LANGUAGE_PACK);
		$status = Request :: get(CdaManager :: PARAM_VARIABLE_TRANSLATION_STATUS);
		
		if($variable_id)
		{
			$this->handle_translation($language_id, $variable_id, $status);
		}
		
		if($language_pack_id)
		{
			$this->handle_language_pack($language_id, $language_pack_id, $status);
		}
		
		$this->handle_language($language_id, $status);
	}
	
	function handle_translation($language_id, $variable_id, $status)
	{
		$variable = $this->retrieve_variable($variable_id);
		$translation = $this->retrieve_variable_translation($language_id, $variable_id);
		
		if($translation)
		{
			$translation->set_status($status);
			$succes = $translation->update();
		}
		
		if($succes)
		{
			if($status == VariableTranslation :: STATUS_NORMAL)
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
			if($status == VariableTranslation :: STATUS_NORMAL)
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
	}
	
	function handle_language_pack($language_id, $language_pack_id, $status)
	{
		$subcondition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack_id);
		$conditions[] = new SubSelectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID, 'cda_' . Variable :: get_table_name(), $subcondition);
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
		$condition = new AndCondition($conditions);
		
		$succes = true;
		$translations = $this->retrieve_variable_translations($condition);
		while($translation = $translations->next_result())
		{
			$translation->set_status($status);
			$succes &= $translation->update();
		}
		
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
	}
	
	function handle_language($language_id, $status)
	{
		$condition = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
		
		$succes = true;
		$translations = $this->retrieve_variable_translations($condition);
		while($translation = $translations->next_result())
		{
			$translation->set_status($status);
			$succes &= $translation->update();
		}
		
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
				$message = 'LanguagePackUnlocked';
			}
			else
			{
				$message = 'LanguagePackLocked';
			}
		}
		
		$this->redirect(Translation :: get($message), !$succes, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_CDA_LANGUAGES));
	}
}
?>