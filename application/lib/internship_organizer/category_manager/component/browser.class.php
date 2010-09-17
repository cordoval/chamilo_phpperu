<?php
require_once dirname(__FILE__) . '/../category_manager.class.php';
require_once dirname(__FILE__) . '/browser/browser_table.class.php';

class InternshipOrganizerCategoryManagerBrowserComponent extends InternshipOrganizerCategoryManager
{
    
    const TAB_SUB_CATEGORIES = 'sc';
    const TAB_LOCATIONS = 'lo';
    const TAB_DETAIL = 'dt';
    
    private $ab;
    private $category_id;
    private $root_category;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_CATEGORY, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('category general');
        
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
        
        $html = array();
        $html[] = '<div style="float: right; width: 80%;">';
        
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
        $parameters[InternshipOrganizerCategoryManager::PARAM_CATEGORY_ID] = $this->get_category();
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_LOCATIONS;
        $table = new InternshipOrganizerCategoryRelLocationBrowserTable($this, $parameters, $this->get_location_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_LOCATIONS, Translation :: get('Locations'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Subcategory table tab
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_SUB_CATEGORIES;
        $table = new InternshipOrganizerCategoryBrowserTable($this, $this->get_parameters(), $this->get_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_SUB_CATEGORIES, Translation :: get('SubCategories'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $tabs->add_tab(new DynamicContentTab(self :: TAB_DETAIL, Translation :: get('Detail'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $this->get_category_info()));
        
        $html[] = $tabs->render();
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_menu_html()
    {
        $category_menu = new InternshipOrganizerCategoryMenu($this->get_category());
        $html = array();
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $category_menu->render_as_tree();
        $html[] = '</div>';
        
        return implode($html, "\n");
    }

    function get_category_info()
    {
        
        $category = $this->retrieve_category($this->get_category());
        
        $html = array();
        
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_CATEGORY, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_category_editing_url($category), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        if ($category->get_parent_id() != 0)
        {
            if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, InternshipOrganizerRights :: LOCATION_CATEGORY, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_category_delete_url($category), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }
        
        $html[] = '<b>' . Translation :: get('Name') . '</b>: ' . $category->get_name() . '<br />';
        
        $description = $category->get_description();
        if ($description)
        {
            $html[] = '<b>' . Translation :: get('Description') . '</b>: ' . $description . '<br />';
        }
        
        $html[] = '<br />';
        $html[] = $toolbar->as_html();
        
        return implode("\n", $html);
    }

    function get_category()
    {
        if (! $this->category_id)
        {
            $this->category_id = Request :: get(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID);
            
            if (! $this->category_id)
            {
                $this->category_id = $this->get_root_category()->get_id();
            }
        
        }
        return $this->category_id;
    }

    function get_root_category()
    {
        if (! $this->root_category)
        {
            $this->root_category = $this->retrieve_root_category();
        }
        
        return $this->root_category;
    }

    function get_condition()
    {
        $condition = new EqualityCondition(InternshipOrganizerCategory :: PROPERTY_PARENT_ID, $this->get_category());
        
        $query = $this->ab->get_query();
        if (isset($query) && $query != '')
        {
            $or_conditions = array();
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerCategory :: PROPERTY_NAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerCategory :: PROPERTY_DESCRIPTION, '*' . $query . '*');
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
        
        $category = $this->retrieve_category($this->get_category());
        
        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID => $this->get_category())));
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ViewRoot'), Theme :: get_common_image_path() . 'action_home.png', $this->get_browse_categories_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_ADD, InternshipOrganizerRights :: LOCATION_CATEGORY, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerCategory'), Theme :: get_common_image_path() . 'action_create.png', $this->get_create_category_url($this->get_category()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddInternshipOrganizerLocation'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_category_subscribe_location_browser_url($category), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            $condition = new EqualityCondition(InternshipOrganizerCategoryRelLocation :: PROPERTY_CATEGORY_ID, $category->get_id());
            $locations = $this->retrieve_category_rel_locations($condition);
            $visible = ($locations->size() > 0);
            
            if ($visible)
            {
                $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Truncate'), Theme :: get_common_image_path() . 'action_recycle_bin.png', $this->get_category_emptying_url($category), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
            else
            {
                $action_bar->add_tool_action(new ToolbarItem(Translation :: get('TruncateNA'), Theme :: get_common_image_path() . 'action_recycle_bin_na.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        
        }
              
        return $action_bar;
    }

    function get_location_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerCategoryRelLocation :: PROPERTY_CATEGORY_ID, $this->get_category());
        
        $query = $this->ab->get_query();
        
        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_NAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_ADDRESS, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            
            $search_city_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_CITY_NAME, '*' . $query . '*');
            $search_city_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, '*' . $query . '*');
            $city_conditions = new OrCondition($search_city_conditions);
            
            $search_city_subselect_condition = new SubselectCondition(InternshipOrganizerLocation :: PROPERTY_REGION_ID, InternshipOrganizerRegion :: PROPERTY_ID, InternshipOrganizerRegion :: get_table_name(), $city_conditions);
            $or_conditions[] = $search_city_subselect_condition;
            
            //            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_CITY_NAME, '*' . $query . '*');
            //            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, '*' . $query . '*');
            

            //$or_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_CITY, '*' . $query . '*');
            //$or_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_STREET, '*' . $query . '*');
            //$or_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_STREET_NUMBER, '*' . $query . '*');
            $condition = new OrCondition($or_conditions);
            
            $locations = InternshipOrganizerDataManager :: get_instance()->retrieve_locations($condition);
            while ($location = $locations->next_result())
            {
                $location_conditions[] = new EqualityCondition(InternshipOrganizerCategoryRelLocation :: PROPERTY_LOCATION_ID, $location->get_id());
            }
            
            if (count($location_conditions))
                $conditions[] = new OrCondition($location_conditions);
            else
                $conditions[] = new EqualityCondition(InternshipOrganizerCategoryRelLocation :: PROPERTY_LOCATION_ID, 0);
        
        }
        
        $condition = new AndCondition($conditions);
        
        return $condition;
    }

}
?>