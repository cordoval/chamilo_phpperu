<?php
namespace admin;
use common\libraries\Utilities;
use common\libraries\Path;
use common\extensions\category_manager\PlatformCategory;
require_once Path :: get_common_extensions_path() . 'category_manager/php/platform_category.class.php';

/**
 * $Id: admin_category.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.category_manager
 * @author Sven Vanpoucke
 */

class AdminCategory extends PlatformCategory
{
    const CLASS_NAME = __CLASS__;

    function create()
    {
        $wdm = AdminDataManager :: get_instance();
        $this->set_display_order($wdm->select_next_display_order($this->get_parent()));
        return $wdm->create_category($this);
    }

    function update()
    {
        return AdminDataManager :: get_instance()->update_category($this);
    }

    function delete()
    {
        return AdminDataManager :: get_instance()->delete_category($this);
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}