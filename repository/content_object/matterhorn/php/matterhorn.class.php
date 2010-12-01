<?php
namespace repository\content_object\matterhorn;

use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\Versionable;
use common\libraries\AndCondition;

use repository\ContentObject;
use repository\ExternalSetting;

class Matterhorn extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    function get_video_url()
    {
        $synchronization_data = $this->get_synchronization_data();

        $conditions = array();
        $conditions[] = new EqualityCondition(ExternalSetting :: PROPERTY_VARIABLE, 'url');
        $conditions[] = new EqualityCondition(ExternalSetting :: PROPERTY_EXTERNAL_ID, $synchronization_data->get_external_id());
        $condition = new AndCondition($conditions);
        $settings = $this->get_data_manager()->retrieve_external_settings($condition)->next_result();

        return $settings->get_value() . '/engage/ui/embed.html?id=' . $synchronization_data->get_external_object_id();
    }
}
?>