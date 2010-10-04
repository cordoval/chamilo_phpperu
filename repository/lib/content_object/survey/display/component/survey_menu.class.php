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

    function SurveyMenu($parent, $current_context_path, $url_format, $survey)
    {
        $this->survey = $survey;
        $this->survey_id = $this->survey->get_id();
        
        $this->user_id = $parent->get_parent()->get_parameter(SurveyViewerWizard :: PARAM_INVITEE_ID);
        $this->menu_matrix = $this->survey->get_context_paths(true);
        dump($this->menu_matrix);
        
        $this->publication_id = $parent->get_parent()->get_parameter(SurveyViewerWizard :: PARAM_PUBLICATION_ID);
        
        $this->urlFmt = $url_format;
        $menu = $this->get_menu();
        parent :: __construct($menu);
        $this->forceCurrentUrl($this->get_url($current_context_path));
    }

    function get_menu()
    {
        
        //        $menu = $this->create_menu();
        

        $menu = $this->get_menu_items();
        //        dump(count($menu));
        dump($menu);
        //        dump($this->context_path_menus);
        

        //       $menu =   $this->merge_page_sub_menus($menu);
        

        exit();
        return $this->get_menu_items();
    }

    private function merge_page_sub_menus($menu)
    {
        
        $level_count = $this->survey->count_levels();
        
        for($level = 2; $level <= ($level_count + 2); $level ++)
        {
            foreach ($menu[$level][$parent_id] as $id => $pages)
            {
            
            }
        
        }
    }

    private function create_menu()
    {
        
        $context_paths = $this->survey->get_context_paths();
        dump($context_paths);
        $level_count = $this->survey->count_levels();
        
        for($level = 2; $level <= ($level_count + 2); $level ++)
        {
            foreach ($context_paths as $context_path)
            {
                $path_ids = explode('_', $context_path);
                $id_count = count($path_ids);
                if ($id_count == $level)
                {
                    array_pop($path_ids);
                    $path = implode('_', $path_ids);
                    $this->context_path_menus[$level][$path] = $context_path;
                }
            }
        }
        
        $menu = array();
        $count = $level_count + 2;
        dump($count);
        
        for($level = $count; $level >= 2; $level --)
        {
            dump('level');
            dump($level);
            dump('child array');
            dump($this->context_path_menus[$level]);
            foreach ($this->context_path_menus[$level] as $parent_path => $context_path_menu)
            {
                dump('parent_path');
                dump($parent_path);
                dump('parent array');
                dump($this->context_path_menus[$level - 1]);
                foreach ($this->context_path_menus[$level - 1] as $index => $context_path)
                {
                    if ($parent_path == $context_path)
                    {
                        dump('equelity contpath');
                        dump($context_path);
                        dump('index');
                        dump($index);
                        //						$this->context_path_menus[$level-1][$index] = array($context_path_menu);
                    }
                }
                
            //            	$path_ids = explode('_', $context_path);
            //               	array_pop($path_ids);
            //                $path = implode('_', $path_ids);
            //                
            //                $this->context_path_menus[$level][$path] = $context_path;
            //                
            //                //                    dump('level inside');
            //                //                    dump($level);
            //                //                    dump($context_path);
            //                //                    //                    $context_name = $this->menu_matrix[$level][$parent_id]['context_' . $id];
            //                //                    
            //                //
            //                ////                    dump('contextpathcount ' . count($path_ids));
            //                //                    dump('level: ' . $level);
            //                
            //
            //                $menu_item = array();
            //                $menu_item['title'] = 'test';
            //                $menu_item['url'] = $this->get_url($path);
            //                dump('level inside');
            //                dump($level);
            //                if ($level >= 3)
            //                {
            //                    //                        dump($path_ids);
            //                    
            //
            //                    //                                    	dump($parent_path);
            //                    //                    	dump($parent_path);                       
            //                    array_pop($path_ids);
            //                    //                                    	dump($path_ids);
            //                    $parent_path = implode('_', $path_ids);
            //                    
            //                    $menu_item['class'] = 'survey';
            //                    $menu_item[OptionsMenuRenderer :: KEY_ID] = $path;
            //                    //                        $menu[$context_path] = $menu_item;
            //                    dump('sublevelsitem');
            //                    dump($menu_item);
            //                    $menu[$parent_path]['sub'][] = $menu_item;
            //                
            //                }
            //                else
            //                {
            //                    $menu_item['class'] = 'survey';
            //                    $menu_item[OptionsMenuRenderer :: KEY_ID] = $path;
            //                    dump('menu item level: ' . $level);
            //                    dump($menu_item);
            //                    $menu[$path] = $menu_item;
            //                }
            

            //                    dump($menu_item);
            

            }
            //            dump('menu level: ' . $level);
            //            dump($menu);
            if ($level == 5)
            {
                //                                exit();
            }
        
        }
        
        return $menu;
    }

    private function create_page_sub_menu($path, $pages_context_as_tree)
    {
        
        $menu = array();
        $menu['title'] = 'test';
        $menu['url'] = $this->get_url($path);
        
        foreach ($pages_context_as_tree as $page_context_as_tree)
        {
            $page_id = $page_context_as_tree[0];
            $page_path = $path . '_' . $page_id;
            
            $survey_page = $this->survey->get_page_by_id($page_id);
            
            $page_menu_item = array();
            $page_menu_item['title'] = $survey_page->get_title();
            $page_menu_item['url'] = $this->get_url($path);
            
            //        $this->context_paths[] = $page_path;
            //        $this->page_context_paths[$page_path] = $this->page_nr;
            $this->page_nr ++;
            
            $questions = $page_context_as_tree[$page_id];
            $sub_index = 1;
            $sub_menu_items = array();
            foreach ($questions as $index => $question_id)
            {
                
                if (is_int($index))
                {
                    //                $this->context_paths[] = $page_path . '_' . $question_id;
                    $complex_question = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($question_id);
                    if (! $complex_question instanceof ComplexSurveyDescription)
                    {
                        if ($complex_question->is_visible())
                        {
                            $menu_item = array();
                            $menu_item['title'] = 'vraag: ' . $question_id;
                            $menu_item['url'] = $this->get_url($path);
                            $menu_item['class'] = 'survey';
                            $menu_item[OptionsMenuRenderer :: KEY_ID] = $path . '_' . $question_id;
                            $sub_menu_items[] = $menu_item;
                            $this->question_context_paths[$page_path . '_' . $question_id] = $this->question_nr;
                            $this->question_nr ++;
                            $sub_index = 1;
                        }
                        else
                        {
                            $this->question_context_paths[$page_path . '_' . $question_id] = $this->question_nr . '.' . $sub_index;
                            $sub_index ++;
                        }
                    }
                
                }
            
            }
            $page_menu_item['sub'] = $sub_menu_items;
            $page_menu_item['class'] = 'survey';
            $page_menu_item[OptionsMenuRenderer :: KEY_ID] = $path;
            $menu['sub'] = $page_menu_item;
        }
        $this->context_path_menus[$path] = $menu;
        return $menu;
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
        
        //        if ($level == 1)
        //        {
        //            $this->question_nr = 1;
        //        }
        

        foreach ($this->menu_matrix[$level][$parent_id] as $id => $pages)
        {
            if (is_int($id))
            {
                if ($level == 1)
                {
                    $path = null;
                    //                    $this->question_nr = 1;
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
                
                $context_name = $this->menu_matrix[$level][$parent_id]['context_' . $id];
                
                $menu_item['title'] = $context_name;
                $menu_item['url'] = $this->get_url($path);
                
                $page_menu_items = $this->create_page_sub_menu($path, $pages);
                dump('page sub items');
                dump($page_menu_items);
                
                if ($level < ($this->survey->count_levels()))
                {
                    $sub_menu_items = $this->get_menu_items($level + 1, $id, $path);
                    $menu_item['sub'] = $sub_menu_items;
                }
                dump('sub items');
                dump($menu_item['sub']);
                dump('merge');
                dump(array_merge($menu_item['sub'], array($page_menu_items)));
                
                //                $menu_item['sub'] = $page_menu_items;
                

                $menu_item['class'] = 'survey';
                $menu_item[OptionsMenuRenderer :: KEY_ID] = $path;
                $menu[$path] = $menu_item;
            
            }
            else
            {
                continue;
            }
        }
        
        return $menu;
        
        //old
        

        foreach ($this->menu_matrix[$level][$parent_id] as $id => $context_name)
        {
            
            if (! strstr($id, 'contex'))
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
                
                $menu_item = array();
                
                $context_name = $this->menu_matrix[$level][$parent_id]['context_' . $id];
                
                $menu_item['title'] = $context_name;
                $menu_item['url'] = $this->get_url($path);
                //                $pages = $this->menu_matrix[$level][$parent_id][$id];
                //                $menu_item['sub'] =	$this->get_page_menu_items($path, $pages);
                //                
                $sub_menu_items = $this->get_menu_items($level + 1, $id, $path);
                
                if (count($sub_menu_items) > 0)
                {
                    //                    $pages = $this->menu_matrix[$level][$parent_id][$id];
                    //                	foreach ($sub_menu_items as $sub_parent_id => $sub_menu_item)
                    //                    {
                    //                        $sub_menu_items[$sub_parent_id]['sub'] = array_merge($sub_menu_items[$sub_parent_id]['sub'] , $this->get_page_menu_items($path, $pages));
                    //                    }
                    $menu_item['sub'] = $sub_menu_items;
                }
                //                dump($menu_item);
                

                //                dump('submenu_items');
                //                dump($sub_menu_items);
                //                $pages = $this->menu_matrix[$level][$parent_id][$id];
                //                foreach ($sub_menu_items as $sub_menu_item)
                //                {
                //                    
                //                	
                //                	dump($sub_menu_item);
                //                }
                

                //                $menu_item['sub'] = array_merge($menu_item['sub'], $page_menu_items);
                //                dump('in path' .$path);
                //                dump('pages: ');
                //                 dump($pages);
                //                dump('page menu items: ');
                //                dump($page_menu_items);
                

                $menu_item['class'] = 'survey';
                $menu_item[OptionsMenuRenderer :: KEY_ID] = $path;
                $menu[$path] = $menu_item;
            }
            else
            {
                continue;
            }
        }
        
        return $menu;
    }

    function get_page_menu_items($path, $pages)
    {
        
        $menu = array();
        foreach ($pages as $index => $page)
        {
            if (! is_int($index))
            {
                
                $menu_item = array();
                $menu_item['title'] = $page;
                $menu_item['url'] = $this->get_url($path);
                
                $ids = explode('_', $index);
                $page_id = $ids[1];
                $questions = $pages[$page_id];
                //				dump('in: '. $page);
                //                dump($questions);
                $sub_menu_items = array();
                foreach ($questions as $question_index => $question)
                {
                    //                   dump('in question index before int check: '.$question_index);
                    

                    if (! is_int($question_index))
                    {
                        //                        dump('in question index after int check: '.$question_index);
                        $question_ids = explode('_', $question_index);
                        $question_id = $question_ids[1];
                        $sub_menu_item = array();
                        $sub_menu_item['title'] = $question;
                        $sub_menu_item['url'] = $this->get_url($path);
                        $sub_menu_item['class'] = 'survey';
                        $sub_menu_item[OptionsMenuRenderer :: KEY_ID] = $path . '_' . $page_id . '_' . $question_id;
                        $sub_menu_items[$path . '_' . $page_id . '_' . $question_id] = $sub_menu_item;
                    }
                
                }
                $menu_item['sub'] = $sub_menu_items;
                $menu_item['class'] = 'survey';
                $menu_item[OptionsMenuRenderer :: KEY_ID] = $path . '_' . $page_id;
                $menu[$path . '_' . $page_id] = $menu_item;
            
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