<?php
namespace repository\content_object\survey;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\EqualityCondition;
use common\libraries\ObjectTableOrder;
use common\libraries\OptionsMenuRenderer;
use common\libraries\TreeMenuRenderer;
use common\libraries\Translation;
use common\libraries\Theme;
use repository\content_object\survey_page\SurveyPage;
use repository\content_object\survey_description\ComplexSurveyDescription;

use repository\RepositoryDataManager;

use HTML_Menu;
use HTML_Menu_ArrayRenderer;

class SurveyMenu extends HTML_Menu
{
    const TREE_NAME = __CLASS__;
    
    const MENU_ITEM_TYPE = 'type';
    const TYPE_SURVYEY = 'survey';
    const TYPE_CONTEXT = 'survey_context';
    const TYPE_PAGE = 'survey_page';
    const TYPE_QUESTION = 'survey_question';
    
    const ID = 'id';
    const PARENT_ID = 'parent_id';
    const CONTEXT_ID = 'context_id';
    const PAGE_ID = 'page_id';
    const QUESTION_ID = 'question_id';
    
    private $urlFmt;
    
    /**
     * @var Survey
     */
    private $survey;
    
    private $survey_id;
    
    private $user_id;
    
    private $publication_id;
    
    private $context_path_relations;
    
    private $parent_ids;
    
    private $page_contexts;
    
    private $questions_contexts;
    
    private $finished_questions;
    
    private $question_count;
    
    private $visible_question_count;
    
    private $parent;
    
    private $page_nrs;
    
    private $page_nr = 1;
    
    private $new_context_id;
    private $first_page_url = false;
    private $context_urls;

    function __construct($parent, $current_context_path, $url_format, $survey)
    {
        $this->parent = $parent;
        $this->survey = $survey;
        $this->survey_id = $this->survey->get_id();
        
        $this->user_id = $parent->get_parameter(SurveyDisplaySurveyViewerComponent :: PARAM_INVITEE_ID);
        $this->publication_id = $parent->get_parameter(SurveyDisplaySurveyViewerComponent :: PARAM_PUBLICATION_ID);
        
        $this->urlFmt = $url_format;
        $menu = $this->get_menu();
        //        dump('context_path: '.$current_context_path);
        parent :: __construct($menu);
        $this->forceCurrentUrl($this->get_url($current_context_path));
    }

    function get_menu()
    {
        $this->create_context_path_relations();
        $this->visible_question_count = 0;
        return $this->get_menu_items();
    }

    function create_context_path_relations()
    {
        $context_paths = $this->survey->get_context_paths();
        
        //        $context_paths = array_reverse($context_paths);
        

        //            dump($context_paths);
        

        $this->context_path_relations = array();
        
        $has_context = $this->survey->has_context();
        
        $page_count = 0;
        
        foreach ($context_paths as $index => $context_path)
        {
            
            if ($has_context)
            {
                $context_path_ids = explode('|', $context_path);
                $parent_path = $context_path_ids[0] . '|' . $context_path_ids[1];
                $path_ids = explode('_', $context_path_ids[2]);
            }
            else
            {
                $path_ids = explode('_', $context_path);
            }
            
            $id_count = count($path_ids);
            //            dump($parent_path);
            //            dump($id_count);
            //questions
            $context = array();
            $question_id = $path_ids[$id_count - 1];
            $context[self :: QUESTION_ID] = $question_id;
            
            array_pop($path_ids);
            if ($has_context)
            {
                $question_parent_context = $parent_path . '|' . $path_ids[0];
            }
            else
            {
                $question_parent_context = implode('_', $path_ids);
            }
            
            //            dump('question_parent_context '.$question_parent_context);
            //            exit;
            

            $context[self :: PARENT_ID] = $question_parent_context;
            $context[self :: ID] = $context_path;
            $context[self :: MENU_ITEM_TYPE] = self :: TYPE_QUESTION;
            $this->context_path_relations[$context_path] = $context;
            if (! $this->questions_contexts[$context_path])
            {
                $this->question_count ++;
                $this->questions_contexts[$question_parent_context] = $this->question_count;
            }
            
            //pages
            $context = array();
            $page_id = $path_ids[$id_count - 2];
            $context[self :: PAGE_ID] = $page_id;
            
            array_pop($path_ids);
            if ($has_context)
            {
                $page_parent_context = $parent_path;
            }
            else
            {
                $page_parent_context = implode('_', $path_ids);
            }
            
            $context[self :: PARENT_ID] = $page_parent_context;
            $context[self :: ID] = $question_parent_context;
            $context[self :: MENU_ITEM_TYPE] = self :: TYPE_PAGE;
            $this->context_path_relations[$question_parent_context] = $context;
            if (! $this->page_contexts[$question_parent_context])
            {
                $page_count ++;
                $this->page_contexts[$question_parent_context] = $page_count;
            }
            
            $parent_ids[] = $page_parent_context;
        
        }
        
        $parent_ids = array_unique($parent_ids);
        
        $level_count = $this->survey->count_levels();
        $levels = $level_count + 1;
        $level = $levels;
        while ($level >= 1)
        {
            foreach ($parent_ids as $index => $context_path)
            {
                
                if ($has_context)
                {
                    $context_path_ids = explode('|', $context_path);
                    $path_ids = explode('_', $context_path_ids[1]);
                    $id_count = count($path_ids) + 1;
                    $count = count($path_ids);
                
                }
                else
                {
                    $path_ids = explode('_', $context_path);
                    $id_count = count($path_ids);
                }
                
                if ($id_count == $level)
                {
                    $context = array();
                    $context[self :: ID] = $context_path;
                    if ($has_context)
                    {
                        $context[self :: CONTEXT_ID] = $path_ids[$count - 1];
                    }
                    else
                    {
                        $context[self :: CONTEXT_ID] = $path_ids[$id_count - 1];
                    }
                    
                    if ($level == 1)
                    {
                        $parent_id = 0;
                        $context[self :: MENU_ITEM_TYPE] = self :: TYPE_SURVYEY;
                    }
                    else
                    {
                        array_pop($path_ids);
                        if (count($path_ids) > 0)
                        {
                            if ($has_context)
                            {
                                $parent_id = $context_path_ids[0] . '|' . implode('_', $path_ids);
                            }
                            else
                            {
                                $parent_id = implode('_', $path_ids);
                            }
                        
                        }
                        else
                        {
                            if ($has_context)
                            {
                                $parent_id = $context_path_ids[0];
                            }
                            else
                            {
                                $parent_id = implode('_', $path_ids);
                            }
                        
                        }
                        
                        //
                        $parent_ids[] = $parent_id;
                        $context[self :: MENU_ITEM_TYPE] = self :: TYPE_CONTEXT;
                    }
                    
                    $context[self :: PARENT_ID] = $parent_id;
                    $this->context_path_relations[$context_path] = $context;
                    unset($parent_ids[$index]);
                }
            }
            $level --;
        }
        
        if ($has_context)
        {
            $context = array();
            $context[self :: ID] = $this->survey_id;
            $context[self :: CONTEXT_ID] = $this->survey_id;
            $context[self :: MENU_ITEM_TYPE] = self :: TYPE_SURVYEY;
            $context[self :: PARENT_ID] = 0;
            $this->context_path_relations[$this->survey_id] = $context;
        }
    
     //        dump($this->context_path_relations);
    //        $index = 0;
    //        foreach ($this->context_path_relations as $this->context_path_relation)
    //        {
    //        	if($this->context_path_relation['type'] = self :: TYPE_CONTEXT){
    //        		$index++;
    ////        		dump($this->context_path_relation);
    //        	}
    //        }
    //    	dump($index);
    //      exit();
    

    }

    /**
     * Returns the menu items.
     * @return array An array with all menu items. The structure of this array
     * is the structure needed by PEAR::HTML_Menu, on which this
     * class is based.
     */
    private function get_menu_items($parent_id = 0, $path = null, $current_url = null, $page_id)
    {
        
        $menu = array();
        $page_answers = array();
        
        $level_count = $this->survey->count_levels();
        if (! $current_url)
        {
            $current_url = $this->get_url($path);
        }
        
        //        dump($level_count);
        

        foreach ($this->context_path_relations as $context_path => $context_path_relation)
        {
            
            if ($context_path_relation[self :: PARENT_ID] == $parent_id)
            {
                $path_ids = explode('_', $context_path);
                $id_count = count($path_ids);
                
                $type = $context_path_relation[self :: MENU_ITEM_TYPE];
                if ($type == self :: TYPE_PAGE)
                {
                    $page_id = $context_path_relation[self :: PAGE_ID];
                    $this->page_nrs[$context_path] = $this->page_nr;
                    $this->page_nr ++;
                    unset($page_answers);
                }
                
                $menu_item = array();
                $visible = true;
                switch ($type)
                {
                    case self :: TYPE_SURVYEY :
                        $title = $this->survey->get_title();
                        $title = Survey :: parse($this->get_survey()->get_invitee_id(), $context_path, $title);
                        $menu_item['class'] = self :: TYPE_SURVYEY;
                        $menu_item['url'] = $current_url;
                        break;
                    case self :: TYPE_CONTEXT :
                        $context = SurveyContextDataManager :: get_instance()->retrieve_survey_context_by_id($context_path_relation[self :: CONTEXT_ID]);
                        $title = $context->get_name();
                        $menu_item['class'] = self :: TYPE_CONTEXT;
                        $this->new_context_id = $context->get_id();
                        if($this->first_page_url){
                        	
                        }
                        $this->first_page_url = true;
                        //                        dump('incontext');
                        $in_context = true;
                        //                        dump($context_path_relation);
                        //                        $menu_item['url'] = $this->context_urls[$context_path_relation[self :: CONTEXT_ID]];
                        break;
                    case self :: TYPE_PAGE :
                        $survey_page = $this->survey->get_page_by_id($context_path_relation[self :: PAGE_ID]);
                        $title = $survey_page->get_title();
                        //                       	$title = Survey :: parse($this->get_survey()->get_invitee_id(), $context_path, $title);
                        //                        $title = 'page ' . $this->page_contexts[$context_path] . ' title: ' . $title;
                        $menu_item['class'] = self :: TYPE_PAGE . '_tree';
                        $current_url = $this->get_url($context_path);
                        $menu_item['url'] = $current_url;
                        if ($this->first_page_url)
                        {
                            $this->first_page_url = false;
                            $in_firstpage = true;
                            $c_ids = explode('|', $context_path);
//                            dump($context_path_relation);
                            array_pop($c_ids);
                            $c_id = implode('|', $c_ids);
                            $c_parent_id = $context_path_relation[self :: PARENT_ID];
                            $this->context_urls[$c_parent_id] = $current_url;
                        	$p_c_ids = explode('|', $c_parent_id);
                            $path_c_ids = explode('_', $p_c_ids[1]);
//                            dump($c_ids);
                            array_pop($path_c_ids);
                            $p_p_c_id = $p_c_ids[0].'|'.implode('_', $path_c_ids);
                        	
                            $p_c_id = implode('|', $c_ids);
//                        	dump($p_c_id);
                        	if(!array_key_exists($p_p_c_id, $this->context_urls)){
//                        		dump('notexist');
                        		$this->context_urls[$p_p_c_id] = $current_url;
                        	}
//                        	dump($this->context_urls);
                        }
     //							dump('inpage');
                        //                            dump($this->new_context_id);
                        //                            dump($context_path);
                        //                            dump($menu);
                        

                        //                        	exit;
                        
                        break;
                    case self :: TYPE_QUESTION :
                        $complex_question_id = $context_path_relation[self :: QUESTION_ID];
                        $complex_question = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_question_id);
                        $answer = $this->parent->get_answer($complex_question_id, $context_path);
                        
                        $is_description = $complex_question instanceof ComplexSurveyDescription;
                        
                        if (! $complex_question->is_visible())
                        {
                            //                            dump($complex_question_id);
                            if (! $answer)
                            {
                                //                                dump('invisible');
                                if (! $this->is_question_visible($complex_question_id, $page_id, $page_answers))
                                {
                                    $visible = false;
                                }
                            }
                        }
                        
                        if ($answer)
                        {
                            $page_answers[$complex_question_id] = $answer;
                        }
                        
                        //                        if ($visible)
                        //                        {
                        //                            dump('visible');
                        //                            dump($complex_question_id);
                        $question = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_question->get_ref());
                        $title = $question->get_title();
                        $title = Survey :: parse($this->get_survey()->get_invitee_id(), $context_path, $title);
                        //                        $answer = $this->parent->get_answer($complex_question_id, $context_path);
                        if ($answer)
                        {
                            $menu_item['class'] = $question->get_type_name() . '_checked';
                            //                            $title = Theme :: get_common_image('status_ok_mini') . ' ' . $title;
                            $this->finished_questions[] = $context_path;
                        }
                        else
                        {
                            $menu_item['class'] = $question->get_type_name();
                        
                        }
                        $menu_item['url'] = $current_url . '#' . $complex_question_id;
                        
                        //                        }
                        

                        break;
                    default :
                        ;
                        break;
                }
                
                if ($visible)
                {
                    if ($type == self :: TYPE_QUESTION)
                    {
                        if (! $is_description)
                        {
                            $this->visible_question_count ++;
                        }
                    }
                    
                    $title = Utilities :: truncate_string($title, 50);
                    $menu_item['title'] = $title;
                    $menu_item[OptionsMenuRenderer :: KEY_ID] = $context_path;
                    
                    //                    $menu_item['title'] = $complex_question_id . " " . $title;
                    $sub_menu_items = $this->get_menu_items($context_path_relation[self :: ID], $context_path, $current_url, $page_id);
                    
                    if (count($sub_menu_items))
                    {
                        $menu_item['sub'] = $sub_menu_items;
                    }
                    
                    //                    dump($this->context_urls);
                    if ($in_context)
                    {
                        //                    	dump('incontextmenu');
                        //                    	dump($context_path_relation); 
                        if (! $this->context_urls[$context_path_relation[self :: ID]])
                        {
//                             dump($this->context_urls);
//                        	dump($menu_item);
                        
                        }
                        
                        $menu_item['url'] = $this->context_urls[$context_path_relation[self :: ID]];
                    
     //                    	dump($menu_item);
                    }
                    
                    //                    if($in_firstpage){
                    //                    	dump('infirstpagmenu'); 
                    //                    	dump($menu_item);
                    //                    }
                    //                    $menu_item[OptionsMenuRenderer :: KEY_ID] = $context_path;
                    $menu[$context_path] = $menu_item;
                }
            
            }
            else
            {
                continue;
            }
        }
        
        //        dump($this->page_contexts);
        

        // dump($menu);
        // exit;
        return $menu;
    }

    function get_url($context_path)
    {
        $test = sprintf($this->urlFmt, $this->publication_id, $this->survey_id, $context_path);
        //        $test = $test . '&display_action=survey_viewer&_qf_page_' . $context_path . '_display=true';
        $test = $test . '&display_action=survey_viewer';
        return htmlentities($test);
    }

    function get_progress()
    {
        //        dump(count($this->finished_questions));
        //        dump($this->question_count);
        //        dump($this->visible_question_count);
        return (count($this->finished_questions) / ($this->visible_question_count)) * 100;
    }

    function get_page_nr($page_context_path)
    {
        return $this->page_nrs[$page_context_path];
    }

    function get_page_nrs()
    {
        return $this->page_nrs;
    }

    function is_question_visible($guestion_id_tocheck, $page_id, $page_answers)
    {
        $survey_page = RepositoryDataManager :: get_instance()->retrieve_content_object($page_id);
        $configs = $survey_page->get_config();
        
        foreach ($configs as $config)
        {
            
            foreach ($page_answers as $question_id => $answer_to_match)
            {
                
                $from_question_id = $config[SurveyPage :: FROM_VISIBLE_QUESTION_ID];
                if ($question_id == $from_question_id)
                {
                    
                    $answer = $config[SurveyPage :: ANSWERMATCHES];
                    $answers_to_match = array();
                    foreach ($answer as $key => $value)
                    {
                        $oids = explode('_', $key);
                        if (count($oids) == 2)
                        {
                            $answers_to_match[] = $oids[1];
                        }
                        elseif (count($oids) == 3)
                        {
                            $option = $oids[1];
                            $answers_to_match[$option] = $value;
                        
                        }
                    }
                    
                    $diff = array_diff($answers_to_match, $answer_to_match);
                    if (count($diff) == 0)
                    {
                        if (in_array($guestion_id_tocheck, $config[SurveyPage :: TO_VISIBLE_QUESTIONS_IDS]))
                        {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Renders the menu as a tree
     * @return string The HTML formatted tree
     */
    function render_as_tree()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }

    function get_survey()
    {
        return $this->survey;
    }

    static function get_tree_name()
    {
        return Utilities :: get_classname_from_namespace(self :: TREE_NAME, true);
    }
}