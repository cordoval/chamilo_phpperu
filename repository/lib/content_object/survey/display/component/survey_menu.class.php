<?php

require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';

class SurveyMenu extends HTML_Menu
{
    const TREE_NAME = __CLASS__;
    
    const PARAM_TEMPLATE_ID = 'template_id';
    
    private $urlFmt;
    
    private $current_template_id;
    
    private $root_context_template_id;
    
    private $survey_id;
    
    private $user_id;
    
    private $publication_id;
    
    
    private $menu_matrix;
    
    private $level_matrix;

    function SurveyMenu($parent, $current_template_id, $url_format, $survey)
    {
        $this->root_context_template_id = $survey->get_context_template_id();
        $this->survey_id = $survey->get_id();
        
        $this->user_id = $parent->get_parent()->get_parameter(SurveyViewerWizard :: PARAM_INVITEE_ID);
        $this->publication_id = $parent->get_parent()->get_parameter(SurveyViewerWizard :: PARAM_PUBLICATION_ID);
        
        $current_context_template_id = $parent->get_parent()->get_parameter(SurveyViewerWizard :: PARAM_CONTEXT_TEMPLATE_ID);
        $current_context_id = $parent->get_parent()->get_parameter(SurveyViewerWizard :: PARAM_CONTEXT_ID, $current_context_id);
        $current_context_path = $parent->get_parent()->get_parameter(SurveyViewerWizard :: PARAM_CONTEXT_PATH);
        
        $this->current_context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($current_template_id);
        
        $this->current_template_id = $current_template_id;
        
        $this->urlFmt = $url_format;
        $menu = $this->get_menu();
        parent :: __construct($menu);
        $this->forceCurrentUrl($this->get_url($current_context_path));
        
    //        $this->forceCurrentUrl($this->get_url($current_context_template_id, $this->current_template_id, $current_context_id, $current_context_path));
    }

    private function create_menu_matrix()
    {
        
        $context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($this->root_context_template_id);
        
        $level = 1;
        $this->level_matrix[$level] = $context_template->get_id();
        $context_template_children = $context_template->get_children(true);
        while ($child_template = $context_template_children->next_result())
        {
            $level ++;
            $this->level_matrix[$level] = $child_template->get_id();
        }
        
        $condition = new EqualityCondition(SurveyTemplate :: PROPERTY_USER_ID, $this->user_id, SurveyTemplate :: get_table_name());
        $templates = SurveyContextDataManager :: get_instance()->retrieve_survey_templates($context_template->get_type(), $condition);
        
        $menu_matrix = array();
        
        while ($template = $templates->next_result())
        {
            
            $level = 1;
            $property_names = $template->get_additional_property_names(true);
            $parent_id = 0;
            foreach ($property_names as $property_name => $context_type)
            {
                $context_id = $template->get_additional_property($property_name);
                
                $context = SurveyContextDataManager :: get_instance()->retrieve_survey_context_by_id($context_id);
                $template_id = $template->get_id();
                
                $menu_matrix[$level][$parent_id][$context_id] = $context->get_name();
                $menu_matrix[self :: PARAM_TEMPLATE_ID][$parent_id][$context_id] = $template->get_id();
                
                $parent_id = $context_id;
                $level ++;
            }
        }
        
        //        dump($menu_matrix);
        

        $this->menu_matrix = $menu_matrix;
    }

    function get_menu()
    {
        $this->create_menu_matrix();
        return $this->get_menu_items();
    }

    /**
     * Returns the menu items.
     * @return array An array with all menu items. The structure of this array
     * is the structure needed by PEAR::HTML_Menu, on which this
     * class is based.
     */
    private function get_menu_items($level = 1, $parent_id = 0, $path = null)
    {
        
        foreach ($this->menu_matrix[$level][$parent_id] as $id => $context_name)
        {
            $template_id = $this->menu_matrix[self :: PARAM_TEMPLATE_ID][$parent_id][$id];
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
            $menu_item['title'] = $context_name;
            $menu_item['url'] = $this->get_url($path);
            
            //            $menu_item['url'] = $this->get_url($context_template_id, $template_id, $id, $path);
            

            $sub_menu_items = $this->get_menu_items($level + 1, $id, $path);
            if (count($sub_menu_items) > 0)
            {
                foreach ($sub_menu_items as $sub_parent_id => $sub_menu_item)
                {
                    $menu_item['sub'] = $sub_menu_items;
                }
            }
            $menu_item['class'] = 'survey';
            $menu_item[OptionsMenuRenderer :: KEY_ID] = $id;
            $menu[$id] = $menu_item;
        }
        return $menu;
    }

    function get_url($context_path)
    {
    	$test = sprintf($this->urlFmt,$this->publication_id, $this->survey_id, $this->user_id, $context_path);
    	$test = $test.'&display_action=survey_viewer&_qf_question_page_3_display=true';
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