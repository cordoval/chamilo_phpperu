<?php

require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';

class SurveyMenu extends HTML_Menu
{
    const TREE_NAME = __CLASS__;
    
    private $urlFmt;
    
    private $survey_id;
    
    /**
     * @var Survey
     */
    private $survey;
    
    private $user_id;
    
    private $publication_id;
    
    private $menu_matrix;
    
    private $context_path_menus;
    
    private $context_path_relations;
    
    private $parent_ids;

    function SurveyMenu($parent, $current_context_path, $url_format, $survey)
    {
        $this->survey = $survey;
        $this->survey_id = $this->survey->get_id();
        
        $this->user_id = $parent->get_parent()->get_parameter(SurveyViewerWizard :: PARAM_INVITEE_ID);
        //        $this->menu_matrix = $this->survey->get_context_paths(true);
        //        dump($this->menu_matrix);
        

        $this->publication_id = $parent->get_parent()->get_parameter(SurveyViewerWizard :: PARAM_PUBLICATION_ID);
        
        $this->urlFmt = $url_format;
        $menu = $this->get_menu();
        
        //        dump($menu);
        //        exit();
        

        parent :: __construct($menu);
        $this->forceCurrentUrl($this->get_url($current_context_path));
    }

    function get_menu()
    {
        $this->create_context_path_relations();
        
        //        $menu = array();
        //        $menu_item['title'] = $this->survey->get_title();
        //        
        //        $path = $this->survey->get_id();
        //        
        //        $menu_item['url'] = $this->get_url($path);
        //        
        //        //        $menu_sub_items = array();
        //        
        //
        //        $menu_item['class'] = 'survey';
        //        $menu_item[OptionsMenuRenderer :: KEY_ID] = $path;
        //        $menu_sub_items = $this->get_menu_items(1, $this->survey->get_id());
        //        if (count($menu_sub_items))
        //        {
        //            $menu_item['sub'] = $menu_sub_items;
        //        }
        //        $menu[] = $menu_item;
        

        $menu = $this->get_menu_items();
        
        return $menu;
    }

    function create_context_path_relations()
    {
        $context_paths = $this->survey->get_context_paths();
        //        dump('context paths');
        //        dump($context_paths);
        

        $level_count = $this->survey->count_levels();
        //        dump($level_count);
        $levels = $level_count + 3;
        
        $this->context_path_relations = array();
        
        $level = $levels;
        //        while ($level > 0)
        //        {
        //            dump('level');
        //            dump($level);
        foreach ($context_paths as $index => $context_path)
        {
            $path_ids = explode('_', $context_path);
            //            dump('first path ids');
            //            dump($path_ids);
            $id_count = count($path_ids);
            
            //questions
            $context = array();
            
            $question_id = $path_ids[$id_count - 1];
            $context['question_id'] = $question_id;
            
            array_pop($path_ids);
            
            $question_parent_context = implode('_', $path_ids);
            
            $context['parent_id'] = $question_parent_context;
            $context['id'] = $context_path;
            $this->context_path_relations[$context_path] = $context;
            
            //pages
            $context = array();
            
            $page_id = $path_ids[$id_count - 2];
            //			dump('page id');
            //            dump($path_ids);
            $context['page_id'] = $page_id;
            
            array_pop($path_ids);
            
            $page_parent_context = implode('_', $path_ids);
            
            $context['parent_id'] = $page_parent_context;
            $context['id'] = $question_parent_context;
            $this->context_path_relations[$question_parent_context] = $context;
            
            $parent_ids[] = $page_parent_context;
        
        }
        
        //            }
        //            dump($context_paths);
        //            $level --;
        //        }
        //                dump('relatuins');
        //        dump($this->context_path_relations);
        

        $parent_ids = array_unique($parent_ids);
        
        //        dump('parent_ids');
        //        dump($parent_ids);
        //        exit();
        

        //        dump('parent_count');
        //        dump(count($parent_ids));
        

        //        $context_parent_paths = array();
        

        $levels = $level_count + 1;
        
        //        dump('startlevels');
        //        dump($levels);
        $level = $levels;
        while ($level >= 1)
        {
            //            dump('in level');
            //            dump($level);
            

            foreach ($parent_ids as $index => $context_path)
            {
                $path_ids = explode('_', $context_path);
                $id_count = count($path_ids);
                if ($id_count == $level)
                {
                    //                    dump('idcount');
                    //                    dump($id_count);
                    $context = array();
                    
                    //                    $id = $path_ids[$id_count - 1];
                    $id = $context_path;
                    $context['id'] = $id;
                    $context['context_id'] = $path_ids[$id_count - 1];
                    
                    $context['level'] = $level;
                    
                    if ($level == 1)
                    {
                        $parent_id = 0;
                    }
                    else
                    {
                        array_pop($path_ids);
                        $parent_id = implode('_', $path_ids);
                        $parent_ids[] = $parent_id;
                    }
                    
                    $context['parent_id'] = $parent_id;
                    $context_parent_paths[$context_path] = $context;
                    $this->context_path_relations[$context_path] = $context;
                    //                $context_parent_paths[$level.'_'.$context_id] = $context;
                    //                            if ($level <= 4)
                    //                            {
                    //                                $parent_ids[] = $parent_id;
                    //                            }
                    

                    unset($parent_ids[$index]);
                
                }
            
            }
            //            dump('context + count na ' . $level . ' skip !');
            //            dump($parent_ids);
            //            dump(count($parent_ids));
            $level --;
        }
        
    //        dump('after level lus');
    //        dump($parent_ids);
    //        dump(count($parent_ids));
    //        
    //
    //        dump($this->context_path_menus);
    //        
    //        dump(count($this->context_path_menus));
    //        exit;
    }

    /**
     * Returns the menu items.
     * @return array An array with all menu items. The structure of this array
     * is the structure needed by PEAR::HTML_Menu, on which this
     * class is based.
     */
    private function get_menu_items($level = 1, $parent_id = 0, $path = null)
    {
        
        $menu = array();
        
        //        dump($level);
        

        //        dump('before parent_id');
        //        dump($parent_id);
        $level_count = $this->survey->count_levels();
        
        foreach ($this->context_path_relations as $context_path => $context_path_relation)
        {
            
//            if ($context_path_relation['parent_id'] == $parent_id && $context_path_relation['level'] == $level)
                if ($context_path_relation['parent_id'] == $parent_id)
                
                {
                    
                    //                dump('in relation');
                    //                dump($context_path_relation['parent_id']);
                    //                dump($context_path_relation['id']);
                    

                    $path_ids = explode('_', $context_path);
                    $id_count = count($path_ids);
                    
                    //                if ($level == 1)
                    //                {
                    //                    $title = $this->survey->get_title();
                    //                }
                    //                elseif ($level > 1 && $level <= $level_count)
                    //                {
                    //                    $context_id = $path_ids[$id_count - 1];
                    //                    dump($context_id);
                    //                    //                	$context = SurveyContextDataManager::get_instance()->retrieve_survey_context_by_id($context_id);
                    //                    //                	$title = $context->get_name();
                    //                    $title = 'context';
                    //                }
                    //                elseif ($level == $level_count + 1)
                    //                {
                    //                    
                    //                    $page_id = $path_ids[$id_count - 1];
                    //                    //                	$survey_page = $this->survey->get_page_by_id($page_id);
                    //                    $title = 'page';
                    //                    //                	$title = $survey_page->get_title();
                    //                
                    //
                    //                }
                    //                else
                    //                {
                    //                    $complex_question_id = $path_ids[$id_count - 1];
                    //                    $complex_question = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_question_id);
                    //                    $question = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_question->get_ref());
                    //                    $title = $question->get_title();
                    //                }
                    

                    $menu_item['title'] = $context_path;
                    $menu_item['url'] = $this->get_url($context_path);
                    
                    //                                unset($this->context_path_relations[$context_path]);
                    

                    //                dump('count after unset for parent');
                    //                dump(count($this->context_path_relations));
                    

                    $sub_menu_items = $this->get_menu_items($level + 1, $context_path_relation['id']);
                    //                dump('submenuitems');
                    //                dump($sub_menu_items);
                    //                dump($sub_menu_items);
                    

                    if (count($sub_menu_items))
                    {
                        $menu_item['sub'] = $sub_menu_items;
                    }
                    
                    $menu_item['class'] = 'survey';
                    $menu_item[OptionsMenuRenderer :: KEY_ID] = $context_path;
                    $menu[$context_path] = $menu_item;
                
                }
                else
                {
                    continue;
                    //                dump('no relation');
                //                dump($context_path_relation['parent_id']);
                //                dump($context_path_relation['id']);
                }
        }
        
        return $menu;
    }

    function get_url($context_path)
    {
        $test = sprintf($this->urlFmt, $this->publication_id, $this->survey_id, $this->user_id, $context_path);
        $test = $test . '&display_action=survey_viewer&_qf_page_' . $context_path . '_display=true';
        return htmlentities($test);
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

    static function get_tree_name()
    {
        return Utilities :: camelcase_to_underscores(self :: TREE_NAME);
    }
}