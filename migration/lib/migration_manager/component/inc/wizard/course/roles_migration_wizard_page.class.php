<?php
/**
 * $Id: roles_migration_wizard_page.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/../migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/../../../../../migration_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../../../logger.class.php';
require_once dirname(__FILE__) . '/../../../../../import.class.php';

/**
 * Class for course roles migration
 * @author Sven Vanpoucke
 */
class RolesMigrationWizardPage extends MigrationWizardPage
{
    private $include_deleted_files;

    /**
     * Constructor creates a new RolesMigrationWizardPage
     * @param string $page_name the page name
     * @param $parent the parent of the controller
     * @param bool $command_execute to see if the page is executed by commandline or webinterface
     */
    function RolesMigrationWizardPage($page_name, $parent, $command_execute = false)
    {
        MigrationWizardPage :: MigrationWizardPage($page_name, $parent, $command_execute);
        $this->succes = array(0, 0, 0, 0);
    }

    /**
     * @return string Title of the page
     */
    function get_title()
    {
        return Translation :: get('Roles_title');
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
                return Translation :: get('Roles');
            case 1 :
                return Translation :: get('Role_groups');
            case 2 :
                return Translation :: get('Role_permissions');
            case 3 :
                return Translation :: get('Role_users');
            default :
                return Translation :: get('Roles');
        }
    }

    /**
     * Execute the page
     * Starts migration for roles, role groups, role permissions and role users
     */
    function perform()
    {
        $logger = new Logger('migration.txt', true);
        
        if ($logger->is_text_in_file('roles'))
        {
            echo (Translation :: get('Roles') . ' ' . Translation :: get('already_migrated') . '<br />');
            return false;
        }
        
        if ($this->command_execute)
            require (dirname(__FILE__) . '/../../../../../../settings.inc.php');
        else
            $exportvalues = $this->controller->exportValues();
        
        $this->old_system = $exportvalues['old_system'];
        $old_directory = $exportvalues['old_directory'];
        $this->include_deleted_files = $exportvalues['migrate_deleted_files'];
        
        //Create logfile
        $this->logfile = new Logger('roles.txt');
        $this->logfile->set_start_time();
        
        //Create migrationdatamanager
        $this->old_mgdm = OldMigrationDataManager :: getInstance($this->old_system, $old_directory);
        $mgdm = MigrationDataManager :: get_instance();
        
        if (isset($exportvalues['move_files']) && $exportvalues['move_files'] == 1)
            $this->old_mgdm->set_move_file(true);
        
        if (isset($exportvalues['migrate_roles']) && $exportvalues['migrate_roles'] == 1)
        {
            //Migrate the dropbox
            if (isset($exportvalues['migrate_courses']) && isset($exportvalues['migrate_users']) && $exportvalues['migrate_courses'] == 1 && $exportvalues['migrate_users'] == 1)
            {
                $courseclass = Import :: factory($this->old_system, '_course');
                $database_table = $courseclass->get_database_table(null);
                
                $max_records = $this->old_mgdm->count_records($database_table['database'], $database_table['table']);
                $retrieve_parms = array();
                $retrieve_parms['old_mgdm'] = $this->old_mgdm;
                
                $current_record = 0;
                $mgdm = MigrationDataManager :: get_instance();
                
                while ($max_records > 0)
                {
                    if ($max_records - 1000 > 0)
                    {
                        $retrieve_parms['offset'] = $current_record;
                        $retrieve_parms['limit'] = 1000;
                    }
                    else
                    {
                        $retrieve_parms['offset'] = $current_record;
                        $retrieve_parms['limit'] = $max_records;
                    }
                    
                    $courses = array();
                    $courses = $courseclass->get_all($retrieve_parms);
                    
                    foreach ($courses as $i => $course)
                    {
                        if ($mgdm->get_failed_element('dokeos_main.course', $course->get_code()))
                        {
                            continue;
                        }
                        
                        //$this->migrate('_Roles', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array('old_mgdm' => $this->old_mgdm), $course,0);
                        //$this->migrate('_Role_Group', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array('old_mgdm' => $this->old_mgdm), $course,1);
                        //$this->migrate('_Role_Permissions', old_mgdm('mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array('old_mgdm' => $this->old_mgdm), $course,2);
                        //$this->migrate('_Role_User', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array('old_mgdm' => $this->old_mgdm), $course,3);
                        

                        unset($course);
                        unset($courses[$i]);
                    }
                    
                    $courses = array();
                    unset($courses);
                    $current_record += $retrieve_parms['limit'];
                    $max_records -= $retrieve_parms['limit'];
                }
            }
            else
            {
                echo (Translation :: get('Roles') . Translation :: get('failed') . ' ' . Translation :: get('because') . ' ' . Translation :: get('Users') . ' ' . Translation :: get('skipped') . '<br />');
                $this->logfile->add_message('Roles failed because users or courses skipped');
                $this->succes = array(0, 0, 0, 0);
            }
        
        }
        else
        {
            echo (Translation :: get('Roles') . ' ' . Translation :: get('skipped') . '<br />');
            $this->logfile->add_message('Roles skipped');
            
            return false;
        }
        
        //Close the logfile
        $this->passedtime = $this->logfile->write_passed_time();
        $this->logfile->close_file();
        
        $logger->write_text('roles');
        
        return true;
    }

}
?>