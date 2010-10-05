<?php

require_once dirname(__FILE__) . '/../agreement_manager.class.php';

require_once Path :: get_application_path() . 'internship_organizer/php/publisher/publication_table/publication_table.class.php';
require_once Path :: get_application_path() . 'internship_organizer/php/agreement_manager/component/viewer.class.php';

class InternshipOrganizerAgreementManagerMomentViewerComponent extends InternshipOrganizerAgreementManager
{
    
    const TAB_PUBLICATIONS = 1;
    const TAB_EVALUATIONS = 2;
    
    private $action_bar;
    private $moment;

    function run()
    {
        
        $moment_id = $_GET[self :: PARAM_MOMENT_ID];
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, $moment_id, InternshipOrganizerRights :: TYPE_MOMENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $this->moment = $this->retrieve_moment($moment_id);
        $agreement_id = $this->moment->get_agreement_id();
        $agreement = $this->retrieve_agreement($agreement_id);
        $trail = BreadcrumbTrail :: get_instance();
        
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header($trail);
        
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        
        echo '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_location.png);">';
        echo '<div class="title">' . Translation :: get('Details') . '</div>';
        echo '<b>' . Translation :: get('Name') . '</b>: ' . $this->moment->get_name();
        echo '<br /><b>' . Translation :: get('Description') . '</b>: ' . $this->moment->get_description();
        
        echo '<br /><br /><b>' . Translation :: get('Begin') . '</b>: ' . DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatLong'), $this->moment->get_begin());
        echo '<br /><b>' . Translation :: get('End') . '</b>: ' . DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatLong'), $this->moment->get_end());
        
        echo '<div class="clear">&nbsp;</div>';
        echo '</div>';
        
        echo '<div>';
        echo $this->get_tabs();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

    function get_tabs()
    {
        
        $html = array();
        $html[] = '<div>';
        
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[self :: PARAM_MOMENT_ID] = $this->moment->get_id();
        
        // Publications table tab
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_PUBLICATIONS;
        $table = new InternshipOrganizerPublicationTable($this, $parameters, $this->get_publications_condition(array(Document :: get_type_name())));
        $tabs->add_tab(new DynamicContentTab(self :: TAB_PUBLICATIONS, Translation :: get('InternshipOrganizerPublications'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_EVALUATIONS;
        $table = new InternshipOrganizerPublicationTable($this, $parameters, $this->get_publications_condition(array(Survey :: get_type_name())));
        $tabs->add_tab(new DynamicContentTab(self :: TAB_EVALUATIONS, Translation :: get('InternshipOrganizerEvaluations'), Theme :: get_image_path('internship_organizer') . 'place_mini_survey.png', $table->as_html()));
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url(array(self :: PARAM_MOMENT_ID => $this->moment->get_id())));
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_PUBLISH, $this->moment->get_id(), InternshipOrganizerRights :: TYPE_MOMENT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_moment_publish_url($this->moment), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        return $action_bar;
    }

    function get_publications_condition($types = array(''))
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerPublication :: PROPERTY_PUBLICATION_PLACE, InternshipOrganizerPublicationPlace :: MOMENT);
        $conditions[] = new EqualityCondition(InternshipOrganizerPublication :: PROPERTY_PLACE_ID, $this->moment->get_id());
        $conditions[] = new InCondition(InternshipOrganizerPublication :: PROPERTY_CONTENT_OBJECT_TYPE, $types);
        
        $query = $this->action_bar->get_query();
        
        if (isset($query) && $query != '')
        {
            
            $publication_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerPublication :: get_table_name());
            $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
            $object_alias = RepositoryDataManager :: get_instance()->get_alias(ContentObject :: get_table_name());
            
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*', $user_alias, true);
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*', $user_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerPublication :: PROPERTY_NAME, '*' . $query . '*', $publication_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerPublication :: PROPERTY_DESCRIPTION, '*' . $query . '*', $publication_alias, true);
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*', $object_alias, true);
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*', $object_alias, true);
            $conditions[] = new OrCondition($search_conditions);
        }
        
        return new AndCondition($conditions);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipOrganizerAgreements')));
        $moment_id = Request :: get(self :: PARAM_MOMENT_ID);
        $moment = $this->retrieve_moment($moment_id);
        $agreement_id = $moment->get_agreement_id();
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_MOMENTS)), Translation :: get('ViewInternshipOrganizerAgreement')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_AGREEMENT_ID, self :: PARAM_MOMENT_ID);
    }

}
?>