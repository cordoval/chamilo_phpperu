<?php
class TabsRenderer
{
    const PARAM_SELECTED_TAB = 'selected_tab';
    
    const TYPE_CONTENT = 1;
    const TYPE_ACTIONS = 2;
    
    private $name;
    private $tabs;

    public function TabsRenderer($name)
    {
        $this->name = $name;
        $this->tabs = array();
    }

    /**
     * @return the $name
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @return the $tabs
     */
    public function get_tabs()
    {
        return $this->tabs;
    }

    /**
     * @param $name the $name to set
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     * @param $tabs the $tabs to set
     */
    public function set_tabs($tabs)
    {
        $this->tabs = $tabs;
    }

    public function add_tab(Tab $tab)
    {
        $this->tabs[] = $tab;
    }

    public function render()
    {
        $selected_tab = 0;
        $tabs = $this->get_tabs();
        
        $html = array();
        
        $html[] = '<a name="top"></a>';
        $html[] = '<div id="' . $this->name . '_tabs">';
        
        // Tab headers
        $html[] = '<ul class="tabs-header">';
        foreach ($tabs as $key => $tab)
        {
            if (Request :: get(self :: PARAM_SELECTED_TAB) == $key)
            {
                $selected_tab = $index - 1;
            }
            
            $html[] = $tab->get_header($this->name . '_' . $key);
        }
        $html[] = '</ul>';
        
        // Tab content
        foreach ($tabs as $key => $tab)
        {
            $html[] = $tab->get_content($this->name . '_' . $key);
        }
        
        $html[] = '</div>';
        $html[] = '<br /><a href="#top">' . Translation :: get('Top') . '</a>';
        $html[] = '<script type="text/javascript">';
        $html[] = 'var tabnumber = ' . $selected_tab . ';';
        $html[] = '$(document).ready(function ()';
        $html[] = '{';
        $html[] = '	$("#' . $this->get_name() . '_tabs ul").css(\'display\', \'block\');';
        $html[] = '	$("#' . $this->get_name() . '_tabs h2").hide();';
        $html[] = '	$("#' . $this->get_name() . '_tabs").tabs();';
        $html[] = '	var tabs = $(\'#' . $this->get_name() . '_tabs\').tabs(\'paging\', { cycle: false, follow: false, nextButton : "", prevButton : "" } );';
        $html[] = '	tabs.tabs(\'select\', tabnumber);';
        $html[] = '});';
        $html[] = '</script>';
        
        return implode("\n", $html);
    }

}