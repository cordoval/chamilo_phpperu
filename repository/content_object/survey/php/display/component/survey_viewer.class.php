<?php
namespace repository\content_object\survey;

use common\libraries\Request;
use repository\RepositoryDataManager;
use common\libraries\ResourceManager;
use common\libraries\Path;
use common\libraries\Translation;

class SurveyDisplaySurveyViewerComponent extends SurveyDisplay
{
    
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
        $survey_id = Request :: get(self :: PARAM_SURVEY_ID);
        $this->context_path = Request :: get(self :: PARAM_CONTEXT_PATH);
        
        $invitee_id = $this->get_parent()->get_user_id();
        $this->survey = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_id);
        
        $this->survey->initialize($invitee_id);
        $paths = $this->survey->get_context_paths();
//        dump('surveypaths: ');
//        dump($paths);
        
        $page_context_paths = $this->survey->get_page_context_paths();
//        dump('pagepaths: ');
//        dump($page_context_paths);
//        
//        exit;
//        
        $this->context_paths = array();
        $page_order = array();
        $page_count = 0;
        foreach ($page_context_paths as $page_context_path)
        {
            $page_count ++;
            $page_order[$page_count - 1] = $page_context_path;
            $this->context_paths[$page_context_path] = $page_count;
         }
//             dump('surveycontextpaths: ');
//   dump($this->context_paths);
        
//        exit; 
        
        if (! $this->context_path)
        {
            $this->context_path = $page_context_paths[0];
        }
        
//        dump($this->context_path);
        
       
        
        $current_page = $this->survey->get_survey_page($this->context_path);
        
//        dump($current_page);
//         exit;
         
        $this->current_page = $current_page;
              
        $this->started();
        
        $action = $this->get_parent()->get_url();
        $page_nrs  = array_flip($page_order);
//        dump($page_order);
        $page_nr = $page_nrs[$this->context_path]+1;

//        dump($page_nr);
        
        $form = new SurveyViewerForm($this->context_path, $this, $this->context_path, $this->survey, $action, $page_order, $page_nr);
     
        
        if ($form->validate())
        {
            $form->process_answers();
            if ($form->is_finished())
            {
                $this->finished($this->survey_menu->get_progress());
                $this->build_summery_viewer();
            }
            else
            {
                $this->context_path = $form->get_next_context_path();
//                dump($page_order);
                $page_nrs  = array_flip($page_order);
//                dump($pages);
                
            	$page_nr = $page_nrs[$this->context_path]+1;
            	
//            	dump($page_nr);
//            	exit;
            	
            	$this->current_page = $this->survey->get_survey_page($this->context_path);
                $action = $this->get_parent()->get_url(array(self :: PARAM_CONTEXT_PATH => $this->context_path));
                $form = new SurveyViewerForm($this->context_path, $this, $this->context_path, $this->survey, $action, $page_order, $page_nr);
                $this->build_question_viewer($form);
            }
        }
        else
        {
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
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'repository/content_object/survey/resources/javascript/survey.js');
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
        $html[] = $this->survey->parse($context_path, $finish_text);
        $html[] = '</div></div>';
        $back_url = $this->get_go_back_url();
        $html[] = '<a href="' . $back_url . '">' . Translation :: get('GoBack') . '</a>';
        
        echo implode("\n", $html);
        
        $this->get_parent()->display_footer();
    }

    function get_menu_html()
    {
        $url = $this->get_parent()->get_url(array(), array(SurveyDisplay :: PARAM_DISPLAY_ACTION, self :: PARAM_PUBLICATION_ID, self :: PARAM_SURVEY_ID, self :: PARAM_CONTEXT_PATH));
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

    function finished($progress)
    {
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
}
?>