<?php

interface InternshipOrganizerDataManagerInterface
{

    function create_internship_organizer_location($location);

    function update_internship_organizer_location($location);

    function delete_internship_organizer_location($location);

    function count_locations($conditions = null);

    function retrieve_location($id);

    function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null);

    function create_internship_organizer_organisation($organisation);

    function update_internship_organizer_organisation($organisation);

    function delete_internship_organizer_organisation($organisation);

    function count_organisations($conditions = null);

    function retrieve_organisation($id);

    function retrieve_organisations($condition = null, $offset = null, $count = null, $order_property = null);

    function delete_internship_organizer_category($category);

    function delete_category_rel_location($categoryrellocation);

    function update_internship_organizer_category($category);

    function create_internship_organizer_category($category);

    function create_internship_organizer_category_rel_location($categoryrellocation);

    function count_categories($conditions = null);

    function count_category_rel_locations($conditions = null);

    function retrieve_internship_organizer_category($id);

    function truncate_category($id);

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_root_category();

    function retrieve_category_rel_location($location_id, $category_id);

    function retrieve_category_rel_locations($condition = null, $offset = null, $count = null, $order_property = null);

    
    function create_internship_organizer_moment($moment);

    function update_internship_organizer_moment($moment);

    function delete_internship_organizer_moment($moment);

    function count_moments($conditions = null);

    function retrieve_moment($id);

    function retrieve_moments($condition = null, $offset = null, $count = null, $order_property = null);

    function create_internship_organizer_agreement($organisation);

    function update_internship_organizer_agreement($organisation);

    function delete_internship_organizer_agreement($organisation);

    function count_agreements($conditions = null);

    function retrieve_agreement($id);

    function retrieve_agreements($condition = null, $offset = null, $count = null, $order_property = null);

    function delete_internship_organizer_region($region);

    function update_internship_organizer_region($region);

    function create_internship_organizer_region($region);

    function count_regions($conditions = null);

    function retrieve_internship_organizer_region($id);

    //     function truncate_region($id);
    

    function retrieve_regions($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_root_region();

    function count_mentors($conditions = null);

    function retrieve_mentor($id);

    function retrieve_mentors($condition = null, $offset = null, $count = null, $order_property = null);

    function delete_internship_organizer_period($period);

    function update_internship_organizer_period($period);

    function create_internship_organizer_period($period);

    function count_periods($conditions = null);

    function retrieve_internship_organizer_period($id);

    //     function truncate_period($id);
    

    function retrieve_periods($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_root_period();
    
    
    function count_period_rel_users($conditions = null);

    function retrieve_period_rel_users($condition = null, $offset = null, $count = null, $order_property = null);
    
    
    
}
?>