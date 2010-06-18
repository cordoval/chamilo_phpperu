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
        
        $trail = BreadcrumbTrail :: get_instance();
        
        $this->parent->display_header($trail);
        
        $all_pages = $current_page->controller->_pages;
        $total_number_of_pages = count($all_pages);
        
    	$current_page_number = 0;
        $page_number = 0;
        foreach ($all_pages as $index => $page)
        {
        	$page_number ++;

            if ($page->get_title() == $current_page->get_title())
            {
                $current_page_number = $page_number;
            }
        }
        
        echo '<div id="progressbox">';
        echo '<ul id="progresstrail">';
        $page_number = 0;
        foreach ($all_pages as $index => $page)
        {
        	$page_number ++;

            if ($page_number <= $current_page_number)
            {
                echo '<li class="active"><a href="#">' . $page_number . '.&nbsp;&nbsp;' . $page->get_title() . '</a></li>';
            }
            else
            {
                echo '<li><a href="#">' . $page_number . '.&nbsp;&nbsp;' . $page->get_title() . '</a></li>';
            }
        }

        echo '</ul>';
        echo '<div class="clear"></div>';
        echo '</div>';
        
        echo '<div id="theForm">';
        echo '<div id="select" class="row"><div class="formc formc_no_margin">';
        echo '<b>' . Translation :: get('Step') . ' ' . $current_page_number . ' ' . Translation :: get('of') . ' ' . ($total_number_of_pages + 1) . ' &ndash; ' . $current_page->get_title() . '</b><br />';
        echo $current_page->get_info();
        echo '</div>';
        echo '</div><br />';
        
        parent :: _renderForm($current_page);
        echo '</div>';
        
        $this->parent->display_footer();
    }
}
?>