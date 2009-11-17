<?php
/**
 * $Id: admin_manager_component.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.admin_manager
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

abstract class AdminManagerComponent extends CoreApplicationComponent
{

    protected function AdminManagerComponent($admin_manager)
    {
        parent :: __construct($admin_manager);
    }

    /**
     * @see AdminManager :: retrieve_system_announcement_publication()
     */
    function retrieve_system_announcement_publication($id)
    {
        return $this->get_parent()->retrieve_system_announcement_publication($id);
    }

    /**
     * @see AdminManager :: count_registrations()
     */
    function count_registrations($condition = null)
    {
        return $this->get_parent()->count_registrations($condition);
    }

    function retrieve_registration($id)
    {
        return $this->get_parent()->retrieve_registration($id);
    }

    /**
     * @see AdminManager :: retrieve_registrations()
     */
    function retrieve_registrations($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->get_parent()->retrieve_registrations($condition, $order_by, $offset, $max_objects);
    }

    /**
     * @see AdminManager :: count_remote_packages()
     */
    function count_remote_packages($condition = null)
    {
        return $this->get_parent()->count_remote_packages($condition);
    }

    /**
     * @see AdminManager :: retrieve_remote_package()
     */
    function retrieve_remote_package($id)
    {
        return $this->get_parent()->retrieve_remote_package($id);
    }

    /**
     * @see AdminManager :: retrieve_remote_packages()
     */
    function retrieve_remote_packages($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->get_parent()->retrieve_remote_packages($condition, $order_by, $offset, $max_objects);
    }

    /**
     * @see AdminManager :: retrieve_system_announcement_publications()
     */
    function retrieve_system_announcement_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->get_parent()->retrieve_system_announcement_publications($condition, $order_by, $offset, $max_objects);
    }

    /**
     * @see AdminManager :: count_system_announcement_publications()
     */
    function count_system_announcement_publications($condition = null)
    {
        return $this->get_parent()->count_system_announcement_publications($condition);
    }

    function get_system_announcement_publication_deleting_url($system_announcement_publication)
    {
        return $this->get_parent()->get_system_announcement_publication_deleting_url($system_announcement_publication);
    }

    function get_system_announcement_publication_visibility_url($system_announcement_publication)
    {
        return $this->get_parent()->get_system_announcement_publication_visibility_url($system_announcement_publication);
    }

    function get_system_announcement_publication_viewing_url($system_announcement_publication)
    {
        return $this->get_parent()->get_system_announcement_publication_viewing_url($system_announcement_publication);
    }

    function get_system_announcement_publication_editing_url($system_announcement_publication)
    {
        return $this->get_parent()->get_system_announcement_publication_editing_url($system_announcement_publication);
    }

    function get_system_announcement_publication_creating_url()
    {
        return $this->get_parent()->get_system_announcement_publication_creating_url();
    }
}
?>