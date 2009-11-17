<?php
/**
 * $Id: survey_viewer_wizard_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.viewer.wizard
 */
/**
 *
 * @author Sven Vanpoucke
 */
class SurveyViewerWizardDisplay extends HTML_QuickForm_Action_Display
{
    
    private $parent;

    public function SurveyViewerWizardDisplay($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Displays the HTML-code of a page in the wizard
     * @param HTML_Quickform_Page $page The page to display.
     */
    function _renderForm($current_page)
    {
        //		$renderer = $current_page->defaultRenderer();
        //		$current_page->setRequiredNote('<font color="#FF0000">*</font> '.Translation :: get('ThisFieldIsRequired'));
        //		$element_template = "\n\t<tr>\n\t\t<td valign=\"top\"><!-- BEGIN required --><span style=\"color: #ff0000\">*</span> <!-- END required -->{label}</td>\n\t\t<td valign=\"top\" align=\"left\"><!-- BEGIN error --><span style=\"color: #ff0000;font-size:x-small;margin:2px;\">{error}</span><br /><!-- END error -->\t{element}</td>\n\t</tr>";
        //		$renderer->setElementTemplate($element_template);
        //		$header_template = "\n\t<tr>\n\t\t<td valign=\"top\" colspan=\"2\">{header}</td>\n\t</tr>";
        //		$renderer->setHeaderTemplate($header_template);
        //		HTML_QuickForm :: setRequiredNote('<font color="red">*</font> <small>'.Translation :: get('ThisFieldIsRequired').'</small>');
        //		$current_page->accept($renderer);
        

        echo '<div class="assessment">';
        echo '<h2>' . $this->parent->get_survey()->get_title() . '</h2>';
        
        if ($this->parent->get_survey()->has_description() && $current_page->get_page_number() == 1)
        {
            echo '<div class="description">';
            echo $this->parent->get_survey()->get_description();
            echo '</div>';
        }
        
        echo '<br />';
        
        if (strlen($this->parent->get_survey()->get_introduction_text()) > 0 && $current_page->get_page_number() == 1)
        {
            echo '<div class="description">';
            echo $this->parent->get_survey()->get_introduction_text();
            echo '</div>';
        }
        
        echo '</div>';
        
        echo '<div style="width: 100%; text-align: center;">';
        echo $current_page->get_page_number() . ' / ' . $this->parent->get_total_pages();
        echo '</div>';
        
        echo '<div>';
        parent :: _renderForm($current_page);
        echo '</div>';
        
        echo '<div style="width: 100%; text-align: center;">';
        echo $current_page->get_page_number() . ' / ' . $this->parent->get_total_pages();
        echo '</div>';
    }
}
?>
