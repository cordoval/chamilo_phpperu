<?php
/**
 * @package common.html.action_bar
 * $Id: action_bar_renderer.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 */
require_once dirname(__FILE__) . '/action_bar_search_form.class.php';
require_once dirname(__FILE__) . '/condition_property.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/wiki/component/wiki_actionbar.class.php';

/**
 * Class that renders an action bar divided in 3 parts, a left menu for actions, a middle menu for actions
 * and a right menu for a search bar.
 */
class ActionBarRenderer extends WikiActionbar
{
    const ACTION_BAR_COMMON = 'common';
    const ACTION_BAR_TOOL = 'tool';
    const ACTION_BAR_SEARCH = 'search';
    
    const TYPE_HORIZONTAL = 'hoirzontal';
    const TYPE_VERTICAL = 'vertical';
    
    private $name;
    private $actions = array();
    private $search_form;
    private $type;

    function ActionBarRenderer($type, $name = 'component')
    {
        $this->type = $type;
        $this->name = $name;
    }

    function set_name($name)
    {
        $this->name = $name;
    }

    function get_name()
    {
        return $this->name;
    }

    function set_type($type)
    {
        $this->type = $type;
    }

    function get_type()
    {
        return $this->type;
    }

    function add_action($type = self :: ACTION_BAR_COMMON, $action)
    {
        $this->actions[$type][] = $action;
    }

    function add_common_action($action)
    {
        $this->actions[self :: ACTION_BAR_COMMON][] = $action;
    }

    function add_tool_action($action)
    {
        $this->actions[self :: ACTION_BAR_TOOL][] = $action;
    }

    function get_tool_actions()
    {
        return $this->actions[self :: ACTION_BAR_TOOL];
    }

    function get_common_actions()
    {
        return $this->actions[self :: ACTION_BAR_COMMON];
    }

    function get_search_url()
    {
        return $this->actions[self :: ACTION_BAR_SEARCH];
    }

    function set_tool_actions($actions)
    {
        $this->actions[self :: ACTION_BAR_TOOL] = $actions;
    }

    function set_common_actions($actions)
    {
        $this->actions[self :: ACTION_BAR_COMMON] = $actions;
    }

    function set_search_url($search_url)
    {
        $this->actions[self :: ACTION_BAR_SEARCH] = $search_url;
        $this->search_form = new ActionBarSearchForm($search_url);
    }

    function as_html()
    {
        $type = $this->type;
        
        switch ($type)
        {
            case self :: TYPE_HORIZONTAL :
                return $this->render_horizontal();
                break;
            case self :: TYPE_VERTICAL :
                return $this->render_vertical();
                break;
            case self :: TYPE_WIKI :
                return $this->render_wiki();
                break;
            default :
                return $this->render_horizontal();
                break;
        }
    }

    function render_horizontal()
    {
        $html = array();
        
        $html[] = '<div id="' . $this->get_name() . '_action_bar_text" class="action_bar_text" style="float:left; display: none;"><div class="bevel"><a href="#"><img src="' . Theme :: get_common_image_path() . 'action_bar.png" style="vertical-align: middle;" />' . Translation :: get('ShowActionBar') . '</a></div></div>';
        $html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';
        $html[] = '<div id="' . $this->get_name() . '_action_bar" class="action_bar">';
        $html[] = '<div class="bevel">';
        
        $common_actions = $this->get_common_actions();
        $tool_actions = $this->get_tool_actions();
        
        $html[] = '<div class="common_menu split">';
        
        if (count($common_actions) >= 0)
        {
            $toolbar = new Toolbar();
            $toolbar->set_items($common_actions);
            $toolbar->set_type(Toolbar :: TYPE_HORIZONTAL);
            $html[] = $toolbar->as_html();
        }
        
        $html[] = '</div>';
        
        $html[] = '<div class="tool_menu split split_bevel">';
        
        if (count($tool_actions) >= 0)
        {
            $toolbar = new Toolbar();
            $toolbar->set_items($tool_actions);
            $toolbar->set_type(Toolbar :: TYPE_HORIZONTAL);
            $html[] = $toolbar->as_html();
        }
        
        $html[] = '</div>';
        
        $html[] = '<div class="search_menu split_bevel">';
        if (! is_null($this->search_form))
        {
            $search_form = $this->search_form;
            if ($search_form)
            {
                $html[] = '<div class="search_form">';
                $html[] = $search_form->as_html();
                $html[] = '</div>';
            }
        }
        $html[] = '</div>';
        
        $html[] = '<div class="clear"></div>';
        $html[] = '<div id="' . $this->get_name() . '_action_bar_hide_container" class="action_bar_hide_container">';
        $html[] = '<a id="' . $this->get_name() . '_action_bar_hide" class="action_bar_hide" href="#"><img src="' . Theme :: get_common_image_path() . 'action_ajax_hide.png" /></a>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/action_bar_horizontal.js');
        
        $html[] = '<div class="clear"></div>';
        
        return implode("\n", $html);
    }

    function render_vertical()
    {
        $html = array();
        
        $html[] = '<div id="' . $this->get_name() . '_action_bar_left" class="action_bar_left">';
        //		$html[] = '<div id="action_bar_left_options"';
        

        $html[] = '<h3>' . Translation :: get('ActionBar') . '</h3>';
        
        $common_actions = $this->get_common_actions();
        $tool_actions = $this->get_tool_actions();
        
        $action_bar_has_search_form = ! is_null($this->search_form);
        $action_bar_has_common_actions = (count($common_actions) > 0);
        $action_bar_has_tool_actions = (count($tool_actions) > 0);
        $action_bar_has_common_and_tool_actions = (count($common_actions) > 0) && (count($tool_actions) > 0);
        
        if (! is_null($this->search_form))
        {
            $search_form = $this->search_form;
            $html[] = $search_form->as_html();
        }
        
        if ($action_bar_has_search_form && ($action_bar_has_common_actions || $action_bar_has_tool_actions))
        {
            $html[] = '<div class="divider"></div>';
        }
        
        if ($action_bar_has_common_actions)
        {
            $html[] = '<div class="clear"></div>';
            
            $toolbar = new Toolbar();
            $toolbar->set_items($common_actions);
            $toolbar->set_type(Toolbar :: TYPE_VERTICAL);
            $html[] = $toolbar->as_html();
        }
        
        if ($action_bar_has_common_and_tool_actions)
        {
            $html[] = '<div class="divider"></div>';
        }
        
        if ($action_bar_has_tool_actions)
        {
            $html[] = '<div class="clear"></div>';
            
            $toolbar = new Toolbar();
            $toolbar->set_items($tool_actions);
            $toolbar->set_type(Toolbar :: TYPE_VERTICAL);
            $html[] = $toolbar->as_html();
        }
        
        $html[] = '<div class="clear"></div>';
        //		$html[] = '</div>';
        

        $html[] = '<div id="' . $this->get_name() . '_action_bar_left_hide_container" class="action_bar_left_hide_container hide">';
        $html[] = '<a id="' . $this->get_name() . '_action_bar_left_hide" class="action_bar_left_hide" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_hide.png" /></a>';
        $html[] = '<a id="' . $this->get_name() . '_action_bar_left_show" class="action_bar_left_show" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_show.png" /></a>';
        $html[] = '</div>';
        $html[] = '</div>';
        
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/action_bar_vertical.js');
        
        $html[] = '<div class="clear"></div>';
        
        return implode("\n", $html);
    }

    function get_query()
    {
        if ($this->search_form)
        {
            return $this->search_form->get_query();
        }
        else
        {
            return null;
        }
    }
    
    function get_conditions($properties = array ())
    {
        if (! is_array($properties))
        {
            $properties = array($properties);
        }
        
        $query = $this->get_query();
        if($query && $query != '')
        {
	        $query = '*' . $query . '*';
	        $pattern_conditions = array();
	        
	        foreach ($properties as $property)
	        {
	        	$pattern_conditions[] = new PatternMatchCondition($property->get_property(), $query, $property->get_storage_unit());
	        }
	        if (count($pattern_conditions) > 1)
	        {
	            $condition = new OrCondition($pattern_conditions);
	        }
	        else
	        {
	            $condition = $pattern_conditions[0];
	        }
	        
	        return $condition;
        }
    }
    
}

?>