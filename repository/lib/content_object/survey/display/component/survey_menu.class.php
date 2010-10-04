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
        

        $menu = $this->get_menu_items(1, $this->survey->get_id());
        
        return $menu;
    }

    function create_context_path_relations()
    {
        $context_paths = $this->survey->get_context_paths();
        //        dump('context paths');
        //        dump($context_paths);
        

        $level_count = $this->survey->count_levels();
        dump($level_count);
        $total_level_count = $level_count + 3;
        
        $this->context_path_relations = array();
        
        for($level = 1; $level <= $total_level_count; $level ++)
        {
            foreach ($context_paths as $context_path)
            {
                $path_ids = explode('_', $context_path);
                
                $id_count = count($path_ids);
                
                $page_id = $path_ids[$id_count - 2];
                $question_id = $path_ids[$id_count - 1];
                
                array_pop($path_ids);
                array_pop($path_ids);
                $parent_id = implode('_', $path_ids);
                
                $parent_ids[] = $parent_id;
                
                $context = array();
                $context['level'] = $level;
                $context['page_id'] = $page_id;
                $context['question_id'] = $question_id;
                $context['parent_id'] = $parent_id;
                $this->context_path_relations[$context_path] = $context;
            
            }
        }
        
        $parent_ids = array_unique($parent_ids);
        dump($parent_ids);
        
        $context_parent_paths = array();
        
        $levels = $level_count+1;
        
        for($level = $levels; $level <= 1; $level--)
        {
            dump('level');
        	dump($level);
        	
        	
        	
        	foreach ($parent_ids as $index => $context_path)
            {
                $path_ids = explode('_', $context_path);
                $id_count = count($path_ids);
                if($id_count == $levels){
                	dump('idcount = $level count');
                	dump($context_path);
                	unset($parent_ids[$index]);
                }
                $context = array();
//                $context['level'] = $level;
                $context_id = $path_ids[$id_count-1];
                $context['context_id'] = $context_id;
//                array_pop($path_ids);
//                $parent_id = implode('_', $path_ids);
                $parent_id = $path_ids[$id_count-2];
                $context['parent_context_id'] = $parent_id;
                $context_parent_paths[] = $context;
                
//                $context_parent_paths[$level.'_'.$context_id] = $context;
            }
            dump($context_parent_paths);
        }
       
        dump('after level lus');
        dump($context_parent_paths);
       dump(count($context_parent_paths)); 
        
//        dump($this->context_path_relations);
        dump(count($this->context_path_relations));
    }

    /**
     * Returns the menu items.
     * @return array An array with all menu items. The structure of this array
     * is the structure needed by PEAR::HTML_Menu, on which this
     * class is based.
     */
    private function get_menu_items($level, $parent_id, $path = null)
    {
        
        $menu = array();
        
        //        dump($level);
        

        //        dump('before parent_id');
        //        dump($parent_id);
        

        foreach ($this->context_path_relations as $context_path => $context_path_relation)
        {
            
            if ($context_path_relation['parent_id'] == $parent_id && $context_path_relation['level'] == $level)
            {
                
                //                dump('in relation');
                //                dump($context_path_relation['parent_id']);
                //                dump($context_path_relation['id']);
                

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