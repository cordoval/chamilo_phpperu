<?php
require_once dirname(__FILE__) . '/../region_manager.class.php';
require_once dirname(__FILE__) . '/browser/browser_table.class.php';

class InternshipOrganizerRegionManagerBrowserComponent extends InternshipOrganizerRegionManager
{
    
    const TAB_SUB_REGIONS = 'sr';
    const TAB_DETAIL = 'dt';
    
    private $action_bar;
    private $region;
    private $parent_region;
    private $root_region;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_REGION, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $this->action_bar = $this->get_action_bar();
        
        $menu = $this->get_menu_html();
        $output = $this->get_browser_html();
        
        $this->display_header();
        echo $this->action_bar->as_html() . '<br />';
        echo $menu;
        echo $output;
        $this->display_footer();
    }

    function get_browser_html()
    {
        
        $html = array();
        $html[] = '<div style="float: right; width: 80%;">';
        
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        
        // Subregion table tab
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_SUB_REGIONS;
        $table = new InternshipOrganizerRegionBrowserTable($this, $parameters, $this->get_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_SUB_REGIONS, Translation :: get('SubRegions'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $tabs->add_tab(new DynamicContentTab(self :: TAB_DETAIL, Translation :: get('Detail'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $this->get_region_info()));
        
        $html[] = $tabs->render();
        
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

    function get_region_info()
    {
        
        $region = $this->retrieve_region($this->get_region());
        
        $html = array();
        
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_REGION, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_region_editing_url($region), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        if ($region->get_parent_id() != 0)
        {
            if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, InternshipOrganizerRights :: LOCATION_REGION, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_region_delete_url($region), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }
        
        $html[] = '<b>' . Translation :: get('ZipCode') . '</b>: ' . $region->get_zip_code() . '<br />';
        $html[] = '<b>' . Translation :: get('City') . '</b>: ' . $region->get_city_name() . '<br />';
        
        $description = $region->get_description();
        if ($description)
        {
            $html[] = '<b>' . Translation :: get('Description') . '</b>: ' . $description . '<br />';
        }
        
        $html[] = '<br />';
        $html[] = $toolbar->as_html();
        
        return implode("\n", $html);
    }

    function get_region()
    {
        if (! $this->region)
        {
            $region_id = Request :: get(self :: PARAM_REGION_ID);
            
            if (! $region_id)
            {
                $this->region = $this->get_root_region()->get_id();
            }
            else
            {
                $this->region = $region_id;
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
        
        $query = $this->action_bar->get_query();
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
        
        $action_bar->set_search_url($this->get_url(array(self :: PARAM_REGION_ID => $this->get_region())));
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ViewRoot'), Theme :: get_common_image_path() . 'action_home.png', $this->get_browse_regions_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_ADD, InternshipOrganizerRights :: LOCATION_REGION, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerRegion'), Theme :: get_common_image_path() . 'action_create.png', $this->get_region_create_url($this->get_region()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
       
        return $action_bar;
    }
}
?>