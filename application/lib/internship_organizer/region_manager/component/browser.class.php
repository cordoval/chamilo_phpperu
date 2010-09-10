<?php
require_once dirname(__FILE__) . '/../region_manager.class.php';
require_once dirname(__FILE__) . '/browser/browser_table.class.php';

class InternshipOrganizerRegionManagerBrowserComponent extends InternshipOrganizerRegionManager
{
    private $ab;
    private $region;
    private $parent_region;
    private $root_region;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $trail = BreadcrumbTrail :: get_instance();
        //$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
        //$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseInternshipOrganizerRegions')));
        $trail->add_help('region general');
        
        $this->ab = $this->get_action_bar();
        
        $menu = $this->get_menu_html();
        $output = $this->get_browser_html();
        
        $this->display_header($trail);
        echo $this->ab->as_html() . '<br />';
        echo $menu;
        echo $output;
        $this->display_footer();
    }

    function get_browser_html()
    {
        $table = new InternshipOrganizerRegionBrowserTable($this, $this->get_parameters(), $this->get_condition());
        
        $html = array();
        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_menu_html()
    {
        $region_menu = new InternshipOrganizerRegionMenu($this->get_region());
        $html = array();
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $region_menu->render_as_tree();
        $html[] = '</div>';
        
        return implode($html, "\n");
    }

    function get_region()
    {
        if (! $this->region)
        {
            $region_id = Request :: get(InternshipOrganizerRegionManager :: PARAM_REGION_ID);
            $region_parent_id = Request :: get(InternshipOrganizerRegionManager :: PARAM_PARENT_REGION_ID);
            
            if (! $region_id && ! $region_parent_id)
            {
                $this->region = $this->get_root_region()->get_id();
            }
            else
            {
                if ($region_parent_id)
                {
                    $this->region = $region_parent_id;
                }
                else
                {
                    $this->region = $region_id;
                }
            }
        
        }
        
        return $this->region;
    }

    function get_root_region()
    {
        if (! $this->root_region)
        {
            $this->root_region = $this->retrieve_root_region();
        }
        
        return $this->root_region;
    }

    function get_condition()
    {
        $condition = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_PARENT_ID, $this->get_region());
        
        $query = $this->ab->get_query();
        if (isset($query) && $query != '')
        {
            $or_conditions = array();
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_CITY_NAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $or_condition = new OrCondition($or_conditions);
            
            $and_conditions = array();
            $and_conditions[] = $condition;
            $and_conditions[] = $or_condition;
            $condition = new AndCondition($and_conditions);
        }
        
        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerRegionManager :: PARAM_REGION_ID => $this->get_region())));
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerRegion'), Theme :: get_common_image_path() . 'action_create.png', $this->get_region_create_url($this->get_region()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ViewRoot'), Theme :: get_common_image_path() . 'action_home.png', $this->get_browse_regions_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_browse_regions_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }
}
?>