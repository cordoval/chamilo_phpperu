<?php
require_once dirname(__FILE__) . '/external_repository_browser_gallery_table/external_repository_browser_gallery_table.class.php';
require_once dirname(__FILE__) . '/external_repository_browser_table/external_repository_browser_table.class.php';

class ExternalRepositoryBrowserComponent extends ExternalRepositoryComponent
{
    private $action_bar;
    private $menu;

    function ExternalRepositoryBrowserComponent($application)
    {
        parent :: __construct($application);
    }

    function get_menu()
    {
        return $this->menu;
    }

    function set_menu($menu)
    {
        $this->menu = $menu;
    }

    function render_menu()
    {
        $extra = $this->get_menu_items();
        if ($this->action_bar->get_query())
        {
            $search_url = '#';
            $search = array();

            $search['title'] = Translation :: get('SearchResults');

            $search['url'] = $search_url;
            $search['class'] = 'search_results';
            $extra[] = $search;
        }
        else
        {
            $search_url = null;
        }

        $this->menu = new ExternalRepositoryMenu(Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID), $this->get_parent(), $extra);

        if ($search_url)
        {
            $this->menu->forceCurrentUrl($search_url);
        }

        $html = array();
        if ($this->menu->count_menu_items() > 0)
        {
            $html[] = '<div style=" width: 20%; overflow: auto; float: left">';
            $html[] = $this->menu->render_as_tree();
            $html[] = '</div>';
        }
        return implode("\n", $html);
    }

    function run()
    {
        $this->action_bar = $this->get_action_bar();
        $query = $this->action_bar->get_query();
        $html = array();

        if (isset($query) && $query != '')
        {
            $this->set_parameter(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY, $query);
        }

        $this->display_header();

        $html[] = $this->action_bar->as_html();
        $html[] = '<div id="action_bar_browser">';

        if ($this->get_menu() == null)
        {
            $html[] = $this->render_menu();
        }
        if ($this->menu->count_menu_items() > 0)
        {
            $html[] = '<div style=" width: 80%; overflow: auto; float: center">';
        }

        $renderer = ExternalRepositoryObjectRenderer :: factory($this->get_parent()->get_renderer(), $this);
        $html[] = $renderer->as_html();

        if ($this->menu->count_menu_items() > 0)
        {
            $html[] = '</div>';
        }
        $html[] = '</div>';

        echo (implode("\n", $html));
        $this->display_footer();
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            return $this->translate_search_query($query);
        }
        return null;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $renderers = $this->get_parent()->get_available_renderers();

        if (count($renderers) > 1)
        {
            foreach ($renderers as $renderer)
            {
                $action_bar->add_tool_action(new ToolbarItem(Translation :: get(Utilities :: underscores_to_camelcase($renderer) . 'View'), Theme :: get_image_path() . 'view_' . $renderer . '.png', $this->get_url(array(
                        ExternalRepositoryManager :: PARAM_RENDERER => $renderer)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }

        return $action_bar;
    }
}
?>