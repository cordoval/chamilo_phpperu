<?php
/**
 * $Id: survey_publication_category_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.survey.category_manager
 */
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
        $adm = SurveyDataManager :: get_instance();
        return $adm->count_survey_publication_categories($condition);
    }

    function retrieve_categories($condition, $offset, $count, $order_property)
    {
        $adm = SurveyDataManager :: get_instance();
        return $adm->retrieve_survey_publication_categories($condition, $offset, $count, $order_property);
    }

    function get_next_category_display_order($parent_id)
    {
        $adm = SurveyDataManager :: get_instance();
        return $adm->select_next_survey_publication_category_display_order($parent_id);
    }
}
?>