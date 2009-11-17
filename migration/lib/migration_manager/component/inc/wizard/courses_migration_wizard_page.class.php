<?php
/**
 * $Id: courses_migration_wizard_page.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/../../../../migration_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../../logger.class.php';
require_once dirname(__FILE__) . '/../../../../import.class.php';
/**
 * Class for user migration execution
 * @author Sven Vanpoucke
 */
class CoursesMigrationWizardPage extends MigrationWizardPage
{

    /**
     * Constructor creates a new CoursesMigrationWizardPage
     * @param string $page_name the page name
     * @param $parent the parent of the controller
     * @param bool $command_execute to see if the page is executed by commandline or webinterface
     */
    function CoursesMigrationWizardPage($page_name, $parent, $command_execute = false)
    {
        MigrationWizardPage :: MigrationWizardPage($page_name, $parent);
        $this->command_execute = $command_execute;
        $this->succes = array(0, 0, 0, 0, 0);
    }

    /**
     * @return string Title of the page
     */
    function get_title()
    {
        return Translation :: get('Courses_title');
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
                return Translation :: get('Course_Categories');
            case 1 :
                return Translation :: get('Courses');
            case 2 :
                return Translation :: get('Course_User_Categories');
            case 3 :
                return Translation :: get('Course_User_Relations');
            case 4 :
                return Translation :: get('Course_Class_Relations');
                ;
            default :
                return Translation :: get('Courses');
        }
    }

    /**
     * Execute the page
     * Starts migration for courses, course users, course classes and course user categories
     */
    function perform()
    {
        $logger = new Logger('migration.txt', true);
        
        if ($logger->is_text_in_file('courses'))
        {
            echo (Translation :: get('Courses') . ' ' . Translation :: get('already_migrated') . '<br />');
            return false;
        }
        
        $logger->write_text('courses');
        
        if ($this->command_execute)
            require (dirname(__FILE__) . '/../../../../../settings.inc.php');
        else
            $exportvalues = $this->controller->exportValues();
        
        $this->old_system = $exportvalues['old_system'];
        $old_directory = $exportvalues['old_directory'];
        
        //Create logfile
        $this->logfile = new Logger('courses.txt');
        $this->logfile->set_start_time();
        
        //Create temporary tables, create migrationdatamanager
        $this->old_mgdm = OldMigrationDataManager :: getInstance($this->old_system, $old_directory);
        
        if (isset($exportvalues['migrate_courses']) && $exportvalues['migrate_courses'] == 1)
        {
            //Migrate course categories
            $this->migrate('_Course_Category', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array(), null, 0);
            
            //Migrate the courses
            $this->migrate('_Course', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array(), null, 1);
            
            //Migrate the class users
            if (isset($exportvalues['migrate_users']) && $exportvalues['migrate_users'] == 1)
            {
                //Migrate the user course categories
                $this->migrate('_User_Course_Category', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array(), null, 2);
                
                //Migrate course users
                $this->migrate('_Course_Rel_User', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array(), null, 3);
            }
            else
            {
                echo (Translation :: get('Course_User_Categories') . ' & ' . Translation :: get('Course_User_Relations') . ' ' . Translation :: get('failed') . ' ' . Translation :: get('because') . ' ' . Translation :: get('Users') . ' ' . Translation :: get('skipped') . '<br />');
                $this->logfile->add_message('Course user categories and user relations failed because users skipped');
                $this->succes[1] = 0;
                $this->succes[3] = 0;
            }
            
            if (isset($exportvalues['migrate_classes']) && $exportvalues['migrate_classes'] == 1)
            {
                //Migrate course classes
            //$this->migrate('CourseRelClass', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array(), null,4);
            

            }
            else
            {
                echo (Translation :: get('Course_Class_Relations') . ' ' . Translation :: get('failed') . ' ' . Translation :: get('because') . ' ' . Translation :: get('Classes') . ' ' . Translation :: get('skipped') . '<br />');
                $this->logfile->add_message('Course classes failed because users skipped');
                $this->succes[4] = 0;
            }
        
        }
        else
        {
            echo (Translation :: get('Courses') . ' ' . Translation :: get('skipped') . '<br />');
            $this->logfile->add_message('Courses skipped');
            
            return false;
        }
        
        //Close the logfile
        $this->passedtime = $this->logfile->write_passed_time();
        $this->logfile->close_file();
        
        return true;
    }
}
?>