<?php

require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once Path :: get_repository_path() . '/lib/content_object/survey/survey_context_template.class.php';

class SurveyContextTemplateMenu extends HTML_Menu
{
    const TREE_NAME = __CLASS__;
    
    /**
     * The string passed to sprintf() to format category URLs
     */
    private $urlFmt;
    
    private $survey_id;
    
    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;
    
    //	private $include_root;
    

    private $current_template_id;

    //	private $show_complete_tree;
    

    //	private $hide_current_template;
    

    function SurveyContextTemplateMenu($current_template_id, $survey_id, $url_format = '?go=content_object_manager&application=repository&action=context_browser&manage=context&content_object_type=survey&survey_id=%s&context_template_id=%s')
    {
        
        //		dump($current_template);
        //		dump($survey_id);
        

        $this->survey_id = $survey_id;
        //		$this->include_root = $include_root;
        //		$this->show_complete_tree = $show_complete_tree;
        //		$this->hide_current_template = $hide_current_template;
        

        //		if ($current_template == '0' || is_null ( $current_template )) {
        //			$survey = RepositoryDataManager::get_instance ()->retrieve_content_object ( $this->survey_id );
        //			$template_id = $survey->get_context_template_id ();
        //			$this->current_template = SurveyContextDataManager::get_instance ()->retrieve_survey_context_template ( $template_id );
        //		} else {
        //
        //			$this->current_template = SurveyContextDataManager::get_instance ()->retrieve_survey_context_template ( $current_template );
        //		}
        

        //		$this->current_template = SurveyContextDataManager::get_instance ()->retrieve_survey_context_template ( $template_id );
        $this->current_template_id = $current_template_id;
        
        $this->urlFmt = $url_format;
        $menu = $this->get_menu();
        parent :: __construct($menu);
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_url($this->current_template_id));
    }

    function get_menu()
    {
        //		$include_root = $this->include_root;
        $survey = RepositoryDataManager :: get_instance()->retrieve_content_object($this->survey_id);
        $context_template_id = $survey->get_context_template_id();
        
        //		if (! $include_root) {
        //			return $this->get_menu_items ( $template );
        //		} else {
        $menu = array();
        
        $context_template = SurveyContextDataManager::get_instance()->retrieve_survey_context_template($context_template_id);
        
        $menu_item = array();
        $menu_item['title'] = $context_template->get_context_type_name();
        $menu_item['url'] = $this->get_url($context_template_id);
        
        $sub_menu_items = $this->get_menu_items($context_template_id);
        if (count($sub_menu_items) > 0)
        {
            $menu_item['sub'] = $sub_menu_items;
        }
        
        $menu_item['class'] = 'home';
        $menu_item[OptionsMenuRenderer :: KEY_ID] = $context_template_id;
        $menu[$context_template_id] = $menu_item;
        return $menu;
        //		}
    }

    /**
     * Returns the menu items.
     * @param array $extra_items An array of extra tree items, added to the
     * root.
     * @return array An array with all menu items. The structure of this array
     * is the structure needed by PEAR::HTML_Menu, on which this
     * class is based.
     */
    private function get_menu_items($parent_id = 0)
    {
        $current_template_id = $this->current_template_id;
        
        //		$show_complete_tree = $this->show_complete_tree;
        //		$hide_current_template = $this->hide_current_template;
        

        $condition = new EqualityCondition(SurveyContextTemplate :: PROPERTY_PARENT_ID, $parent_id);
        $context_templates = SurveyContextDataManager :: get_instance()->retrieve_survey_context_templates($condition);
        
        while ($context_template = $context_templates->next_result())
        {
            $template_id = $context_template->get_id();
            
            if (! ($template_id == $current_template_id))
            {
                $menu_item = array();
                $menu_item['title'] = $context_template->get_context_type_name();
                $menu_item['url'] = $this->get_url($template_id);
                
                if ($context_template->is_parent_of($current_template) || $context_template->get_id() == $current_template_id)
                {
                    if ($context_template->has_children())
                    {
                        $menu_item['sub'] = $this->get_menu_items($template_id);
                    }
                }
                else
                {
                    if ($context_template->has_children())
                    {
                        $menu_item['children'] = 'expand';
                    }
                }
                
                $menu_item['class'] = 'category';
                $menu_item[OptionsMenuRenderer :: KEY_ID] = $template_id;
                $menu[$template_id] = $menu_item;
            }
        }
        
        return $menu;
    }
  
    function get_url($context_template_id)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(sprintf($this->urlFmt, $this->survey_id, $context_template_id));
    }

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