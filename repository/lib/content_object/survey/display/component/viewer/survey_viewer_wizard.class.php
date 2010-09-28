<?php

require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_display.class.php';
require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_process.class.php';
require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_next.class.php';
require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/questions_survey_viewer_wizard_page.class.php';

class SurveyViewerWizard extends HTML_QuickForm_Controller
{
    
    const PARAM_SURVEY_ID = 'survey_id';
    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_CONTEXT_TEMPLATE_ID = 'context_template_id';
    const PARAM_TEMPLATE_ID = 'template_id';
    const PARAM_CONTEXT_ID = 'context_id';
    const PARAM_INVITEE_ID = 'invitee_id';
    const PARAM_CONTEXT_PATH = 'path';
    
//    const PARAM_CURRENT_PAGE = 'current_page';
    
    private $parent;
    private $survey;
    private $context_template_id;
    
    private $context_path;
    
    
    private $invitee_id;
    
    private $page_matrix;
    
    private $has_template;
    
    private $context;
    
    private $total_pages;
    private $total_questions;
    private $pages;
    private $real_pages;
    private $question_visibility;

    function SurveyViewerWizard($parent)
    {
        $this->parent = $parent;
        $survey__id = Request :: get(self :: PARAM_SURVEY_ID);
        $this->get_parent()->set_parameter(self :: PARAM_SURVEY_ID, $survey__id);
        
        parent :: HTML_QuickForm_Controller('SurveyViewerWizard_' . $survey__id, true);
        
        $this->invitee_id = Request :: get(self :: PARAM_INVITEE_ID);
        
        $this->survey = RepositoryDataManager :: get_instance()->retrieve_content_object($survey__id);
        
        if ($this->survey->has_context())
        {
            $this->context_path = Request :: get(self :: PARAM_CONTEXT_PATH);
            if (!$this->context_path)
            {
                $context_template = $this->survey->get_context_template(1);
                $this->context_template_id = $context_template->get_id();
                
                $this->parent->started_context($this->survey, $context_template, $context_id);
            }else{
            	$path = explode('_', $this->context_path);
            	$level = count($path);
            	$context_template = $this->survey->get_context_template($level);
            	
            }
            $this->add_context_pages();
        }else{
        	 $this->add_pages();
        }
                  
        $this->addAction('next', new SurveyViewerWizardNext($this));
        $this->addAction('process', new SurveyViewerWizardProcess($this));
        $this->addAction('display', new SurveyViewerWizardDisplay($this));
        
    }

    function add_pages()
    {
        
        $complex_survey_page_items = $this->survey->get_pages(true);
        $page_nr = 0;
        $question_nr = 0;
        $this->question_visibility = array();
        
        while ($survey_page_item = $complex_survey_page_items->next_result())
        {
            $survey_page = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_page_item->get_ref());
            
            $page_nr ++;
            $this->real_pages[$page_nr] = $survey_page->get_id();
            
            $this->addPage(new QuestionsSurveyViewerWizardPage('question_page_' . $page_nr, $this, $page_nr));
            
            $questions = array();
            $questions_items = $survey_page->get_questions(true);
            
            while ($question_item = $questions_items->next_result())
            {
                $question = RepositoryDataManager :: get_instance()->retrieve_content_object($question_item->get_ref());
                
                if ($question_item->get_visible() == 1)
                {
                    $this->question_visibility[$question->get_id()] = true;
                }
                else
                {
                    $this->question_visibility[$question->get_id()] = false;
                }
                
                if ($question->get_type() == SurveyDescription :: get_type_name())
                {
                    $questions[$question->get_id() . 'description'] = $question;
                }
                else
                {
                    if ($question_item->get_visible() == 1)
                    {
                        $question_nr ++;
                        $questions[$question_nr] = $question;
                    }
                    else
                    {
                        //                    	$question_nr ++;
                        $bis_nr = $question_nr . '.1';
                        $questions[$bis_nr] = $question;
                    }
                
                }
            
            }
            
            $this->pages[$page_nr] = array(page => $survey_page, questions => $questions);
        
        }
        
        //there are no pages added to this survey so add a page with just a message that says exactly that!
        if ($page_nr == 0)
        {
            $this->addPage(new QuestionsSurveyViewerWizardPage('question_page_' . $page_nr, $this, $page_nr));
        }
        
        $this->total_pages = $page_nr;
        $this->total_questions = $question_nr;
    
    }

    function add_context_pages()
    {
        
        $this->create_page_matrix();
      
        dump($this->get_context_pages());
        
    exit;    
        $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $this->survey->get_id());
    	$conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $this->context_template_id);
        $condition = new AndCondition($conditions);
        
        $template_rel_pages = SurveyContextDataManager :: get_instance()->retrieve_template_rel_pages($condition);
        $pages_ids = array();
        while ($template_rel_page = $template_rel_pages->next_result())
        {
            $pages_ids[] = $template_rel_page->get_page_id();
        }
        
//        dump($pages_ids);
        
        $complex_survey_page_items = $this->survey->get_pages(true);
        $page_nr = 0;
        $question_nr = 0;
        $this->question_visibility = array();
        
        while ($survey_page_item = $complex_survey_page_items->next_result())
        {
            if ($check_allowed_pages)
            {
                if (! in_array($survey_page_item->get_ref(), $allowed_pages))
                {
                    continue;
                }
            }
            
            $survey_page = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_page_item->get_ref());
            
            $page_nr ++;
            $this->real_pages[$page_nr] = $survey_page->get_id();
            //             dump($page_nr);
            $this->addPage(new QuestionsSurveyViewerWizardPage('question_page_' . $page_nr, $this, $page_nr));
            $questions = array();
            $questions_items = $survey_page->get_questions(true);
            
            while ($question_item = $questions_items->next_result())
            {
                $question = RepositoryDataManager :: get_instance()->retrieve_content_object($question_item->get_ref());
                
                if ($question_item->get_visible() == 1)
                {
                    $this->question_visibility[$question->get_id()] = true;
                }
                else
                {
                    $this->question_visibility[$question->get_id()] = false;
                }
                
                if ($question->get_type() == SurveyDescription :: get_type_name())
                {
                    $questions[$question->get_id() . 'description'] = $question;
                }
                else
                {
                    if ($question_item->get_visible() == 1)
                    {
                        $question_nr ++;
                        $questions[$question_nr] = $question;
                    }
                    else
                    {
                        //                    	$question_nr ++;
                        $bis_nr = $question_nr . '.1';
                        $questions[$bis_nr] = $question;
                    }
                
                }
            
            }
            
            $this->pages[$page_nr] = array(page => $survey_page, questions => $questions);
            
        //            dump('pagenr:'.$page_nr);
        }
        
        if ($page_nr == 0)
        {
            
            $this->addPage(new QuestionsSurveyViewerWizardPage('question_page_' . $page_nr, $this, $page_nr));
        }
        
        //        dump(array_keys($this->pages));
        

        $this->total_pages = $page_nr;
        $this->total_questions = $question_nr;
    
    }

    function get_questions($page_number)
    {
        $page = $this->pages[$page_number];
        $questions = $page['questions'];
        return $questions;
    }

    function get_page($page_number)
    {
        $page = $this->pages[$page_number];
        $page_object = $page['page'];
        return $page_object;
    }

    function get_real_page_nr($page_nr)
    {
        return $this->real_pages[$page_nr];
    }

    function get_question_visibility($question_id)
    {
        return $this->question_visibility[$question_id];
    }

    function get_parent()
    {
        return $this->parent;
    }

    function get_survey()
    {
        return $this->survey;
    }

    function has_context()
    {
        return $this->survey->has_context();
    }

    function get_total_pages()
    {
        return $this->total_pages;
    }

    function get_total_questions()
    {
        $count = 0;
        
        foreach ($this->question_visibility as $visible)
        {
            if ($visible)
            {
                $count = $count + 1;
            }
        }
        
        return $count;
    }

    function save_answer($complex_question_id, $answer)
    {
        $this->parent->save_answer($complex_question_id, $answer);
    }

    function parse($value)
    {
        
        if ($this->context)
        {
            $explode = explode('$V{', $value);
            
            $new_value = array();
            foreach ($explode as $part)
            {
                
                $vars = explode('}', $part);
                
                if (count($vars) == 1)
                {
                    $new_value[] = $vars[0];
                }
                else
                {
                    $var = $vars[0];
                    
                    $replace = $this->context->get_additional_property($var);
                    
                    $new_value[] = $replace . ' ' . $vars[1];
                }
            
            }
            return implode(' ', $new_value);
        }
        else
        {
            return $value;
        }
    
    }

    private function create_page_matrix()
    {
        
        $context_template = $this->survey->get_context_template(1);
      
//        $context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($root_context_template_id);
        
//        $level = 1;
//        $this->level_matrix[$level] = $context_template->get_id();
//        $context_template_children = $context_template->get_children(true);
//        while ($child_template = $context_template_children->next_result())
//        {
//            $level ++;
//            $this->level_matrix[$level] = $child_template->get_id();
//        }
//        
//        dump($this->level_matrix);
        
        $condition = new EqualityCondition(SurveyTemplate :: PROPERTY_USER_ID, $this->invitee_id, SurveyTemplate :: get_table_name());
        $templates = SurveyContextDataManager :: get_instance()->retrieve_survey_templates($context_template->get_type(), $condition);
        
        $page_matrix = array();
        
        while ($template = $templates->next_result())
        {
            
            $level = 1;
            $property_names = $template->get_additional_property_names(true);
            $parent_id = 0;
            foreach ($property_names as $property_name => $context_type)
            {
                
                $conditions = array();
            	$conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $this->survey->get_id());
                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $this->survey->get_context_template($level)->get_id($level));
                $condition = new AndCondition($conditions);
                $template_rel_pages = SurveyContextDataManager :: get_instance()->retrieve_template_rel_pages($condition);
                $pages_ids = array();
                while ($template_rel_page = $template_rel_pages->next_result())
                {
//                   dump($template_rel_page);
                	$pages_ids[] = $template_rel_page->get_page_id();
                }
                
                $context_id = $template->get_additional_property($property_name);
                
                $context = SurveyContextDataManager :: get_instance()->retrieve_survey_context_by_id($context_id);
               
                $page_matrix[$level][$parent_id][$context_id] = $pages_ids;
//                $page_matrix[$level][$parent_id][$context_id] = $context->get_name();
                $page_matrix[self :: PARAM_TEMPLATE_ID][$parent_id][$context_id] = $template->get_id();
                
                $parent_id = $context_id;
                $level ++;
            }
        }
        
        dump($page_matrix);
        
        $this->page_matrix = $page_matrix;
    }
	
	private function get_context_pages($level = 1, $parent_id = 0, $path = null)
    {
        
        foreach ($this->page_matrix[$level][$parent_id] as $id => $pages)
        {
            $template_id = $this->menu_matrix[self :: PARAM_TEMPLATE_ID][$parent_id][$id];
            $context_template_id = $this->level_matrix[$level];
            
            if ($level == 1)
            {
                $path = null;
            }
            
            if ($path)
            {
                $contexts = explode('_', $path);
                
                if (count($contexts) == $level)
                {
                    $index = 0;
                    $path = '';
                    while ($index != $level - 1)
                    {
                        if ($index != 0)
                        {
                            $path = $path . '_' . $contexts[$index];
                        }
                        else
                        {
                            $path = $contexts[$index];
                        }
                        $index ++;
                    }
                }
                
                $path = $path . '_' . $id;
            
            }
            else
            {
                $path = $id;
            }
            
            $menu_item = array();
            foreach ($pages as $page_id) {
            	 $menu_item[$path.'_'.$page_id] = $page_id;
            }
                    
            $sub_menu_items = $this->get_context_pages($level + 1, $id, $path);
            if (count($sub_menu_items) > 0)
            {
                foreach ($sub_menu_items as $sub_parent_id => $sub_menu_item)
                {
                    $menu_item['sub'] = $sub_menu_items;
                }
            }
            $menu[$id] = $menu_item;
        }
        return $menu;
    }
    
}
?>