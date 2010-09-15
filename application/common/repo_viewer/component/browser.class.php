<?php
/**
 * $Id: browser.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.repo_viewer.component
 */
require_once dirname(__FILE__) . '/content_object_table/content_object_table.class.php';
/**
 * This class represents a encyclopedia repo_viewer component which can be used
 * to browse through the possible learning objects to publish.
 */
class RepoViewerBrowserComponent extends RepoViewer
{
    const SHARED_BROWSER = 'shared';

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

        $form_parameters = $this->get_parameter();
        $form_parameters[RepoViewer :: PARAM_ACTION] = RepoViewer :: ACTION_BROWSER;
        if ($this->is_shared_object_browser())
        {
            $form_parameters[self :: SHARED_BROWSER] = 1;
        }

        $this->set_form(new FormValidator('search', 'post', $this->get_url($form_parameters), '', array('id' => 'search'), false));
        $this->get_form()->addElement('hidden', RepoViewer :: PARAM_ACTION);
        $this->get_form()->addElement('text', RepoViewer :: PARAM_QUERY, Translation :: get('Find'), 'size="30" class="search_query"');
        $this->get_form()->addElement('style_submit_button', 'submit', Theme :: get_common_image('action_search'), array('class' => 'search'));
    }

    /*
	 * Inherited
	 */
    function run()
    {
    	$this->renderer = clone $this->form->defaultRenderer();
        $this->renderer->setElementTemplate('<span>{element}</span> ');
        $this->form->accept($this->renderer);

        $html = array();
        $html[] = '<div class="search_form" style="float: right; margin: 0px 0px 5px 0px;">';
        $html[] = '<div class="simple_search">';
        $html[] = $this->renderer->toHTML();
        $html[] = '</div>';
        $html[] = '</div>';

    	$toolbar = $this->get_default_browser_actions();

        $table_actions = $toolbar->get_items();
        foreach ($table_actions as $table_action)
        {
            $table_action->set_href(str_replace('__ID__', '%d', $table_action->get_href()));
        }

        if ($this->get_maximum_select() > RepoViewer :: SELECT_SINGLE)
        {
            $html[] = '<b>' . sprintf(Translation :: get('SelectMaximumNumberOfContentObjects'), $this->get_maximum_select()) . '</b><br />';
        }

        $menu = $this->get_menu();
        $table = $this->get_object_table($toolbar);

        $html[] = '<br />';

        $html[] = '<div style="width: 15%; overflow: auto; float:left">';
        $html[] = $menu->render_as_tree();
        $html[] = '</div>';

        $html[] = '<div style="width: 83%; float: right;">';
        $html[] = $table->as_html();
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';

        $this->display_header();
        echo implode("\n", $html);
        $this->display_footer();
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

        $extra = array();

        $shared = array();
        $shared['title'] = Translation :: get('SharedContentObjects');

        $extra_parameters = array();
        $extra_parameters[RepoViewer :: PARAM_ACTION] = RepoViewer :: ACTION_BROWSER;
        $extra_parameters['category'] = 1;

        if ($this->is_shared_object_browser())
        {
            $extra_parameters[self :: SHARED_BROWSER] = 1;
        }

        $shared['url'] = $this->get_url(array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_BROWSER, 'category' => 1, self :: SHARED_BROWSER => 1)));
        $shared['class'] = 'shared_objects';
        $shared[OptionsMenuRenderer :: KEY_ID] = 1;
        $extra[] = $shared;

        if ($this->get_query())
        {
            $search_url = '#';
            $search = array();

            if ($this->is_shared_object_browser())
            {
                $search['title'] = Translation :: get('SharedSearchResults');
            }
            else
            {
                $search['title'] = Translation :: get('SearchResults');
            }

            $search['url'] = $search_url;
            $search['class'] = 'search_results';
            $extra[] = $search;
        }
        else
        {
            $search_url = null;
        }

        $menu = new ContentObjectCategoryMenu($this->get_user_id(), Request :: get('category') ? Request :: get('category') : 0, $url, $extra, $this->get_types());

        if ($search_url)
        {
            $menu->forceCurrentUrl($search_url);
        }
        elseif($this->is_shared_object_browser())
        {
            $menu->forceCurrentUrl($shared['url']);
        }

        return $menu;
    }

    function get_default_browser_actions()
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Publish'),
        		Theme :: get_common_image_path() . 'action_publish.png',
        		$this->get_url(array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_PUBLISHER, RepoViewer :: PARAM_ID => '__ID__')), false),
        		ToolbarItem :: DISPLAY_ICON
        ));

        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Preview'),
        		Theme :: get_common_image_path() . 'action_browser.png',
        		$this->get_url(array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_VIEWER, RepoViewer :: PARAM_ID => '__ID__')), false),
        		ToolbarItem :: DISPLAY_ICON
        ));

        if (!$this->is_shared_object_browser())
        {
        	$toolbar->add_item(new ToolbarItem(
	        		Translation :: get('EditAndPublish'),
	        		Theme :: get_common_image_path() . 'action_editpublish.png',
	        		$this->get_url(array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_CREATOR, RepoViewer :: PARAM_EDIT_ID => '__ID__')), false),
	        		ToolbarItem :: DISPLAY_ICON
	        ));
        }

        return $toolbar;
    }

    function is_shared_object_browser()
    {
        return (Request :: get(self :: SHARED_BROWSER) == 1);
    }
    
   function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
   {
   		$breadcrumbtrail->add_help('repo_viewer_browser');
   }
   
   function get_additional_parameters()
   {
   	
   }
}
?>