<?php
/**
 * $Id: build_wizard_display.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.home_manager.component.wizards.build
 */
/**
 * This class provides the needed functionality to show a page in a maintenance
 * wizard.
 */
class BuildWizardDisplay extends HTML_QuickForm_Action_Display
{
    /**
     * The repository tool in which the wizard runs
     */
    private $parent;
    
    /**
     * @var BreadcrumbTrail;
     */
    private $breadcrumbtrail;

    /**
     * Constructor
     * @param Tool $parent The repository tool in which the wizard
     * runs
     */
    public function BuildWizardDisplay($parent, $trail)
    {
        $this->parent = $parent;
    }

    /**
     * Displays the HTML-code of a page in the wizard
     * @param HTML_Quickform_Page $page The page to display.
     */
    function _renderForm($current_page)
    {
        /* $breadcrumbs = array();
        $breadcrumbs[] = array('url' => $this->parent->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), 'name' => Translation :: get('Home'));
        $breadcrumbs[] = array('url' => $this->parent->get_url(), 'name' => Translation :: get('BuildHome'));*/
        
        $this->parent->display_header($this->breadcrumbtrail, false, 'home build');
        if (isset($_SESSION['build_message']))
        {
            Display :: normal_message($_SESSION['build_message']);
            unset($_SESSION['build_message']);
        }
        if (isset($_SESSION['build_error_message']))
        {
            Display :: error_message($_SESSION['build_error_message']);
            unset($_SESSION['build_error_message']);
        }
        parent :: _renderForm($current_page);
        $this->parent->display_footer();
    }
}
?>