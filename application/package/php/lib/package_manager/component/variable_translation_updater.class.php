<?php

namespace application\package;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Display;
use common\libraries\Request;
use common\libraries\WebApplication;
use common\libraries\NotCondition;
use common\libraries\InCondition;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Path;
use common\libraries\ResourceManager;
use common\libraries\Utilities;
/**
 * @package application.package.package.component
 */
require_once WebApplication :: get_application_class_lib_path('package') . 'forms/variable_translation_form.class.php';

/**
 * Component to edit an existing variable_translation object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerVariableTranslationUpdaterComponent extends PackageManager
{
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
		
		$can_translate = PackageRights :: is_allowed_in_languages_subtree(PackageRights :: VIEW_RIGHT, $language_id, 'package_language');
		$can_lock = PackageRights :: is_allowed_in_languages_subtree(PackageRights :: EDIT_RIGHT, $language_id, 'package_language');
		
		if (($can_translate && !$variable_translation->is_locked()) || $can_lock)
		{
			$language = $this->retrieve_package_language($language_id);
			$language_pack = $this->retrieve_language_pack($variable->get_language_pack_id());
			
			$form = new VariableTranslationForm($variable_translation, $variable, 
					$this->get_url(array(PackageManager :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id())), $this->get_user());
	
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
						$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_TRANSLATED, 0);
						$conditions[] = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $variable->get_language_pack_id(), Variable :: get_table_name());
						$condition = new AndCondition($conditions);
						$next_variable = $this->retrieve_variable_translations($condition, 0, 1)->next_result();
						
						if(is_null($next_variable))
						{
							$conditions = array();
							
							if($extra_condition)
								$conditions[] = $extra_condition;
							
							$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
							$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_TRANSLATED, 0);
							$condition = new AndCondition($conditions);
							$next_variable = $this->retrieve_variable_translations($condition, 0, 1)->next_result();
						}
						
						if (!is_null($next_variable))
						{
							if(!$message)
							{
								$object = Translation :: get('PreviousVariableTranslation');
                                                                $message = $success ? Translation :: get('ObjectUpdated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES) :
                                                                                    Translation :: get('ObjectNotUpdated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES);

								$message .= '<br /> ' . $variable->get_variable() . ' = ' . $variable_translation->get_translation(); 
							}
								
							$parameters[PackageManager :: PARAM_ACTION] = PackageManager :: ACTION_EDIT_VARIABLE_TRANSLATION;
							/*$parameters[PackageManager :: PARAM_PACKAGE_LANGUAGE] = $language_id;
							$parameters[PackageManager :: PARAM_VARIABLE] = $next_variable->get_variable_id();*/
							$parameters[PackageManager :: PARAM_VARIABLE_TRANSLATION] = $next_variable->get_id();
						}
						else
						{
							$message = Translation :: get('LanguageCompletelyTranslated');
							$message .= '<br /> ' . $variable->get_variable() . ' = ' . $variable_translation->get_translation(); 
							$parameters[PackageManager :: PARAM_ACTION] = PackageManager :: ACTION_BROWSE_PACKAGE_LANGUAGES;
							$success = true;
						}
						break;
					case VariableTranslationForm :: SUBMIT_SAVE :
                                                $object = Translation :: get('VariableTranslationUpdated');
                                                 $message = $success ? Translation :: get('ObjectUpdated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES) :
                                                                       Translation :: get('ObjectNotUpdated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES);

						$message .= '<br /> ' . $variable->get_variable() . ' = ' . $variable_translation->get_translation(); 
						$parameters = array();
						$parameters[PackageManager :: PARAM_ACTION] = PackageManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS;
						$parameters[PackageManager :: PARAM_PACKAGE_LANGUAGE] = $language_id;
						$parameters[PackageManager :: PARAM_LANGUAGE_PACK] = $variable->get_language_pack_id();
						break;
				}
				
				$this->redirect($message, !$success, $parameters);
			}
			else
			{
				$this->display_header();

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
	
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_package_languages_url(), Translation :: get('PackageManagerPackageLanguagesBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_LANGUAGE_PACKS, PackageManager :: PARAM_PACKAGE_LANGUAGE => Request :: get(self :: PARAM_PACKAGE_LANGUAGE))), Translation :: get('PackageManagerLanguagePacksBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS, PackageManager :: PARAM_LANGUAGE_PACK => Request :: get(self :: PARAM_LANGUAGE_PACK), PackageManager :: PARAM_PACKAGE_LANGUAGE => Request :: get(self :: PARAM_PACKAGE_LANGUAGE))), Translation :: get('PackageManagerVariableTranslationsBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_VIEW_VARIABLE_TRANSLATION, PackageManager :: PARAM_VARIABLE_TRANSLATION => Request :: get(self :: PARAM_VARIABLE_TRANSLATION))), Translation :: get('PackageManagerVariableTranslationViewerComponent')));
    	$breadcrumbtrail->add_help('package_variable_translations_updater');
    }
    
	function get_additional_parameters()
    {
    	return array(PackageManager :: PARAM_VARIABLE_TRANSLATION, self :: PARAM_PACKAGE_LANGUAGE, self :: PARAM_VARIABLE_TRANSLATION);
    }
}
?>