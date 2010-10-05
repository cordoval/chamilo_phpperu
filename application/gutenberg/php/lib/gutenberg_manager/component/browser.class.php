<?php
/**
 * $Id: browser.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.gutenberg.gutenberg_manager.component
 */
require_once dirname(__FILE__) . '/../gutenberg_manager.class.php';
require_once dirname(__FILE__) . '/../../gutenberg_publication_renderer.class.php';

class GutenbergManagerBrowserComponent extends GutenbergManager
{
    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
    	$trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Gutenberg')));
        $trail->add_help('gutenberg general');
        
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header($trail);
        echo '<a name="top"></a>';
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        $renderer = GutenbergPublicationRenderer :: factory($this->get_renderer(), $this);
        echo $renderer->as_html();
        echo '</div>';
        $this->display_footer();
    }

    function get_condition()
    {
        $conditions = array();
        $user = $this->get_user();
        
        $subselect_conditions = array();
        $subselect_conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, ComicBook :: get_type_name());
        
        $query = $this->action_bar->get_query();
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $subselect_conditions[] = new OrCondition($search_conditions);
        }
        
        $subselect_condition = new AndCondition($subselect_conditions);
        $conditions[] = new SubselectCondition(GutenbergPublication :: PROPERTY_CONTENT_OBJECT, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
        
        if (! $user->is_platform_admin())
        {
            $visibility = array();
            $visibility[] = new EqualityCondition(GutenbergPublication :: PROPERTY_HIDDEN, false);
            $visibility[] = new EqualityCondition(GutenbergPublication :: PROPERTY_PUBLISHER, $user->get_id());
            $conditions[] = new OrCondition($visibility);
            
            $dates = array();
            $dates[] = new AndCondition(array(new InequalityCondition(GutenbergPublication :: PROPERTY_FROM_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, time()), new InequalityCondition(GutenbergPublication :: PROPERTY_TO_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, time())));
            $dates[] = new EqualityCondition(GutenbergPublication :: PROPERTY_PUBLISHER, $user->get_id());
            $conditions[] = new OrCondition($dates);
        }
        
        return new AndCondition($conditions);
    }

    function get_action_bar()
    {
        if (! isset($this->action_bar))
        {
            $this->action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
            $this->action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(Application :: PARAM_ACTION => GutenbergManager :: ACTION_CREATE_PUBLICATION))));
            
            $renderers = $this->get_available_renderers();
            
            if (count($renderers) > 1)
            {
                foreach ($renderers as $renderer)
                {
                    $this->action_bar->add_tool_action(new ToolbarItem(Translation :: get(Utilities :: underscores_to_camelcase($renderer) . 'View'), Theme :: get_image_path() . 'view_' . $renderer . '.png', $this->get_url(array(GutenbergManager :: PARAM_RENDERER => $renderer)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                }
            }
            
            $this->action_bar->set_search_url($this->get_url());
        }
        return $this->action_bar;
    }
}
?>