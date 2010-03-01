<?php
/**
 * $Id: browser.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.repo_viewer.component
 */
require_once dirname(__FILE__) . '/../repo_viewer_component.class.php';
require_once dirname(__FILE__) . '/content_object_table/content_object_table.class.php';
/**
 * This class represents a encyclopedia repo_viewer component which can be used
 * to browse through the possible learning objects to publish.
 */
class RepoViewerBrowserComponent extends RepoViewerComponent
{
    /**
     * The search form
     */
    private $form;

    /**
     * The renderer for the search form
     */
    private $renderer;

    /**
     * The browser actions
     */
    private $browser_actions;

    function RepoViewerBrowserComponent($parent)
    {
        parent :: __construct($parent);
        $this->set_browser_actions($this->get_default_browser_actions());

        $this->set_form(new FormValidator('search', 'post', $this->get_url($this->get_parameters()), '', array('id' => 'search'), false));
        $this->get_form()->addElement('hidden', RepoViewer :: PARAM_ACTION);
        $this->get_form()->addElement('text', RepoViewer :: PARAM_QUERY, Translation :: get('Find'), 'size="30" class="search_query"');
        $this->get_form()->addElement('style_submit_button', 'submit', Theme :: get_common_image('action_search'), array('class' => 'search'));
    }

    /*
	 * Inherited
	 */
    function as_html()
    {
        $this->renderer = clone $this->form->defaultRenderer();
        $this->renderer->setElementTemplate('<span>{element}</span> ');
        $this->form->accept($this->renderer);

        $html = array();
        $html[] = '<div class="search_form" style="float: right; clear: both;">';
        $html[] = '<div class="simple_search">';
        $html[] = $this->renderer->toHTML();
        $html[] = '</div>';
        $html[] = '</div>';

        $actions = $this->get_browser_actions();
        foreach ($actions as $key => $action)
        {
            $actions[$key]['href'] = str_replace('__ID__', '%d', $action['href']);
        }

        if ($this->get_maximum_select() > RepoViewer :: SELECT_SINGLE)
        {
            $html[] = '<b>' . sprintf(Translation :: get('SelectMaximumLO'), $this->get_maximum_select()) . '</b><br />';
        }

        $menu = $this->get_menu();
        $table = $this->get_object_table($actions);

        $html[] = '<br />';

        $html[] = '<div style="width: 15%; overflow: auto; float:left">';
        $html[] = $menu->render_as_tree();
        $html[] = '</div>';

        $html[] = '<div style="width: 83%; float: right;">';
        $html[] = $table->as_html();
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';

        return implode("\n", $html);
    }

    protected function get_object_table($actions)
    {
        return new ContentObjectTable($this, $this->get_user(), $this->get_types(), $this->get_query(), $actions);
    }

    /**
     * Returns the search query.
     * @return string|null The query, or null if none.
     */
    protected function get_query()
    {
        if ($this->get_form()->validate())
        {
            return $this->get_form()->exportValue(RepoViewer :: PARAM_QUERY);
        }

        if (Request :: get(RepoViewer :: PARAM_QUERY))
        {
            return Request :: get(RepoViewer :: PARAM_QUERY);
        }

        return null;
    }

    function get_browser_actions()
    {
        return $this->browser_actions;
    }

    function set_browser_actions($browser_actions)
    {
        $this->browser_actions = $browser_actions;
    }

    function get_form()
    {
        return $this->form;
    }

    function set_form($form)
    {
        $this->form = $form;
    }

    function get_menu()
    {
        $url = $this->get_url($this->get_parameters()) . '&category=%s';
        $extra = array(array('title' => Translation :: get('SharedContentObjects'), 'url' => $this->get_url(array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_BROWSER, 'category' => 1, 'sharedbrowser' => 1))), 'class' => '', OptionsMenuRenderer :: KEY_ID => 1));
        $menu = new ContentObjectCategoryMenu($this->get_user_id(), Request :: get('category') ? Request :: get('category') : 0, $url, $extra);
        return $menu;
    }

    function get_default_browser_actions()
    {
        $browser_actions = array();

        $browser_actions[] = array('href' => $this->get_url(
        array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_PUBLISHER, RepoViewer :: PARAM_ID => '__ID__')), false), 'img' => Theme :: get_common_image_path() . 'action_publish.png', 'label' => Translation :: get('Publish'));

        if (! Request :: get('sharedbrowser') == 1)
        {
            $browser_actions[] = array('href' => $this->get_url(array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_CREATOR, RepoViewer :: PARAM_EDIT_ID => '__ID__'))), 'img' => Theme :: get_common_image_path() . 'action_editpublish.png', 'label' => Translation :: get('EditAndPublish'));
        }

        return $browser_actions;
    }
}
?>