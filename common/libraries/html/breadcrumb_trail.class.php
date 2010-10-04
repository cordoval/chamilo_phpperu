<?php
/**
 * $Id: breadcrumb_trail.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html
 */
class BreadcrumbTrail
{
    // Singleton
    private static $instance;
    
    private $breadcrumbtrail;
    
    private $help_items;
    
    private $extra_items;

    /**
     * @return BreadcrumbTrail
     */
    static function get_instance()
    {
        if (self :: $instance == null)
        {
            self :: $instance = new BreadcrumbTrail();
        }
        
        return self :: $instance;
    }

    function BreadcrumbTrail($include_main_index = true)
    {
        $this->breadcrumbtrail = array();
        $this->help_items = array();
        $this->extra_items = array();
        if ($include_main_index)
        {
            $this->add(new Breadcrumb($this->get_path(WEB_PATH) . 'index.php', $this->get_setting('site_name', 'admin')));
        }
    }

    function add($breadcrumb)
    {
        $this->breadcrumbtrail[] = $breadcrumb;
    }

    function add_help($help_item)
    {
        $this->help_items[] = $help_item;
    }

    function add_extra($extra_item)
    {
        $this->extra_items[] = $extra_item;
    }

    function get_help_items()
    {
        return $this->help_items;
    }

    function set_help_items($help_items)
    {
        $this->help_items = $help_items;
    }

    function remove($breadcrumb_index)
    {
        unset($this->breadcrumbtrail[$breadcrumb_index]);
    }

    function get_first()
    {
        return $this->breadcrumbtrail[0];
    }

    function get_last()
    {
        $breadcrumbtrail = $this->breadcrumbtrail;
        $last_key = count($breadcrumbtrail) - 1;
        return $breadcrumbtrail[$last_key];
    }

    function truncate($keep_main_index = false)
    {
        $this->breadcrumbtrail = array();
    	if ($keep_main_index)
        {
            $this->add(new Breadcrumb($this->get_path(WEB_PATH) . 'index.php', $this->get_setting('site_name', 'admin')));
        }
    }

    function render()
    {
        $html = array();
        
        $html[] = $this->render_breadcrumbs();
        $html[] = $this->render_help();
        $html[] = $this->render_extra();
        
        return implode("\n", $html);
    }

    function render_breadcrumbs()
    {
        $html = array();
        $html[] = '<ul id="breadcrumbtrail">';
        
        $breadcrumbtrail = $this->breadcrumbtrail;
        if (is_array($breadcrumbtrail) && count($breadcrumbtrail) > 0)
        {
            foreach ($breadcrumbtrail as $breadcrumb)
            {
                $html[] = '<li><a href="' . $breadcrumb->get_url() . '" target="_self">' . Utilities :: truncate_string($breadcrumb->get_name(), 50, true) . '</a></li>';
            }
        }
        
        $html[] = '</ul>';
        
        return implode("\n", $html);
    }

    function render_help()
    {
        $html = array();
        $help_items = $this->help_items;
        
        if (is_array($help_items) && count($help_items) > 0)
        {
            $items = array();
            
            foreach ($help_items as $help_item)
            {
                $item = HelpManager :: get_tool_bar_help_item($help_item);
                if ($item)
                {
                    $items[] = $item;
                }
            }
            
            if (count($items) > 0)
            {
                $html[] = '<div id="help_item">';
                $toolbar = new Toolbar();
                $toolbar->set_items($items);
                $toolbar->set_type(Toolbar :: TYPE_HORIZONTAL);
                $html[] = $toolbar->as_html();
                $html[] = '</div>';
            }
        }
        
        return implode("\n", $html);
    }

    function render_extra()
    {
        $html = array();
        $extra_items = $this->extra_items;
        
        $html[] = '<div id="extra_item">';
        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('ShowActionBar'), Theme :: get_common_image_path() . 'action_bar.png', '#', ToolbarItem :: DISPLAY_ICON_AND_LABEL, false, 'action_bar_text'));
        $toolbar->add_item(new ToolbarItem(Translation :: get('ShowReportingFilter'), Theme :: get_common_image_path() . 'reporting_filter.png', '#', ToolbarItem :: DISPLAY_ICON_AND_LABEL, false, 'reporting_filter_text'));
        
        if (is_array($extra_items) && count($extra_items) > 0)
        {
            $toolbar->add_items($extra_items);
            $toolbar->set_type(Toolbar :: TYPE_HORIZONTAL);
        }
        
        $html[] = $toolbar->as_html();
        $html[] = '</div>';
        
        return implode("\n", $html);
    }

    function size()
    {
        return count($this->breadcrumbtrail);
    }

    function display()
    {
        $html = $this->render();
        echo $html;
    }

    function get_breadcrumbtrail()
    {
        return $this->breadcrumbtrail;
    }

    function set_breadcrumbtrail($breadcrumbtrail)
    {
        $this->breadcrumbtrail = $breadcrumbtrail;
    }

    function get_setting($variable, $application)
    {
        return PlatformSetting :: get($variable, $application);
    }

    function get_path($path_type)
    {
        return Path :: get($path_type);
    }

    function get_breadcrumbs()
    {
        return $this->breadcrumbtrail;
    }

    function merge($trail)
    {
        $this->breadcrumbtrail = array_merge($this->breadcrumbtrail, $trail->get_breadcrumbtrail());
    }
}
?>