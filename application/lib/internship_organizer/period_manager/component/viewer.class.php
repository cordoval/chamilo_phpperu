<?php

class InternshipOrganizerPeriodManagerViewerComponent extends InternshipOrganizerPeriodManager
{
    private $period;
    private $ab;
    private $root_period;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();

        $id = Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID);
        if ($id)
        {
            $this->period = $this->retrieve_period($id);

            $this->root_period = $this->retrieve_periods(new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_PARENT_ID, 0))->next_result();

            $period = $this->period;

            if (! $this->get_user()->is_platform_admin())
            {
                Display :: not_allowed();
            }
           
            $trail->add(new Breadcrumb($this->get_browse_periods_url(), Translation :: get('BrowseInternshipOrganizerPeriods')));
            $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $id)), $period->get_name()));
            $trail->add_help('period general');

            $this->display_header($trail);
            $this->ab = $this->get_action_bar();
            echo $this->ab->as_html() . '<br />';

            echo '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_period.png);">';
            echo '<div class="title">' . Translation :: get('Details') . '</div>';
            echo '<b>' . Translation :: get('Name') . '</b>: ' . $period->get_name();
            echo '<br /><b>' . Translation :: get('Description') . '</b>: ' . $period->get_description();
            echo '<div class="clear">&nbsp;</div>';
            echo '</div>';
			$table = new InternshipOrganizerPeriodBrowserTable($this, array(Application :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_VIEW_PERIOD, InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $id), $this->get_condition());
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
        $conditions[] = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_PARENT_ID, Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID));

        $query = $this->ab->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerPeriod :: PROPERTY_NAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerPeriod :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $condition = new OrCondition($or_conditions);

            $periods = InternshipOrganizerDataManager::get_instance()->retrieve_periods($condition);
            while ($period = $periods->next_result())
            {
                $period_conditions[] = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_PERIOD_ID, $period->get_id());
            }

            if (count($period_conditions))
                $conditions[] = new OrCondition($period_conditions);
            else
                $conditions[] = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_PERIOD_ID, 0);

        }

        $condition = new AndCondition($conditions);

        return $condition;
    }

    function get_action_bar()
    {
        $period = $this->period;

        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $period->get_id())));
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_period_viewing_url($period), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_period_editing_url($period), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddUsers'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_period_subscribe_users_browser_url($period), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        if($this->period != $this->root_period)
        {
        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_period_delete_url($period), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

//        $condition = new EqualityCondition(InternshipOrganizerPeriodRelLocation :: PROPERTY_PERIOD_ID, $period->get_id());
//        $locations = $this->retrieve_period_rel_locations($condition);
//        $visible = ($locations->size() > 0);
//
//        if ($visible)
//        {
//            $toolbar_data[] = array('href' => $this->get_period_emptying_url($period), 'label' => Translation :: get('Truncate'), 'img' => Theme :: get_common_image_path() . 'action_recycle_bin.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
//            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Truncate'), Theme :: get_common_image_path() . 'action_recycle_bin.png', $this->get_period_emptying_url($period), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
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