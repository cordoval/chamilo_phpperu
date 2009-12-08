<?php
/**
 * $Id: archive_wizard_display.class.php 151 2009-11-10 12:23:34Z kariboe $
 * @package tracking.lib.tracking_manager.component.wizards.archive
 */

/**
 * This class provides the needed functionality to show a page in a tracking archiver
 * wizard.
 * @author Sven Vanpoucke
 */
class ArchiveWizardDisplay extends HTML_QuickForm_Action_Display
{
    /**
     * The component in which the wizard runs
     */
    private $parent;

    /**
     * Constructor
     * @param TrackingManagerArchiveComponent $parent The component in which the wizard runs
     */
    public function ArchiveWizardDisplay($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Displays the HTML-code of a page in the wizard
     * @param HTML_Quickform_Page $page The page to display.
     */
    function _renderForm($current_page)
    {
        /*$renderer = $current_page->defaultRenderer();
        $current_page->setRequiredNote('<font color="#FF0000">*</font> ' . Translation :: get('ThisFieldIsRequired'));
        $element_template = "\n\t<tr>\n\t\t<td valign=\"top\"><!-- BEGIN required --><span style=\"color: #ff0000\">*</span> <!-- END required -->{label}</td>\n\t\t<td valign=\"top\" align=\"left\"><!-- BEGIN error --><span style=\"color: #ff0000;font-size:x-small;margin:2px;\">{error}</span><br /><!-- END error -->\t{element}</td>\n\t</tr>";
        $renderer->setElementTemplate($element_template);
        $header_template = "\n\t<tr>\n\t\t<td valign=\"top\" colspan=\"2\">{header}</td>\n\t</tr>";
        $renderer->setHeaderTemplate($header_template);
        HTML_QuickForm :: setRequiredNote('<font color="red">*</font> <small>' . Translation :: get('ThisFieldIsRequired') . '</small>');
        $current_page->accept($renderer);*/
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb($this->parent->get_url(array(Application :: PARAM_ACTION => TrackingManager :: ACTION_ARCHIVE)), Translation :: get('Archiver')));
        
        $this->parent->display_header($trail, false);
        
        echo '<div style="float: left; background-color:#EFEFEF; width: 17%; margin-right: 20px;padding: 15px; min-height: 400px;">';
        echo '<img src="layout/aqua/images/common/chamilo.png" alt="logo"/>';
        $all_pages = $current_page->controller->_pages;
        $total_number_of_pages = count($all_pages);
        $current_page_number = 0;
        $page_number = 0;
        echo '<ol>';
        foreach ($all_pages as $page)
        {
            $page_number ++;
            if ($page->get_title() == $current_page->get_title())
            {
                $current_page_number = $page_number;
                echo '<li style="font-weight: bold;">' . $page->get_title() . '</li>';
            }
            else
            {
                echo '<li>' . $page->get_title() . '</li>';
            }
        }
        echo '</ol>';
        echo '</div>' . "\n";
        
        echo '<div style="margin: 10px; float: right; width: 75%;">';
        echo '<h2>' . Translation :: get('Step') . ' ' . $current_page_number . ' ' . Translation :: get('of') . ' ' . $total_number_of_pages . ' &ndash; ' . $current_page->get_title() . '</h2>';
        echo '<div>';
        echo $current_page->get_info();
        echo '</div>';
        
        if (isset($_SESSION['install_message']))
        {
            Display :: normal_message($_SESSION['install_message']);
            unset($_SESSION['install_message']);
        }
        if (isset($_SESSION['install_error_message']))
        {
            Display :: error_message($_SESSION['install_error_message']);
            unset($_SESSION['install_error_message']);
        }
        
        parent :: _renderForm($current_page);
        echo '</div>';
        
        $this->parent->display_footer();
    }
}
?>