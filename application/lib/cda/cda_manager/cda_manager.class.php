<?php
/**
 * @package application.lib.cda.cda_manager
 */
require_once dirname(__FILE__).'/cda_manager_component.class.php';
require_once dirname(__FILE__).'/../cda_data_manager.class.php';
require_once dirname(__FILE__).'/component/cda_language_browser/cda_language_browser_table.class.php';
require_once dirname(__FILE__).'/component/language_pack_browser/language_pack_browser_table.class.php';
require_once dirname(__FILE__).'/component/variable_browser/variable_browser_table.class.php';
require_once dirname(__FILE__).'/component/variable_translation_browser/variable_translation_browser_table.class.php';
require_once dirname(__FILE__).'/../cda_rights.class.php';

/**
 * A cda manager
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
 class CdaManager extends WebApplication
 {
 	const APPLICATION_NAME = 'cda';

	const PARAM_CDA_LANGUAGE = 'cda_language';
	const PARAM_DELETE_SELECTED_CDA_LANGUAGES = 'delete_selected_cda_languages';

	const ACTION_DELETE_CDA_LANGUAGE = 'delete_cda_language';
	const ACTION_EDIT_CDA_LANGUAGE = 'edit_cda_language';
	const ACTION_CREATE_CDA_LANGUAGE = 'create_cda_language';
	const ACTION_BROWSE_CDA_LANGUAGES = 'browse_cda_languages';
	const ACTION_ADMIN_BROWSE_CDA_LANGUAGES = 'admin_browse_cda_languages';

	const PARAM_LANGUAGE_PACK = 'language_pack';
	const PARAM_DELETE_SELECTED_LANGUAGE_PACKS = 'delete_selected_language_packs';

	const ACTION_DELETE_LANGUAGE_PACK = 'delete_language_pack';
	const ACTION_EDIT_LANGUAGE_PACK = 'edit_language_pack';
	const ACTION_CREATE_LANGUAGE_PACK = 'create_language_pack';
	const ACTION_BROWSE_LANGUAGE_PACKS = 'browse_language_packs';
	const ACTION_ADMIN_BROWSE_LANGUAGE_PACKS = 'admin_browse_language_packs';

	const PARAM_VARIABLE = 'variable';
	const PARAM_DELETE_SELECTED_VARIABLES = 'delete_selected_variables';
	const PARAM_VARIABLE_TRANSLATION_STATUS = 'translation_status';

	const ACTION_DELETE_VARIABLE = 'delete_variable';
	const ACTION_EDIT_VARIABLE = 'edit_variable';
	const ACTION_CREATE_VARIABLE = 'create_variable';
	const ACTION_BROWSE_VARIABLES = 'browse_variables';
	const ACTION_ADMIN_BROWSE_VARIABLES = 'admin_browse_variables';

	const ACTION_EDIT_VARIABLE_TRANSLATION = 'edit_variable_translation';
	const ACTION_BROWSE_VARIABLE_TRANSLATIONS = 'browse_variable_translations';
	const ACTION_LOCK_VARIABLE_TRANSLATION = 'lock_variable_translation';
	const ACTION_VIEW_VARIABLE_TRANSLATION = 'view_variable_translation';
	const ACTION_EXPORT_TRANSLATIONS = 'export_translations';
	const ACTION_IMPORT_TRANSLATIONS = 'import_translations';
	const ACTION_ADMIN_IMPORT_TRANSLATIONS = 'admin_import_translations';
	const ACTION_RATE_VARIABLE_TRANSLATION = 'rate_variable_translation';
	const ACTION_VERIFY_VARIABLE_TRANSLATION = 'verify_variable_translation';
	const ACTION_DEPRECATE_VARIABLE_TRANSLATION = 'deprecate_variable_translation';
	const ACTION_SEARCH_VARIABLE_TRANSLATIONS = 'search_variable_translations';
	const PARAM_VARIABLE_TRANSLATION = 'variable_translation';

	const ACTION_CREATE_TRANSLATOR_APPLICATION = 'create_translator_application';
	const ACTION_BROWSE_TRANSLATOR_APPLICATIONS = 'browse_translator_applications';
	const ACTION_ACTIVATE_TRANSLATOR_APPLICATION = 'activate_translator_application';
	const ACTION_DEACTIVATE_TRANSLATOR_APPLICATION = 'deactivate_translator_application';
	const ACTION_DELETE_TRANSLATOR_APPLICATION = 'delete_translator_application';
	const PARAM_TRANSLATOR_APPLICATION = 'translator_application';

	const ACTION_DELETE_HISTORIC_VARIABLE_TRANSLATION = 'delete_historic_variable_translation';
	const ACTION_REVERT_HISTORIC_VARIABLE_TRANSLATION = 'restore_historic_variable_translation';
	const PARAM_HISTORIC_VARIABLE_TRANSLATION = 'historic_variable_translation';

	const PARAM_COMPARE_SELECTED_VARIABLE_TRANSLATIONS = 'compare_selected_variable_translations';

	/**
	 * Constructor
	 * @param User $user The current user
	 */
    function CdaManager($user = null)
    {
    	parent :: __construct($user);
    	$this->parse_input_from_table();
    }

    /**
	 * Run this cda manager
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;

		if($action != self :: ACTION_EDIT_VARIABLE_TRANSLATION)
			unset($_SESSION['skipped_variable_translations']);

		switch ($action)
		{
			case self :: ACTION_BROWSE_CDA_LANGUAGES :
				$component = CdaManagerComponent :: factory('CdaLanguagesBrowser', $this);
				break;
			case self :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES :
				$component = CdaManagerComponent :: factory('AdminCdaLanguagesBrowser', $this);
				break;
			case self :: ACTION_DELETE_CDA_LANGUAGE :
				$component = CdaManagerComponent :: factory('CdaLanguageDeleter', $this);
				break;
			case self :: ACTION_EDIT_CDA_LANGUAGE :
				$component = CdaManagerComponent :: factory('CdaLanguageUpdater', $this);
				break;
			case self :: ACTION_CREATE_CDA_LANGUAGE :
				$component = CdaManagerComponent :: factory('CdaLanguageCreator', $this);
				break;
			case self :: ACTION_BROWSE_LANGUAGE_PACKS :
				$component = CdaManagerComponent :: factory('LanguagePacksBrowser', $this);
				break;
			case self :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS :
				$component = CdaManagerComponent :: factory('AdminLanguagePacksBrowser', $this);
				break;
			case self :: ACTION_DELETE_LANGUAGE_PACK :
				$component = CdaManagerComponent :: factory('LanguagePackDeleter', $this);
				break;
			case self :: ACTION_EDIT_LANGUAGE_PACK :
				$component = CdaManagerComponent :: factory('LanguagePackUpdater', $this);
				break;
			case self :: ACTION_CREATE_LANGUAGE_PACK :
				$component = CdaManagerComponent :: factory('LanguagePackCreator', $this);
				break;
			case self :: ACTION_BROWSE_VARIABLES :
				$component = CdaManagerComponent :: factory('VariablesBrowser', $this);
				break;
			case self :: ACTION_ADMIN_BROWSE_VARIABLES :
				$component = CdaManagerComponent :: factory('AdminVariablesBrowser', $this);
				break;
			case self :: ACTION_DELETE_VARIABLE :
				$component = CdaManagerComponent :: factory('VariableDeleter', $this);
				break;
			case self :: ACTION_EDIT_VARIABLE :
				$component = CdaManagerComponent :: factory('VariableUpdater', $this);
				break;
			case self :: ACTION_CREATE_VARIABLE :
				$component = CdaManagerComponent :: factory('VariableCreator', $this);
				break;
			case self :: ACTION_BROWSE_VARIABLE_TRANSLATIONS :
				$component = CdaManagerComponent :: factory('VariableTranslationsBrowser', $this);
				break;
			case self :: ACTION_EDIT_VARIABLE_TRANSLATION :
				$component = CdaManagerComponent :: factory('VariableTranslationUpdater', $this);
				break;
			case self :: ACTION_LOCK_VARIABLE_TRANSLATION :
				$component = CdaManagerComponent :: factory('VariableTranslationLocker', $this);
				break;
			case self :: ACTION_VIEW_VARIABLE_TRANSLATION :
				$component = CdaManagerComponent :: factory('VariableTranslationViewer', $this);
				break;
			case self :: ACTION_EXPORT_TRANSLATIONS :
				$component = CdaManagerComponent :: factory('TranslationExporter', $this);
				break;
			case self :: ACTION_IMPORT_TRANSLATIONS :
				$component = CdaManagerComponent :: factory('TranslationImporter', $this);
				break;
			case self :: ACTION_ADMIN_IMPORT_TRANSLATIONS :
				$component = CdaManagerComponent :: factory('AdminTranslationImporter', $this);
				break;
			case self :: ACTION_RATE_VARIABLE_TRANSLATION :
				$component = CdaManagerComponent :: factory('VariableTranslationRater', $this);
				break;
			case self :: ACTION_CREATE_TRANSLATOR_APPLICATION :
				$component = CdaManagerComponent :: factory('TranslatorApplicationCreator', $this);
				break;
			case self :: ACTION_BROWSE_TRANSLATOR_APPLICATIONS :
				$component = CdaManagerComponent :: factory('TranslatorApplicationBrowser', $this);
				break;
			case self :: ACTION_ACTIVATE_TRANSLATOR_APPLICATION :
				$component = CdaManagerComponent :: factory('TranslatorApplicationActivator', $this);
				break;
			case self :: ACTION_DEACTIVATE_TRANSLATOR_APPLICATION :
				$component = CdaManagerComponent :: factory('TranslatorApplicationDeactivator', $this);
				break;
			case self :: ACTION_DELETE_TRANSLATOR_APPLICATION :
				$component = CdaManagerComponent :: factory('TranslatorApplicationDeleter', $this);
				break;
			case self :: ACTION_SEARCH_VARIABLE_TRANSLATIONS :
				$component = CdaManagerComponent :: factory('VariableTranslationsSearcher', $this);
				break;
			case self :: ACTION_DELETE_HISTORIC_VARIABLE_TRANSLATION :
			    $component = CdaManagerComponent :: factory('HistoricVariableTranslationDeleter', $this);
			    break;
			case self :: ACTION_REVERT_HISTORIC_VARIABLE_TRANSLATION :
			    $component = CdaManagerComponent :: factory('HistoricVariableTranslationReverter', $this);
			    break;
			case self :: ACTION_VERIFY_VARIABLE_TRANSLATION :
			    $component = CdaManagerComponent :: factory('VariableTranslationVerifier', $this);
			    break;
			case self :: ACTION_DEPRECATE_VARIABLE_TRANSLATION :
			    $component = CdaManagerComponent :: factory('VariableTranslationDeprecater', $this);
			    break;
			default :
				$this->set_action(self :: ACTION_BROWSE_CDA_LANGUAGES);
				$component = CdaManagerComponent :: factory('CdaLanguagesBrowser', $this);

		}
		$component->run();
	}

  	public function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = array('name' => Translation :: get('ManageLanguages'), 'description' => Translation :: get('ManageLanguagesDescription'), 'action' => 'list', 'url' => $this->get_admin_browse_cda_languages_link());
        $links[] = array('name' => Translation :: get('ManageLanguagePacks'), 'description' => Translation :: get('ManageLanguagePacksDescription'), 'action' => 'add', 'url' => $this->get_admin_browse_language_packs_link());
        $links[] = array('name' => Translation :: get('ManageTranslatorApplications'), 'description' => Translation :: get('ManageTranslatorApplicationsDescription'), 'action' => 'list', 'url' => $this->get_browse_translator_applications_link());
        $links[] = array('name' => Translation :: get('ImportLanguageFiles'), 'description' => Translation :: get('ImportLanguageFilesDescription'), 'action' => 'list', 'url' => $this->get_admin_import_variable_translations_url());

        $info = parent :: get_application_platform_admin_links();
        $info['links'] = $links;
        return $info;
    }

	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			switch ($_POST['action'])
			{
				case self :: PARAM_DELETE_SELECTED_CDA_LANGUAGES :

					$selected_ids = $_POST[CdaLanguageBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_CDA_LANGUAGE);
					$_GET[self :: PARAM_CDA_LANGUAGE] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_LANGUAGE_PACKS :

					$selected_ids = $_POST[LanguagePackBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_LANGUAGE_PACK);
					$_GET[self :: PARAM_LANGUAGE_PACK] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_VARIABLES :

					$selected_ids = $_POST[VariableBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_VARIABLE);
					$_GET[self :: PARAM_VARIABLE] = $selected_ids;
					break;
			}

		}
	}

	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}

	// Data Retrieving

	function count_cda_languages($condition)
	{
		return CdaDataManager :: get_instance()->count_cda_languages($condition);
	}

	function retrieve_cda_languages($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return CdaDataManager :: get_instance()->retrieve_cda_languages($condition, $offset, $count, $order_property);
	}

 	function retrieve_cda_language($id)
	{
		return CdaDataManager :: get_instance()->retrieve_cda_language($id);
	}

	function count_language_packs($condition)
	{
		return CdaDataManager :: get_instance()->count_language_packs($condition);
	}

	function retrieve_language_packs($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return CdaDataManager :: get_instance()->retrieve_language_packs($condition, $offset, $count, $order_property);
	}

 	function retrieve_language_pack($id)
	{
		return CdaDataManager :: get_instance()->retrieve_language_pack($id);
	}

	function count_variables($condition)
	{
		return CdaDataManager :: get_instance()->count_variables($condition);
	}

	function retrieve_variables($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return CdaDataManager :: get_instance()->retrieve_variables($condition, $offset, $count, $order_property);
	}

 	function retrieve_variable($id)
	{
		return CdaDataManager :: get_instance()->retrieve_variable($id);
	}

	function count_variable_translations($condition)
	{
		return CdaDataManager :: get_instance()->count_variable_translations($condition);
	}

	function retrieve_variable_translations($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return CdaDataManager :: get_instance()->retrieve_variable_translations($condition, $offset, $count, $order_property);
	}

  	function retrieve_variable_translation($variable_translation_id)
	{
		return CdaDataManager :: get_instance()->retrieve_variable_translation($variable_translation_id);
	}

 	function retrieve_variable_translation_by_parameters($language_id, $variable_id)
	{
		return CdaDataManager :: get_instance()->retrieve_variable_translation_by_parameters($language_id, $variable_id);
	}

 	function retrieve_english_translation($variable_id)
	{
		return CdaDataManager :: get_instance()->retrieve_english_translation($variable_id);
	}

	function can_language_be_locked($language)
	{
		return CdaDataManager :: get_instance()->can_language_be_locked($language);
	}

	function can_language_be_unlocked($language)
	{
		return CdaDataManager :: get_instance()->can_language_be_unlocked($language);
	}

	function can_language_pack_be_locked($language_pack, $language_id)
	{
		return CdaDataManager :: get_instance()->can_language_pack_be_locked($language_pack, $language_id);
	}

	function can_language_pack_be_unlocked($language_pack, $language_id)
	{
		return CdaDataManager :: get_instance()->can_language_pack_be_unlocked($language_pack, $language_id);
	}

 	function get_progress_for_language($language)
	{
		return CdaDataManager :: get_instance()->get_progress_for_language($language);
	}

	function get_progress_for_language_pack($language_pack, $language_id = null)
	{
		return CdaDataManager :: get_instance()->get_progress_for_language_pack($language_pack, $language_id);
	}

  	function get_status_for_language($language)
	{
		return CdaDataManager :: get_instance()->get_status_for_language($language);
	}

 	function get_status_for_language_pack($language_pack, $language_id = null)
	{
		return CdaDataManager :: get_instance()->get_status_for_language_pack($language_pack, $language_id);
	}

	// Url Creation

	function get_create_cda_language_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CDA_LANGUAGE));
	}

	function get_update_cda_language_url($cda_language)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CDA_LANGUAGE,
								    self :: PARAM_CDA_LANGUAGE => $cda_language->get_id()));
	}

 	function get_delete_cda_language_url($cda_language)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CDA_LANGUAGE,
								    self :: PARAM_CDA_LANGUAGE => $cda_language->get_id()));
	}

	function get_browse_cda_languages_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CDA_LANGUAGES));
	}

 	function get_admin_browse_cda_languages_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES));
	}

 	function get_admin_browse_cda_languages_link()
	{
		return $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES));
	}

	function get_create_language_pack_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_LANGUAGE_PACK));
	}

	function get_update_language_pack_url($language_pack)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_LANGUAGE_PACK,
								    self :: PARAM_LANGUAGE_PACK => $language_pack->get_id()));
	}

 	function get_delete_language_pack_url($language_pack)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_LANGUAGE_PACK,
								    self :: PARAM_LANGUAGE_PACK => $language_pack->get_id()));
	}

	function get_browse_language_packs_url($language_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_LANGUAGE_PACKS,
									self :: PARAM_CDA_LANGUAGE => $language_id));
	}

 	function get_admin_browse_language_packs_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS));
	}

 	function get_admin_browse_language_packs_link()
	{
		return $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS));
	}

	function get_create_variable_url($language_pack_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_VARIABLE,
									self :: PARAM_LANGUAGE_PACK => $language_pack_id));
	}

	function get_update_variable_url($variable)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_VARIABLE,
								    self :: PARAM_VARIABLE => $variable->get_id()));
	}

 	function get_delete_variable_url($variable)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_VARIABLE,
								    self :: PARAM_VARIABLE => $variable->get_id()));
	}

	function get_browse_variables_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_VARIABLES));
	}

 	function get_admin_browse_variables_url($language_pack_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_VARIABLES,
								    self :: PARAM_LANGUAGE_PACK => $language_pack_id));
	}

	function get_update_variable_translation_url($variable_translation)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_VARIABLE_TRANSLATION,
									self :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id()));
	}

	function get_browse_variable_translations_url($language_id, $language_pack_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_VARIABLE_TRANSLATIONS,
									self :: PARAM_CDA_LANGUAGE => $language_id,
									self :: PARAM_LANGUAGE_PACK => $language_pack_id));
	}

 	function get_lock_variable_translation_url($variable_translation)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_LOCK_VARIABLE_TRANSLATION,
								    self :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id()));
	}

  	function get_verify_variable_translation_url($variable_translation)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VERIFY_VARIABLE_TRANSLATION,
								    self :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id()));
	}

   	function get_deprecate_variable_translation_url($variable_translation)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DEPRECATE_VARIABLE_TRANSLATION,
								    self :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id()));
	}

	function get_lock_language_pack_url($language_pack, $language_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_LOCK_VARIABLE_TRANSLATION,
								    self :: PARAM_CDA_LANGUAGE => $language_id,
								    self :: PARAM_LANGUAGE_PACK => $language_pack->get_id(),
								    self :: PARAM_VARIABLE_TRANSLATION_STATUS => VariableTranslation :: STATUS_BLOCKED));
	}

 	function get_lock_language_url($language)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_LOCK_VARIABLE_TRANSLATION,
								    self :: PARAM_CDA_LANGUAGE => $language->get_id(),
								    self :: PARAM_VARIABLE_TRANSLATION_STATUS => VariableTranslation :: STATUS_BLOCKED));
	}

 	function get_unlock_variable_translation_url($variable_translation)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_LOCK_VARIABLE_TRANSLATION,
								    self :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id()));
	}

	function get_unlock_language_pack_url($language_pack, $language_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_LOCK_VARIABLE_TRANSLATION,
									self :: PARAM_CDA_LANGUAGE => $language_id,
								    self :: PARAM_LANGUAGE_PACK => $language_pack->get_id(),
								    self :: PARAM_VARIABLE_TRANSLATION_STATUS => VariableTranslation :: STATUS_NORMAL));
	}

 	function get_unlock_language_url($language)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_LOCK_VARIABLE_TRANSLATION,
								    self :: PARAM_CDA_LANGUAGE => $language->get_id(),
								    self :: PARAM_VARIABLE_TRANSLATION_STATUS => VariableTranslation :: STATUS_NORMAL));
	}

 	function get_view_variable_translation_url($variable_translation)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_VARIABLE_TRANSLATION,
								    self :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id()));
	}

 	function get_rate_variable_translation_url($variable_translation)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_RATE_VARIABLE_TRANSLATION,
								    self :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id()));
	}

 	function get_export_translations_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXPORT_TRANSLATIONS));
	}

 	function get_translator_application_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_TRANSLATOR_APPLICATION));
	}

 	function get_activate_translator_application_url($translator_application)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ACTIVATE_TRANSLATOR_APPLICATION, self :: PARAM_TRANSLATOR_APPLICATION => $translator_application->get_id()));
	}

	function get_deactivate_translator_application_url($translator_application)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DEACTIVATE_TRANSLATOR_APPLICATION, self :: PARAM_TRANSLATOR_APPLICATION => $translator_application->get_id()));
	}

	function get_delete_translator_application_url($translator_application)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_TRANSLATOR_APPLICATION, self :: PARAM_TRANSLATOR_APPLICATION => $translator_application->get_id()));
	}

 	function count_translator_applications($condition)
	{
		return CdaDataManager :: get_instance()->count_translator_applications($condition);
	}

	function retrieve_translator_applications($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return CdaDataManager :: get_instance()->retrieve_translator_applications($condition, $offset, $count, $order_property);
	}

 	function retrieve_translator_application($id)
	{
		return CdaDataManager :: get_instance()->retrieve_translator_application($id);
	}

  	function get_browse_translator_applications_link()
	{
		return $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_TRANSLATOR_APPLICATIONS));
	}

	function get_variable_translations_searcher_url()
	{
		return $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_SEARCH_VARIABLE_TRANSLATIONS));
	}

	function get_import_variable_translations_url()
	{
		return $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_IMPORT_TRANSLATIONS));
	}

	function get_admin_import_variable_translations_url()
	{
		return $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_ADMIN_IMPORT_TRANSLATIONS));
	}

 	function update_variable_translations($properties = array(), $condition, $offset = null, $max_objects = null, $order_by = array())
	{
		return CdaDataManager :: get_instance()->update_variable_translations($properties, $condition, $offset, $max_objects, $order_by);
	}

 	function count_historic_variable_translations($condition)
	{
		return CdaDataManager :: get_instance()->count_historic_variable_translations($condition);
	}

	function retrieve_historic_variable_translations($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return CdaDataManager :: get_instance()->retrieve_historic_variable_translations($condition, $offset, $count, $order_property);
	}

  	function retrieve_historic_variable_translation($historic_variable_translation_id)
	{
		return CdaDataManager :: get_instance()->retrieve_historic_variable_translation($historic_variable_translation_id);
	}

 	function get_delete_historic_variable_translation_url($historic_variable_translation)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_HISTORIC_VARIABLE_TRANSLATION, self :: PARAM_HISTORIC_VARIABLE_TRANSLATION => $historic_variable_translation->get_id()));
	}

  	function get_revert_historic_variable_translation_url($historic_variable_translation)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REVERT_HISTORIC_VARIABLE_TRANSLATION, self :: PARAM_HISTORIC_VARIABLE_TRANSLATION => $historic_variable_translation->get_id()));
	}
}
?>