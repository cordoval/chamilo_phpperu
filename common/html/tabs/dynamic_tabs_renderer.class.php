<?php
class DynamicTabsRenderer
{
    const PARAM_SELECTED_TAB = 'tab';

    const TYPE_CONTENT = 1;
    const TYPE_ACTIONS = 2;

    private $name;
    private $tabs;

    public function DynamicTabsRenderer($name)
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

    /**
     * @param DynamicTab $tab
     */
    public function add_tab(DynamicTab $tab)
    {
        $this->tabs[] = $tab;
    }

    public function header()
    {
        $tabs = $this->get_tabs();

        $requested_tab = Request :: get(self :: PARAM_SELECTED_TAB);

        $html = array();

        $html[] = '<a name="top"></a>';
        $html[] = '<div id="' . $this->name . '_tabs">';

        // Tab headers
        $html[] = '<ul class="tabs-header">';
        foreach ($tabs as $key => $tab)
        {
            if ($requested_tab == $tab->get_id() && ! is_null($requested_tab))
            {
                $selected_tab = $key;
            }

            $html[] = $tab->header($this->name . '_' . $key);
        }
        $html[] = '</ul>';

        return implode("\n", $html);
    }

    public function footer()
    {
        $html = array();
        $html[] = '</div>';
        $html[] = '<br /><a href="#top">' . Translation :: get('Top') . '</a>';
        $html[] = '<script type="text/javascript">';
        $html[] = '$(document).ready(function ()';
        $html[] = '{';
        $html[] = '	$("#' . $this->get_name() . '_tabs ul").css(\'display\', \'block\');';
        $html[] = '	$("#' . $this->get_name() . '_tabs h2").hide();';
        $html[] = '	$("#' . $this->get_name() . '_tabs").tabs();';
        $html[] = '	var tabs = $(\'#' . $this->get_name() . '_tabs\').tabs(\'paging\', { cycle: false, follow: false, nextButton : "", prevButton : "" } );';
        if (isset($selected_tab))
        {
            $html[] = '	tabs.tabs(\'select\', "' . $selected_tab . '");';
        }
        $html[] = '});';
        $html[] = '</script>';

        return implode("\n", $html);
    }

    public function render()
    {
        $html = array();
        $html[] = $this->header();

        // Tab content
        $tabs = $this->get_tabs();

        foreach ($tabs as $key => $tab)
        {
            $html[] = $tab->body($this->name . '_' . $key);
        }

        $html[] = $this->footer();

        return implode("\n", $html);
    }

}