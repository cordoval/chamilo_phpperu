<?php
require_once dirname(__FILE__) . '/../peer_assessment_data_manager.class.php';
require_once dirname(__FILE__) . '/peer_assessment_publication_category.class.php';
/**
 *	@author Nick Van Loocke
 */

class PeerAssessmentPublicationCategoryManager extends CategoryManager
{

    function PeerAssessmentPublicationCategoryManager($parent, $trail)
    {
        parent :: __construct($parent, $trail);
    }

    function get_category()
    {
        return new PeerAssessmentPublicationCategory();
    }

    function count_categories($condition)
    {
        return PeerAssessmentDataManager :: get_instance()->count_peer_assessment_publication_categories($condition);
    }

    function retrieve_categories($condition, $offset, $count, $order_property)
    {
        return PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication_categories($condition, $offset, $count, $order_property);
    }

    function get_next_category_display_order($parent_id)
    {
        $condition = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $parent_id);
        $sort = PeerAssessmentDataManager :: get_instance()->retrieve_max_sort_value(PeerAssessmentPublicationCategory :: get_table_name(), PlatformCategory :: PROPERTY_DISPLAY_ORDER, $condition);
        return $sort + 1;
    }
}
?>