<?php
require_once Path :: get_application_library_path() . 'category_manager/platform_category.class.php';
require_once dirname(__FILE__) . '/../cba_data_manager.class.php';
/**
 *	@author Nick Van Loocke
 */
class CompetencyCategory extends PlatformCategory
{

    function create()
    {
        $cdm = CbaDataManager :: get_instance();
        $condition = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $this->get_parent());
        $sort = $cdm->retrieve_max_sort_value(self :: get_table_name(), PlatformCategory :: PROPERTY_DISPLAY_ORDER, $condition);
        $this->set_display_order($sort + 1);
        
        return $cdm->create_competency_category($this);
    }

    function update()
    {
        return CbaDataManager :: get_instance()->update_competency_category($this);
    }

    function delete()
    {
        return CbaDataManager :: get_instance()->delete_competency_category($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(__CLASS__);
    }
}
?>