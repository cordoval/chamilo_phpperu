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
    
    private $context_path_tree;
    private $user_id;
    private $context_paths;
    private $question_context_paths;
    private $question_nr = 1;
    
    private $context_objects;

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

    function get_context_template($level = 1)
    {
        
        $context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($this->get_additional_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID));
        if ($level != 1)
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

    function count_levels()
    {
        if (! $this->has_context())
        {
            return 1;
        }
        else
        {
            return $this->get_context_template(1)->count_children(true) + 1;
        }
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

    /**
     * @param int $page_id
     * @return SurveyPage
     */
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

    //    function get_context_paths($user_id, $with_question_id = false)
    //    {
    //        $this->user_id = $user_id;
    //        
    //        //        dump('hello');
    //        $this->get_context_pages();
    //        dump('after get_pages');
    //        dump($this->context_paths);
    //        exit();
    //        return $this->context_paths;
    //    
    //    }
    

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

    public function get_context_paths($user_id, $tree = false)
    {
        $this->user_id = $user_id;
        if ($tree)
        {
            if (! $this->context_path_tree)
            {
                $this->create_context_path_tree();
            }
            return $this->context_path_tree;
        }
        else
        {
            
            if (! $this->context_paths)
            {
                $this->create_context_paths();
            }
            return $this->context_paths;
        }
    }

    private function create_context_path_tree()
    {
        
        $context_path_tree = array();
        
        if ($this->has_context())
        {
            $context_template = $this->get_context_template(1);
            
            $condition = new EqualityCondition(SurveyTemplate :: PROPERTY_USER_ID, $this->user_id, SurveyTemplate :: get_table_name());
            $templates = SurveyContextDataManager :: get_instance()->retrieve_survey_templates($context_template->get_type(), $condition);
            
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
                        $survey_page = $this->get_page_by_id($template_rel_page->get_page_id());
                        $page_id = $survey_page->get_id();
                        $pages_ids[] = $page_id;
                        $pages_ids['page_' . $page_id] = $survey_page->get_title();
                        
                        $complex_questions = $survey_page->get_questions(true);
                        $questions_ids = array();
                        while ($complex_question = $complex_questions->next_result())
                        {
                            if (! $complex_question instanceof ComplexSurveyDescription)
                            {
                                if ($complex_question->is_visible())
                                {
                                    $question_id = $complex_question->get_id();
                                    $questions_ids[] = $question_id;
                                    $questions_ids['question_' . $question_id] = 'vraag ' . $question_id;
                                
                                }
                            }
                        }
                        $pages_ids[$page_id] = $questions_ids;
                    }
                    
                    $context_id = $template->get_additional_property($property_name);
                    
                    $context = SurveyContextDataManager :: get_instance()->retrieve_survey_context_by_id($context_id);
                    
                    $context_path_tree[$level][$parent_id]['context_' . $context_id] = $context->get_name();
                    $context_path_tree[$level][$parent_id][$context_id] = $pages_ids;
                    
                    $parent_id = $context_id;
                    $level ++;
                }
            }
        }
        else
        {
            $pages = $this->get_pages();
            $page_ids = array();
            //            dump($page_ids);
            foreach ($pages as $page)
            {
                $page_ids[] = $page->get_id();
            }
            $context_path_tree[1][0][1] = $page_ids;
        }
        $this->context_path_tree = $context_path_tree;
    }

    private function create_page_context_paths($path, $page_context_as_tree)
    {
        $page_id = $page_context_as_tree[0];
        $page_path = $path . '_' . $page_id;
        $this->context_paths[] = $page_path;
        $questions = $page_context_as_tree[$page_id];
        
        foreach ($questions as $index => $question_id)
        {
            if (is_int($index))
            {
                $this->context_paths[] = $page_path . '_' . $question_id;
            }
        }
    }

    private function create_context_paths($level = 1, $parent_id = 0, $path = null)
    {
        
        if (! $this->context_path_tree)
        {
            $this->create_context_path_tree();
        }
        
        if ($level == 1)
        {
            $this->question_nr = 1;
        }
        
        foreach ($this->context_path_tree[$level][$parent_id] as $id => $pages)
        {
            if (is_int($id))
            {
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
                
                $this->create_page_context_paths($path, $pages);
                
                if ($level < ($this->count_levels()))
                {
                    $this->create_context_paths($level + 1, $id, $path);
                }
            }
            else
            {
                continue;
            }
        }
    }

    function parse($context_path, $value)
    {
        
        $context_objects = $this->get_context_objects($context_path);
        
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
                
                foreach ($context_objects as $index => $context_object)
                {
                    if ($index != 'user')
                    {
                        $replace = $context_object->get_additional_property($var);
                    }
                    else
                    {
                        $replace = $context_object->get_default_property($var);
                    }
                }
                
                $new_value[] = $replace . ' ' . $vars[1];
            }
        
        }
        return implode(' ', $new_value);
    
    }

    private function get_context_objects($context_path)
    {
        
        if ($this->context_objects)
        {
            return $this->context_objects;
        }
        else
        {
            $this->context_objects = array();
            
            $user = UserDataManager :: get_instance()->retrieve_user($this->user_id);
            $this->context_objects['user'] = $user;
            $level_count = $this->count_levels();
            $ids = explode('_', $context_path);
            $count = count($ids);
            if ($count > 1)
            {
                $index = 1;
                while ($index < $level_count)
                {
                    $this->context_objects[] = SurveyContextDataManager :: get_instance()->retrieve_survey_context_by_id($ids[$index]);
                    $index ++;
                }
            }
        }
    }

    function get_question_nr($question_context_path)
    {
        return $this->question_context_paths[$question_context_path];
    }

    static function get_managers()
    {
        $managers = array();
        $managers[] = self :: MANAGER_CONTEXT;
        return $managers;
    }
}

?>