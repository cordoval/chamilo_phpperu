<?php
/**
 * $Id: maintenance_wizard_display.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.maintenance.inc.wizard
 */
/**
 * This class provides the needed functionality to show a page in a maintenance
 * wizard.
 */
class MaintenanceWizardDisplay extends HTML_QuickForm_Action_Display
{
    /**
     * The repository tool in which the wizard runs
     */
    private $parent;

    /**
     * Constructor
     * @param Tool $parent The repository tool in which the wizard
     * runs
     */
    public function MaintenanceWizardDisplay($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Displays the HTML-code of a page in the wizard
     * @param HTML_Quickform_Page $page The page to display.
     */
    function _renderForm($current_page)
    {
        $trail = BreadcrumbTrail :: get_instance();
        
        $this->parent->display_header();
        if (isset($_SESSION['maintenance_message']))
        {
            Display :: normal_message($_SESSION['maintenance_message']);
            unset($_SESSION['maintenance_message']);
        }
        if (isset($_SESSION['maintenance_error_message']))
        {
            Display :: error_message($_SESSION['maintenance_error_message']);
            unset($_SESSION['maintenance_error_message']);
        }
        parent :: _renderForm($current_page);
        $this->parent->display_footer();
    }
}
?>