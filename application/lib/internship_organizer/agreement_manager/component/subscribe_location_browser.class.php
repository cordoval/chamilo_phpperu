<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/subscribe_location_browser/subscribe_location_browser_table.class.php';

class InternshipOrganizerAgreementManagerSubscribeLocationBrowserComponent extends InternshipOrganizerAgreementManager
{
    
    private $action_bar;
    private $agreement;

    function run()
    {
        
        $agreement_id = $_GET[InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID];
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: ADD_LOCATION_RIGHT, $agreement_id, InternshipOrganizerRights :: TYPE_AGREEMENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $this->agreement = $this->retrieve_agreement($agreement_id);
        
        $trail = BreadcrumbTrail :: get_instance();

        $this->action_bar = $this->get_action_bar();
        
        $this->display_header($trail);
        
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        
        echo '<div>';
        echo $this->get_table();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

    function get_table()
    {
        
        $parameters = $this->get_parameters();
        $parameters[InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID] = $this->agreement->get_id();
        $table = new InternshipOrganizerSubscribeLocationBrowserTable($this, $parameters, $this->get_condition());
        return $table->as_html();
    
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $this->agreement->get_id())));
        
        return $action_bar;
    }

    function get_condition()
    {
        
        $dm = InternshipOrganizerDataManager :: get_instance();
        $period_id = $this->agreement->get_period_id();
        $condition = new EqualityCondition(InternshipOrganizerCategoryRelPeriod :: PROPERTY_PERIOD_ID, $period_id);
        $category_rel_periods = $dm->retrieve_category_rel_periods($condition);
        $category_ids = array();
        while ($category_rel_period = $category_rel_periods->next_result())
        {
            $category_ids[] = $category_rel_period->get_category_id();
        }
        
        if (count($category_ids))
        {
            $conditions = array();
            $conditions[] = new InCondition(InternshipOrganizerCategoryRelLocation :: PROPERTY_CATEGORY_ID, $category_ids);
            
            $query = $this->action_bar->get_query();
            if (isset($query) && $query != '')
            {
                $search_conditions = array();
                $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_NAME, '*' . $query . '*', InternshipOrganizerLocation :: get_table_name());
                $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_DESCRIPTION, '*' . $query . '*', InternshipOrganizerLocation :: get_table_name());
                $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_ADDRESS, '*' . $query . '*', InternshipOrganizerLocation :: get_table_name());
                $search_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, '*' . $query . '*', InternshipOrganizerRegion :: get_table_name());
                $search_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_CITY_NAME, '*' . $query . '*', InternshipOrganizerRegion :: get_table_name());
                $conditions[] = new OrCondition($search_conditions);
            }
            return new AndCondition($conditions);
        }
        else
        {
            return null;
        }
    
    }

    function get_agreement()
    {
        return $this->agreement;
    }

}
?>