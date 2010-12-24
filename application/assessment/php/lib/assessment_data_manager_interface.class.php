<?php

namespace application\assessment;


interface AssessmentDataManagerInterface
{

    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function create_assessment_publication($assessment_publication);

    function update_assessment_publication($assessment_publication);

    function delete_assessment_publication($assessment_publication);

    function count_assessment_publications($conditions = null);

    function retrieve_assessment_publication($id);

    function retrieve_assessment_publications($condition = null, $offset = null, $count = null, $order_property = null);

    function create_assessment_publication_category($assessment_category);

    function update_assessment_publication_category($assessment_category);

    function delete_assessment_publication_category($assessment_category);

    function count_assessment_publication_categories($conditions = null);

    function retrieve_assessment_publication_category($id);

    function retrieve_assessment_publication_categories($condition = null, $offset = null, $count = null, $order_property = null);

    function select_next_assessment_publication_category_display_order($parent);
  
}
?>