<?php
/**
 * $Id: content_object_metadata_catalog.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
class ContentObjectMetadataCatalog extends RepositoryDataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_TYPE = 'type';
    const PROPERTY_VALUE = 'value';
    const PROPERTY_NAME = 'name';
    const PROPERTY_SORT = 'sort';

    const CATALOG_LANGUAGE = 'language';
    const CATALOG_COPYRIGHT = 'copyright';
    const CATALOG_ROLE = 'role';
    const CATALOG_DAY = 'day';
    const CATALOG_MONTH = 'month';
    const CATALOG_YEAR = 'year';
    const CATALOG_HOUR = 'hour';
    const CATALOG_MIN = 'min';
    const CATALOG_SEC = 'sec';

    function ContentObjectMetadataCatalog($defaultProperties = array ())
    {
        parent :: __construct($defaultProperties);
    }

    /*************************************************************************/

    function set_type($type)
    {
        if (isset($type) && strlen($type) > 0)
        {
            $this->set_default_property(self :: PROPERTY_TYPE, $type);
        }
    }

    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /*************************************************************************/

    function set_value($value)
    {
        if (isset($value))
        {
            $this->set_default_property(self :: PROPERTY_VALUE, $value);
        }
    }

    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /*************************************************************************/

    function set_name($value)
    {
        if (isset($value))
        {
            $this->set_default_property(self :: PROPERTY_NAME, $value);
        }
    }

    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /*************************************************************************/

    function set_sort($value)
    {
        if (isset($value))
        {
            $this->set_default_property(self :: PROPERTY_SORT, $value);
        }
    }

    function get_sort()
    {
        return $this->get_default_property(self :: PROPERTY_SORT);
    }

    /*************************************************************************/

    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_TYPE;
        $extended_property_names[] = self :: PROPERTY_VALUE;
        $extended_property_names[] = self :: PROPERTY_NAME;
        $extended_property_names[] = self :: PROPERTY_SORT;

        return parent :: get_default_property_names($extended_property_names);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function create()
    {
        $dm = RepositoryDataManager :: get_instance();

        $this->set_creation_date(time());

        return $dm->create_content_object_metadata_catalog($this);
    }

    function update()
    {
        if (! $this->is_identified())
        {
            throw new Exception('Learning object metadata catalog could not be saved as its identity is not set');
        }

        $this->set_modification_date(time());

        $dm = RepositoryDataManager :: get_instance();
        $result = $dm->update_content_object_metadata_catalog($this);

        return $result;
    }

    function delete()
    {
        $dm = RepositoryDataManager :: get_instance();
        $result = $dm->delete_content_object_metadata_catalog($this);

        return $result;
    }

/*************************************************************************/
}
?>