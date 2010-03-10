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
        $adm = CbaDataManager :: get_instance();
        $this->set_display_order($adm->select_next_competency_category_display_order($this->get_parent()));
        return $adm->create_competency_category($this);
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