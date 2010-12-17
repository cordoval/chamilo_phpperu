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
     * Retrieves the number of tabs
     */
    public function size()
    {
    	return count($this->tabs);
    }

    /**
     * @param DynamicTab $tab
     */
    public function add_tab(DynamicTab $tab)
    {
        $tab->set_id($this->name . '_' . $tab->get_id());
        $this->tabs[] = $tab;
    }

    public function header()
    {
        $tabs = $this->get_tabs();

        $html = array();

        //$html[] = '<a name="top"></a>';
        $html[] = '<div id="' . $this->name . '_tabs">';

        // Tab headers
        $html[] = '<ul class="tabs-header">';
        foreach ($tabs as $key => $tab)
        {
            $html[] = $tab->header();
        }
        $html[] = '</ul>';

        return implode("\n", $html);
    }

    public function get_selected_tab()
    {
        $selected_tab = Request :: get(self :: PARAM_SELECTED_TAB);

        if ($selected_tab)
        {
            return $this->get_name() . '_' . $selected_tab;
        }
        else
        {
            return null;
        }
    }

    public function footer()
    {
        $html = array();
        $html[] = '</div>';
        $html[] = '<script type="text/javascript">';
        $html[] = 'function setSearchTab(e, ui)
	{
		var searchForm = $("div.action_bar div.search_form form");
		var url = $.query.load(searchForm.attr(\'action\'));
		var currentTabId = $("div.admin_tab:visible").attr(\'id\').replace("'. $this->get_name() .'_", "");
		searchForm.attr(\'action\', url.set("tab", currentTabId).toString());
	}';

        $html[] = '$(document).ready(function ()';
        $html[] = '{';
        $html[] = '	$("#' . $this->get_name() . '_tabs ul").css(\'display\', \'block\');';
        $html[] = '	$("#' . $this->get_name() . '_tabs h2").hide();';
        $html[] = '	$("#' . $this->get_name() . '_tabs").tabs();';
        $html[] = '	var tabs = $(\'#' . $this->get_name() . '_tabs\').tabs(\'paging\', { cycle: false, follow: false, nextButton : "", prevButton : "" } );';

        $selected_tab = $this->get_selected_tab();
        if (isset($selected_tab))
        {
            $html[] = '	$(\'#' . $this->get_name() . '_tabs\').tabs( "option", "selected", "' . $selected_tab . '" );';
        }

        $html[] = '	$("#' . $this->get_name() . '_tabs").live(\'tabsshow\', setSearchTab);';

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
            $html[] = $tab->body($this->name . '_' . $tab->get_id());
        }

        $html[] = $this->footer();

        return implode("\n", $html);
    }

}