<?php

require_once dirname(__FILE__) . '/../survey_data_manager.class.php';
require_once dirname(__FILE__) . '/survey_publication_category.class.php';

class SurveyPublicationCategoryManager extends CategoryManager
{

    function SurveyPublicationCategoryManager($parent, $trail)
    {
        parent :: __construct($parent, $trail);
    }

    function get_category()
    {
        return new SurveyPublicationCategory();
    }

    function count_categories($condition)
    {
        $dm = SurveyDataManager :: get_instance();
        return $dm->count_survey_publication_categories($condition);
    }

    function retrieve_categories($condition, $offset, $count, $order_property)
    {
        $dm = SurveyDataManager :: get_instance();
        return $dm->retrieve_survey_publication_categories($condition, $offset, $count, $order_property);
    }

    function get_next_category_display_order($parent_id)
    {
        $dm = SurveyDataManager :: get_instance();
        return $dm->select_next_survey_publication_category_display_order($parent_id);
    }
}
?>