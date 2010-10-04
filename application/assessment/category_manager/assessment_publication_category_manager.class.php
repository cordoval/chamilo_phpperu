<?php
/**
 * $Id: assessment_publication_category_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.assessment.category_manager
 */
require_once dirname(__FILE__) . '/../assessment_data_manager.class.php';
require_once dirname(__FILE__) . '/assessment_publication_category.class.php';

class AssessmentPublicationCategoryManager extends CategoryManager
{

    function AssessmentPublicationCategoryManager($parent, $trail)
    {
        parent :: __construct($parent, $trail);
    }

    function get_category()
    {
        return new AssessmentPublicationCategory();
    }

    function count_categories($condition)
    {
        $adm = AssessmentDataManager :: get_instance();
        return $adm->count_assessment_publication_categories($condition);
    }

    function retrieve_categories($condition, $offset, $count, $order_property)
    {
        $adm = AssessmentDataManager :: get_instance();
        return $adm->retrieve_assessment_publication_categories($condition, $offset, $count, $order_property);
    }

    function get_next_category_display_order($parent_id)
    {
        $adm = AssessmentDataManager :: get_instance();
        return $adm->select_next_assessment_publication_category_display_order($parent_id);
    }
}
?>