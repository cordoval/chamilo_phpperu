<?php
/**
 * $Id: webservice_browser.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib.webservice_manager.component
 */

/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class WebserviceManagerWebserviceBrowserComponent extends WebserviceManager implements AdministrationComponent
{
    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category = WebserviceDataManager :: get_instance()->retrieve_webservice_category($this->get_webservice_category());
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }

        $this->action_bar = $this->get_action_bar();
        $output = $this->get_user_html();
        $menu = $this->get_menu_html();

        $this->display_header();
        echo '<br />' . $this->action_bar->as_html() . '<br />';
        echo $output;
        echo $menu;
        $this->display_footer();

    }

    function get_user_html()
    {
        $parameters = $this->get_parameters();
    	$parameters[WebserviceManager :: PARAM_WEBSERVICE_CATEGORY_ID] = $this->get_webservice_category();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        
    	$table = new WebserviceBrowserTable($this, $parameters, $this->get_condition());

        $html = array();
        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    function get_menu_html()
    {
        $webservice_category_menu = new WebserviceCategoryMenu($this->get_webservice_category());
        $html = array();
        $html[] = '<div style="float: left; width: 20%;">';
        $html[] = $webservice_category_menu->render_as_tree();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    function get_condition()
    {
        $condition = new EqualityCondition(WebserviceCategory :: PROPERTY_PARENT, $this->get_webservice_category());

        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $or_conditions = array();
            $or_conditions[] = new PatternMatchCondition(WebserviceCategory :: PROPERTY_NAME, '*' . $query . '*');
            $or_condition = new OrCondition($or_conditions);

            $and_conditions = array();
            $and_conditions[] = $condition;
            $and_conditions[] = $or_condition;
            $condition = new AndCondition($and_conditions);
        }

        return $condition;
    }

    function get_webservice()
    {

        return (Request :: get(WebserviceManager :: PARAM_WEBSERVICE_ID) ? Request :: get(WebserviceManager :: PARAM_WEBSERVICE_ID) : 0);
    }

    function get_webservice_category()
    {
        return (Request :: get(WebserviceManager :: PARAM_WEBSERVICE_CATEGORY_ID) ? Request :: get(WebserviceManager :: PARAM_WEBSERVICE_CATEGORY_ID) : 0);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url(array(WebserviceManager :: PARAM_WEBSERVICE_CATEGORY_ID => $this->get_webservice_category())));
        $action_bar->add_common_action($this->get_tool_bar_item($this->get_webservice_category()));

        return $action_bar;
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('webservice_rights_editor');
    }
}
?>