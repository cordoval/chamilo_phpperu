<?php
/**
 * $Id: publisher_wizard_display.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.publication_wizard.pages
 */
/**
 * This class provides the needed functionality to show a page in a maintenance
 * wizard.
 */
class PublisherWizardDisplay extends HTML_QuickForm_Action_Display
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
    public function PublisherWizardDisplay($parent)
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
        //$element_template = "\n\t<tr>\n\t\t<td valign=\"top\"><!-- BEGIN required --><span style=\"color: #ff0000\">*</span> <!-- END required -->{label}</td>\n\t\t<td valign=\"top\" align=\"left\"><!-- BEGIN error --><span style=\"color: #ff0000;font-size:x-small;margin:2px;\">{error}</span><br /><!-- END error -->\t{element}</td>\n\t</tr>";
        //$renderer->setElementTemplate($element_template);
        $header_template = "\n\t<tr>\n\t\t<td valign=\"top\" colspan=\"2\">{header}</td>\n\t</tr>";
        $renderer->setHeaderTemplate($header_template);
        HTML_QuickForm :: setRequiredNote('<font color="red">*</font> <small>' . Translation :: get('ThisFieldIsRequired') . '</small>');
        $current_page->accept($renderer);
        
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->parent->get_url(), Translation :: get('Publish')));
        
        $this->parent->display_header($trail, false, true, 'repository publication wizard');
        
        /*echo '<div style="background-color:#EFEFEF;padding: 10px; height: 35px;">';
		$all_pages = $current_page->controller->_pages;
		$total_number_of_pages = count($all_pages);
		$current_page_number = 0;
		$page_number = 0;
		echo '<ol>';
		foreach($all_pages as $index => $page)
		{
			$page_number++;
			if($page->get_title() == $current_page->get_title())
			{
				$current_page_number = $page_number;
				echo ' <li style="float: left; font-weight: bold; padding-right: 25px;">'.$page->get_title().'</li>';
			}
			else
			{
				echo ' <li style="float: left; padding-right: 25px;">'.$page->get_title().'</li>';
			}
		}
		echo '</ol>';
		echo '</div>';*/
        
        echo '<div style="margin: 10px;">';
        /*echo '<h2>'.Translation :: get('Step').' '.$current_page_number.' '.Translation :: get('of').' '.$total_number_of_pages.' &ndash; '.$current_page->get_title().'</h2>';*/
        echo '<div>';
        echo $current_page->get_info();
        echo '</div>';
        
        parent :: _renderForm($current_page);
        echo '</div>';
        
        $this->parent->display_footer();
    }
}
?>