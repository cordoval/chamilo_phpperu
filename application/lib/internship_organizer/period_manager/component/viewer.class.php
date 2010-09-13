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
    const TAB_AGREEMENT = 'agrt';
    const TAB_PUBLICATIONS = 'put';
    const TAB_DETAIL = 'det';
    
    private $period;
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_PERIOD, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $trail = BreadcrumbTrail :: get_instance();
        
        $id = Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID);
        
        if ($id)
        {
            $this->period = $this->retrieve_period($id);
            
            $this->root_period = $this->retrieve_periods(new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_PARENT_ID, 0))->next_result();
            
            $period = $this->period;
            $trail->add_help('period general');
            
            $this->display_header($trail);
            $this->ab = $this->get_action_bar();
            echo $this->ab->as_html();
            
            echo $this->get_tables();
            
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

    function get_tables()
    {
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID] = $this->period->get_id();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
        
        // Coordinator table tab
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_COORDINATOR;
        $table = new InternshipOrganizerPeriodUserBrowserTable($this, $parameters, $this->get_users_condition(InternshipOrganizerUserType :: COORDINATOR), InternshipOrganizerUserType :: COORDINATOR);
        $tabs->add_tab(new DynamicContentTab(self :: TAB_COORDINATOR, Translation :: get('InternshipOrganizerCoordinator'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Student table tab
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_STUDENT;
        $table = new InternshipOrganizerPeriodUserBrowserTable($this, $parameters, $this->get_users_condition(InternshipOrganizerUserType :: STUDENT), InternshipOrganizerUserType :: STUDENT);
        $tabs->add_tab(new DynamicContentTab(self :: TAB_STUDENT, Translation :: get('InternshipOrganizerStudent'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Coach table tab
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_COACH;
        $table = new InternshipOrganizerPeriodUserBrowserTable($this, $parameters, $this->get_users_condition(InternshipOrganizerUserType :: COACH), InternshipOrganizerUserType :: COACH);
        $tabs->add_tab(new DynamicContentTab(self :: TAB_COACH, Translation :: get('InternshipOrganizerCoach'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Agreement table tab
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_AGREEMENT;
        $table = new InternshipOrganizerPeriodRelAgreementBrowserTable($this, $parameters, $this->get_agreement_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_AGREEMENT, Translation :: get('InternshipOrganizerAgreement'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Publications table tab
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_PUBLICATIONS;
        $table = new InternshipOrganizerPublicationTable($this, $parameters, $this->get_publications_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_PUBLICATIONS, Translation :: get('InternshipOrganizerPublications'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Detail tab
        $tabs->add_tab(new DynamicContentTab(self :: TAB_DETAIL, Translation :: get('InternshipOrganizerDetail'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $this->get_detail($this->period)));
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    
    }

    function get_detail($period)
    {
        $html = array();
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_period.png);">';
        $html[] = '<div class="title">' . Translation :: get('Details') . '</div>';
        $html[] = '<b>' . Translation :: get('Name') . '</b>: ' . $period->get_name();
        $html[] = '<br /><b>' . Translation :: get('Description') . '</b>: ' . $period->get_description();
        $html[] = '<b>' . Translation :: get('Begin') . '</b>: ' . DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatLong'), $period->get_begin());
        $html[] = '<br /><b>' . Translation :: get('End') . '</b>: ' . DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatLong'), $period->get_end());
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

    function get_agreement_condition()
    {
        $condtions = array();
        $condtions[] = new EqualityCondition(InternshipOrganizerAgreement :: PROPERTY_PERIOD_ID, $this->period->get_id());
        $condtions[] = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_TYPE, InternshipOrganizerUserType :: STUDENT, InternshipOrganizerAgreementRelUser :: get_table_name());
        return new AndCondition($condtions);
    }

    function get_period()
    {
        return $this->period;
    }

}
?>