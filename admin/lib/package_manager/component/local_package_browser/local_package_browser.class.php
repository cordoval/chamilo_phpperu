<?php
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

    function LocalPackageBrowser($manager)
    {
        $this->manager = $manager;
    }

    function to_html()
    {
        $sections = array('application', 'content_object', 'language');
        
        $current_section = Request :: get(PackageManager :: PARAM_SECTION);
        $current_section = $current_section ? $current_section : 'application';
        $html[] = '<br /><div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
        
        foreach ($sections as $section)
        {
            $html[] = '<li><a';
            if ($current_section == $section)
            {
                $html[] = ' class="current"';
            }
            $params = $this->manager->get_parameters();
            $params[PackageManager :: PARAM_SECTION] = $section;
            $html[] = ' href="' . $this->manager->get_url($params) . '">' . htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($section) . 'Title')) . '</a></li>';
        }
        
        $html[] = '</ul><div class="tabbed-pane-content">';
        
        $data = call_user_func(array($this, 'get_' . $current_section . '_data'));
        
        $table = new SimpleTable($data, new LocalPackageBrowserCellRenderer(), null, 'diagnoser');
        $html[] = $table->toHTML();
        
        $html[] = '</div></div>';
        
        return implode("\n", $html);
    }

    function get_language_data()
    {
        $languages = array();
        
        $language_path = Path :: get_language_path();
        $language_folders = Filesystem :: get_directory_content($language_path, Filesystem :: LIST_DIRECTORIES, false);
        
        $active_languages = AdminDataManager :: get_instance()->retrieve_languages();
        $installed_languages = array();
        
        while ($active_language = $active_languages->next_result())
        {
            $installed_languages[] = $active_language->get_folder();
        }
        
        $installable_languages = array_diff($language_folders, $installed_languages);
        sort($installable_languages, SORT_STRING);
        
        foreach ($installable_languages as $installable_language)
        {
            if ($installable_language !== '.svn')
            {
                $data = array();
                $data[] = Utilities :: underscores_to_camelcase_with_spaces($installable_language);
                
                $toolbar_data = array();
                $toolbar_data[] = array('href' => $this->manager->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_INSTALL_PACKAGE, PackageManager :: PARAM_SECTION => 'language', PackageManager :: PARAM_PACKAGE => $installable_language, PackageManager :: PARAM_INSTALL_TYPE => 'local')), 'label' => Translation :: get('Install'), 'img' => Theme :: get_image_path() . 'action_install.png');
                
                $data[] = Utilities :: build_toolbar($toolbar_data);
                
                $languages[] = $data;
            }
        }
        
        return $languages;
    }

    function get_content_object_data()
    {
        $objects = array();
        
        $object_path = Path :: get_repository_path() . 'lib/content_object/';
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
            if ($installable_object !== '.svn')
            {
                $data = array();
                $data[] = Utilities :: underscores_to_camelcase_with_spaces($installable_object);
                
                $toolbar_data = array();
                $toolbar_data[] = array('href' => $this->manager->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_INSTALL_PACKAGE, PackageManager :: PARAM_INSTALL_TYPE => PackageManager :: INSTALL_LOCAL, PackageManager :: PARAM_SECTION => 'content_object', PackageManager :: PARAM_PACKAGE => $installable_object)), 'label' => Translation :: get('Install'), 'img' => Theme :: get_image_path() . 'action_install.png');
                
                $data[] = Utilities :: build_toolbar($toolbar_data);
                
                $objects[] = $data;
            }
        }
        
        return $objects;
    }

    function get_application_data()
    {
        $applications = array();
        
        $application_path = Path :: get_application_path() . 'lib/';
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
            if ($installable_application !== '.svn')
            {
                $data = array();
                $data[] = Utilities :: underscores_to_camelcase_with_spaces($installable_application);
                
                $toolbar_data = array();
                $toolbar_data[] = array('href' => $this->manager->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_INSTALL_PACKAGE, PackageManager :: PARAM_INSTALL_TYPE => PackageManager :: INSTALL_LOCAL, PackageManager :: PARAM_SECTION => 'application', PackageManager :: PARAM_PACKAGE => $installable_application)), 'label' => Translation :: get('Install'), 'img' => Theme :: get_image_path() . 'action_install.png');
                
                $data[] = Utilities :: build_toolbar($toolbar_data);
                
                $applications[] = $data;
            }
        }
        
        return $applications;
    }
}
?>