<?php
namespace repository;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
/**
 * $Id: external_repository.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
class ExternalInstance extends RepositoryDataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_INSTANCE_TYPE = 'instance_type';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_TYPE = 'type';

    const PROPERTY_ENABLED = 'enabled';

    /**
     * Contains a list of already required export types
     * Allow to spare some business logic processing
     *
     * @var array
     */
    private static $already_required_types = array();

    function __construct($defaultProperties = array ())
    {
        parent :: __construct($defaultProperties);
    }

    function set_title($title)
    {
        if (isset($title) && strlen($title) > 0)
        {
            $this->set_default_property(self :: PROPERTY_TITLE, $title);
        }
    }

    /**
     * @return string The export title
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    function set_instance_type($instance_type)
    {
        if (isset($instance_type) && strlen($instance_type) > 0)
        {
            $this->set_default_property(self :: PROPERTY_INSTANCE_TYPE, $instance_type);
        }
    }

    /**
     * @return string The export title
     */
    function get_instance_type()
    {
        return $this->get_default_property(self :: PROPERTY_INSTANCE_TYPE);
    }

    function set_description($description)
    {
        if (isset($description) && strlen($description) > 0)
        {
            $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
        }
    }

    /**
     * @return string The export description
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    function set_type($type)
    {
        if (isset($type) && strlen($type) > 0)
        {
            $this->set_default_property(self :: PROPERTY_TYPE, $type);
        }
    }

    /**
     * @return string The export type
     */
    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    function set_enabled($enabled)
    {
        if (isset($enabled) && is_bool($enabled))
        {
            $this->set_default_property(self :: PROPERTY_ENABLED, $enabled);
        }
    }

    /**
     * @return boolean Indicates wether the export is enabled or not
     */
    function get_enabled()
    {
        return $this->get_default_property(self :: PROPERTY_ENABLED, false);
    }

    function is_enabled()
    {
        return $this->get_enabled();
    }

    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_TITLE;
        $extended_property_names[] = self :: PROPERTY_DESCRIPTION;
        $extended_property_names[] = self :: PROPERTY_TYPE;
        $extended_property_names[] = self :: PROPERTY_INSTANCE_TYPE;
        $extended_property_names[] = self :: PROPERTY_ENABLED;

        return parent :: get_default_property_names($extended_property_names);
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    /*************************************************************************
     * Fat model methods
     *************************************************************************/

    public function create()
    {
        if (! parent :: create())
        {
            return false;
        }
        else
        {
            if (! ExternalSetting :: initialize($this))
            {

                return false;
            }
        }

        //        $succes = RepositoryRights :: create_location_in_external_repositories_subtree($this->get_title(), $this->get_id(), RepositoryRights :: get_external_repositories_subtree_root_id());
        //        if (! $succes)
        //        {
        //            return false;
        //        }


        return true;
    }

    public function delete()
    {
        if (! parent :: delete())
        {
            return false;
        }
        else
        {
            $condition = new EqualityCondition(ExternalSetting :: PROPERTY_EXTERNAL_ID, $this->get_id());
            $settings = $this->get_data_manager()->retrieve_external_settings($condition);

            while ($setting = $settings->next_result())
            {
                if (! $setting->delete())
                {
                    return false;
                }
            }
        }

        //        $location = RepositoryRights :: get_location_by_identifier_from_external_repositories_subtree($this->get_id());
        //        if ($location)
        //        {
        //            if (! $location->remove())
        //            {
        //                return false;
        //            }
        //        }


        return true;
    }

    public function activate()
    {
        $this->set_enabled(true);
    }

    public function deactivate()
    {
        $this->set_enabled(false);
    }

}

?>