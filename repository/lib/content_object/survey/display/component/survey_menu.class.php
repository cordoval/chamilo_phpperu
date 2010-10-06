<?php

require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';

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

    function SurveyMenu($parent, $current_context_path, $url_format, $survey)
    {
        $this->survey = $survey;
        $this->survey_id = $this->survey->get_id();
        
        $this->user_id = $parent->get_parent()->get_parameter(SurveyViewerWizard :: PARAM_INVITEE_ID);
        $this->publication_id = $parent->get_parent()->get_parameter(SurveyViewerWizard :: PARAM_PUBLICATION_ID);
        
        $this->urlFmt = $url_format;
        $menu = $this->get_menu();
        
        parent :: __construct($menu);
        //        $this->forceCurrentUrl($this->get_url($current_context_path));
    }

    function get_menu()
    {
        $this->create_context_path_relations();
        return $this->get_menu_items();
    }

    function create_context_path_relations()
    {
        $context_paths = $this->survey->get_context_paths();
        
        $this->context_path_relations = array();
        
        foreach ($context_paths as $index => $context_path)
        {
            $path_ids = explode('_', $context_path);
            $id_count = count($path_ids);
            
            //questions
            $context = array();
            $question_id = $path_ids[$id_count - 1];
            $context[self :: QUESTION_ID] = $question_id;
            
            array_pop($path_ids);
            $question_parent_context = implode('_', $path_ids);
            
            $context[self :: PARENT_ID] = $question_parent_context;
            $context[self :: ID] = $context_path;
            $context[self :: MENU_ITEM_TYPE] = self :: TYPE_QUESTION;
            $this->context_path_relations[$context_path] = $context;
            
            //pages
            $context = array();
            $page_id = $path_ids[$id_count - 2];
            $context[self :: PAGE_ID] = $page_id;
            
            array_pop($path_ids);
            $page_parent_context = implode('_', $path_ids);
            
            $context[self :: PARENT_ID] = $page_parent_context;
            $context[self :: ID] = $question_parent_context;
            $context[self :: MENU_ITEM_TYPE] = self :: TYPE_PAGE;
            $this->context_path_relations[$question_parent_context] = $context;
            
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
                $path_ids = explode('_', $context_path);
                $id_count = count($path_ids);
                if ($id_count == $level)
                {
                    $context = array();
                    $context[self :: ID] = $context_path;
                    $context[self :: CONTEXT_ID] = $path_ids[$id_count - 1];
                    
                    if ($level == 1)
                    {
                        $parent_id = 0;
                        $context[self :: MENU_ITEM_TYPE] = self :: TYPE_SURVYEY;
                    }
                    else
                    {
                        array_pop($path_ids);
                        $parent_id = implode('_', $path_ids);
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
    
    }

    /**
     * Returns the menu items.
     * @return array An array with all menu items. The structure of this array
     * is the structure needed by PEAR::HTML_Menu, on which this
     * class is based.
     */
    private function get_menu_items($parent_id = 0, $path = null)
    {
        
        $menu = array();
        
        $level_count = $this->survey->count_levels();
        
        foreach ($this->context_path_relations as $context_path => $context_path_relation)
        {
            
            if ($context_path_relation[self :: PARENT_ID] == $parent_id)
            
            {
                $path_ids = explode('_', $context_path);
                $id_count = count($path_ids);
                
                $type = $context_path_relation[self :: MENU_ITEM_TYPE];
                
                $menu_item = array();
                
                switch ($type)
                {
                    case self :: TYPE_SURVYEY :
                        $title = $this->survey->get_title();
                        $menu_item['class'] = self :: TYPE_SURVYEY;
                        $menu_item['url'] = 'dsfsdf';
                        break;
                    case self :: TYPE_CONTEXT :
                        $context = SurveyContextDataManager :: get_instance()->retrieve_survey_context_by_id($context_path_relation[self :: CONTEXT_ID]);
                        $title = $context->get_name();
                        $menu_item['class'] = self :: TYPE_CONTEXT;
                        $menu_item['url'] = 'dsfsdf';
                        break;
                    case self :: TYPE_PAGE :
                        $survey_page = $this->survey->get_page_by_id($context_path_relation[self :: PAGE_ID]);
                        $title = $survey_page->get_title();
                        $menu_item['class'] = self :: TYPE_PAGE;
                        $menu_item['url'] = $this->get_url($context_path);
                        break;
                    case self :: TYPE_QUESTION :
                        $complex_question_id = $context_path_relation[self :: QUESTION_ID];
                        $complex_question = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_question_id);
                        $question = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_question->get_ref());
                        $title = $question->get_title();
                        $menu_item['class'] = self :: TYPE_QUESTION;
                        $menu_item['url'] = 'dsfsdf';
                        break;
                    default :
                        ;
                        break;
                }
                
                $menu_item['title'] = $title;
                
                $sub_menu_items = $this->get_menu_items($context_path_relation[self :: ID]);
                
                if (count($sub_menu_items))
                {
                    $menu_item['sub'] = $sub_menu_items;
                }
                
                $menu_item[OptionsMenuRenderer :: KEY_ID] = $context_path;
                $menu[$context_path] = $menu_item;
            
            }
            else
            {
                continue;
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