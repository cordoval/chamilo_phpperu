<?php
/**
 * $Id: archive_wizard.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib.tracking_manager.component.wizards
 */

require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__) . '/archive/archive_wizard_page.class.php';
require_once dirname(__FILE__) . '/archive/archive_wizard_display.class.php';
require_once dirname(__FILE__) . '/archive/archive_wizard_process.class.php';
require_once dirname(__FILE__) . '/archive/trackers_selection_archive_wizard_page.class.php';
require_once dirname(__FILE__) . '/archive/settings_archive_wizard_page.class.php';
require_once dirname(__FILE__) . '/archive/confirmation_archive_wizard_page.class.php';

/**
 * A wizard which guides the user through several steps to perform the archive
 * 
 * @author Sven Vanpoucke
 */
class ArchiveWizard extends HTML_QuickForm_Controller
{
    /** 
     * The component in which the wizard runs
     */
    private $parent;

    /**
     * Creates a new ArchiveWizard
     * @param ArchiveComponent $parent The archive component 
     * in which this wizard runs.
     */
    function ArchiveWizard($parent)
    {
        $this->parent = $parent;
        parent :: HTML_QuickForm_Controller('ArchiveWizard', true);
        $this->addPage(new TrackersSelectionArchiveWizardPage('page_trackers', $this->parent));
        $this->addPage(new SettingsArchiveWizardPage('page_settings', $this->parent));
        $this->addPage(new ConfirmationArchiveWizardPage('page_confirmation', $this->parent));
        
        $this->addAction('display', new ArchiveWizardDisplay($this->parent));
        $this->addAction('process', new ArchiveWizardProcess($this->parent));
    }

}
?>