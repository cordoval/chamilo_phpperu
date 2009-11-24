<?php
/**
 * $Id: forum_publication_category.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.category_manager
 */
require_once Path :: get_application_library_path() . 'category_manager/platform_category.class.php';
require_once dirname(__FILE__) . '/../forum_data_manager.class.php';

/**
 *	@author Sven Vanpoucke
 */

class ForumPublicationCategory extends PlatformCategory
{
    const CLASS_NAME = __CLASS__;

    function create()
    {
        $fdm = ForumDataManager :: get_instance();

        $condition = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $this->get_parent());
        $sort = $fdm->retrieve_max_sort_value(self :: get_table_name(), PlatformCategory :: PROPERTY_DISPLAY_ORDER, $condition);
        $this->set_display_order($sort + 1);

        return $fdm->create_forum_publication_category($this);
    }

    function update()
    {
        return ForumDataManager :: get_instance()->update_forum_publication_category($this);
    }

    function delete()
    {
        return ForumDataManager :: get_instance()->delete_forum_publication_category($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}