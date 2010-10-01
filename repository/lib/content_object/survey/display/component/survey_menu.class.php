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
   
    function SurveyMenu($parent, $current_context_path, $url_format, $survey)
    {
        $this->survey = $survey;
    	$this->survey_id = $this->survey->get_id();
        
        $this->user_id = $parent->get_parent()->get_parameter(SurveyViewerWizard :: PARAM_INVITEE_ID);
        $this->menu_matrix = $this->survey->get_context_paths($this->user_id, true);
        
        $this->publication_id = $parent->get_parent()->get_parameter(SurveyViewerWizard :: PARAM_PUBLICATION_ID);
        
        $this->urlFmt = $url_format;
        $menu = $this->get_menu();
        parent :: __construct($menu);
        $this->forceCurrentUrl($this->get_url($current_context_path));
    }

    function get_menu()
    {
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
//        dump($this->menu_matrix);  	
        
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
            }else{
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