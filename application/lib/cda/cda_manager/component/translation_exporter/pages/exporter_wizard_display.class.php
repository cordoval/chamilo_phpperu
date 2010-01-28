<?php
/**
 * $Id: exporter_wizard_display.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * @package exporter.lib.exportermanager.component.inc.wizard
 */
/**
 * This class provides the needed functionality to show a page in a maintenance
 * wizard.
 */
class ExporterWizardDisplay extends HTML_QuickForm_Action_Display
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
    public function ExporterWizardDisplay($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Displays the HTML-code of a page in the wizard
     * @param HTML_Quickform_Page $page The page to display.
     */
	function _renderForm($current_page)
    {
        $renderer = $current_page->defaultRenderer();
        $current_page->setRequiredNote('<font color="#FF0000">*</font> ' . Translation :: get('ThisFieldIsRequired'));
        $header_template = "\n\t<tr>\n\t\t<td valign=\"top\" colspan=\"2\">{header}</td>\n\t</tr>";
        $renderer->setHeaderTemplate($header_template);
        HTML_QuickForm :: setRequiredNote('<font color="red">*</font> <small>' . Translation :: get('ThisFieldIsRequired') . '</small>');
        $current_page->accept($renderer);
        
        $trail = new BreadcrumbTrail(false);
        
        $this->parent->display_header($trail);
        
        echo '<div style="margin: 10px;">';
        echo '<div>';
        echo $current_page->get_info();
        echo '</div>';
        
        parent :: _renderForm($current_page);
        echo '</div>';
        
        $this->parent->display_footer();
    }
}
?>