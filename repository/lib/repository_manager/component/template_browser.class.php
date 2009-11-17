<?php
/**
 * $Id: template_browser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */

class RepositoryManagerTemplateBrowserComponent extends RepositoryManagerComponent
{
    private $action_bar;
    private $form;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->action_bar = $this->get_action_bar();
        $this->form = new RepositoryFilterForm($this, $this->get_url());
        
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseTemplates')));
        
        $output = $this->get_table_html();
        
        $session_filter = Session :: retrieve('filter');
        
        if ($session_filter != null && ! $session_filter == 0)
        {
            if (is_numeric($session_filter))
            {
                $condition = new EqualityCondition(UserView :: PROPERTY_ID, $session_filter);
                $user_view = RepositoryDataManager :: get_instance()->retrieve_user_views($condition)->next_result();
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Filter') . ': ' . $user_view->get_name()));
            }
            else
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Filter') . ': ' . Utilities :: underscores_to_camelcase(($session_filter))));
        }
        
        $this->display_header($trail);
        echo $this->action_bar->as_html();
        echo '<br />' . $this->form->display() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_table_html()
    {
        $condition = $this->get_condition();
        $parameters = $this->get_parameters(true);
        $table = new TemplateBrowserTable($this, $parameters, $condition);
        return $table->as_html();
    }

    function get_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, 0);
        
        $cond = $this->form->get_filter_conditions();
        if ($cond)
        {
            $conditions[] = $cond;
        }
        
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $or_conditions[] = new LikeCondition(ContentObject :: PROPERTY_TITLE, $query);
            $or_conditions[] = new LikeCondition(ContentObject :: PROPERTY_DESCRIPTION, $query);
            
            $conditions[] = new OrCondition($or_conditions);
        }
        
        return new AndCondition($conditions);
    
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }

}
?>
