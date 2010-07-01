<?php

require_once dirname(__FILE__) . '/../agreement_manager.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/publisher/publication_table/publication_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/viewer.class.php';


class InternshipOrganizerAgreementManagerMomentViewerComponent extends InternshipOrganizerAgreementManager
{
    
   const TAB_PUBLICATIONS = 'pub_tab';
    
    private $action_bar;
    private $moment;

    function run()
    {
        
        $moment_id = $_GET[InternshipOrganizerAgreementManager :: PARAM_MOMENT_ID];
        $this->moment = $this->retrieve_moment($moment_id);
        $agreement_id = $this->moment->get_agreement_id();
        $agreement = $this->retrieve_agreement($agreement_id);
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipOrganizerAgreements')));
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id, DynamicTabsRenderer::PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent:: TAB_MOMENTS)), $agreement->get_name()));
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_MOMENT, InternshipOrganizerAgreementManager :: PARAM_MOMENT_ID => $moment_id)), $this->moment->get_name()));
        
        
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
        $parameters[InternshipOrganizerAgreementManager :: PARAM_MOMENT_ID] = $this->moment->get_id();
       
        // Publications table tab
        $table = new InternshipOrganizerPublicationTable($this, $parameters, $this->get_publications_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_PUBLICATIONS, Translation :: get('InternshipOrganizerPublications'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
          
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
            
        
        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_MOMENT_ID => $this->moment->get_id())));
        
        return $action_bar;
    }
   

    function get_publications_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerPublication :: PROPERTY_PUBLICATION_PLACE, InternshipOrganizerPublicationPlace :: MOMENT);
        $conditions[] = new EqualityCondition(InternshipOrganizerPublication :: PROPERTY_PLACE_ID, $this->moment->get_id());
        return new AndCondition($conditions);
    }

}
?>