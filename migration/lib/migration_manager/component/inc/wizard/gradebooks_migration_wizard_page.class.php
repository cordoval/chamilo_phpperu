<?php
/**
 * $Id: gradebooks_migration_wizard_page.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/../migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/../../../../../migration_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../../../logger.class.php';
require_once dirname(__FILE__) . '/../../../../../import.class.php';

/**
 * Class for gradebooks migration
 * @author Sven Vanpoucke
 */
class GradebooksMigrationWizardPage extends MigrationWizardPage
{
    private $include_deleted_files;

    /**
     * Constructor creates a new GradebooksMigrationWizardPage
     * @param string $page_name the page name
     * @param $parent the parent of the controller
     * @param bool $command_execute to see if the page is executed by commandline or webinterface
     */
    function GradebooksMigrationWizardPage($page_name, $parent, $command_execute = false)
    {
        MigrationWizardPage :: MigrationWizardPage($page_name, $parent, $command_execute);
        $this->succes = array(0, 0, 0, 0, 0);
    }

    /**
     * @return string Title of the page
     */
    function get_title()
    {
        return Translation :: get('Gradebooks_title');
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
                return Translation :: get('Gradebook_categories');
            case 1 :
                return Translation :: get('Gradebook_evaluations');
            case 2 :
                return Translation :: get('Gradebook_links');
            case 3 :
                return Translation :: get('Gradebook_result');
            case 4 :
                return Translation :: get('Gradebook_score_displays');
            default :
                return Translation :: get('Gradebook');
        }
    }

    /**
     * Execute the page
     * Starts migration for gradebooksevaluations, gradebook categories, gradebook links, gradebook results and gradebook scoredisplay
     */
    function perform()
    {
        $logger = new Logger('migration.txt', true);
        
        if ($logger->is_text_in_file('gradebooks'))
        {
            echo (Translation :: get('Gradebooks') . ' ' . Translation :: get('already_migrated') . '<br />');
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
        $this->logfile = new Logger('gradebooks.txt');
        $this->logfile->set_start_time();
        
        //Create migrationdatamanager
        $this->old_mgdm = OldMigrationDataManager :: getInstance($this->old_system, $old_directory);
        
        if (isset($exportvalues['move_files']) && $exportvalues['move_files'] == 1)
            $this->old_mgdm->set_move_file(true);
        
        if (isset($exportvalues['migrate_gradebooks']) && $exportvalues['migrate_gradebooks'] == 1)
        {
            //Migrate the dropbox
            if (isset($exportvalues['migrate_courses']) && isset($exportvalues['migrate_users']) && $exportvalues['migrate_courses'] == 1 && $exportvalues['migrate_users'] == 1)
            {
                //$this->migrate('_Gradebook_Category', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array('old_mgdm' => $this->old_mgdm), $course,0);
            //$this->migrate('_Gradebook_Evaluation', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array('old_mgdm' => $this->old_mgdm), $course,1);
            //$this->migrate('_Gradebook_Link', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array('old_mgdm' => $this->old_mgdm), $course,2);
            //$this->migrate('_Gradebook_Result', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array('old_mgdm' => $this->old_mgdm), $course,3);
            //$this->migrate('_Gradebook_Score_Display', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array('old_mgdm' => $this->old_mgdm), $course,4);
            }
            else
            {
                echo (Translation :: get('Gradebooks') . Translation :: get('failed') . ' ' . Translation :: get('because') . ' ' . Translation :: get('Users') . ' ' . Translation :: get('skipped') . '<br />');
                $this->logfile->add_message('Gradebooks failed because users or courses skipped');
                $this->succes = array(0, 0, 0, 0, 0);
            }
        
        }
        else
        {
            echo (Translation :: get('Gradebooks') . ' ' . Translation :: get('skipped') . '<br />');
            $this->logfile->add_message('Gradebooks skipped');
            
            return false;
        }
        
        //Close the logfile
        $this->passedtime = $this->logfile->write_passed_time();
        $this->logfile->close_file();
        
        $logger->write_text('gradebooks');
        
        return true;
    }
}
?>