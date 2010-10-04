<?php
require_once Path :: get_common_extensions_path() . 'category_manager/platform_category.class.php';
require_once dirname(__FILE__) . '/../peer_assessment_data_manager.class.php';
/**
 *	@author Nick Van Loocke
 */

class PeerAssessmentPublicationCategory extends PlatformCategory
{
    const CLASS_NAME = __CLASS__;

    function create()
    {
        $fdm = PeerAssessmentDataManager :: get_instance();

        $condition = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $this->get_parent());
        $sort = $fdm->retrieve_max_sort_value(self :: get_table_name(), PlatformCategory :: PROPERTY_DISPLAY_ORDER, $condition);
        $this->set_display_order($sort + 1);
        return $fdm->create_peer_assessment_publication_category($this);
    }

    function update()
    {
        return PeerAssessmentDataManager :: get_instance()->update_peer_assessment_publication_category($this);
    }

    function delete()
    {
        return PeerAssessmentDataManager :: get_instance()->delete_peer_assessment_publication_category($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}