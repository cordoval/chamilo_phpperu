<?php
namespace repository\content_object\survey;

use repository\content_object\survey_description\ComplexSurveyDescription;
use repository\ContentObject;
use common\libraries\ComplexContentObjectSupport;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\InCondition;
use repository\content_object\survey_page\SurveyPage;
use repository\RepositoryDataManager;
use repository\ComplexContentObjectItem;
use user\UserDataManager;
/**
 * $Id: survey.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey
 */
/**
 * This class represents an survey
 */

class Survey extends ContentObject implements ComplexContentObjectSupport
{
    const PROPERTY_HEADER = 'header';
    const PROPERTY_FOOTER = 'footer';
    const PROPERTY_FINISH_TEXT = 'finish_text';
    const PROPERTY_CONTEXT_TEMPLATE_ID = 'context_template_id';
    const CLASS_NAME = __CLASS__;
    const MANAGER_CONTEXT = 'context';
    
    private $context;
    private $invitee_id;
    private $context_paths;
    private $survey_pages;
    private $page_context_paths;
    private $context_objects;

    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_HEADER, self :: PROPERTY_FOOTER, self :: PROPERTY_FINISH_TEXT, 
                self :: PROPERTY_CONTEXT_TEMPLATE_ID);
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
        $template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($this->get_context_template_id());
        return empty($template) ? '' : $template->get_name();
    
    }

    function get_context_template_for_level($level = 1)
    {
        
        if (! $this->get_context_template_id() == 0)
        {
            $context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($this->get_context_template_id());
        	            
            $level_count = $this->count_levels();
            $index = 1;
            while($level_count !=0){
            	$level_count--;
            	if($index ==1){
            		$level_matrix[$index] = $context_template;
            	}else{
            		$condition = new EqualityCondition(SurveyContextTemplate::PROPERTY_PARENT_ID, $context_template->get_parent_id());
            		$context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_templates($condition)->next_result();
            		$level_matrix[$index] = $context_template;
            	}
            	$index++;
            }
                    
            return $level_matrix[$level];
            
//            if ($level != 1)
//            {
//                
//                $index = 1;
//                $level_matrix[$index] = $context_template;
//                $context_template_children = $context_template->get_children(true);
//                while ($child_template = $context_template_children->next_result())
//                {
//                    $index ++;
//                    $level_matrix[$index] = $child_template;
//                }
//                dump($level_matrix);
//                return $level_matrix[$level];
//            }
//            
//            return $context_template;
        }
        else
        {
            return null;
        }
    
    }

    function get_context_template($parent_id = 0)
    {
        
        if (! $this->get_context_template_id() == 0)
        {
            if ($parent_id == 0)
            {
                $context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($this->get_context_template_id());
            }
            else
            {
                $condition = new EqualityCondition(SurveyContextTemplate :: PROPERTY_PARENT_ID, $parent_id);
                $context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_templates($condition)->next_result();
                if (! $context_template)
                {
                    $context_template = null;
                }
            }
            
            //        	if ($level != 1)
            //            {
            //                
            //                $index = 1;
            //                $level_matrix[$index] = $context_template;
            //                $context_template_children = $context_template->get_children(true);
            //                while ($child_template = $context_template_children->next_result())
            //                {
            //                    
            //                	$index ++;
            //                    $level_matrix[$index] = $child_template;
            //                }
            //                dump($level_matrix);
            //                return $level_matrix[$level];
            //            }
            

            return $context_template;
        }
        else
        {
            return null;
        }
    
    }

    function count_levels()
    {
        if (! $this->has_context())
        {
            return 0;
        }
        else
        {
            return $this->get_context_template()->count_children(true) + 1;
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

    function initialize($invitee_id)
    {
        $this->invitee_id = $invitee_id;
        $this->create_context_path();
    }

    function get_invitee_id()
    {
        return $this->invitee_id;
    }

    function get_complex_questions()
    {
        $pages = $this->get_pages();
        $questions = array();
        foreach ($pages as $page)
        {
            $complex_questions = $page->get_questions(true);
            while ($complex_question = $complex_questions->next_result())
            {
                $questions[$complex_question->get_id()] = $complex_question;
            }
        
        }
        return $questions;
    }

    function get_complex_questions_for_context_template_ids($context_template_ids)
    {
        if (! empty($context_template_ids))
        {
            if (! is_array($context_template_ids))
            {
                $context_template_ids = array($context_template_ids);
            }
        }
        
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $this->get_id());
        $conditions[] = new InCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $context_template_ids);
        $condition = new AndCondition($conditions);
        $survey_context_rel_pages = SurveyContextDataManager :: get_instance()->retrieve_template_rel_pages($condition);
        $pages = array();
        while ($survey_context_rel_page = $survey_context_rel_pages->next_result())
        {
            $pages[] = $survey_context_rel_page->get_page();
        }
        $questions = array();
        foreach ($pages as $page)
        {
            $complex_questions = $page->get_questions(true);
            while ($complex_question = $complex_questions->next_result())
            {
                $questions[$complex_question->get_id()] = $complex_question;
            }
        
        }
        return $questions;
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

    function count_pages()
    {
        if (! $this->page_context_paths)
        {
            $this->create_context_path();
        }
        //        dump($this->page_context_paths);
        return count($this->page_context_paths);
    }

    function get_page_complex_questions($context_path)
    {
        //        dump($context_path);
        $questions = array();
        if ($this->has_context())
        {
            $path_ids = explode('|', $context_path);
            $ids = explode('_', $path_ids[2]);
            $page_id = $ids[0];
        
     //            dump($page_id);
        }
        else
        {
            $ids = explode('_', $context_path);
            $count = count($ids);
            $page_id = $ids[$count - 1];
        }
        
        $page = $this->get_page_by_id($page_id);
        
        $complex_questions = $page->get_questions(true);
        while ($complex_question = $complex_questions->next_result())
        {
            $questions[] = $complex_question;
        }
        return $questions;
    }

    public function get_context_paths()
    {
        //        if ($tree)
        //        {
        //            if (! isset($this->context_paths))
        //            {
        //                $this->create_context_path();
        //            }
        //            return $this->context_paths;
        //        }
        //        else
        //        {
        

        if (! isset($this->context_paths))
        {
            $this->create_context_path();
        }
        return $this->context_paths;
    
     //        }
    }

    public function get_page_context_paths()
    {
        if (! isset($this->page_context_paths))
        {
            //            dump('hi');
            $this->create_context_path();
        }
        //        dump($this->context_paths);
        //        exit;
        $paths = array_keys($this->page_context_paths);
        return array_reverse($paths);
    }

    public function get_survey_page($page_context_path)
    {
        if (! isset($this->survey_pages))
        {
            $this->create_context_path();
        }
        //        dump($page_context_path);
        //        dump(array_keys($this->survey_pages));
        return $this->survey_pages[$page_context_path];
    }

    private function create_context_path()
    {
        
        //        $context_path_tree = array();
        

        if ($this->has_context())
        {
            $context_template = $this->get_context_template();
            $context_parent_id = $context_template->get_id();
            
            //			dump($this->invitee_id);
            $condition = new EqualityCondition(SurveyTemplateUser :: PROPERTY_USER_ID, $this->invitee_id, SurveyTemplateUser :: get_table_name());
            $template_users = SurveyContextDataManager :: get_instance()->retrieve_survey_template_users($context_template->get_type(), $condition);
            
            $template_count = 0;
            
            $question_nr = 0;
            $subindex = 0;
            
            $page_nr = 0;
            
            while ($template_user = $template_users->next_result())
            {
                $template_count ++;
                //                dump('template_count: ' . $template_count);
                //                dump($template_user);
                $level = 1;
                $property_names = $template_user->get_additional_property_names(true);
                //                                dump($property_names);
                $parent_id = 0;
                $context_path = 0;
                foreach ($property_names as $property_name => $context_type)
                {
                    
                    //                    dump('property_name: ' . $property_name);
                    //                    dump('level: ' . $level);
                    //                                       dump('template_id: '.$this->get_context_template($level)->get_id());
                    

                    if ($level != 1)
                    {
                        $context_template_id = $this->get_context_template($context_parent_id)->get_id();
                        //                        $context_tmpl = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($context_template_id);
                        $context_parent_id = $context_template_id;
                    
     //                        dump($context_tmpl);
                    //                        dump('context_parent_id: ' . $context_parent_id);
                    //                        dump('contexttemeplateid ' . $context_template_id);
                    }
                    else
                    {
                        $context_template_id = $context_template->get_id();
                        $context_parent_id = $context_template_id;
                    
     //						dump('level 1');
                    //                        dump($context_template);
                    //                        dump('context_parent_id: ' . $context_parent_id);
                    //                        dump('contexttemeplateid ' . $context_template_id);
                    }
                    
                    $conditions = array();
                    $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $this->get_id(), SurveyContextTemplateRelPage :: get_table_name());
                    $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $context_template_id, SurveyContextTemplateRelPage :: get_table_name());
                    $condition = new AndCondition($conditions);
                    $template_rel_pages = SurveyContextDataManager :: get_instance()->retrieve_template_rel_pages($condition);
                    
                    $context_id = $template_user->get_additional_property($property_name);
                    $context = SurveyContextDataManager :: get_instance()->retrieve_survey_context_by_id($context_id);
                    
                    //                    $this->context_paths[$level][$parent_id]['context_' . $context_id] = $context->get_name();
                    //                    $context_path_tree[$level][$parent_id][$context_id] = $context->get_name();
                    if ($parent_id == 0)
                    {
                        $path = $this->get_id() . '|' . $context_id . '|';
                        $context_path = $context_id;
                    
                    }
                    else
                    {
                        $context_path = $context_path . '_' . $context_id;
                        $path = $this->get_id() . '|' . $context_path . '|';
                    
                    }
                    
                    $count = 0;
                    
                    //                    dump('pathfor level: ' . $level . ' path ' . $path);
                    

                    while ($template_rel_page = $template_rel_pages->next_result())
                    {
                        $page_path = $path;
                        
                        $count ++;
                        //                                              dump('pagecount: ' . $count);
                        //                        dump('level: ' . $level);
                        //                        dump($template_rel_page);
                        $pages_ids = array();
                        $survey_page = $this->get_page_by_id($template_rel_page->get_page_id());
                        $page_id = $survey_page->get_id();
                        $pages_ids[] = $page_id;
                        //                        $pages_ids['page_' . $page_id] = $survey_page->get_title();
                        //                        dump($pages_ids);
                        $page_path = $page_path . $page_id;
                        //                        dump('pagepath: ' . $path);
                        $page_nr ++;
                        if (! array_key_exists($page_path, $this->page_context_paths))
                        {
//                            dump($page_path);
                        	$this->page_context_paths[$page_path] = $page_nr;
                        }
                        
                        $this->survey_pages[$page_path] = $survey_page;
                        
                        $complex_questions = $survey_page->get_questions(true);
                        $questions_ids = array();
                        while ($complex_question = $complex_questions->next_result())
                        {
                            //                            dump($complex_question);
//                            if (! $complex_question instanceof ComplexSurveyDescription)
//                            {
                                if ($complex_question->is_visible())
                                {
                                    $question_id = $complex_question->get_id();
                                    //                                    dump('visible: ' . $question_id);
                                    //                                    $questions_ids[] = $question_id;
                                    //                                    $questions_ids['question_' . $question_id] = 'vraag ' . $question_id;
                                    //                                    dump('path: ' . $path);
                                    $question_path = $page_path . '_' . $question_id;
                                    $this->context_paths[] = $question_path;
                                    // 
                                    if (! array_key_exists($question_path, $this->question_context_paths))
                                    {
                                        $question_nr ++;
                                        $subindex = 0;
                                        //                                        dump('question_nr: ' . $question_nr);
                                        $this->question_context_paths[$question_path] = $question_nr;
                                    }
                                
                                }
                                else
                                {
                                    $question_id = $complex_question->get_id();
                                    //                                    dump('invisible: ' . $question_id);
                                    //                                    $questions_ids[] = $question_id;
                                    //                                    $questions_ids['question_' . $question_id] = 'vraag ' . $question_id;
                                    //                                    dump('path: ' . $path);
                                    $question_path = $page_path . '_' . $question_id;
                                    $this->context_paths[] = $question_path;
                                    
                                    if (! array_key_exists($question_path, $this->question_context_paths))
                                    {
                                        $subindex ++;
                                        //                                        dump('question_nr: ' . $question_nr);
                                        $this->question_context_paths[$question_path] = $question_nr . '.' . $subindex;
                                    }
                                }
//                            }
                        }
                        $pages_ids[$page_id] = $questions_ids;
                    
     //                        dump($page_ids);
                    //                        $context_path_tree[$level][$parent_id][$context_id][$page_id] = $pages_ids;
                    }
                    
                    //                                    dump($context_path_tree);
                    //                    				dump($level);
                    

                    //                  old bug ?  $context_path_tree[$level][$parent_id][$context_id] = $pages_ids;
                    

                    $parent_id = $context_id;
                    
                    $level ++;
                
     //                    dump('parent_id: ' . $parent_id);
                //                    dump('newlevel: ' . $level);
                }
            }
            //            $this->page_context_paths = array_reverse($this->page_context_paths, true);
            //                  dump('pagecontesxtpaths');
            //              dump($this->page_context_paths);
            $this->context_paths = array_unique($this->context_paths);
            sort($this->context_paths, SORT_STRING);
          
//        	dump($this->page_context_paths);
            $page_cp_keys = array_keys($this->page_context_paths);
//            dump($page_cp_keys);
            sort($page_cp_keys, SORT_STRING);
//            dump($page_cp_keys);
            $index = 1;
            $page_cps = array();
            foreach ($page_cp_keys as $page_key){
            	$page_cps[$page_key] = $index;
            	$index++;
            }
            $this->page_context_paths = $page_cps;
            
//            dump($this->question_context_paths); 
//            $question_cp_keys = array_keys($this->question_context_paths);
//            dump($question_cp_keys);
//            sort($question_cp_keys, SORT_STRING);
//            dump($question_cp_keys);
//            $index = 1;
//            $question_cps = array();
//            foreach ($question_cp_keys as $question_key){
//            	$question_cps[$question_key] = $index;
//            	$index++;
//            }
//            $this->question_context_paths = $question_cps;
//                dump($this->question_context_paths);
//            exit;
//            dump($this->context_paths);
//            dump($this->page_context_paths);
//        	exit;
     //             dump($this->test_context_paths);
        //		exit;
        }
        else
        {
            $pages = $this->get_pages();
            
            $path = $this->get_id();
            $page_nr = 0;
            $question_nr = 0;
            //            dump($page_ids);
            foreach ($pages as $page)
            {
                
                $page_ids = array();
                $page_id = $page->get_id();
                //                $page_ids[] = $page_id;
                //                $page_ids['page_' . $page_id] = $page->get_title();
                //              
                $page_path = $path . '_' . $page_id;
                
                $page_nr ++;
                if (! array_key_exists($page_path, $this->page_context_paths))
                {
                    $this->page_context_paths[$page_path] = $page_nr;
                }
                
                $this->survey_pages[$page_path] = $page;
                
                $complex_questions = $page->get_questions(true);
                $questions_ids = array();
                while ($complex_question = $complex_questions->next_result())
                {
//                    if (! $complex_question instanceof ComplexSurveyDescription)
//                    {
                        if ($complex_question->is_visible())
                        {
                            $question_id = $complex_question->get_id();
                            //                                    dump('visible: ' . $question_id);
                            //                                    $questions_ids[] = $question_id;
                            //                                    $questions_ids['question_' . $question_id] = 'vraag ' . $question_id;
                            //                                    dump('path: ' . $path);
                            $question_path = $page_path . '_' . $question_id;
                            $this->context_paths[] = $question_path;
                            // 
                            if (! array_key_exists($question_path, $this->question_context_paths))
                            {
                                $question_nr ++;
                                $subindex = 0;
                                //                                        dump('question_nr: ' . $question_nr);
                                $this->question_context_paths[$question_path] = $question_nr;
                            }
                        
                        }
                        else
                        {
                            $question_id = $complex_question->get_id();
                            //                                    dump('invisible: ' . $question_id);
                            //                                    $questions_ids[] = $question_id;
                            //                                    $questions_ids['question_' . $question_id] = 'vraag ' . $question_id;
                            //                                    dump('path: ' . $path);
                            $question_path = $page_path . '_' . $question_id;
                            $this->context_paths[] = $question_path;
                            
                            if (! array_key_exists($question_path, $this->question_context_paths))
                            {
                                $subindex ++;
                                //                                        dump('question_nr: ' . $question_nr);
                                $this->question_context_paths[$question_path] = $question_nr . '.' . $subindex;
                            }
                        }
//                    }
                }
            
     //                        dump($questions_ids);
            //                $page_ids[$page_id] = $questions_ids;
            //                $context_path_tree[1][0][1][$page_id] = $page_ids;
            

            //                dump($page_ids);
            }
        
     //            $context_path_tree[1][0]['context_' . 1] = 'NOCONTEXT';
        

        }
        
        //        exit;
        //        dump($this->test_context_paths);
        //        exit;
        return $this->context_paths;
    
     //        dump($context_path_tree);
    //        exit;
    //        $this->context_path_tree = $context_path_tree;
    }

    static function parse($user_id, $context_path, $value)
    {
        
        
//    	dump('contextpath '.$context_path);
//        dump('value '.$value);
    	
//    	$context_objects = self->get_context_objects($context_path);
    		$context_objects = array();
            
            $user = UserDataManager :: get_instance()->retrieve_user($user_id);
            $context_objects['user'] = $user;
//            $level_count = $this->count_levels();
            $context_ids = explode('|', $context_path);
//         dump('contextids');
//   dump($context_ids);
//   dump(count($context_ids));         
   if(count($context_ids) > 1){
    $ids = explode('_', $context_ids[1]);
//            dump($level_count);
//            dump($ids);
            $count = count($ids);
            $level_count = $count;
//              dump($level_count);
            if ($count > 0)
            {
                $index = 0;
                while ($index < $count)
                {
                    $context_id = $ids[$index];
//                	dump($context_id);
                    $context = SurveyContextDataManager :: get_instance()->retrieve_survey_context_by_id($context_id);
                	$context_objects[$level_count-$index] = $context;
                    $index ++;
                }
            }
   }
  
    	
    	
//    	dump($context_objects);
//    	exit;
        $explode = explode('$V{', $value);
//        dump('explode');
//        dump($explode);
//        exit;
        $new_value = array();
        foreach ($explode as $part)
        {
            
            $vars = explode('}', $part);
//            dump($vars);
            if (count($vars) == 1)
            {
                $new_value[] = $vars[0];
            }
            else
            {
                $var = $vars[0];
//                dump('var '.$var);
                $level =1;
                foreach ($context_objects as $index => $context_object)
                {
                    if ($index != 'user')
                    {
//                        dump($context_object);
                    	$replace = $context_object->get_additional_property($var);
//                    	dump('replace '.$replace);
                    }
                    else
                    {
                        $replace = $context_object->get_default_property($var);
                    }
                    if(isset($replace)){
                    	break;
                    }
                }
                
                $new_value[] = $replace . ' ' . $vars[1];
            }
//        dump('inbetween nv '.implode(' ', $new_value));
        }
//        dump('newvalue '.implode(' ', $new_value));
//        exit;
        return implode(' ', $new_value);
    
    }

    private function get_context_objects($context_path)
    {
        
//        if ($this->context_objects)
//        {
//            return $this->context_objects;
//        }
//        else
//        {
//		dump($context_path);
    	$this->context_objects = array();
            
            $user = UserDataManager :: get_instance()->retrieve_user($this->invitee_id);
            $this->context_objects['user'] = $user;
            $level_count = $this->count_levels();
            $context_ids = explode('|', $context_path);
//            dump('contextids');
//            dump($context_ids);
            $ids = explode('_', $context_ids[1]);
//            dump($level_count);
//            dump($ids);
            $count = count($ids);
            if ($count > 1)
            {
                $index = 0;
                while ($index < $level_count)
                {
                    $context_id = $ids[$index];
//                	dump($context_id);
                    $context = SurveyContextDataManager :: get_instance()->retrieve_survey_context_by_id($context_id);
                	$this->context_objects[$level_count-$index] = $context;
                    $index ++;
                }
            }
            return $this->context_objects;
//        }
    }

    function get_question_nr($question_context_path)
    {
        //        dump($this->question_context_paths);
        return $this->question_context_paths[$question_context_path];
    }

    static function get_managers()
    {
        $managers = array();
        $managers[] = self :: MANAGER_CONTEXT;
        return $managers;
    }

    //old not static way
//   function parse($context_path, $value)
//    {
//        
//        
////    	dump('contextpath '.$context_path);
////        dump('value '.$value);
//    	
//    	$context_objects = $this->get_context_objects($context_path);
//        
//    	
////    	dump($context_objects);
//    	
//        $explode = explode('$V{', $value);
////        dump('explode');
////        dump($explode);
//        $new_value = array();
//        foreach ($explode as $part)
//        {
//            
//            $vars = explode('}', $part);
////            dump($vars);
//            if (count($vars) == 1)
//            {
//                $new_value[] = $vars[0];
//            }
//            else
//            {
//                $var = $vars[0];
////                dump('var '.$var);
//                $level =1;
//                foreach ($context_objects as $index => $context_object)
//                {
//                    if ($index != 'user')
//                    {
////                        dump($context_object);
//                    	$replace = $context_object->get_additional_property($var);
////                    	dump('replace '.$replace);
//                    }
//                    else
//                    {
//                        $replace = $context_object->get_default_property($var);
//                    }
//                    if(isset($replace)){
//                    	break;
//                    }
//                }
//                
//                $new_value[] = $replace . ' ' . $vars[1];
//            }
////        dump('inbetween nv '.implode(' ', $new_value));
//        }
////        dump('newvalue '.implode(' ', $new_value));
//        return implode(' ', $new_value);
//    
//    }
//
//    private function get_context_objects($context_path)
//    {
//        
////        if ($this->context_objects)
////        {
////            return $this->context_objects;
////        }
////        else
////        {
////		dump($context_path);
//    	$this->context_objects = array();
//            
//            $user = UserDataManager :: get_instance()->retrieve_user($this->invitee_id);
//            $this->context_objects['user'] = $user;
//            $level_count = $this->count_levels();
//            $context_ids = explode('|', $context_path);
////            dump('contextids');
////            dump($context_ids);
//            $ids = explode('_', $context_ids[1]);
////            dump($level_count);
////            dump($ids);
//            $count = count($ids);
//            if ($count > 1)
//            {
//                $index = 0;
//                while ($index < $level_count)
//                {
//                    $context_id = $ids[$index];
////                	dump($context_id);
//                    $context = SurveyContextDataManager :: get_instance()->retrieve_survey_context_by_id($context_id);
//                	$this->context_objects[$level_count-$index] = $context;
//                    $index ++;
//                }
//            }
//            return $this->context_objects;
////        }
//    }
//
//    function get_question_nr($question_context_path)
//    {
//        //        dump($this->question_context_paths);
//        return $this->question_context_paths[$question_context_path];
//    }
//
//    static function get_managers()
//    {
//        $managers = array();
//        $managers[] = self :: MANAGER_CONTEXT;
//        return $managers;
//    }
}
?>