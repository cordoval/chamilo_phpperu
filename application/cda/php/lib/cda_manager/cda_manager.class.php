<?php
/**
 * @package application.lib.cda.cda_manager
 */
require_once WebApplication :: get_application_class_lib_path('cda') . 'cda_manager/component/cda_language_browser/cda_language_browser_table.class.php';
require_once WebApplication :: get_application_class_lib_path('cda') . 'cda_manager/component/language_pack_browser/language_pack_browser_table.class.php';
require_once WebApplication :: get_application_class_lib_path('cda') . 'cda_manager/component/variable_browser/variable_browser_table.class.php';
require_once WebApplication :: get_application_class_lib_path('cda') . 'cda_manager/component/variable_translation_browser/variable_translation_browser_table.class.php';

/**
 * A cda manager
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManager extends WebApplication
{
    const APPLICATION_NAME = 'cda';

    const ACTION_DELETE_CDA_LANGUAGE = 'cda_language_deleter';
    const ACTION_EDIT_CDA_LANGUAGE = 'cda_language_updater';
    const ACTION_CREATE_CDA_LANGUAGE = 'cda_language_creator';
    const ACTION_BROWSE_CDA_LANGUAGES = 'cda_languages_browser';
    const ACTION_ADMIN_BROWSE_CDA_LANGUAGES = 'admin_cda_languages_browser';

    const ACTION_DELETE_LANGUAGE_PACK = 'language_pack_deleter';
    const ACTION_EDIT_LANGUAGE_PACK = 'language_pack_updater';
    const ACTION_CREATE_LANGUAGE_PACK = 'language_pack_creator';
    const ACTION_BROWSE_LANGUAGE_PACKS = 'language_packs_browser';
    const ACTION_ADMIN_BROWSE_LANGUAGE_PACKS = 'admin_language_packs_browser';

    const ACTION_DELETE_VARIABLE = 'variable_deleter';
    const ACTION_EDIT_VARIABLE = 'variable_updater';
    const ACTION_CREATE_VARIABLE = 'variable_creator';
    const ACTION_BROWSE_VARIABLES = 'variables_browser';
    const ACTION_ADMIN_BROWSE_VARIABLES = 'admin_variables_browser';

    const ACTION_EDIT_VARIABLE_TRANSLATION = 'variable_translation_updater';
    const ACTION_BROWSE_VARIABLE_TRANSLATIONS = 'variable_translations_browser';
    const ACTION_LOCK_VARIABLE_TRANSLATION = 'variable_translation_locker';
    const ACTION_VIEW_VARIABLE_TRANSLATION = 'variable_translation_viewer';
    const ACTION_EXPORT_TRANSLATIONS = 'translation_exporter';
    const ACTION_IMPORT_TRANSLATIONS = 'translation_importer';
    const ACTION_ADMIN_IMPORT_TRANSLATIONS = 'admin_translation_importer';
    const ACTION_RATE_VARIABLE_TRANSLATION = 'variable_translation_rater';
    const ACTION_VERIFY_VARIABLE_TRANSLATION = 'variable_translation_verifier';
    const ACTION_DEPRECATE_VARIABLE_TRANSLATION = 'variable_translation_deprecater';
    const ACTION_SEARCH_VARIABLE_TRANSLATIONS = 'variable_translations_searcher';

    const ACTION_CREATE_TRANSLATOR_APPLICATION = 'translator_application_creator';
    const ACTION_BROWSE_TRANSLATOR_APPLICATIONS = 'translator_application_browser';
    const ACTION_ACTIVATE_TRANSLATOR_APPLICATION = 'translator_application_activator';
    const ACTION_DEACTIVATE_TRANSLATOR_APPLICATION = 'translator_application_deactivator';
    const ACTION_DELETE_TRANSLATOR_APPLICATION = 'translator_application_deleter';

    const ACTION_DELETE_HISTORIC_VARIABLE_TRANSLATION = 'historic_variable_translation_deleter';
    const ACTION_REVERT_HISTORIC_VARIABLE_TRANSLATION = 'historic_variable_translation_reverter';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_CDA_LANGUAGES;

    const PARAM_CDA_LANGUAGE = 'cda_language';
    const PARAM_DELETE_SELECTED_CDA_LANGUAGES = 'delete_selected_cda_languages';
    const PARAM_LANGUAGE_PACK = 'language_pack';
    const PARAM_DELETE_SELECTED_LANGUAGE_PACKS = 'delete_selected_language_packs';
    const PARAM_VARIABLE = 'variable';
    const PARAM_DELETE_SELECTED_VARIABLES = 'delete_selected_variables';
    const PARAM_VARIABLE_TRANSLATION_STATUS = 'translation_status';
    const PARAM_VARIABLE_TRANSLATION = 'variable_translation';
    const PARAM_TRANSLATOR_APPLICATION = 'translator_application';
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

        if ($action != self :: ACTION_EDIT_VARIABLE_TRANSLATION)
            unset($_SESSION['skipped_variable_translations']);

        switch ($action)
        {
            case self :: ACTION_BROWSE_CDA_LANGUAGES :
                $component = $this->create_component('CdaLanguagesBrowser');
                break;
            case self :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES :
                $component = $this->create_component('AdminCdaLanguagesBrowser');
                break;
            case self :: ACTION_DELETE_CDA_LANGUAGE :
                $component = $this->create_component('CdaLanguageDeleter');
                break;
            case self :: ACTION_EDIT_CDA_LANGUAGE :
                $component = $this->create_component('CdaLanguageUpdater');
                break;
            case self :: ACTION_CREATE_CDA_LANGUAGE :
                $component = $this->create_component('CdaLanguageCreator');
                break;
            case self :: ACTION_BROWSE_LANGUAGE_PACKS :
                $component = $this->create_component('LanguagePacksBrowser');
                break;
            case self :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS :
                $component = $this->create_component('AdminLanguagePacksBrowser');
                break;
            case self :: ACTION_DELETE_LANGUAGE_PACK :
                $component = $this->create_component('LanguagePackDeleter');
                break;
            case self :: ACTION_EDIT_LANGUAGE_PACK :
                $component = $this->create_component('LanguagePackUpdater');
                break;
            case self :: ACTION_CREATE_LANGUAGE_PACK :
                $component = $this->create_component('LanguagePackCreator');
                break;
            case self :: ACTION_BROWSE_VARIABLES :
                $component = $this->create_component('VariablesBrowser');
                break;
            case self :: ACTION_ADMIN_BROWSE_VARIABLES :
                $component = $this->create_component('AdminVariablesBrowser');
                break;
            case self :: ACTION_DELETE_VARIABLE :
                $component = $this->create_component('VariableDeleter');
                break;
            case self :: ACTION_EDIT_VARIABLE :
                $component = $this->create_component('VariableUpdater');
                break;
            case self :: ACTION_CREATE_VARIABLE :
                $component = $this->create_component('VariableCreator');
                break;
            case self :: ACTION_BROWSE_VARIABLE_TRANSLATIONS :
                $component = $this->create_component('VariableTranslationsBrowser');
                break;
            case self :: ACTION_EDIT_VARIABLE_TRANSLATION :
                $component = $this->create_component('VariableTranslationUpdater');
                break;
            case self :: ACTION_LOCK_VARIABLE_TRANSLATION :
                $component = $this->create_component('VariableTranslationLocker');
                break;
            case self :: ACTION_VIEW_VARIABLE_TRANSLATION :
                $component = $this->create_component('VariableTranslationViewer');
                break;
            case self :: ACTION_EXPORT_TRANSLATIONS :
                $component = $this->create_component('TranslationExporter');
                break;
            case self :: ACTION_IMPORT_TRANSLATIONS :
                $component = $this->create_component('TranslationImporter');
                break;
            case self :: ACTION_ADMIN_IMPORT_TRANSLATIONS :
                $component = $this->create_component('AdminTranslationImporter');
                break;
            case self :: ACTION_RATE_VARIABLE_TRANSLATION :
                $component = $this->create_component('VariableTranslationRater');
                break;
            case self :: ACTION_CREATE_TRANSLATOR_APPLICATION :
                $component = $this->create_component('TranslatorApplicationCreator');
                break;
            case self :: ACTION_BROWSE_TRANSLATOR_APPLICATIONS :
                $component = $this->create_component('TranslatorApplicationBrowser');
                break;
            case self :: ACTION_ACTIVATE_TRANSLATOR_APPLICATION :
                $component = $this->create_component('TranslatorApplicationActivator');
                break;
            case self :: ACTION_DEACTIVATE_TRANSLATOR_APPLICATION :
                $component = $this->create_component('TranslatorApplicationDeactivator');
                break;
            case self :: ACTION_DELETE_TRANSLATOR_APPLICATION :
                $component = $this->create_component('TranslatorApplicationDeleter');
                break;
            case self :: ACTION_SEARCH_VARIABLE_TRANSLATIONS :
                $component = $this->create_component('VariableTranslationsSearcher');
                break;
            case self :: ACTION_DELETE_HISTORIC_VARIABLE_TRANSLATION :
                $component = $this->create_component('HistoricVariableTranslationDeleter');
                break;
            case self :: ACTION_REVERT_HISTORIC_VARIABLE_TRANSLATION :
                $component = $this->create_component('HistoricVariableTranslationReverter');
                break;
            case self :: ACTION_VERIFY_VARIABLE_TRANSLATION :
                $component = $this->create_component('VariableTranslationVerifier');
                break;
            case self :: ACTION_DEPRECATE_VARIABLE_TRANSLATION :
                $component = $this->create_component('VariableTranslationDeprecater');
                break;
            default :
                $this->set_action(self :: ACTION_BROWSE_CDA_LANGUAGES);
                $component = $this->create_component('CdaLanguagesBrowser');

        }
        $component->run();
    }

    public static function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('ManageLanguages'), Translation :: get('ManageLanguagesDescription'), Theme :: get_image_path() . 'browse_list.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES)));
        $links[] = new DynamicAction(Translation :: get('ManageLanguagePacks'), Translation :: get('ManageLanguagePacksDescription'), Theme :: get_image_path() . 'browse_add.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS)));
        $links[] = new DynamicAction(Translation :: get('ManageTranslatorApplications'), Translation :: get('ManageTranslatorApplicationsDescription'), Theme :: get_image_path() . 'browse_list.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                self :: PARAM_ACTION => self :: ACTION_BROWSE_TRANSLATOR_APPLICATIONS)));
        $links[] = new DynamicAction(Translation :: get('ImportLanguageFiles'), Translation :: get('ImportLanguageFilesDescription'), Theme :: get_image_path() . 'browse_list.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                self :: PARAM_ACTION => self :: ACTION_ADMIN_IMPORT_TRANSLATIONS)));

        $info = parent :: get_application_platform_admin_links(self :: APPLICATION_NAME);
        $info['links'] = $links;
        return $info;
    }

    private function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            switch ($_POST['action'])
            {
                case self :: PARAM_DELETE_SELECTED_CDA_LANGUAGES :

                    $selected_ids = $_POST[CdaLanguageBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];

                    if (empty($selected_ids))
                    {
                        $selected_ids = array();
                    }
                    elseif (! is_array($selected_ids))
                    {
                        $selected_ids = array($selected_ids);
                    }

                    $this->set_action(self :: ACTION_DELETE_CDA_LANGUAGE);
                    $_GET[self :: PARAM_CDA_LANGUAGE] = $selected_ids;
                    break;
                case self :: PARAM_DELETE_SELECTED_LANGUAGE_PACKS :

                    $selected_ids = $_POST[LanguagePackBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];

                    if (empty($selected_ids))
                    {
                        $selected_ids = array();
                    }
                    elseif (! is_array($selected_ids))
                    {
                        $selected_ids = array($selected_ids);
                    }

                    $this->set_action(self :: ACTION_DELETE_LANGUAGE_PACK);
                    $_GET[self :: PARAM_LANGUAGE_PACK] = $selected_ids;
                    break;
                case self :: PARAM_DELETE_SELECTED_VARIABLES :

                    $selected_ids = $_POST[VariableBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];

                    if (empty($selected_ids))
                    {
                        $selected_ids = array();
                    }
                    elseif (! is_array($selected_ids))
                    {
                        $selected_ids = array($selected_ids);
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
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CDA_LANGUAGE, self :: PARAM_CDA_LANGUAGE => $cda_language->get_id()));
    }

    function get_delete_cda_language_url($cda_language)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CDA_LANGUAGE, self :: PARAM_CDA_LANGUAGE => $cda_language->get_id()));
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
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_LANGUAGE_PACK, self :: PARAM_LANGUAGE_PACK => $language_pack->get_id()));
    }

    function get_delete_language_pack_url($language_pack)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_LANGUAGE_PACK, self :: PARAM_LANGUAGE_PACK => $language_pack->get_id()));
    }

    function get_browse_language_packs_url($language_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_LANGUAGE_PACKS, self :: PARAM_CDA_LANGUAGE => $language_id));
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
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_VARIABLE, self :: PARAM_LANGUAGE_PACK => $language_pack_id));
    }

    function get_update_variable_url($variable)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_VARIABLE, self :: PARAM_VARIABLE => $variable->get_id()));
    }

    function get_delete_variable_url($variable)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_VARIABLE, self :: PARAM_VARIABLE => $variable->get_id()));
    }

    function get_browse_variables_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_VARIABLES));
    }

    function get_admin_browse_variables_url($language_pack_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_VARIABLES, self :: PARAM_LANGUAGE_PACK => $language_pack_id));
    }

    function get_update_variable_translation_url($variable_translation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_VARIABLE_TRANSLATION, self :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id()));
    }

    function get_browse_variable_translations_url($language_id, $language_pack_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_VARIABLE_TRANSLATIONS, self :: PARAM_CDA_LANGUAGE => $language_id, self :: PARAM_LANGUAGE_PACK => $language_pack_id));
    }

    function get_lock_variable_translation_url($variable_translation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_LOCK_VARIABLE_TRANSLATION, self :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id()));
    }

    function get_verify_variable_translation_url($variable_translation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VERIFY_VARIABLE_TRANSLATION, self :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id()));
    }

    function get_deprecate_variable_translation_url($variable_translation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DEPRECATE_VARIABLE_TRANSLATION, self :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id()));
    }

    function get_lock_language_pack_url($language_pack, $language_id)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_LOCK_VARIABLE_TRANSLATION, self :: PARAM_CDA_LANGUAGE => $language_id, self :: PARAM_LANGUAGE_PACK => $language_pack->get_id(),
                self :: PARAM_VARIABLE_TRANSLATION_STATUS => VariableTranslation :: STATUS_BLOCKED));
    }

    function get_lock_language_url($language)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_LOCK_VARIABLE_TRANSLATION, self :: PARAM_CDA_LANGUAGE => $language->get_id(), self :: PARAM_VARIABLE_TRANSLATION_STATUS => VariableTranslation :: STATUS_BLOCKED));
    }

    function get_unlock_variable_translation_url($variable_translation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_LOCK_VARIABLE_TRANSLATION, self :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id()));
    }

    function get_unlock_language_pack_url($language_pack, $language_id)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_LOCK_VARIABLE_TRANSLATION, self :: PARAM_CDA_LANGUAGE => $language_id, self :: PARAM_LANGUAGE_PACK => $language_pack->get_id(),
                self :: PARAM_VARIABLE_TRANSLATION_STATUS => VariableTranslation :: STATUS_NORMAL));
    }

    function get_unlock_language_url($language)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_LOCK_VARIABLE_TRANSLATION, self :: PARAM_CDA_LANGUAGE => $language->get_id(), self :: PARAM_VARIABLE_TRANSLATION_STATUS => VariableTranslation :: STATUS_NORMAL));
    }

    function get_view_variable_translation_url($variable_translation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_VARIABLE_TRANSLATION, self :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id()));
    }

    function get_rate_variable_translation_url($variable_translation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_RATE_VARIABLE_TRANSLATION, self :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id()));
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

    function retrieve_first_untranslated_variable_translation($language_id, $language_pack_id = null, $status = null)
    {
        return CdaDataManager :: get_instance()->retrieve_first_untranslated_variable_translation($language_id, $language_pack_id, $status);
    }

    function get_delete_historic_variable_translation_url($historic_variable_translation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_HISTORIC_VARIABLE_TRANSLATION, self :: PARAM_HISTORIC_VARIABLE_TRANSLATION => $historic_variable_translation->get_id()));
    }

    function get_revert_historic_variable_translation_url($historic_variable_translation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REVERT_HISTORIC_VARIABLE_TRANSLATION, self :: PARAM_HISTORIC_VARIABLE_TRANSLATION => $historic_variable_translation->get_id()));
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }
}
?>