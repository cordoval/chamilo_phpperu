<?php
/**
 * $Id: migration_wizard_display.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc.wizard
 */


/**
 * This class provides the needed functionality to show a page in a migration
 * wizard.
 *
 * @author Sven Vanpoucke
 */
class MigrationWizardDisplay extends HTML_QuickForm_Action_Display
{
    /**
     * The Migration Wizard Component in which the wizard runs
     */
    private $parent;

    /**
     * Constructor
     * @param Tool $parent The repository tool in which the wizard
     * runs
     */
    public function MigrationWizardDisplay($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Displays the HTML-code of a page in the wizard
     * @param HTML_Quickform_Page $page The page to display.
     */
    function _renderForm($current_page)
    {
        $this->parent->display_header();

        echo'<div class="migration_info">';
        $current_page->display_page_info();
        echo '</div>';
        
        echo '<div id="dynamic_div" style="display:block;margin-left:40%;margin-top:10px;height:50px;"></div>';
        echo '<script src="' . Path :: get(WEB_LIB_PATH) . 'javascript/upload.js" type="text/javascript"></script>';
        echo '<script type="text/javascript">var myUpload = new upload(' . (abs(intval(1)) * 1000) . '); myUpload.start(\'dynamic_div\',\'' . Theme :: get_common_image_path() . 'action_progress_bar.gif\',\'' . Translation :: get('PleaseStandBy') . '\',\'\');</script>';
        flush();
        echo '<script type="text/javascript">myUpload.stop();</script>';
		
        echo '<br />';
        parent :: _renderForm($current_page);
		echo '<br />';
        
        echo'<div class="migration_info">';
        $current_page->display_next_page_info();
        echo '</div>';

        $this->parent->display_footer();
    }
}
?>