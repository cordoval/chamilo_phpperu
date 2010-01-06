<?php
/**
 * $Id: rights_templater.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_template_manager.component
 */
require_once Path :: get_rights_path() . 'lib/rights_template_manager/component/location_rights_template_browser_table/location_rights_template_browser_table.class.php';

class RightsTemplateManagerRightsTemplaterComponent extends RightsTemplateManagerComponent
{
    private $action_bar;
    
    private $application;
    private $location;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->application = Request :: get(RightsTemplateManager :: PARAM_SOURCE);
        $location = Request :: get(RightsTemplateManager :: PARAM_LOCATION);
        $rights_template = Request :: get(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID);
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => RightsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Rights')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES)), Translation :: get('UserRights')));
        
        if (! isset($this->application))
        {
            $this->application = 'admin';
        }
        
        $conditions = array();
        $conditions[] = new EqualityCondition(Location :: PROPERTY_PARENT, 0);
        $conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->application);
        $condition = new AndCondition($conditions);
        $root = RightsDataManager :: get_instance()->retrieve_locations($condition, null, 1, array(new ObjectTableOrder(Location :: PROPERTY_LOCATION)))->next_result();
        
        if (isset($location))
        {
            $this->location = $this->retrieve_location($location);
        }
        else
        {
            $this->location = $root;
        }
        
        $parents = array_reverse($this->location->get_parents()->as_array());
        foreach ($parents as $parent)
        {
            $trail->add(new Breadcrumb($this->get_url(array('location' => $parent->get_id())), $parent->get_location()));
        }
        
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header($trail);
        
        $html = array();
        $application_url = $this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_SOURCE => Application :: PLACEHOLDER_APPLICATION));
        $html[] = Application :: get_selecter($application_url, $this->application);
        $html[] = $this->action_bar->as_html() . '<br />';
        
        $url_format = $this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_CONFIGURE_LOCATION_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_SOURCE => $this->application, RightsTemplateManager :: PARAM_LOCATION => '%s'));
        $url_format = str_replace('=%25s', '=%s', $url_format);
        $location_menu = new LocationRightMenu($root->get_id(), $this->location->get_id(), $url_format);
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $location_menu->render_as_tree();
        $html[] = '</div>';
        
        $table = new LocationRightsTemplateBrowserTable($this, $this->get_parameters(), $this->get_condition());
        
        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = $table->as_html();
        $html[] = RightsUtilities :: get_rights_legend();
        $html[] = '</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'rights/javascript/configure_rights_template.js');
        
        echo implode("\n", $html);
        
        $this->display_footer();
    }

    function get_condition()
    {
        return null;
        
        $condition = new EqualityCondition(Location :: PROPERTY_PARENT, $this->location->get_id());
        
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $and_conditions = array();
            $and_conditions[] = $condition;
            $and_conditions[] = new PatternMatchCondition(Location :: PROPERTY_LOCATION, '*' . $query . '*');
            $condition = new AndCondition($and_conditions);
        }
        
        return $condition;
    }

    function get_source()
    {
        return $this->application;
    }

    function get_location()
    {
        return $this->location;
    }

    function get_applications()
    {
        $application = $this->application;
        
        $html = array();
        
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/application.js');
        $html[] = '<div class="configure">';
        
        $the_applications = WebApplication :: load_all();
        $the_applications = array_merge(array('admin', 'tracking', 'repository', 'user', 'group', 'rights', 'home', 'menu', 'webservice', 'reporting'), $the_applications);
        
        foreach ($the_applications as $the_application)
        {
            if (isset($application) && $application == $the_application)
            {
                $html[] = '<div class="application_current">';
            }
            else
            {
                $html[] = '<div class="application">';
            }
            
            $application_name = Translation :: get(Utilities :: underscores_to_camelcase($the_application));
            
            $html[] = '<a href="' . $this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_SOURCE => $the_application)) . '">';
            $html[] = '<img src="' . Theme :: get_image_path('admin') . 'place_' . $the_application . '.png" border="0" style="vertical-align: middle;" alt="' . $application_name . '" title="' . $application_name . '"/><br />' . $application_name;
            $html[] = '</a>';
            $html[] = '</div>';
        }
        
        $html[] = '</div>';
        $html[] = '<div style="clear: both;"></div>';
        
        return implode("\n", $html);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url(array(UserRightManager :: PARAM_SOURCE => $this->application, RightsTemplateManager :: PARAM_LOCATION => $this->location->get_id())));
        
        return $action_bar;
    }
}
?>