<?php
/**
 * $Id: survey.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey
 */
/**
 * This class represents an survey
 */

require_once (dirname(__FILE__) . '/survey_context.class.php');

class Survey extends ContentObject implements ComplexContentObjectSupport
{
    const PROPERTY_HEADER = 'header';
    const PROPERTY_FOOTER = 'footer';
    const PROPERTY_FINISH_TEXT = 'finish_text';
    const PROPERTY_CONTEXT_TEMPLATE_ID = 'context_template_id';
    
    const CLASS_NAME = __CLASS__;
    
    const MANAGER_CONTEXT = 'context';
    
    private $context;
    
    private $page_matrix;
    private $user_id;
    private $context_paths;
    

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_HEADER, self :: PROPERTY_FOOTER, self :: PROPERTY_FINISH_TEXT, self :: PROPERTY_CONTEXT_TEMPLATE_ID);
    }

    function get_header()
    {
        return $this->get_additional_property(self :: PROPERTY_HEADER);
    }

    function set_header($text)
    {
        $this->set_additional_property(self :: PROPERTY_HEADER, $text);
    }

    function get_footer()
    {
        return $this->get_additional_property(self :: PROPERTY_FOOTER);
    }

    function set_footer($text)
    {
        $this->set_additional_property(self :: PROPERTY_FOOTER, $text);
    }

    function get_finish_text()
    {
        return $this->get_additional_property(self :: PROPERTY_FINISH_TEXT);
    }

    function set_finish_text($value)
    {
        $this->set_additional_property(self :: PROPERTY_FINISH_TEXT, $value);
    }

    function get_context_template_name()
    {
        $template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($this->get_additional_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID));
        return empty($template) ? '' : $template->get_name();
    
    }

    function get_context_template($level = null)
    {
        
        $context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($this->get_additional_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID));
        if ($level)
        {
            
            $index = 1;
            $level_matrix[$index] = $context_template;
            $context_template_children = $context_template->get_children(true);
            while ($child_template = $context_template_children->next_result())
            {
                $index ++;
                $level_matrix[$index] = $child_template;
            }
            return $level_matrix[$level];
        }
        
        return $context_template;
    
    }

    function get_context_template_id()
    {
        return $this->get_additional_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID);
    
    }

    function set_context_template_id($value)
    {
        $this->set_additional_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID, $value);
    }

    function has_context()
    {
        return $this->get_context_template_id() != 0;
    }

    function get_allowed_types()
    {
        return array(SurveyPage :: get_type_name());
    }

    function get_table()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function get_pages($complex_items = false)
    {
        
        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name()));
        
        if ($complex_items)
        {
            return $complex_content_objects;
        }
        
        $survey_pages = array();
        
        while ($complex_content_object = $complex_content_objects->next_result())
        {
            $survey_pages[] = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());
        }
        
        return $survey_pages;
    }

    function get_page_by_id($page_id)
    {
        return RepositoryDataManager :: get_instance()->retrieve_content_object($page_id);
    }

    function get_page_by_index($index)
    {
        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name()))->as_array();
        
        if (isset($complex_content_objects[$index - 1]))
        {
            return RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_objects[$index - 1]->get_ref());
        }
        else
        {
            return null;
        }
    }

    function count_pages()
    {
        return RepositoryDataManager :: get_instance()->count_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name()));
    }

    function get_context_paths($user_id, $with_question_id = false)
    {
        $this->user_id = $user_id;
    	$context_paths = array();
        if (! $this->has_context())
        {
            
            $pages = $this->get_pages();
            foreach ($pages as $page)
            {
                $page_id = $page->get_id();
                if ($with_question_id)
                {
                    $complex_questions = $page->get_questions(true);
                    while ($complex_question = $complex_questions->next_result())
                    {
                        $context_paths[] = $page_id . '_' . $complex_question->get_id();
                    }
                }
                else
                {
                    $context_paths[] = $page_id;
                }
            }
        }else{
        	
        	$contexts = $this->get_context_pages();
        	$level_count = $this->get_context_template(1)->get_level_count();
        	
//        	dump($contexts);
//        	dump($this->context_paths);
//        	dump(count($this->context_paths));
//        	dump(array_keys($contexts));
        	$context_paths = $this->context_paths;
        }
        
        return $context_paths;
    }

    function get_page_complex_questions($user_id, $context_path)
    {
        $questions = array();
        
        $ids = explode('_', $context_path);
        $count = count($ids);
        $page_id = $ids[$count - 1];
        
        $page = $this->get_page_by_id($page_id);
        
        $complex_questions = $page->get_questions(true);
        while ($complex_question = $complex_questions->next_result())
        {
            $questions[] = $complex_question;
        }
        
        return $questions;
    }
	
private function create_page_matrix()
    {
        
        $context_template = $this->get_context_template(1);
       
        $condition = new EqualityCondition(SurveyTemplate :: PROPERTY_USER_ID, $this->user_id, SurveyTemplate :: get_table_name());
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
            	$conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $this->get_id());
                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $this->get_context_template($level)->get_id($level));
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
                               
                $parent_id = $context_id;
                $level ++;
            }
        }
        
//        dump($page_matrix);
        
        $this->page_matrix = $page_matrix;
    }
	
	private function get_context_pages($level = 1, $parent_id = 0, $path = null)
    {
        
		if(!$this->page_matrix){
			$this->create_page_matrix();
		}
    	
			
    	foreach ($this->page_matrix[$level][$parent_id] as $id => $pages)
        {
//            $template_id = $this->menu_matrix[self :: PARAM_TEMPLATE_ID][$parent_id][$id];
            
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
            	 $this->context_paths[$path.'_'.$page_id] =$page_id;
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
    
    static function get_managers()
    {
        $managers = array();
        $managers[] = self :: MANAGER_CONTEXT;
        return $managers;
    }
}

?>