<?php

require_once dirname(__FILE__) . '/rel_user_browser/rel_user_browser_table.class.php';
require_once dirname(__FILE__) . '/rel_group_browser/rel_group_browser_table.class.php';
require_once dirname(__FILE__) . '/user_browser/user_browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/publisher/publication_table/publication_table.class.php';

class InternshipOrganizerPeriodManagerViewerComponent extends InternshipOrganizerPeriodManager
{
    const TAB_COORDINATOR = 'cot';
    const TAB_STUDENT = 'stt';
    const TAB_COACH = 'cat';
    const TAB_PUBLICATIONS = 'put';
    
    private $period;
    private $ab;
    private $root_period;
    private $parent_period;
    private $parent_parent_id;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        
        $id = Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID);
//        $parent_id = Request :: get(InternshipOrganizerPeriodManager :: PARAM_PARENT_PERIOD_ID);
        
        if ($id)
        {
            $this->period = $this->retrieve_period($id);
            
            $this->root_period = $this->retrieve_periods(new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_PARENT_ID, 0))->next_result();
            
//            $this->parent_period = $this->retrieve_period($parent_id);
            
//            if ($parent_id)
//            {
//                $this->parent_parent_id = $this->parent_period->get_parent_id();
//            }
            
            $period = $this->period;
            
//            $parent_period = $this->parent_period;
            
//            $parent_parent_id = $this->parent_parent_id;
            
            $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
            $trail->add(new Breadcrumb($this->get_browse_periods_url(), Translation :: get('BrowseInternshipOrganizerPeriods')));
            
//            if ($parent_id && $parent_parent_id)
//            {
//                $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $parent_id, InternshipOrganizerPeriodManager :: PARAM_PARENT_PERIOD_ID => $parent_parent_id)), $parent_period->get_name()));
//            }
            
            $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $id)), $period->get_name()));
            $trail->add_help('period general');
            
            $this->display_header($trail);
            $this->ab = $this->get_action_bar();
            echo $this->ab->as_html() . '<br />';
            
            echo '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_period.png);">';
            echo '<div class="title">' . Translation :: get('Details') . '</div>';
            echo '<b>' . Translation :: get('Name') . '</b>: ' . $period->get_name();
            echo '<br /><b>' . Translation :: get('Description') . '</b>: ' . $period->get_description();
            echo '<b>' . Translation :: get('Begin') . '</b>: ' . DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatLong'), $period->get_begin());
            echo '<br /><b>' . Translation :: get('End') . '</b>: ' . DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatLong'), $period->get_end());
            
            echo '<div class="clear">&nbsp;</div>';
            echo '</div>';
            
            $users_tables = $this->get_users_types_tables();
            if ($users_tables)
            {
                echo $users_tables;
            }
            else
            {
                echo '<div class="title"><b>' . Translation :: get('NoUsers') . '</b><br/><br/></div>';
            }
            
            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }

    function get_action_bar()
    {
        $period = $this->period;
        
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $period->get_id())));
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_period_viewing_url($period), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_period_editing_url($period), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
//        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddUsers'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_period_subscribe_users_url($period), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        if ($this->period != $this->root_period)
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

    function get_users_types_tables()
    {
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID] = $this->period->get_id();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
        
        // Coordinator table tab
        $table = new InternshipOrganizerPeriodUserBrowserTable($this, $parameters, $this->get_users_condition(InternshipOrganizerUserType :: COORDINATOR), InternshipOrganizerUserType :: COORDINATOR);
        $tabs->add_tab(new DynamicContentTab(self :: TAB_COORDINATOR, Translation :: get('InternshipOrganizerCoordinator'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Student table tab
        $table = new InternshipOrganizerPeriodUserBrowserTable($this, $parameters, $this->get_users_condition(InternshipOrganizerUserType :: STUDENT), InternshipOrganizerUserType :: STUDENT);
        $tabs->add_tab(new DynamicContentTab(self :: TAB_STUDENT, Translation :: get('InternshipOrganizerStudent'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Coach table tab
        $table = new InternshipOrganizerPeriodUserBrowserTable($this, $parameters, $this->get_users_condition(InternshipOrganizerUserType :: COACH), InternshipOrganizerUserType :: COACH);
        $tabs->add_tab(new DynamicContentTab(self :: TAB_COACH, Translation :: get('InternshipOrganizerCoach'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Publications table tab
        $table = new InternshipOrganizerPublicationTable($this, $parameters, $this->get_publications_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_PUBLICATIONS, Translation :: get('InternshipOrganizerPublications'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    
    }

    function get_publications_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerPublication :: PROPERTY_PUBLICATION_PLACE, InternshipOrganizerPublicationPlace :: PERIOD);
        $conditions[] = new EqualityCondition(InternshipOrganizerPublication :: PROPERTY_PLACE_ID, $this->period->get_id());
        return new AndCondition($conditions);
    }

    function get_users_condition($user_type)
    {
        $query = $this->ab->get_query();
        $conditions = array();
              
        $user_ids = $this->period->get_user_ids($user_type);
        
        
        if (count($user_ids))
        {
            $conditions[] = new InCondition(User :: PROPERTY_ID, $user_ids);
        }
        else
        {
            $conditions[] = new EqualityCondition(User :: PROPERTY_ID, 0);
        }
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*');
            //            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*');
            $conditions[] = new OrCondition($search_conditions);
        }
        return new AndCondition($conditions);
    
    }
	
    function get_period(){
    	return $this->period;
    }
    
}
?>