<?php
/**
 * $Id: browser.class.php 226 2009-11-13 14:44:03Z chellee $
 * @package help.lib.help_manager.component
 */
/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */

require_once dirname(__FILE__) . '/../../help_rights.class.php';

class HelpManagerBrowserComponent extends HelpManager implements AdministrationComponent
{
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }

        $this->ab = $this->get_action_bar();
        $output = $this->get_user_html();

        $this->display_header();
        echo '<br />' . $this->ab->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_user_html()
    {
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
        
    	$table = new HelpItemBrowserTable($this, $parameters, $this->get_condition());

        $html = array();
        $html[] = '<div style="float: right; width: 100%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    function get_help_item()
    {
        return (Request :: get(HelpManager :: PARAM_HELP_ITEM) ? Request :: get(HelpManager :: PARAM_HELP_ITEM) : 0);
    }

    function get_condition()
    {
        $query = $this->ab->get_query();
        if (isset($query) && $query != '')
        {
            $condition = new PatternMatchCondition(HelpItem :: PROPERTY_NAME, '*' . $query . '*');
        }

        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(HelpManager :: PARAM_HELP_ITEM => $this->get_help_item())));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(HelpManager :: PARAM_HELP_ITEM => $this->get_help_item())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('help_browser');
    }
}
?>