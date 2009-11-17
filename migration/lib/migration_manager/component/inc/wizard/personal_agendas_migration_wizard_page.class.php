<?php
/**
 * $Id: personal_agendas_migration_wizard_page.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/../../../../migration_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../../logger.class.php';
require_once dirname(__FILE__) . '/../../../../import.class.php';
/**
 * Class for class migration execution
 * @author Sven Vanpoucke
 */
class PersonalAgendasMigrationWizardPage extends MigrationWizardPage
{

    /**
     * Constructor creates a new PersonalAgendasMigrationWizardPage
     * @param string $page_name the page name
     * @param $parent the parent of the controller
     * @param bool $command_execute to see if the page is executed by commandline or webinterface
     */
    function PersonalAgendasMigrationWizardPage($page_name, $parent, $command_execute = false)
    {
        MigrationWizardPage :: MigrationWizardPage($page_name, $parent);
        $this->command_execute = $command_execute;
        $this->succes = array(0);
    }

    /**
     * @return string Title of the page
     */
    function get_title()
    {
        return Translation :: get('Personal_agenda_title');
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
                return Translation :: get('Personal_agendas');
            default :
                return Translation :: get('Personal_agendas');
        }
    }

    /**
     * Builds the next button
     */
    function buildForm()
    {
        $this->_formBuilt = true;
        $prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next') . ' >>');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
    }

    /**
     * Execute the page
     * Starts migration for personal agendas, resources
     */
    function perform()
    {
        $logger = new Logger('migration.txt', true);
        
        if ($logger->is_text_in_file('personalagendas'))
        {
            echo (Translation :: get('Personal_agendas') . ' ' . Translation :: get('already_migrated') . '<br />');
            return false;
        }
        
        $logger->write_text('personalagendas');
        
        if ($this->command_execute)
            require (dirname(__FILE__) . '/../../../../../settings.inc.php');
        else
            $exportvalues = $this->controller->exportValues();
        
        $this->old_system = $exportvalues['old_system'];
        $old_directory = $exportvalues['old_directory'];
        
        //Create logfile
        $this->logfile = new Logger('personalagenda.txt');
        $this->logfile->set_start_time();
        
        //Create temporary tables, create migrationdatamanager
        $this->old_mgdm = OldMigrationDataManager :: getInstance($this->old_system, $old_directory);
        
        if (isset($exportvalues['migrate_personal_agendas']) && $exportvalues['migrate_personal_agendas'] == 1)
        {
            //Migrate the personal agendas
            if (isset($exportvalues['migrate_users']) && $exportvalues['migrate_users'] == 1)
            {
                //$this->migrate_personal_agendas();
                $this->migrate('_Personal_Agenda', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array(), null, 0);
            }
            else
            {
                echo (Translation :: get('Personal_agendas') . ' ' . Translation :: get('failed') . ' ' . Translation :: get('because') . ' ' . Translation :: get('Users') . ' ' . Translation :: get('skipped') . '<br />');
                $this->logfile->add_message('Personal agendas failed because users skipped');
                $this->succes[0] = 0;
            }
        
        }
        else
        {
            echo (Translation :: get('Personal_agendas') . ' ' . Translation :: get('skipped') . '<br />');
            $this->logfile->add_message('personal agendas skipped');
            
            return false;
        }
        
        //Close the logfile
        $this->passedtime = $this->logfile->write_passed_time();
        $this->logfile->close_file();
        
        return true;
    }
}
?>