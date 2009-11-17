<?php
/**
 * $Id: classes_migration_wizard_page.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
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
class ClassesMigrationWizardPage extends MigrationWizardPage
{

    /**
     * Constructor creates a new ClassesMigrationWizardPage
     * @param string $page_name the page name
     * @param $parent the parent of the controller
     * @param bool $command_execute to see if the page is executed by commandline or webinterface
     */
    function ClassesMigrationWizardPage($page_name, $parent, $command_execute = false)
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
        return Translation :: get('Class_title');
    }

    /**
     * @return string Info of the page
     */
    /*
	function get_info()
	{		
		for($i=0; $i<2; $i++)
		{
			$message = $message . '<br />' . $this->succes[$i] . ' ' . $this->get_message($i) . ' ' .
				Translation :: get('migrated');
			
			if(count($this->failed_elements[$i]) > 0)
				$message = $message . '<br / >' . count($this->failed_elements[$i]) . ' ' .
					 $this->get_message($i) . ' ' . Translation :: get('failed');
		}
		$message = $message . '<br/><br/>Please check the <a href="' . Path :: get(WEB_PATH) . 'documentation/migration.html" target="about_blank">migration manual</a> for more information';
		$message = $message . '<br />';		
		$message = $message . '<br />' . Translation :: get('Dont_forget');
		$message = $message . '<br/><br/>Time used: ' . $this->passedtime;
		return $message;
	}*/
    
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
                return Translation :: get('Classes');
            case 1 :
                return Translation :: get('Class_users');
            default :
                return Translation :: get('Classes');
        }
    }

    /**
     * Execute the page
     * Starts migration for classes and class users
     */
    function perform()
    {
        $logger = new Logger('migration.txt', true);
        
        if ($logger->is_text_in_file('classes'))
        {
            echo (Translation :: get('Classes') . ' ' . Translation :: get('already_migrated') . '<br />');
            return false;
        }
        
        $logger->write_text('classes');
        
        if ($this->command_execute)
            require (dirname(__FILE__) . '/../../../../../settings.inc.php');
        else
            $exportvalues = $this->controller->exportValues();
        
        $this->old_system = $exportvalues['old_system'];
        $old_directory = $exportvalues['old_directory'];
        
        //Create logfile
        $this->logfile = new Logger('classes.txt');
        $this->logfile->set_start_time();
        
        //Create temporary tables, create migrationdatamanager
        $this->old_mgdm = OldMigrationDataManager :: getInstance($this->old_system, $old_directory);
        
        if (isset($exportvalues['migrate_classes']) && $exportvalues['migrate_classes'] == 1)
        {
            //Migrate the classes
            //$this->migrate_classes();
            $this->migrate('_Class', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array(), null, 0);
            //Migrate the class users
            if (isset($exportvalues['migrate_users']) && $exportvalues['migrate_users'] == 1)
            {
                //$this->migrate_class_users();
                $this->migrate('_Class_User', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files), array(), null, 1);
            }
            else
            {
                echo (Translation :: get('Class_users') . ' ' . Translation :: get('failed') . ' ' . Translation :: get('because') . ' ' . Translation :: get('Users') . ' ' . Translation :: get('skipped') . '<br />');
                $this->logfile->add_message('Classes failed because users skipped');
                $this->succes[1] = 0;
            }
        
        }
        else
        {
            echo (Translation :: get('Classes') . ' ' . Translation :: get('skipped') . '<br />');
            $this->logfile->add_message('Classes skipped');
            
            return false;
        }
        
        //Close the logfile
        $this->passedtime = $this->logfile->write_passed_time();
        $this->logfile->close_file();
        
        return true;
    }
}
?>