<?php
namespace admin;

use common\libraries\Utilities;
use common\libraries\DataClass;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;

use repository\RepositoryRights;

/**
 * $Id: registration.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib
 * @author Hans De Bisschop
 */

class Registration extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_TYPE = 'type';
    const PROPERTY_CATEGORY = 'category';
    const PROPERTY_NAME = 'name';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_VERSION = 'version';

    const TYPE_CONTENT_OBJECT = 'content_object';
    const TYPE_CORE = 'core';
    const TYPE_APPLICATION = 'application';
    const TYPE_LANGUAGE = 'language';
    const TYPE_EXTENSION = 'extension';
    const TYPE_LIBRARY = 'library';
    const TYPE_EXTERNAL_REPOSITORY_MANAGER = 'external_repository_manager';
	const TYPE_VIDEO_CONFERENCING_MANAGER = 'video_conferencing_manager';

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * Get the default properties of registrations.
     * @return array The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_ID, self :: PROPERTY_TYPE, self :: PROPERTY_CATEGORY, self :: PROPERTY_NAME, self :: PROPERTY_STATUS, self :: PROPERTY_VERSION));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return AdminDataManager :: get_instance();
    }

    /**
     * Returns the type of this registration.
     * @return int The type
     */
    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /**
     * Returns the category of this registration.
     * @return string the category
     */
    function get_category()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

    /**
     * Returns the name of this registration.
     * @return int the name
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the status of this registration.
     * @return int the status
     */
    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Returns the version of the registered item.
     * @return String the version
     */
    function get_version()
    {
        return $this->get_default_property(self :: PROPERTY_VERSION);
    }

    /**
     * Sets the type of this registration.
     * @param Int $id the registration type.
     */
    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    /**
     * Sets the category of this registration.
     * @param string $category the registration category.
     */
    function set_category($category)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    }

    /**
     * Sets the name of this registration.
     * @param int $name the name.
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Sets the status of this registration.
     * @param int $status the status.
     */
    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    /**
     * Sets the version of this registered item.
     * @param String $version the version.
     */
    function set_version($version)
    {
        $this->set_default_property(self :: PROPERTY_VERSION, $version);
    }

    function is_active()
    {
        return $this->get_status();
    }

    function activate()
    {
        $this->set_status(true);
    }

    function deactivate()
    {
        $this->set_status(false);
    }

    function toggle_status()
    {
        $this->set_status(! $this->get_status());
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    function is_up_to_date()
    {
        if ($this->get_type() != self :: TYPE_APPLICATION && $this->get_type() != self :: TYPE_CONTENT_OBJECT)
        {
            return true;
        }

        $conditions = array();
        $conditions[] = new EqualityCondition(RemotePackage :: PROPERTY_CODE, $this->get_name());
        $conditions[] = new EqualityCondition(RemotePackage :: PROPERTY_SECTION, $this->get_type());
        $conditions[] = new EqualityCondition(RemotePackage :: PROPERTY_CATEGORY, $this->get_category());
        $condition = new AndCondition($conditions);

        $remote_package = $this->get_data_manager()->retrieve_remote_packages($condition, array(), null, 1);
        if ($remote_package->size() == 1)
        {
            $remote_package = $remote_package->next_result();

            if (version_compare($remote_package->get_version(), $this->get_version(), '>'))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            return true;
        }
    }

    function delete()
    {
        if ($this->get_type() == self :: TYPE_CONTENT_OBJECT)
        {
            $location = RepositoryRights :: get_location_by_identifier_from_content_objects_subtree($this->get_id());
            if ($location)
            {
                if (! $location->remove())
                {
                    return false;
                }
            }
        }

        return parent :: delete();
    }

    static function get_types()
    {
        return array(self :: TYPE_APPLICATION, self :: TYPE_CONTENT_OBJECT, self :: TYPE_CORE, self :: TYPE_LANGUAGE, self :: TYPE_EXTERNAL_REPOSITORY_MANAGER, self :: TYPE_VIDEO_CONFERENCING_MANAGER,self :: TYPE_EXTENSION, self :: TYPE_LIBRARY);
    }
    
    function can_be_activated()
    {
        return !in_array($this->get_type(), array(self :: TYPE_CORE, self :: TYPE_EXTENSION, self :: TYPE_LIBRARY));
    }
}
?>