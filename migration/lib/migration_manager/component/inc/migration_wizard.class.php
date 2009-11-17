<?php
/**
 * $Id: migration_wizard.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc
 */
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__) . '/wizard/system_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/settings_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/migration_wizard_display.class.php';
require_once dirname(__FILE__) . '/wizard/users_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/classes_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/courses_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/system_settings_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/personal_agendas_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/course/meta_data_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/course/groups_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/course/announcements_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/course/calendar_events_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/course/documents_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/course/links_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/course/dropboxes_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/course/forums_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/course/learning_paths_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/course/quiz_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/course/student_publications_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/course/surveys_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/course/scorms_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/course/assignments_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/course/user_infos_migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/trackings_migration_wizard_page.class.php';

/**
 * A wizard which guides the user through several steps to perform the migration
 * 
 * @author Sven Vanpoucke
 */
class MigrationWizard extends HTML_QuickForm_Controller
{
    /** 
     * The component in which the wizard runs
     */
    private $parent;

    /**
     * Creates a new MigrationWizard
     * @param MigrationManagerComponent $parent The migrationmanager component 
     * in which this wizard runs.
     */
    function MigrationWizard($parent)
    {
        global $language_interface;
        $this->parent = $parent;
        parent :: HTML_QuickForm_Controller('MigrationWizard', true);
        $this->addPage(new SystemMigrationWizardPage('page_system', $this->parent));
        $this->addPage(new SettingsMigrationWizardPage('page_ssettings', $this->parent));
        
        $this->addpages();
        
        $this->addAction('display', new MigrationWizardDisplay($this->parent));
    }

    /**
     * Creates the pages that belong to a certain old system
     * This pages are defined in wizard.xml in the old system directory
     */
    function addpages()
    {
        $exports = $this->exportValues();
        $old_system = $exports['old_system'];
        
        if (! $old_system)
            return;
        
        $pages = $this->loadpages($old_system);
        foreach ($pages as $name => $page)
        {
            if (isset($exports['settings']))
            {
                if (isset($exports['migrate' . substr($name, 4)]) && $exports['migrate' . substr($name, 4)] == 1)
                {
                    $this->addPage(new $page($name, $this->parent));
                }
            }
            else
                $this->addPage(new $page($name, $this->parent));
        }
    }

    /**
     * Loads all pages from the wizard.xml file in the old system directory
     * @param string $old_system the old system directory
     */
    function loadpages($old_system)
    {
        $file = realpath(Path :: get_migration_path() . 'platform/' . $old_system . '/wizards.xml');
        $doc = new DOMDocument();
        $doc->load($file);
        $platform = $doc->getElementsByTagname('platform')->item(0);
        $name = $platform->getAttribute('name');
        $xml_wizards = $doc->getElementsByTagname('wizard');
        
        $wizardpages = array();
        
        foreach ($xml_wizards as $wizard)
        {
            if ($wizard->hasAttribute('name') && $wizard->hasAttribute('wizardpage'))
                $wizardpages[$wizard->getAttribute('name')] = $wizard->getAttribute('wizardpage');
        }
        
        return $wizardpages;
    
    }
}
?>