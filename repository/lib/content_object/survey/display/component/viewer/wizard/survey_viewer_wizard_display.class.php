<?php

require_once Path :: get_repository_path() . 'lib/content_object/survey/display/component/survey_menu.class.php';

class SurveyViewerWizardDisplay extends HTML_QuickForm_Action_Display
{
    
    private $parent;
    /**
     * @var Survey
     */
    private $survey;

    public function SurveyViewerWizardDisplay($parent)
    {
        
        $this->parent = $parent;
        $this->survey = $this->parent->get_survey();
    }

    /**
     * Displays the HTML-code of a page in the wizard
     * @param HTML_Quickform_Page $page The page to display.
     */
    function _renderForm($current_page)
    {
        $html = array();
        $this->parent->get_parent()->display_header();
        
//        if ($this->survey->has_context())
//        {
            $this->with_menu = true;
            $html[] = $this->get_menu_html($current_page);
//        }
        
        if ($this->with_menu)
        {
            $width = 80;
        }
        else
        {
            $width = 100;
        }
        $html[] = '<div style="float: right; width: ' . $width . '%;">';
        $html[] = '<div class="assessment">';
        $html[] = '<h2>' . $this->survey->parse($current_page->get_context_path(), $this->survey->get_title()) . '</h2>';
        $html[] = '<br />';
        $html[] = '<div style="width: 100%; text-align: center;">';
        $html[] = $current_page->get_page_number() . ' / ' . $this->survey->count_pages();
        $html[] = '</div>';
        $html[] = '<br />';
        
        if (strlen(strip_tags($this->survey->get_header(), '<img>')) > 0)
        {
            $html[] = '<div class="description">';
            $survey_header = $this->survey->get_header();
            $html[] = $this->survey->parse($current_page->get_context_path(), $survey_header);
            $html[] = '</div>';
        }
        
        $html[] = '<br />';
        
        if (strlen(strip_tags($current_page->get_survey_page()->get_introduction_text(), '<img>')) > 0)
        {
            $html[] = '<div class="description">';
            $introduction = $current_page->get_survey_page()->get_introduction_text();
            $html[] = $this->survey->parse($current_page->get_context_path(), $introduction);
            $html[] = '</div>';
        }
        
        $html[] = '</div>';
        $html[] = '<div>';
        $html[] = $current_page->toHtml();
        $html[] = '</div>';
        $html[] = '<br />';
        $html[] = '<div class="assessment">';
        
        if (strlen(strip_tags($current_page->get_survey_page()->get_finish_text(), '<img>')) > 0)
        {
            $html[] = '<div class="description">';
            $finishtext = $current_page->get_survey_page()->get_finish_text();
            $html[] = $this->survey->parse($current_page->get_context_path(), $finishtext);
            $html[] = '</div>';
        }
        
        $html[] = '<br />';
        
        if (strlen(strip_tags($this->survey->get_footer(), '<img>')) > 0)
        {
            
            $html[] = '<div class="description">';
            $survey_footer = $this->survey->get_footer();
            $html[] = $this->survey->parse($current_page->get_context_path(), $survey_footer);
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
        
        $this->parent->get_parent()->display_footer();
    }

    function get_menu_html($current_page)
    {
        
        $url = $this->parent->get_parent()->get_url(array(), array(SurveyDisplay :: PARAM_DISPLAY_ACTION, SurveyViewerWizard :: PARAM_PUBLICATION_ID, SurveyViewerWizard :: PARAM_SURVEY_ID, SurveyViewerWizard :: PARAM_INVITEE_ID, SurveyViewerWizard :: PARAM_CONTEXT_PATH));
        $url = explode('?', $url);
        $url_format = $url[1];
        
        $url_format = '?' . $url_format . '&' . SurveyViewerWizard :: PARAM_PUBLICATION_ID . '=%s&' . SurveyViewerWizard :: PARAM_SURVEY_ID . '=%s&' . SurveyViewerWizard :: PARAM_INVITEE_ID . '=%s&' . SurveyViewerWizard :: PARAM_CONTEXT_PATH . '=%s';
        
        $survey_menu = new SurveyMenu($this->parent, $current_page->get_context_path(), $url_format, $this->survey);
        
        $html = array();
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $survey_menu->render_as_tree();
        $html[] = '</div>';
        return implode("\n", $html);
    }

}
?>