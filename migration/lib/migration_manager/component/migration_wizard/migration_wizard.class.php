<?php
/**
 * $Id: migration_wizard.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc
 */
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__) . 'pages/migration_wizard_display';

/**
 * A wizard which guides the user through several steps to perform the migration
 * 
 * @author Sven Vanpoucke
 */
class MigrationWizard extends HTML_QuickForm_Controller
{
    /** 
     * The component in which the wizard runs
     */
    private $parent;

    /**
     * Creates a new MigrationWizard
     * @param MigrationManagerComponent $parent The migrationmanager component 
     * in which this wizard runs.
     */
    function MigrationWizard($parent)
    {
        parent :: HTML_QuickForm_Controller('MigrationWizard', true);
    	$this->parent = $parent;
        
        $this->addpages();
        $this->addAction('display', new MigrationWizardDisplay($this->parent));
    }

    /**
     * Creates the pages that belong to a certain old system
     * This pages are defined in wizard.xml in the old system directory
     */
    function addpages()
    {
        
    }

}
?>