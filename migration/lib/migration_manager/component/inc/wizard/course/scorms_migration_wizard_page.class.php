<?php
/**
 * $Id: scorms_migration_wizard_page.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/../migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/../../../../../migration_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../../../logger.class.php';
require_once dirname(__FILE__) . '/../../../../../import.class.php';

/**
 * Class for course scorms migration
 * @author Sven Vanpoucke
 */
class ScormsMigrationWizardPage extends MigrationWizardPage
{
    private $include_deleted_files;

    /**
     * Constructor creates a new ScormsMigrationWizardPage
     * @param string $page_name the page name
     * @param $parent the parent of the controller
     * @param bool $command_execute to see if the page is executed by commandline or webinterface
     */
    function ScormsMigrationWizardPage($page_name, $parent, $command_execute = false)
    {
        MigrationWizardPage :: MigrationWizardPage($page_name, $parent, $command_execute);
        $this->succes = array(0);
    }

    /**
     * @return string Title of the page
     */
    function get_title()
    {
        return Translation :: get('Scorms_title');
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
                return Translation :: get('Scorm_documents');
            default :
                return Translation :: get('Scorm_documents');
        }
    }

    /**
     * Execute the page
     * Starts migration for scorm documents
     */
    function perform()
    {
        $logger = new Logger('migration.txt', true);
        
        if ($logger->is_text_in_file('scorms'))
        {
            echo (Translation :: get('Scorms') . ' ' . Translation :: get('already_migrated') . '<br />');
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
        $this->logfile = new Logger('scorms.txt');
        $this->logfile->set_start_time();
        
        //Create migrationdatamanager
        $this->old_mgdm = OldMigrationDataManager :: getInstance($this->old_system, $old_directory);
        $mgdm = MigrationDataManager :: get_instance();
        
        if (isset($exportvalues['move_files']) && $exportvalues['move_files'] == 1)
            $this->old_mgdm->set_move_file(true);
        
        if (isset($exportvalues['migrate_scorms']) && $exportvalues['migrate_scorms'] == 1)
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
                        $old_rel_path = 'courses/' . $course->get_directory() . '/scorm/';
                        $old_rel_path = iconv("UTF-8", "ISO-8859-1", $old_rel_path);
                        $full_path = $this->old_mgdm->append_full_path(false, $old_rel_path);
                        
                        if ($mgdm->get_failed_element('dokeos_main.course', $course->get_code()) || ! is_dir($full_path))
                        {
                            continue;
                        }
                        
                        $this->migrate('_Scorm_document', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array('old_mgdm' => $this->old_mgdm), $course, 0);
                        
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
                echo (Translation :: get('Scorms') . Translation :: get('failed') . ' ' . Translation :: get('because') . ' ' . Translation :: get('Users') . ' ' . Translation :: get('skipped') . '<br />');
                $this->logfile->add_message('Scorms failed because users or courses skipped');
                $this->succes = array(0);
            }
        
        }
        else
        {
            echo (Translation :: get('Scorms') . ' ' . Translation :: get('skipped') . '<br />');
            $this->logfile->add_message('Scorms skipped');
            
            return false;
        }
        
        //Close the logfile
        $this->passedtime = $this->logfile->write_passed_time();
        $this->logfile->close_file();
        
        $logger->write_text('scorms');
        
        return true;
    }

}
?>
