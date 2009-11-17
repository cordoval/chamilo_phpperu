<?php
/**
 * $Id: groups_migration_wizard_page.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/../migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/../../../../../migration_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../../../logger.class.php';
require_once dirname(__FILE__) . '/../../../../../import.class.php';
/**
 * Class for course groups migration
 * @author Sven Vanpoucke
 */
class GroupsMigrationWizardPage extends MigrationWizardPage
{

    /**
     * Constructor creates a new GroupsMigrationWizardPage
     * @param string $page_name the page name
     * @param $parent the parent of the controller
     * @param bool $command_execute to see if the page is executed by commandline or webinterface
     */
    function GroupsMigrationWizardPage($page_name, $parent, $command_execute = false)
    {
        MigrationWizardPage :: MigrationWizardPage($page_name, $parent);
        $this->command_execute = $command_execute;
        $this->succes = array(0, 0, 0, 0);
    }

    /**
     * @return string Title of the page
     */
    function get_title()
    {
        return Translation :: get('Groups_title');
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
                return Translation :: get('Group_categories');
            case 1 :
                return Translation :: get('Groups');
            case 2 :
                return Translation :: get('Group_rel_users');
            case 3 :
                return Translation :: get('Group_rel_tutors');
            default :
                return Translation :: get('Groups');
        }
    }

    /**
     * Execute the page
     * Starts migration for groups, group users , group tutors and group categories
     */
    function perform()
    {
        $logger = new Logger('migration.txt', true);
        
        if ($logger->is_text_in_file('groups'))
        {
            echo (Translation :: get('Groups') . ' ' . Translation :: get('already_migrated') . '<br />');
            return false;
        }
        
        $logger->write_text('groups');
        
        if ($this->command_execute)
            require (dirname(__FILE__) . '/../../../../../../settings.inc.php');
        else
            $exportvalues = $this->controller->exportValues();
        
        $this->old_system = $exportvalues['old_system'];
        $old_directory = $exportvalues['old_directory'];
        
        //Create logfile
        $this->logfile = new Logger('groups.txt');
        $this->logfile->set_start_time();
        
        //Create migrationdatamanager
        $this->old_mgdm = OldMigrationDataManager :: getInstance($this->old_system, $old_directory);
        
        if (isset($exportvalues['move_files']) && $exportvalues['move_files'] == 1)
            $this->old_mgdm->set_move_file(true);
        
        if (isset($exportvalues['migrate_groups']) && $exportvalues['migrate_groups'] == 1)
        {
            //Migrate descriptions, settings and tools
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
                        
                        //$this->migrate('_Group_Category', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array('old_mgdm' => $this->old_mgdm), $course,0);
                        $this->migrate('_Group', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array('old_mgdm' => $this->old_mgdm), $course, 1);
                        //$this->migrate('_Group_Rel_User', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array('old_mgdm' => $this->old_mgdm), $course,2);
                        //$this->migrate('_Group_Rel_Tutor', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array('old_mgdm' => $this->old_mgdm), $course,3);
                        //TODO: group categories, group rel users, group rel tutors;
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
                echo (Translation :: get('Groups') . ' ' . Translation :: get('failed') . ' ' . Translation :: get('because') . ' ' . Translation :: get('Users') . ' OR ' . Translation :: get('Courses') . ' ' . Translation :: get('skipped') . '<br />');
                $this->logfile->add_message('Groups failed because users or courses skipped');
                $this->succes[1] = 0;
            }
        
        }
        else
        {
            echo (Translation :: get('Groups') . ' ' . Translation :: get('skipped') . '<br />');
            $this->logfile->add_message('Groups kipped');
            
            return false;
        }
        
        //Close the logfile
        $this->passedtime = $this->logfile->write_passed_time();
        $this->logfile->close_file();
        
        return true;
    }
}
?>