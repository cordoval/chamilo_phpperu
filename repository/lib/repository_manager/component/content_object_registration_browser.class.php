<?php
/**
 * $Id: template_browser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */

class RepositoryManagerContentObjectRegistrationBrowserComponent extends RepositoryManager
{
    private $action_bar;
    private $form;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->action_bar = $this->get_action_bar();

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseContentObjectRegistrations')));

        $output = $this->get_table_html();
        $this->display_header();
        echo $this->action_bar->as_html();
        echo $output;
        $this->display_footer();
    }

    function get_table_html()
    {
        $condition = $this->get_condition();
        $parameters = $this->get_parameters(true);
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $table = new ContentObjectRegistrationBrowserTable($this, $parameters, $condition);
        return $table->as_html();
    }

    function get_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_CONTENT_OBJECT);

        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(Registration :: PROPERTY_NAME, '*' . $query . '*');
        }

        return new AndCondition($conditions);

    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageRights'), Theme :: get_common_image_path() . 'action_rights.png', $this->get_content_object_type_rights_editing_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

}
?>