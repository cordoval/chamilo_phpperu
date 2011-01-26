<?php
namespace repository\content_object\survey;

use common\libraries\Request;
use repository\RepositoryDataManager;
use common\libraries\ResourceManager;
use common\libraries\Path;
use common\libraries\Translation;

class SurveyDisplaySurveyViewerComponent extends SurveyDisplay
{
    
    const FORM_BACK = 'back';
    const FORM_NEXT = 'next';
    const FORM_FINISH = 'finish';
    
    const PARAM_SURVEY_ID = 'survey_id';
    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_CONTEXT_TEMPLATE_ID = 'context_template_id';
    const PARAM_TEMPLATE_ID = 'template_id';
    const PARAM_CONTEXT_ID = 'context_id';
    const PARAM_INVITEE_ID = 'invitee_id';
    const PARAM_CONTEXT_PATH = 'path';
    
    private $context_path;
    private $current_page;
    private $context_paths;
    private $survey_menu;

    /**
     * Runs this component and displays its output.
     */
    
    function run()
    {
        
        $this->started();
        
        $survey_id = Request :: get(self :: PARAM_SURVEY_ID);
        
        $invitee_id = $this->get_parent()->get_user_id();
        $this->survey = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_id);
        
        $this->survey->initialize($invitee_id);
        $paths = $this->survey->get_context_paths();
        
        $page_context_paths = $this->survey->get_page_context_paths();
        $total_page_count = count($page_context_paths);
        
        $this->context_paths = array();
        $page_order = array();
        $page_count = $total_page_count;
        foreach ($page_context_paths as $page_context_path)
        {
            $page_order[$page_count - 1] = $page_context_path;
            $this->context_paths[$page_context_path] = $page_count;
            $page_count --;
        }
        
        $this->context_path = Request :: get(self :: PARAM_CONTEXT_PATH);
        
        if ($this->survey_view_form_submitted() && $this->get_action() == self :: FORM_NEXT)
        {
            $answer_processor = new SurveyAnswerProcessor($this);
            $this->context_path = $answer_processor->save_answers();
        }
        
        if ($this->survey_view_form_submitted() && $this->get_action() == self :: FORM_BACK)
        {
            $answer_processor = new SurveyAnswerProcessor($this);
            $this->context_path = $answer_processor->get_previous_context_path();
        }
        $finished = false;
        
        if ($this->survey_view_form_submitted() && $this->get_action() == self :: FORM_FINISH)
        {
            $finished = true;
        }
        if ($finished)
        {
        	$this->build_summery_viewer();
        }
        else
        {
            if (! $this->context_path)
            {
                $this->context_path = $page_context_paths[$total_page_count - 1];
            }
            
            $this->current_page = $this->survey->get_survey_page($this->context_path);
            
            $action = $this->get_parent()->get_url();
            $page_nrs = array_flip($page_order);
            $page_nr = $page_nrs[$this->context_path] + 1;
            
            $form = new SurveyViewerForm($this->context_path, $this, $this->context_path, $this->survey, $action, $page_order, $page_nr, $invitee_id);
            
            $this->build_question_viewer($form);
        }
    
    }

    private function build_question_viewer($form)
    {
        $html = array();
        $this->get_parent()->display_header();
        $html[] = $this->get_menu_html();
        $html[] = '<div style="float: right; width: 70%;">';
        $html[] = '<div class="assessment">';
        $html[] = '<h2>' . $this->survey->parse($this->context_path, $this->survey->get_title()) . '</h2>';
        $html[] = '<br />';
        $html[] = '<div style="width: 100%; text-align: center;">';
        $html[] = $this->context_paths[$this->context_path] . ' / ' . $this->survey->count_pages();
        $html[] = '</div>';
        $html[] = '<br />';
        
        $html[] = '</div>';
        $html[] = '<div>';
        $html[] = $form->toHtml();
        $html[] = '</div>';
        $html[] = '<br />';
        $html[] = '<div class="assessment">';
        
        if (strlen(strip_tags($this->current_page->get_finish_text(), '<img>')) > 0)
        {
            $html[] = '<div class="description">';
            $finishtext = $this->current_page->get_finish_text();
            $html[] = $this->survey->parse($this->context_path, $finishtext);
            $html[] = '</div>';
        }
        
        $html[] = '<br />';
        
        if (strlen(strip_tags($this->survey->get_footer(), '<img>')) > 0)
        {
            
            $html[] = '<div class="description">';
            $survey_footer = $this->survey->get_footer();
            $html[] = $this->survey->parse($this->context_path, $survey_footer);
            $html[] = '</div>';
        }
        
        $html[] = '<br />';
        $html[] = '<div style="width: 100%; text-align: center;">';
        $html[] = $this->context_paths[$this->context_path] . ' / ' . $this->survey->count_pages();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'repository/content_object/survey/resources/javascript/survey_question.js');
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        echo implode("\n", $html);
        
        $this->get_parent()->display_footer();
    }

    private function build_summery_viewer()
    {
        $this->get_parent()->display_header();
        $html = array();
        $html[] = '<div class="assessment">';
        $html[] = '<h2>' . $this->survey->get_title() . '</h2>';
        $html[] = '</div>';
        $html[] = '<div class="assessment">';
        $html[] = '<div class="description">';
        $finish_text = $this->survey->get_finish_text();
        $html[] = $this->survey->parse($this->context_path, $finish_text);
        $html[] = '</div></div>';
        $back_url = $this->get_go_back_url();
        $html[] = '<a href="' . $back_url . '">' . Translation :: get('GoBack') . '</a>';
        
        echo implode("\n", $html);
        
        $this->get_parent()->display_footer();
    }

    function get_menu_html()
    {
        $url = $this->get_parent()->get_url(array(), array(SurveyDisplay :: PARAM_DISPLAY_ACTION, 
                self :: PARAM_PUBLICATION_ID, self :: PARAM_SURVEY_ID, self :: PARAM_CONTEXT_PATH));
        $url = explode('?', $url);
        $url_format = $url[1];
        $url_format = '?' . $url_format . '&' . self :: PARAM_PUBLICATION_ID . '=%s&' . self :: PARAM_SURVEY_ID . '=%s&' . self :: PARAM_CONTEXT_PATH . '=%s';
        
        $this->survey_menu = new SurveyMenu($this->get_parent(), $this->context_path, $url_format, $this->survey);
        $html = array();
        $html[] = '<div style="float: left; width: 28%; overflow: auto; height: 600px;">';
        $html[] = $this->get_progress_bar();
        $html[] = '<br />';
        $html[] = $this->survey_menu->render_as_tree();
        $html[] = '<br />';
        $html[] = $this->get_progress_bar();
        $html[] = '<br />';
        $html[] = '</div>';
        
        return implode("\n", $html);
    }

    /**
     * Renders the progress bar for the learning path
     *
     * @return array() HTML code of the progress bar
     */
    private function get_progress_bar()
    {
        $progress = $this->survey_menu->get_progress();
        $html = array();
        $html[] = '<div style="position: relative; text-align: center; border: 1px solid black; height: 14px; width:100px;">';
        $html[] = '<div style="background-color: lightblue; height: 14px; width:' . $progress . 'px; text-align: center;">';
        $html[] = '</div>';
        $html[] = '<div style="width: 100px; text-align: center; position: absolute; top: 0px;">' . round($progress) . '%</div></div>';
        return implode("\n", $html);
    }

    function get_progress()
    {
        $this->get_menu_html();
        return $this->survey_menu->get_progress();
    }

    function started()
    {
        $this->get_parent()->started();
    }

    function finished()
    {
        $progress = $this->get_progress();
        $this->get_parent()->finished($progress);
    }

    function save_answer($question_id, $answer, $context_path)
    {
        $this->get_parent()->save_answer($question_id, $answer, $context_path);
    }

    function get_answer($complex_question_id, $context_path)
    {
        return $this->get_parent()->get_answer($complex_question_id, $context_path);
    }

    function get_go_back_url()
    {
        return $this->get_parent()->get_go_back_url();
    }

    function survey_view_form_submitted()
    {
        return ! is_null(Request :: post('_qf__' . SurveyViewerForm :: FORM_NAME));
    }

    function get_action()
    {
        $actions = array(self :: FORM_NEXT, self :: FORM_FINISH, self :: FORM_BACK);
        
        foreach ($actions as $action)
        {
            if (! is_null(Request :: post($action)))
            {
                return $action;
            }
        }
        
        return self :: FORM_NEXT;
    }

    function get_previous_context_path($context_path)
    {
        //        dump('now '.$context_path);
        $previous_page_nr = $this->context_paths[$context_path] - 1;
        $previous_context_path = null;
        foreach ($this->context_paths as $context_path => $page_nr)
        {
            if ($page_nr == $previous_page_nr)
            {
                $previous_context_path = $context_path;
                break;
            }
        }
        //        dump('prev '.$previous_context_path);
        return $previous_context_path;
    }
}
?>