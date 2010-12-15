<?php

namespace application\package;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Display;
use common\libraries\Request;
use common\libraries\WebApplication;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\SubselectCondition;
/**
 * @package application.package.package.component
 */
require_once WebApplication :: get_application_class_lib_path('package') . 'forms/variable_translation_form.class.php';

/**
 * Component to edit an existing variable_translation object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerVariableTranslationLockerComponent extends PackageManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$variable_translation_id = Request :: get(PackageManager :: PARAM_VARIABLE_TRANSLATION);
		$language_id = Request :: get(PackageManager :: PARAM_PACKAGE_LANGUAGE);
		$language_pack_id = Request :: get(PackageManager :: PARAM_LANGUAGE_PACK);
		$status = Request :: get(PackageManager :: PARAM_VARIABLE_TRANSLATION_STATUS);
		
		$can_lock = PackageRights :: is_allowed_in_languages_subtree(PackageRights :: EDIT_RIGHT, $language_id, 'package_language');
		
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
		
		$this->redirect(Translation :: get($message), !$succes, array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS,
				PackageManager :: PARAM_PACKAGE_LANGUAGE => $language_id, PackageManager :: PARAM_LANGUAGE_PACK => $variable->get_language_pack_id()));
		exit;
	}
	
	function handle_language_pack($language_id, $language_pack_id, $status)
	{
		$subcondition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack_id);
		$conditions[] = new SubselectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID, Variable :: get_table_name(), $subcondition);
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
		
		$this->redirect(Translation :: get($message), !$succes, array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_LANGUAGE_PACKS,
				PackageManager :: PARAM_PACKAGE_LANGUAGE => $language_id));
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
		
		$this->redirect(Translation :: get($message), !$succes, array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_PACKAGE_LANGUAGES));
		exit;
	}
	
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_package_languages_url(), Translation :: get('PackageManagerPackageLanguagesBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_LANGUAGE_PACKS, PackageManager :: PARAM_PACKAGE_LANGUAGE => Request :: get(self :: PARAM_PACKAGE_LANGUAGE))), Translation :: get('PackageManagerLanguagePacksBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS, PackageManager :: PARAM_LANGUAGE_PACK => Request :: get(self :: PARAM_LANGUAGE_PACK), PackageManager :: PARAM_PACKAGE_LANGUAGE => Request :: get(self :: PARAM_PACKAGE_LANGUAGE))), Translation :: get('PackageManagerVariableTranslationsBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_VIEW_VARIABLE_TRANSLATION, PackageManager :: PARAM_VARIABLE_TRANSLATION => Request :: get(self :: PARAM_VARIABLE_TRANSLATION))), Translation :: get('PackageManagerVariableTranslationViewerComponent')));
    	$breadcrumbtrail->add_help('package_variable_translations_locker');
    }
    
	function get_additional_parameters()
    {
    	return array(PackageManager :: PARAM_VARIABLE_TRANSLATION, self :: PARAM_PACKAGE_LANGUAGE, self :: PARAM_VARIABLE_TRANSLATION);
    }
}
?>