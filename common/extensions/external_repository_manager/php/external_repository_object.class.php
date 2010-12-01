<?php
namespace common\extensions\external_repository_manager;

use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Session;
use common\libraries\Translation;
use common\libraries\Theme;

use repository\RepositoryDataManager;
use repository\ContentObject;
use repository\ExternalSync;

abstract class ExternalRepositoryObject
{
    /**
     * @var array
     */
    private $default_properties;

    /**
     * @var ExternalSync
     */
    private $synchronization_data;

    const PROPERTY_ID = 'id';
    const PROPERTY_EXTERNAL_REPOSITORY_ID = 'external_repository_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_OWNER_ID = 'owner_id';
    const PROPERTY_CREATED = 'created';
    const PROPERTY_MODIFIED = 'modified';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_RIGHTS = 'rights';

    const RIGHT_EDIT = 1;
    const RIGHT_DELETE = 2;
    const RIGHT_USE = 3;
    const RIGHT_DOWNLOAD = 4;

    /**
     * @param array $default_properties
     */
    function __construct($default_properties = array ())
    {
        $this->default_properties = $default_properties;
    }

    /**
     * Get the default properties of all data classes.
     * @return array The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_ID;
        $extended_property_names[] = self :: PROPERTY_EXTERNAL_REPOSITORY_ID;
        $extended_property_names[] = self :: PROPERTY_TITLE;
        $extended_property_names[] = self :: PROPERTY_DESCRIPTION;
        $extended_property_names[] = self :: PROPERTY_OWNER_ID;
        $extended_property_names[] = self :: PROPERTY_CREATED;
        $extended_property_names[] = self :: PROPERTY_MODIFIED;
        $extended_property_names[] = self :: PROPERTY_TYPE;
        $extended_property_names[] = self :: PROPERTY_RIGHTS;
        return $extended_property_names;
    }

    /**
     * Gets a default property of this data class object by name.
     * @param string $name The name of the property.
     * @param mixed
     */
    function get_default_property($name)
    {
        return (isset($this->default_properties) && array_key_exists($name, $this->default_properties)) ? $this->default_properties[$name] : null;
    }

    /**
     * @param $default_properties the $default_properties to set
     */
    public function set_default_properties($default_properties)
    {
        $this->default_properties = $default_properties;
    }

    /**
     * Sets a default property of this data class by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->default_properties[$name] = $value;
    }

    function get_default_properties()
    {
        return $this->default_properties;
    }

    /**
     * @return string
     */
    public function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * @return string
     */
    public function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * @return int
     */
    public function get_external_repository_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXTERNAL_REPOSITORY_ID);
    }

    /**
     * @return string
     */
    public function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * @return string
     */
    public function get_owner_id()
    {
        return $this->get_default_property(self :: PROPERTY_OWNER_ID);
    }

    /**
     * @return int
     */
    public function get_created()
    {
        return $this->get_default_property(self :: PROPERTY_CREATED);
    }

    /**
     * @return int
     */
    public function get_modified()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFIED);
    }

    /**
     * @return string
     */
    public function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /**
     * @return array
     */
    public function get_rights()
    {
        return $this->get_default_property(self :: PROPERTY_RIGHTS);
    }

    /**
     * @param int $right
     * @return boolean
     */
    public function get_right($right)
    {
        $rights = $this->get_rights();
        if (! in_array($right, array_keys($rights)))
        {
            return false;
        }
        else
        {
            return $rights[$right];
        }
    }

    public static function get_available_rights()
    {
        return array(self :: RIGHT_DELETE, self :: RIGHT_DOWNLOAD, self :: RIGHT_EDIT, self :: RIGHT_USE);
    }

    /**
     * @param string $title
     */
    public function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    /**
     * @param string $id
     */
    public function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * @param int $external_repository_id
     */
    public function set_external_repository_id($external_repository_id)
    {
        $this->set_default_property(self :: PROPERTY_EXTERNAL_REPOSITORY_ID, $external_repository_id);
    }

    /**
     * @param string $description
     */
    public function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * @param string $owner_id
     */
    public function set_owner_id($owner_id)
    {
        $this->set_default_property(self :: PROPERTY_OWNER_ID, $owner_id);
    }

    /**
     * @param int $created
     */
    public function set_created($created)
    {
        $this->set_default_property(self :: PROPERTY_CREATED, $created);
    }

    /**
     * @param int $modified
     */
    public function set_modified($modified)
    {
        $this->set_default_property(self :: PROPERTY_MODIFIED, $modified);
    }

    /**
     * @param string $type
     */
    public function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    /**
     * @param array $rights
     */
    public function set_rights($rights)
    {
        $this->set_default_property(self :: PROPERTY_RIGHTS, $rights);
    }

    /**
     * @param int $right
     * @param boolean $value
     */
    public function set_right($right, $value)
    {
        $rights = $this->get_rights();
        $rights[$right] = $value;
        $this->set_rights($rights);
    }

    /**
     * Gets the name of the icon corresponding to this external_repository object.
     */
    function get_icon_name()
    {
        return $this->get_type();
    }

    /**
     * @return string
     */
    function get_icon_image()
    {
        $source = Theme :: get_image_path(ExternalRepositoryManager :: get_namespace($this->get_object_type())) . 'types/' . $this->get_icon_name() . '.png';
        $name = Translation :: get('Type' . Utilities :: underscores_to_camelcase($this->get_type()), null, ExternalRepositoryManager :: get_namespace($this->get_object_type()));
        return '<img src="' . $source . '" alt="' . $name . '" title="' . $name . '" />';
    }

    /**
     * @return string
     */
    abstract static function get_object_type();

    /**
     * @return boolean
     */
    function is_usable()
    {
        return $this->get_right(self :: RIGHT_USE);
    }

    /**
     * @return boolean
     */
    function is_editable()
    {
        return $this->get_right(self :: RIGHT_EDIT);
    }

    /**
     * @return boolean
     */
    function is_deletable()
    {
        return $this->get_right(self :: RIGHT_DELETE);
    }

    /**
     * @return boolean
     */
    function is_downloadable()
    {
        return $this->get_right(self :: RIGHT_DOWNLOAD);
    }

    /**
     * @return ExternalSync
     */
    function get_synchronization_data()
    {
        if (! isset($this->synchronization_data))
        {
            $sync_conditions = array();
            $sync_conditions[] = new EqualityCondition(ExternalSync :: PROPERTY_EXTERNAL_OBJECT_ID, $this->get_id());
            $sync_conditions[] = new EqualityCondition(ExternalSync :: PROPERTY_EXTERNAL_ID, $this->get_external_repository_id());
            $sync_conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, Session :: get_user_id(), ContentObject :: get_table_name());
            $sync_condition = new AndCondition($sync_conditions);

            $this->synchronization_data = RepositoryDataManager :: get_instance()->retrieve_external_sync($sync_condition);
        }

        return $this->synchronization_data;
    }

    /**
     * @return int
     */
    function get_synchronization_status()
    {
        return $this->get_synchronization_data()->get_synchronization_status(null, $this->get_modified());
    }

    /**
     * @return boolean
     */
    function is_importable()
    {
        return ! $this->get_synchronization_data() instanceof ExternalSync;
    }
}
?>