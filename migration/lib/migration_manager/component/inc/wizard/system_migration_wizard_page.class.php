<?php
/**
 * $Id: system_migration_wizard_page.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/migration_wizard_page.class.php';

/**
 * This form can be used to let the user select the settings
 * 
 * @author Sven Vanpoucke
 */
class SystemMigrationWizardPage extends MigrationWizardPage
{

    /**
     * @return string Title of the page
     */
    function get_title()
    {
        return Translation :: get('System_title');
    }

    /**
     * @return string Info of the page
     */
    function get_info()
    {
        return Translation :: get('System_info') . ':';
    }

    /**
     * Build the form
     */
    function buildForm()
    {
        $this->_formBuilt = true;
        $this->addElement('select', 'old_system', Translation :: get('Old_system') . ':', $this->get_old_systems_list());
        $buttons[0] = HTML_QuickForm :: createElement('submit', $this->getButtonName('next'), Translation :: get('Next') . ' >>');
        $this->addGroup($buttons, 'buttons', '', '&nbsp', false);
        $this->setDefaultAction('next');
        $this->set_form_defaults();
    }

    /**
     * Retrieves the old system list 
     * @return array of strings with the old system list
     */
    function get_old_systems_list()
    {
        $system_list = array();
        
        $path = Path :: get(SYS_APP_MIGRATION_PATH) . 'platform/';
        $directories = Filesystem :: get_directory_content($path, Filesystem :: LIST_DIRECTORIES, false);
        foreach ($directories as $index => $directory)
        {
            if ($directory[0] !== '.')
            {
                $system_list[$directory] = $directory;
            }
        }
        
        return $system_list;
    }

    /**
     * Executes the page
     * Clean the main logger
     */
    function perform()
    {
        new Logger('migration.txt', false);
    }

    /**
     * Set form defaults
     */
    function set_form_defaults()
    {
        $defaults = array();
        $defaults['old_system'] = 'dokeos185';
        $this->setDefaults($defaults);
    }
}
?>