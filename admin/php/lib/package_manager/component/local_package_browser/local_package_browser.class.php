<?php
namespace admin;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\Filesystem;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\SimpleTable;
use common\libraries\DynamicVisualTabsRenderer;
use common\libraries\DynamicVisualTab;
use common\extensions\external_repository_manager\ExternalRepositoryManager;

/**
 * $Id: local_package_browser.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_manager.component.local_package_browser
 */
require_once dirname(__FILE__) . '/local_package_browser_cell_renderer.class.php';

class LocalPackageBrowser
{
    /**
     * The manager where this browser runs on
     */
    private $manager;

    /**
     * The status's
     */
    const STATUS_OK = 1;
    const STATUS_WARNING = 2;
    const STATUS_ERROR = 3;
    const STATUS_INFORMATION = 4;

    function __construct($manager)
    {
        $this->manager = $manager;
    }

    function to_html()
    {
        $sections = array('application',
                'content_object',
                'language',
                'external_repository_manager');

        $current_section = Request :: get(PackageManager :: PARAM_SECTION);
        $current_section = $current_section ? $current_section : 'application';

        $data = call_user_func(array($this,
                'get_' . $current_section . '_data'));

        $table = new SimpleTable($data, new LocalPackageBrowserCellRenderer(), null, 'local_package_browser_table');

        $tabs = new DynamicVisualTabsRenderer('local_package_browser_tabs', $table->toHtml());
        foreach ($sections as $section)
        {
            $selected = ($section == $current_section ? true : false);

            $label = htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($section) . 'Packages'));
            $params = $this->manager->get_parameters();
            $params[PackageManager :: PARAM_SECTION] = $section;
            $link = $this->manager->get_url($params);

            $tabs->add_tab(new DynamicVisualTab($section, $label, Theme :: get_image_path() . 'place_mini_' . $section . '.png', $link, $selected));

        }

        return $tabs->render();
    }

    function get_language_data()
    {
        $languages = array();

        $language_path = Path :: get_common_libraries_path() . 'resources/i18n/';
        $language_files = Filesystem :: get_directory_content($language_path, Filesystem :: LIST_FILES, false);

        $available_languages = array();
        foreach ($language_files as $language_file)
        {
            $file_info = pathinfo($language_file);
            $available_languages[] = $file_info['filename'];
        }

        $active_languages = AdminDataManager :: get_instance()->retrieve_languages();
        $installed_languages = array();

        while ($active_language = $active_languages->next_result())
        {
            $installed_languages[] = $active_language->get_isocode();
        }

        $installable_languages = array_diff($available_languages, $installed_languages);
        sort($installable_languages, SORT_STRING);

        foreach ($installable_languages as $installable_language)
        {
            $data = array();
            $data[] = Utilities :: underscores_to_camelcase_with_spaces($installable_language);

            $toolbar = new Toolbar();
            $toolbar->add_item(new ToolbarItem(Translation :: get('Install', array(), Utilities :: COMMON_LIBRARIES), Theme :: get_image_path() . 'action_install.png', $this->manager->get_url(array(
                    PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_INSTALL_PACKAGE,
                    PackageManager :: PARAM_SECTION => 'language',
                    PackageManager :: PARAM_PACKAGE => $installable_language,
                    PackageManager :: PARAM_INSTALL_TYPE => 'local')), ToolbarItem :: DISPLAY_ICON));
            $data[] = $toolbar->as_html();

            $languages[] = $data;
        }

        return $languages;
    }

    function get_content_object_data()
    {
        $objects = array();

        $object_path = Path :: get_repository_content_object_path();
        $object_folders = Filesystem :: get_directory_content($object_path, Filesystem :: LIST_DIRECTORIES, false);

        $condition = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_CONTENT_OBJECT);
        $registrations = AdminDataManager :: get_instance()->retrieve_registrations($condition);
        $installed_objects = array();

        while ($registration = $registrations->next_result())
        {
            $installed_objects[] = $registration->get_name();
        }

        $installable_objects = array_diff($object_folders, $installed_objects);
        sort($installable_objects, SORT_STRING);

        foreach ($installable_objects as $installable_object)
        {
            $data = array();
            $data[] = Utilities :: underscores_to_camelcase_with_spaces($installable_object);

            $toolbar = new Toolbar();
            $toolbar->add_item(new ToolbarItem(Translation :: get('Install', array(), Utilities :: COMMON_LIBRARIES), Theme :: get_image_path() . 'action_install.png', $this->manager->get_url(array(
                    PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_INSTALL_PACKAGE,
                    PackageManager :: PARAM_INSTALL_TYPE => PackageManager :: INSTALL_LOCAL,
                    PackageManager :: PARAM_SECTION => 'content_object',
                    PackageManager :: PARAM_PACKAGE => $installable_object)), ToolbarItem :: DISPLAY_ICON));
            $data[] = $toolbar->as_html();

            $objects[] = $data;
        }

        return $objects;
    }

    function get_application_data()
    {
        $applications = array();

        $application_path = Path :: get_application_path();
        $application_folders = Filesystem :: get_directory_content($application_path, Filesystem :: LIST_DIRECTORIES, false);

        $condition = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_APPLICATION);
        $registrations = AdminDataManager :: get_instance()->retrieve_registrations($condition);
        $installed_applications = array();

        while ($registration = $registrations->next_result())
        {
            $installed_applications[] = $registration->get_name();
        }

        $installable_applications = array_diff($application_folders, $installed_applications);
        sort($installable_applications, SORT_STRING);

        foreach ($installable_applications as $installable_application)
        {
            $data = array();
            $data[] = Utilities :: underscores_to_camelcase_with_spaces($installable_application);

            $toolbar = new Toolbar();
            $toolbar->add_item(new ToolbarItem(Translation :: get('Install', array(), Utilities :: COMMON_LIBRARIES), Theme :: get_image_path() . 'action_install.png', $this->manager->get_url(array(
                    PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_INSTALL_PACKAGE,
                    PackageManager :: PARAM_INSTALL_TYPE => PackageManager :: INSTALL_LOCAL,
                    PackageManager :: PARAM_SECTION => 'application',
                    PackageManager :: PARAM_PACKAGE => $installable_application)), ToolbarItem :: DISPLAY_ICON));
            $data[] = $toolbar->as_html();

            $applications[] = $data;
        }

        return $applications;
    }

    function get_external_repository_manager_data()
    {
        $external_repository_managers = array();

        $external_repository_manager_path = Path :: get_common_extensions_path() . 'external_repository_manager/implementation/';
        $folders = Filesystem :: get_directory_content($external_repository_manager_path, Filesystem :: LIST_DIRECTORIES, false);

        $condition = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_EXTERNAL_REPOSITORY_MANAGER);
        $registrations = AdminDataManager :: get_instance()->retrieve_registrations($condition);
        $installed = array();

        while ($registration = $registrations->next_result())
        {
            $installed[] = $registration->get_name();
        }

        $installables = array_diff($folders, $installed);
        sort($installables, SORT_STRING);

        foreach ($installables as $installable)
        {
            $data = array();
            $data[] = Translation :: get('TypeName', null, ExternalRepositoryManager :: get_namespace($installable));

            $toolbar = new Toolbar();
            $toolbar->add_item(new ToolbarItem(Translation :: get('Install', array(), Utilities :: COMMON_LIBRARIES), Theme :: get_image_path() . 'action_install.png', $this->manager->get_url(array(
                    PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_INSTALL_PACKAGE,
                    PackageManager :: PARAM_SECTION => 'external_repository_manager',
                    PackageManager :: PARAM_PACKAGE => $installable,
                    PackageManager :: PARAM_INSTALL_TYPE => 'local')), ToolbarItem :: DISPLAY_ICON));
            $data[] = $toolbar->as_html();

            $external_repository_managers[] = $data;
        }

        return $external_repository_managers;
    }
}
?>