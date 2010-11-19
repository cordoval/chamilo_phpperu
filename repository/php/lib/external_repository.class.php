<?php
namespace repository;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
/**
 * $Id: external_repository.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
class ExternalRepository extends RepositoryDataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_CATALOG_NAME = 'catalog_name';
    const PROPERTY_METADATA_XSL_FILENAME = 'metadata_xsl_filename';
    const PROPERTY_TYPED_EXTERNAL_REPOSITORY_ID = 'typed_external_repository_id';
    const PROPERTY_ENABLED = 'enabled';

    const CATALOG_REPOSITORY_LIST = 'repository_list';

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

    /*************************************************************************/

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

    /*************************************************************************/

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

    /*************************************************************************/
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

    /*************************************************************************/

    function set_catalog_name($catalog_name)
    {
        if (isset($catalog_name) && strlen($catalog_name) > 0)
        {
            $this->set_default_property(self :: PROPERTY_CATALOG_NAME, $catalog_name);
        }
    }

    /**
     * @return string The catalog name
     */
    function get_catalog_name()
    {
        return $this->get_default_property(self :: PROPERTY_CATALOG_NAME);
    }

    /*************************************************************************/

    function set_metadata_xsl_filename($metadata_xsl_filename)
    {
        if (isset($metadata_xsl_filename) && strlen($metadata_xsl_filename) > 0)
        {
            $this->set_default_property(self :: PROPERTY_METADATA_XSL_FILENAME, $metadata_xsl_filename);
        }
    }

    /**
     * @return string The XSL filename
     */
    function get_metadata_xsl_filename()
    {
        return $this->get_default_property(self :: PROPERTY_METADATA_XSL_FILENAME);
    }

    /*************************************************************************/

    function set_typed_external_repository_id($id)
    {
        if (isset($id) && strlen($id) > 0)
        {
            $this->set_default_property(self :: PROPERTY_TYPED_EXTERNAL_REPOSITORY_ID, $id);
        }
    }

    /**
     * @return integer The typed export id (from the specific datasource table)
     */
    function get_typed_external_repository_id()
    {
        return $this->get_default_property(self :: PROPERTY_TYPED_EXTERNAL_REPOSITORY_ID, DataClass :: NO_UID);
    }

    /*************************************************************************/

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

    /*************************************************************************/

    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_TITLE;
        $extended_property_names[] = self :: PROPERTY_DESCRIPTION;
        $extended_property_names[] = self :: PROPERTY_TYPE;
        $extended_property_names[] = self :: PROPERTY_CATALOG_NAME;
        $extended_property_names[] = self :: PROPERTY_METADATA_XSL_FILENAME;
        $extended_property_names[] = self :: PROPERTY_TYPED_EXTERNAL_REPOSITORY_ID;
        $extended_property_names[] = self :: PROPERTY_ENABLED;

        return parent :: get_default_property_names($extended_property_names);
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    /*************************************************************************/

    /**
     * Load the object properties from the datasource if the object instance has already an ID set.
     *
     * @return boolean Indicates wether the object properties could be retrieved from the datasource
     */
    function get()
    {
        if ($this->is_identified())
        {
            $dm = RepositoryDataManager :: get_instance();

            $condition = new EqualityCondition(self :: PROPERTY_ID, $this->get_id());

            $result_set = $dm->retrieve_external_repository_condition($condition);
            $object = $result_set->next_result();

            if (isset($object))
            {
                $this->set_default_properties($object->get_default_properties());

                //	            foreach (self :: get_default_property_names() as $property_name)
                //	            {
                //	            	$this->set_default_property($property_name, $object->get_default_property($property_name));
                //	            }


                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Return an instance of an export to a external repository. The object returned is a subclass of ExternalRepository. The exact class type depends on the export type.
     *
     * @return mixed Instance of an export to a external repository
     */
    function get_typed_repository_object()
    {
        if ($this->get())
        {
            $type = $this->get_type();
            if (ExternalRepository :: require_once_external_repository_class_file($type))
            {
                $class_name = 'ExternalRepository' . Utilities :: underscores_to_camelcase($type);

                $typed_repository = new $class_name();

                $typed_repository->set_id($this->get_typed_external_repository_id());

                if ($typed_repository->get())
                {
                    return $typed_repository;
                }
                else
                {
                    return null;
                }
            }
            else
            {
                return null;
            }
        }
        else
        {
            return null;
        }
    }

    /**
     * Set the properties of this ExternalRepository instance by retrieving the property values from the datasource
     * if the typed_external_repository_id property is set
     *
     * @return boolean Indicates wether the object properties could be retrieved from the datasource
     */
    function get_by_typed_external_repository_id()
    {
        $typed_external_repository_id = $this->get_typed_external_repository_id();

        if (isset($typed_external_repository_id) && $typed_external_repository_id != DataClass :: NO_UID)
        {
            $dm = RepositoryDataManager :: get_instance();

            $condition = new EqualityCondition(self :: PROPERTY_TYPED_EXTERNAL_REPOSITORY_ID, $typed_external_repository_id);

            $result_set = $dm->retrieve_external_repository_condition($condition);
            $object = $result_set->next_result();

            if (isset($object))
            {
                $this->set_default_properties($object->get_default_properties());

                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /*************************************************************************/

    /**
     * Removes the trailing slash from a string if it exists
     *
     * @param $string
     * @return string
     */
    function remove_trailing_slash($string)
    {
        return StringUtilities :: remove_trailing($string, '/');
    }

    /**
     * Add a leading slash to a string if it doesn't already starts with a slash
     *
     * @param $string
     * @return string
     */
    function ensure_start_with_slash($string)
    {
        return StringUtilities :: ensure_start_with($string, '/');
    }

    /**
     * Ensure a subclass of ExternalRepository will be found when creating an instance of the class
     *
     * @param $type e.g 'Fedora'
     * @return unknown_type
     */
    private static function require_once_external_repository_class_file($type)
    {
        if (isset($type) && strlen($type) > 0 && ! in_array($type, self :: $already_required_types))
        {
            $camel_type = ucfirst(strtolower($type));
            $class_name = 'ExternalRepository' . $camel_type;
            $file_name = Utilities :: camelcase_to_underscores($class_name) . '.class.php';

            require_once dirname(__FILE__) . '/external/' . $file_name;

            self :: $already_required_types[] = $type;

            return true;
        }
        elseif (in_array($type, self :: $already_required_types))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /*************************************************************************
     * Fat model methods
     *************************************************************************/

    /**
     * Get the list of existing exports to external repositories,
     * and require_once the needed model classes
     *
     * @return array Array of ExternalRepository
     */
    public static function retrieve_external_repository_condition($condition = null)
    {
        if (! isset($condition))
        {
            /*
             * By default retrieve only enabled repositories
             */
            $condition = new EqualityCondition('enabled', 1);
        }

        $dm = RepositoryDataManager :: get_instance();
        $result_set = $dm->retrieve_external_repository_condition($condition);

        $objects = array();
        while ($object = $result_set->next_result())
        {
            $objects[] = $object->get_typed_repository_object();

            ExternalRepository :: require_once_external_repository_class_file($object->get_type());
        }

        return $objects;
    }

    public function create()
    {
        if (! parent :: create())
        {
            return false;
        }
        else
        {
            if (! ExternalRepositorySetting :: initialize($this))
            {
                return false;
            }
        }

        $succes = RepositoryRights :: create_location_in_external_repositories_subtree($this->get_title(), $this->get_id(), RepositoryRights :: get_external_repositories_subtree_root_id());
        if (! $succes)
        {
            return false;
        }

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
            $condition = new EqualityCondition(ExternalRepositorySetting :: PROPERTY_EXTERNAL_REPOSITORY_ID, $this->get_id());
            $settings = $this->get_data_manager()->retrieve_external_repository_settings($condition);

            while ($setting = $settings->next_result())
            {
                if (! $setting->delete())
                {
                    return false;
                }
            }
        }

        $location = RepositoryRights :: get_location_by_identifier_from_external_repositories_subtree($this->get_id());
        if ($location)
        {
            if (! $location->remove())
            {
                return false;
            }
        }

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