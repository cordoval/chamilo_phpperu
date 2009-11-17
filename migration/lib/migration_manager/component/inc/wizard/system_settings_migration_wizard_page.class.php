<?php

/**
 * $Id: system_settings_migration_wizard_page.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/../../../../old_migration_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../../logger.class.php';
require_once dirname(__FILE__) . '/../../../../import.class.php';
/**
 * Class for user migration execution
 * @author Sven Vanpoucke
 */
class SystemSettingsMigrationWizardPage extends MigrationWizardPage
{

    /**
     * Constructor creates a new SystemSettingsMigrationWizardPage
     * @param string $page_name the page name
     * @param $parent the parent of the controller
     * @param bool $command_execute to see if the page is executed by commandline or webinterface
     */
    function SystemSettingsMigrationWizardPage($page_name, $parent, $command_execute = false)
    {
        MigrationWizardPage :: MigrationWizardPage($page_name, $parent);
        $this->command_execute = $command_execute;
        $this->succes = array(0, 0);
    }

    /**
     * @return string Title of the page
     */
    function get_title()
    {
        return Translation :: get('System_Settings_title');
    }

    /**
     * Retrieves the correct message for the correct index, this is used in cooperation with
     * $failed elements and the method getinfo 
     * @param int $index place in $failedelements for which the message must be retrieved
     */
    function get_message($index)
    {
        switch ($index)
        {
            case 0 :
                return Translation :: get('System_Settings');
            case 1 :
                return Translation :: get('System_Announcements');
            default :
                return Translation :: get('System_Settings');
        }
    }

    /**
     * Execute the page
     * Starts migration for system settings and system announcements
     */
    function perform()
    {
        $logger = new Logger('migration.txt', true);
        
        if ($logger->is_text_in_file('systemsettings'))
        {
            echo (Translation :: get('System_Settings') . ' ' . Translation :: get('already_migrated') . '<br />');
            return false;
        }
        
        $logger->write_text('systemsettings');
        
        if ($this->command_execute)
            require (dirname(__FILE__) . '/../../../../../settings.inc.php');
        else
            $exportvalues = $this->controller->exportValues();
        
        $this->old_system = $exportvalues['old_system'];
        $old_directory = $exportvalues['old_directory'];
        
        //Create logfile
        $this->logfile = new Logger('system_settings.txt');
        $this->logfile->set_start_time();
        
        //Create temporary tables, create migrationdatamanager
        $this->old_mgdm = OldMigrationDataManager :: getInstance($this->old_system, $old_directory);
        $new_mgdm = MigrationDataManager :: get_instance();
        
        if (isset($exportvalues['migrate_settings']) && $exportvalues['migrate_settings'] == 1)
        {
            //Migrate system settings
            $condition = new EqualityCondition('category', 'Platform');
            //$this->migrate_system_settings();
            $this->migrate('_Setting_Current', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array(), null, 0);
            //Migrate system announcements
            if (isset($exportvalues['migrate_users']) && $exportvalues['migrate_users'] == 1)
            {
                //$this->migrate_system_announcements();
                $id = $new_mgdm->get_id_reference($this->old_mgdm->get_old_admin_id(), 'user_user');
                $this->migrate('_System_Announcement', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array('admin_id' => $id), null, 1);
            }
            else
            {
                echo (Translation :: get('System_Announcements') . ' ' . Translation :: get('failed') . ' ' . Translation :: get('because') . ' ' . Translation :: get('Users') . ' ' . Translation :: get('skipped') . '<br />');
                $this->logfile->add_message('System announcements failed because users skipped');
                $this->succes[1] = 0;
            }
        
        }
        else
        {
            echo (Translation :: get('System_Settings') . ' & ' . Translation :: get('System_Announcements') . ' ' . Translation :: get('skipped') . '<br />');
            $this->logfile->add_message('system settings & announcements skipped');
            
            return false;
        }
        
        //Close the logfile
        $this->passedtime = $this->logfile->write_passed_time();
        $this->logfile->close_file();
        
        return true;
        
        $logger->close_file();
    }
}
?>