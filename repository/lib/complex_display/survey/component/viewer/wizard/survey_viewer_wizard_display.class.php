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
        
    	
        
    	if ($current_page->get_page_number() != 0)
        {
            $html = array();
            $html[] = '<div class="assessment">';
            $html[] = '<h2>' . $this->parent->get_survey()->get_title() . '</h2>';
            
            //            if ($this->parent->get_survey()->has_description() && $current_page->get_page_number() == 1)
            //            {
            //                $html[] = '<div class="description">';
            //                $description = $this->parent->get_survey()->get_description();
            //                
            //                $html[] = $this->parent->get_parent()->parse($description);
            //                
            //                $html[] = '</div>';
            //            }
            

            $html[] = '<br />';
            
            $html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = $current_page->get_page_number() . ' / ' . $this->parent->get_total_pages();
            $html[] = '</div>';
            
            $html[] = '<br />';
            
            if (strlen($this->parent->get_survey()->get_introduction_text()) > 0)
            {
                $html[] = '<div class="description">';
                $introduction = $this->parent->get_survey()->get_introduction_text();
                
                $html[] = $this->parent->get_parent()->parse($introduction);
                $html[] = '</div>';
            }
            
            $html[] = '<br />';
            
            if (strlen($this->parent->get_page($current_page->get_page_number())->get_introduction_text()) > 0)
            {
                $html[] = '<div class="description">';
                $introduction = $this->parent->get_page($current_page->get_page_number())->get_introduction_text();
                
                $html[] = $this->parent->get_parent()->parse($introduction);
                
                $html[] = '</div>';
            }
            
            $html[] = '</div>';
            
            $html[] = '<div>';
            $html[] = $current_page->toHtml();
            $html[] = '</div>';
            
            $html[] = '<br />';
            
            $html[] = '<div class="assessment">';
            
            if (strlen($this->parent->get_page($current_page->get_page_number())->get_finish_text()) > 0)
            {
                $html[] = '<div class="description">';
                $finishtext = $this->parent->get_page($current_page->get_page_number())->get_finish_text();
                
                $html[] = $this->parent->get_parent()->parse($finishtext);
                
                $html[] = '</div>';
            }
            
            $html[] = '<br />';
            
            if (strlen($this->parent->get_survey()->get_finish_text()) > 0)
            {
                $html[] = '<div class="description">';
                $finishtext = $this->parent->get_survey()->get_finish_text();
                
                $html[] = $this->parent->get_parent()->parse($finishtext);
                $html[] = '</div>';
            }
            
            
            
            $html[] = '<br />';
            
            $html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = $current_page->get_page_number() . ' / ' . $this->parent->get_total_pages();
            $html[] = '</div>';
            
             $html[] = '</div>';
            
            return implode("\n", $html);
        }
        else
        {
            $html = array();
            $html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = Translation :: get('NoSurveyPageAddedToSurvey');
            $html[] = '</div>';
            return implode("\n", $html);
        }
    
    }
}
?>