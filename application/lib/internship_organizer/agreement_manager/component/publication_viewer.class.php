<?php

require_once dirname(__FILE__) . '/../agreement_manager.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/publisher/publication_table/publication_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/viewer.class.php';

class InternshipOrganizerAgreementManagerPublicationViewerComponent extends InternshipOrganizerAgreementManager
{
    
    const TAB_PUBLICATIONS = 'pub_tab';
    
    private $action_bar;
    private $publication;

    function run()
    {
        
        $publication_id = $_GET[InternshipOrganizerAgreementManager :: PARAM_PUBLICATION_ID];
        $this->publication = InternshipOrganizerDataManager :: get_instance()->retrieve_publication($publication_id);
        $place_id = $this->publication->get_place_id();
        
        $place = $this->publication->get_place();
        
        $trail = BreadcrumbTrail :: get_instance();
        // for agreements the places can be Agreement and Moment
        switch ($place)
        {
            case InternshipOrganizerPublicationPlace :: AGREEMENT :
                $place_object = $this->retrieve_agreement($place_id);

                //$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipOrganizerAgreements')));
                //$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $place_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_PUBLICATIONS)), $place_object->get_name()));
                //        //$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_PUBLICATION, InternshipOrganizerAgreementManager :: PARAM_PUBLICATION_ID => $publication_id)), $this->publication->get_name()));
                

                break;
            case InternshipOrganizerPublicationPlace :: MOMENT :
                $place_object = $this->retrieve_moment($place_id);
                break;
            default :
                //error: publication always needs a place_id and corrosponding place;
                break;
        }
        
        $content_object = $this->publication->get_content_object();
        
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header($trail);
        
        //        echo $this->action_bar->as_html();
        //        echo '<div id="action_bar_browser">';
        

        echo '<div>';
        $display = ContentObjectDisplay :: factory($content_object);
        echo $display->get_full_html();
        //        echo $this->get_tabs();
        echo '</div>';
        //        echo '</div>';
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
        
        //        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_MOMENT_ID => $this->moment->get_id())));
        

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