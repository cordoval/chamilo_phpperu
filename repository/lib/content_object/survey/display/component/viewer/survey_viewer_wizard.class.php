<?php

require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_display.class.php';
require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_process.class.php';
require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_next.class.php';
require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/questions_survey_viewer_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/survey_question_viewer_wizard_page.class.php';

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
    /**
     * @var Survey
     */
    private $survey;
//    private $context_template_id;
    
//    private $context_path;
    
    private $invitee_id;
    
//    private $page_matrix;
    
//    private $has_template;
    
//    private $context;
    
    private $total_pages;
    private $total_questions;
    private $pages;
    private $real_pages;
    private $question_visibility;

    function SurveyViewerWizard($parent)
    {
        $this->parent = $parent;
        $survey_id = Request :: get(self :: PARAM_SURVEY_ID);
        
        parent :: HTML_QuickForm_Controller('SurveyViewerWizard_' . $survey_id, true);
        
        $this->invitee_id = Request :: get(self :: PARAM_INVITEE_ID);
        $this->survey = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_id);
        $this->survey->set_invitee_id( $this->invitee_id);
        
        $this->add_pages();
        
        $this->addAction('next', new SurveyViewerWizardNext($this));
        $this->addAction('process', new SurveyViewerWizardProcess($this));
        $this->addAction('display', new SurveyViewerWizardDisplay($this));
    
    }

    function add_pages()
    {
        
        $page_context_paths = $this->survey->get_page_context_paths();
        
//        dump($page_context_paths);
//        dump($this->survey->get_context_paths());
//        dump(count($this->survey->get_context_paths()));
        
        if (count($page_context_paths))
        {
            
//            $this->total_pages = count($context_paths);
//            $level_count = $this->survey->count_levels();
            $page_nr = 1;
            
            foreach ($page_context_paths as $page_context_path)
            {
//                $path_ids = explode('_', $context_path);
//                if (count($path_ids) == $level_count + 1)
//                {
//                    $survey_page_id = $path_ids[$level_count];
                    $this->addPage(new SurveyQuestionViewerWizardPage('page_' . $page_context_path, $this, $page_context_path, $this->survey));
//                    $page_nr ++;
//                }
            
            }
//        	 $this->total_pages = count($page_nr);
        }
        else
        {
            //no pages added to survey !!
        //            $this->addPage(new SurveyQuestionViewerWizardPage('question_page_' . $page_nr, $this, $page_nr));
        }
    }

    function get_parent()
    {
        return $this->parent;
    }

    function get_survey()
    {
        return $this->survey;
    }

    function get_total_pages()
    {
        return $this->total_pages;
    }

    function save_answer($complex_question_id, $answer, $context_path)
    {
        $this->parent->save_answer($complex_question_id, $answer, $context_path);
    }

    function get_answer($complex_question_id, $answer, $context_path)
    {
        $this->parent->get_answer($complex_question_id, $context_path);
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
    
//    private function create_page_matrix()
//    {
//        
//        $context_template = $this->survey->get_context_template(1);
//        
//        $condition = new EqualityCondition(SurveyTemplate :: PROPERTY_USER_ID, $this->invitee_id, SurveyTemplate :: get_table_name());
//        $templates = SurveyContextDataManager :: get_instance()->retrieve_survey_templates($context_template->get_type(), $condition);
//        
//        $page_matrix = array();
//        
//        while ($template = $templates->next_result())
//        {
//            
//            $level = 1;
//            $property_names = $template->get_additional_property_names(true);
//            $parent_id = 0;
//            foreach ($property_names as $property_name => $context_type)
//            {
//                
//                $conditions = array();
//                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $this->survey->get_id());
//                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $this->survey->get_context_template($level)->get_id($level));
//                $condition = new AndCondition($conditions);
//                $template_rel_pages = SurveyContextDataManager :: get_instance()->retrieve_template_rel_pages($condition);
//                $pages_ids = array();
//                while ($template_rel_page = $template_rel_pages->next_result())
//                {
//                    //                   dump($template_rel_page);
//                    $pages_ids[] = $template_rel_page->get_page_id();
//                }
//                
//                $context_id = $template->get_additional_property($property_name);
//                
//                $context = SurveyContextDataManager :: get_instance()->retrieve_survey_context_by_id($context_id);
//                
//                $page_matrix[$level][$parent_id][$context_id] = $pages_ids;
//                //                $page_matrix[$level][$parent_id][$context_id] = $context->get_name();
//                $page_matrix[self :: PARAM_TEMPLATE_ID][$parent_id][$context_id] = $template->get_id();
//                
//                $parent_id = $context_id;
//                $level ++;
//            }
//        }
//        
//        //        dump($page_matrix);
//        
//
//        $this->page_matrix = $page_matrix;
//    }
//
//    private function get_context_pages($level = 1, $parent_id = 0, $path = null)
//    {
//        
//        foreach ($this->page_matrix[$level][$parent_id] as $id => $pages)
//        {
//            $template_id = $this->menu_matrix[self :: PARAM_TEMPLATE_ID][$parent_id][$id];
//            $context_template_id = $this->level_matrix[$level];
//            
//            if ($level == 1)
//            {
//                $path = null;
//            }
//            
//            if ($path)
//            {
//                $contexts = explode('_', $path);
//                
//                if (count($contexts) == $level)
//                {
//                    $index = 0;
//                    $path = '';
//                    while ($index != $level - 1)
//                    {
//                        if ($index != 0)
//                        {
//                            $path = $path . '_' . $contexts[$index];
//                        }
//                        else
//                        {
//                            $path = $contexts[$index];
//                        }
//                        $index ++;
//                    }
//                }
//                
//                $path = $path . '_' . $id;
//            
//            }
//            else
//            {
//                $path = $id;
//            }
//            
//            $menu_item = array();
//            foreach ($pages as $page_id)
//            {
//                $menu_item[$path . '_' . $page_id] = $page_id;
//            }
//            
//            $sub_menu_items = $this->get_context_pages($level + 1, $id, $path);
//            if (count($sub_menu_items) > 0)
//            {
//                foreach ($sub_menu_items as $sub_parent_id => $sub_menu_item)
//                {
//                    $menu_item['sub'] = $sub_menu_items;
//                }
//            }
//            $menu[$id] = $menu_item;
//        }
//        return $menu;
//    }
//
//    function getActionName()
//    {
//        //    	dump(parent :: getActionName());
//        return parent :: getActionName();
//    
//    }


}
?>