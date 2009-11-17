<?php
/**
 * $Id: repository_category.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.category_manager
 */
/**
 *	@author Sven Vanpoucke
 */
require_once Path :: get_application_library_path() . 'category_manager/platform_category.class.php';


class RepositoryCategory extends PlatformCategory
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_USER_ID = 'user_id';

    function create()
    {
        if (! $this->get_user_id())
        {
            $user_id = Session :: get_user_id();
            if ($user_id)
            {
                $this->set_user_id($user_id);
            }
        }
        else
            $user_id = $this->get_user_id();

        $rdm = RepositoryDataManager :: get_instance();
        $this->set_id($rdm->get_next_category_id());
        $this->set_display_order($rdm->select_next_category_display_order($this->get_parent(), $user_id));
        if (! $rdm->create_category($this))
        {
            return false;
        }

        $location = new Location();
        $location->set_location($this->get_name());
        $location->set_application(RepositoryManager :: APPLICATION_NAME);
        $location->set_type_from_object($this);
        $location->set_identifier($this->get_id());

        $parent = $this->get_parent();
        if ($parent == 0)
        {
            $parent = RepositoryRights :: get_user_root_id($user_id);
        }
        else
        {
            $parent = RepositoryRights :: get_location_id_by_identifier('repository_category', $this->get_parent());
        }

        $location->set_parent($parent);
        if (! $location->create())
        {
            return false;
        }

        return true;
    }

    function update()
    {
        return RepositoryDataManager :: get_instance()->update_category($this);
    }

    function delete()
    {
        return RepositoryDataManager :: get_instance()->delete_category($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    static function get_default_property_names()
    {
        return array(self :: PROPERTY_USER_ID, self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_PARENT, self :: PROPERTY_DISPLAY_ORDER);
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }
}