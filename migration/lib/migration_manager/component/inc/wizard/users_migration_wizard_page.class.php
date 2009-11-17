<?php
/**
 * $Id: users_migration_wizard_page.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/../../../../migration_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../../old_migration_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../../logger.class.php';
require_once dirname(__FILE__) . '/../../../../import.class.php';

/**
 * Class for user migration execution
 * 
 */
class UsersMigrationWizardPage extends MigrationWizardPage
{
    private $failed_users = array();
    private $users_succes = 0;

    /**
     * Constructor creates a new UsersMigrationWizardPage
     * @param string $page_name the page name
     * @param $parent the parent of the controller
     * @param bool $command_execute to see if the page is executed by commandline or webinterface
     */
    function UsersMigrationWizardPage($page_name, $parent, $command_execute = false)
    {
        parent :: MigrationWizardPage($page_name, $parent);
        $this->command_execute = $command_execute;
        $this->succes = array(0);
    }

    /**
     * @return string Title of the page
     */
    function get_title()
    {
        return Translation :: get('Users_title');
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
                return Translation :: get('users');
            default :
                return Translation :: get('users');
        }
    }

    /**
     * Execute the page
     * Starts migration for users
     */
    function perform()
    {
        $logger = new Logger('migration.txt', true);
        
        if ($logger->is_text_in_file('users'))
        {
            echo (Translation :: get('Users') . ' ' . Translation :: get('already_migrated') . '<br />');
            
            $logger->close_file();
            return false;
        }
        
        $logger->write_text('users');
        
        if ($this->command_execute)
            require (dirname(__FILE__) . '/../../../../../settings.inc.php');
        else
            $exportvalues = $this->controller->exportValues();
        
        $this->old_system = $exportvalues['old_system'];
        $old_directory = $exportvalues['old_directory'];
        
        //Create logfile
        $this->logfile = new Logger('users.txt');
        $this->logfile->set_start_time();
        
        //Create temporary tables, create migrationdatamanager
        $this->old_mgdm = OldMigrationDataManager :: getInstance($this->old_system, $old_directory);
        
        if (isset($exportvalues['move_files']) && $exportvalues['move_files'] == 1)
            $this->old_mgdm->set_move_file(true);
        
        $mgdm = MigrationDataManager :: get_instance();
        $mgdm->create_temporary_tables();
        
        //Migrate the users
        if (isset($exportvalues['migrate_users']) && $exportvalues['migrate_users'] == 1)
        {
            $lcms_users = array();
            $resultset = UserDataManager :: get_instance()->retrieve_users();
            
            while ($lcms_user = $resultset->next_result())
            {
                $lcms_users[] = $lcms_user;
            }
            $this->migrate('_User', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array('old_mgdm' => $this->old_mgdm, 'lcms_users' => $lcms_users), null, 0);
        }
        else
        {
            echo (Translation :: get('Users') . ' ' . Translation :: get('skipped') . '<br />');
            $this->logfile->add_message('users_skipped');
            return false;
        }
        
        //Close the logfile
        $this->passedtime = $this->logfile->write_passed_time();
        $this->logfile->close_file();
        
        $logger->close_file();
        
        return true;
    }
}
?>
