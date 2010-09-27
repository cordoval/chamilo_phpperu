<?php

require_once Path :: get_repository_path() . 'lib/content_object/survey/display/component/survey_menu.class.php';

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
        
//            	dump('current page');
//            	dump($current_page->get_page_number());
        

        $html = array();
        $this->parent->get_parent()->display_header();
        
//        dump($this->parent->get_parent()->get_parameters());
        
        if ($this->parent->has_context())
        {
            $this->with_menu = true;
            $html[] = $this->get_menu_html();
        }
        
        if ($this->with_menu)
        {
            $width = 80;
        }
        else
        {
            $width = 100;
        }
        $html[] = '<div style="float: right; width: ' . $width . '%;">';
        
        if ($current_page->get_page_number() != 0)
        {
            $html[] = '<div class="assessment">';
            $html[] = '<h2>' . $this->parent->get_survey()->get_title() . '</h2>';
            
            $html[] = '<br />';
            
            $html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = $current_page->get_page_number() . ' / ' . $this->parent->get_total_pages();
            $html[] = '</div>';
            
            $html[] = '<br />';
            
            if (strlen(strip_tags($this->parent->get_survey()->get_header(), '<img>')) > 0)
            {
                $html[] = '<div class="description">';
                $survey_header = $this->parent->get_survey()->get_header();
                
                //                $html[] = $this->parent->get_parent()->parse($survey_header);
                $html[] = $survey_header;
                $html[] = '</div>';
            }
            
            $html[] = '<br />';
            
            if (strlen(strip_tags($this->parent->get_page($current_page->get_page_number())->get_introduction_text(), '<img>')) > 0)
            {
                $html[] = '<div class="description">';
                $introduction = $this->parent->get_page($current_page->get_page_number())->get_introduction_text();
                
                //                $html[] = $this->parent->get_parent()->parse($introduction);
                

                $html[] = $introduction;
                
                $html[] = '</div>';
            }
            
            $html[] = '</div>';
            
            $html[] = '<div>';
            $html[] = $current_page->toHtml();
            $html[] = '</div>';
            
            $html[] = '<br />';
            
            $html[] = '<div class="assessment">';
            
            if (strlen(strip_tags($this->parent->get_page($current_page->get_page_number())->get_finish_text(), '<img>')) > 0)
            {
                
                $html[] = '<div class="description">';
                $finishtext = $this->parent->get_page($current_page->get_page_number())->get_finish_text();
                
                //                $html[] = $this->parent->get_parent()->parse($finishtext);
                $html[] = $finishtext;
                
                $html[] = '</div>';
            }
            
            $html[] = '<br />';
            
            if (strlen(strip_tags($this->parent->get_survey()->get_footer(), '<img>')) > 0)
            {
                
                $html[] = '<div class="description">';
                $survey_footer = $this->parent->get_survey()->get_footer();
                
                //                $html[] = $this->parent->get_parent()->parse($survey_footer);
                $html[] = $survey_footer;
                $html[] = '</div>';
            }
            
            $html[] = '<br />';
            
            $html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = $current_page->get_page_number() . ' / ' . $this->parent->get_total_pages();
            $html[] = '</div>';
            
            $html[] = '</div>';
            $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/survey.js');
            
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
            
            echo implode("\n", $html);
        }
        else
        {
            $html = array();
            $html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = Translation :: get('NoSurveyPageAddedToSurvey');
            $html[] = '</div>';
            echo implode("\n", $html);
        }
        
        $this->parent->get_parent()->display_footer();
    
    }

    function get_menu_html()
    {

        
		$url = $this->parent->get_parent()->get_url(array(), array(SurveyDisplay::PARAM_DISPLAY_ACTION));
		$url = explode('?', $url);
		$url_format = $url[1];
				
    	$url_format = '?'.$url_format.'&'.SurveyViewerWizard :: PARAM_SURVEY_ID.'=%s&'.SurveyViewerWizard :: PARAM_INVITEE_ID.'=%s&'.SurveyViewerWizard :: PARAM_CONTEXT_TEMPLATE_ID.'=%s&'.SurveyViewerWizard :: PARAM_TEMPLATE_ID.'=%s&'.SurveyViewerWizard :: PARAM_CONTEXT_ID.'=%s&'.SurveyViewerWizard :: PARAM_CONTEXT_PATH.'=%s';
    	$include_root = true;
    	$show_complete_tree = false;
        $hide_current_context_template_id = false;
          
        $survey = $this->parent->get_survey();
//    	$user_id = $this->parent->get_parent()->get_invitee_id();
        
        $survey_menu = new SurveyMenu($this->parent, Request::get(SurveyViewerWizard :: PARAM_TEMPLATE_ID), $url_format, $survey);
        
        $html = array();
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $survey_menu->render_as_tree();
        $html[] = '</div>';
        return implode("\n", $html);
    }

}
?>