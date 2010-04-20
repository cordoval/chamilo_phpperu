<?php

class InternshipOrganizerRegionManagerViewerComponent extends InternshipOrganizerRegionManagerComponent
{
    private $region;
    private $ab;
    private $root_region;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();

        $id = Request :: get(InternshipOrganizerRegionManager :: PARAM_REGION_ID);
        if ($id)
        {
            $this->region = $this->retrieve_region($id);

            $this->root_region = $this->retrieve_regions(new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_PARENT_ID, 0))->next_result();

            $region = $this->region;

            if (! $this->get_user()->is_platform_admin())
            {
                Display :: not_allowed();
            }
           
            $trail->add(new Breadcrumb($this->get_browse_regions_url(), Translation :: get('BrowseInternshipOrganizerRegions')));
            $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerRegionManager :: PARAM_REGION_ID => $id)), $region->get_name()));
            $trail->add_help('region general');

            $this->display_header($trail);
            $this->ab = $this->get_action_bar();
            echo $this->ab->as_html() . '<br />';

            echo '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_region.png);">';
            echo '<div class="title">' . Translation :: get('Details') . '</div>';
            echo '<b>' . Translation :: get('Name') . '</b>: ' . $region->get_name();
            echo '<br /><b>' . Translation :: get('Description') . '</b>: ' . $region->get_description();
            echo '<div class="clear">&nbsp;</div>';
            echo '</div>';

            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }

	function get_condition() {
		$condition = new EqualityCondition ( InternshipOrganizerRegion::PROPERTY_PARENT_ID, $this->get_region () );
		
		$query = $this->ab->get_query ();
		if (isset ( $query ) && $query != '') {
			$or_conditions = array ();
			$or_conditions [] = new PatternMatchCondition ( InternshipOrganizerRegion::PROPERTY_NAME, '*' . $query . '*' );
			$or_conditions [] = new PatternMatchCondition ( InternshipOrganizerRegion::PROPERTY_DESCRIPTION, '*' . $query . '*' );
			$or_condition = new OrCondition ( $or_conditions );
			
			$and_conditions = array ();
			$and_conditions [] = $condition;
			$and_conditions [] = $or_condition;
			$condition = new AndCondition ( $and_conditions );
		}
		
		return $condition;
	} //get_condition overgenomen van browser.class.php

    function get_action_bar()
    {
        $region = $this->region;

        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerRegionManager :: PARAM_REGION_ID => $region->get_id())));

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

}
?>