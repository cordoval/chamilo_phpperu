<?php

class InternshipOrganizerRegionManagerViewerComponent extends InternshipOrganizerRegionManager
{
    private $region;
    private $ab;
    private $root_region;
    private $parent_region;
    private $parent_parent_id;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();

        $id = Request :: get(InternshipOrganizerRegionManager :: PARAM_REGION_ID);
        $parent_id = Request :: get(InternshipOrganizerRegionManager :: PARAM_PARENT_REGION_ID);       
        
        if ($id)
        {
            $this->region = $this->retrieve_region($id);
            
            $this->parent_region = $this->retrieve_region($parent_id);
            
            if ($parent_id)
            {
            	$this->parent_parent_id = $this->parent_region->get_parent_id();            
            }
            	
            $this->root_region = $this->retrieve_regions(new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_PARENT_ID, 0))->next_result();
            
            $region = $this->region;
            
            $parent_region = $this->parent_region;
            
            $parent_parent_id = $this->parent_parent_id;
            
            if (! $this->get_user()->is_platform_admin())
            {
                Display :: not_allowed();
            }
           
            $trail->add(new Breadcrumb($this->get_browse_regions_url(), Translation :: get('BrowseInternshipOrganizerRegions')));
            
            if ($parent_id && $parent_parent_id)
            {
            	$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerRegionManager :: PARAM_REGION_ID => $parent_id, InternshipOrganizerRegionManager :: PARAM_PARENT_REGION_ID => $parent_parent_id)), $parent_region->get_city_name()));
            }
            
            $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerRegionManager :: PARAM_REGION_ID => $id, InternshipOrganizerRegionManager :: PARAM_PARENT_REGION_ID => $parent_id)), $region->get_city_name()));
            $trail->add_help('region general');

            $this->display_header($trail);
            $this->ab = $this->get_action_bar();
            echo $this->ab->as_html() . '<br />';

            echo '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_region.png);">';
            echo '<div class="title">' . Translation :: get('Details') . '</div>';
            echo '<b>' . Translation :: get('Name') . '</b>: ' . $region->get_city_name();
            echo '<br /><b>' . Translation :: get('Description') . '</b>: ' . $region->get_description();
            echo '<div class="clear">&nbsp;</div>';
            echo '</div>';
			$table = new InternshipOrganizerRegionBrowserTable($this, array(Application :: PARAM_ACTION => InternshipOrganizerRegionManager :: ACTION_VIEW_REGION, InternshipOrganizerRegionManager :: PARAM_REGION_ID => $id), $this->get_condition());
            echo $table->as_html();
            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }

	function get_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_PARENT_ID, Request :: get(InternshipOrganizerRegionManager :: PARAM_REGION_ID));

        $query = $this->ab->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_CITY_NAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $condition = new OrCondition($or_conditions);

            $regions = InternshipOrganizerDataManager::get_instance()->retrieve_regions($condition);
            while ($region = $regions->next_result())
            {
                $region_conditions[] = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_ID, $region->get_id());
            }

            if (count($region_conditions))
                $conditions[] = new OrCondition($region_conditions);
            else
                $conditions[] = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_ID, 0);

        }

        $condition = new AndCondition($conditions);

        return $condition;
    }

    function get_action_bar()
    {
        $region = $this->region;
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerRegionManager :: PARAM_REGION_ID => $region->get_id())));

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path () . 'action_add.png', $this->get_region_create_url($region->get_id()), ToolbarItem::DISPLAY_ICON_AND_LABEL ) );
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_region_viewing_url($region), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_region_editing_url($region), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		if($this->region != $this->root_region)
        {
        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_region_delete_url($region), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

//        $condition = new EqualityCondition(InternshipOrganizerRegionRelLocation :: PROPERTY_REGION_ID, $region->get_id());
//        $locations = $this->retrieve_region_rel_locations($condition);
//        $visible = ($locations->size() > 0);
//
//        if ($visible)
//        {
//            $toolbar_data[] = array('href' => $this->get_region_emptying_url($region), 'label' => Translation :: get('Truncate'), 'img' => Theme :: get_common_image_path() . 'action_recycle_bin.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
//            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Truncate'), Theme :: get_common_image_path() . 'action_recycle_bin.png', $this->get_region_emptying_url($region), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
//        }
//        else
//        {
//            $toolbar_data[] = array('label' => Translation :: get('TruncateNA'), 'img' => Theme :: get_common_image_path() . 'action_recycle_bin_na.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
//            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('TruncateNA'), Theme :: get_common_image_path() . 'action_recycle_bin_na.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
//        }

        return $action_bar;
    }

	function get_region() 
	{
		if (! $this->region) 
		{
			$region_id = Request::get ( InternshipOrganizerRegionManager::PARAM_REGION_ID );
			
			if (! $region_id) 
			{
				$this->region = $this->get_root_region()->get_id ();
			}else
			{
				$this->region = $region_id;
			}
		
		}
		
		return $this->region;
	}
    
}
?>