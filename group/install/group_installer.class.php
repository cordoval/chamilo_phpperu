<?php
/**
 * $Id: group_installer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.install
 */
/**
 * This installer can be used to create the storage structure for the
 * group application.
 */
class GroupInstaller extends Installer
{

    /**
     * Constructor
     */
    function GroupInstaller($values)
    {
        parent :: __construct($values, GroupDataManager :: get_instance());
    }

    /**
     * Additional installation steps.
     */
    function install_extra()
    {
        if (! $this->create_root_group())
        {
            return false;
        }
        
        return true;
    }

    function create_root_group()
    {
        $values = $this->get_form_values();
        
        $group = new Group();
        $group->set_name($values['organization_name']);
        $group->set_parent(0);
        $group->create();
        
        return true;
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>