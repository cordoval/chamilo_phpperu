<?php
/**
 * $Id: repository_data_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package repository.lib
 */
require_once dirname(__FILE__) . '/data_manager/database/database_content_object_result_set.class.php';

/**
 * This is a skeleton for a data manager for the learning object repository.
 * Data managers must extend this class and implement its abstract methods.
 * If the user configuration dictates that the "database" data manager is to
 * be used, this class will automatically attempt to instantiate
 * "DatabaseRepositoryDataManager"; hence, this naming convention must be
 * respected for all extensions of this class.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class RepositoryDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Associative array that maps learning object types to their
     * corresponding array of property names.
     */
    private $typeProperties;

    /**
     * Array which contains the registered applications running on top of this
     * repositorydatamanager
     */
    private static $applications = array();

    private static $number_of_categories;

    /**
     * Constructor.
     */
    protected function RepositoryDataManager()
    {
        //        $this->initialize();
        //        $this->typeProperties = array();
        self :: load_types();
        self :: $applications = array();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return RepositoryDataManagerInterface The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: load_types();

            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_repository_data_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'RepositoryDataManager';

            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    /**
     * Returns the learning object types registered with the data manager.
     * @param boolean $only_master_types Only return the master type learning
     * objects (which can exist on their own). Returns all learning object types
     * by default.
     * @return array The types.
     */
    public static function get_registered_types($only_master_types = false)
    {
        $adm = AdminDataManager :: get_instance();
        $condition = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_CONTENT_OBJECT);

        $order = new ObjectTableOrder(Registration :: PROPERTY_NAME, SORT_ASC);

        $content_objects = $adm->retrieve_registrations($condition, $order);
        $active_content_objects = array();

        while ($content_object = $content_objects->next_result())
        {
            $active_content_objects[] = $content_object->get_name();
        }

        return $active_content_objects;
    }

    /**
     * Checks if a type name corresponds to an extended learning object type.
     * @param string $type The type name.
     * @return boolean True if the corresponding type is extended, false
     * otherwise.
     */
    public static function is_extended_type($type)
    {
        $temp_class = ContentObject :: factory($type);

        if (! $temp_class)
        {
            return false;
        }

        $has_additional_properties = count($temp_class->get_additional_property_names()) > 0;
        unset($temp_class);
        return $has_additional_properties;
    }

    /**
     * Determines whether the learning object with the given ID has been
     * published in any of the registered applications.
     * @param int $id The ID of the learning object.
     * @return boolean True if the learning object has been published anywhere,
     * false otherwise.
     */
    public static function content_object_is_published($id)
    {
        $applications = self :: get_registered_applications();
        $result = false;
        foreach ($applications as $index => $application_name)
        {
            $application = Application :: factory($application_name);
            if ($application->content_object_is_published($id))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Determines whether a learning object with the given IDs has been
     * published in any of the registered applications.
     * @param array $ids The IDs of the learning objects.
     * @return boolean True if one of the given learning objects has been
     * published anywhere, false otherwise.
     */
    public static function any_content_object_is_published($ids)
    {
        $applications = self :: get_registered_applications();
        $result = false;
        foreach ($applications as $index => $application_name)
        {

            $application = Application :: factory($application_name);
            if ($application->any_content_object_is_published($ids))
            {
                return true;
            }
        }

        $admin = new AdminManager();
        if ($admin->any_content_object_is_published($ids))
        {
            return true;
        }

        return false;
    }

    /**
     * Get the attributes of the learning object publication
     * @param int $id The ID of the learning object.
     * @return array An array of ContentObjectPublicationAttributes objects;
     * empty if the object has not been published anywhere.
     */
    public static function get_content_object_publication_attributes($user, $id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        $applications = self :: get_registered_applications();
        $info = array();
        foreach ($applications as $application_name)
        {
            $application = Application :: factory($application_name, $user);
            $attributes = $application->get_content_object_publication_attributes($id, $type, $offset, $count, $order_property);
            if (! is_null($attributes) && count($attributes) > 0)
            {
                $info = array_merge($info, $attributes);
            }
        }

        $admin = new AdminManager($user);
        $attributes = $admin->get_content_object_publication_attributes($id, $type, $offset, $count, $order_property);
        if (! is_null($attributes) && count($attributes) > 0)
        {
            $info = array_merge($info, $attributes);
        }

        return $info;
    }

    /**
     * Get the attribute of the learning object publication
     * @param int $id The ID of the learning object.
     * @return array An array of ContentObjectPublicationAttributes objects;
     * empty if the object has not been published anywhere.
     */
    public static function get_content_object_publication_attribute($id, $application, $user)
    {
        $application = Application :: factory($application, $user, true);
        return $application->get_content_object_publication_attribute($id);
    }

    /**
     * Determines whether a learning object can be deleted.
     * A learning object can sefely be deleted if
     * - it isn't published in an application
     * - all of its children can be deleted
     * @param ContentObject $object
     * @return boolean True if the given learning object can be deleted
     */
    public static function content_object_deletion_allowed($object, $type = null)
    {
        if ($object->get_owner_id() == 0)
            return true;

        $conditions[] = new EqualityCondition('variable', 'use_object');
        $conditions[] = new EqualityCondition('value', $object->get_id());
        $condition = new AndCondition($conditions);

        $blockinfos = HomeDataManager :: get_instance()->retrieve_home_block_config($condition);
        if ($blockinfos->size() > 0)
        {
            return false;
        }

        if (isset($type))
        {
            if (self :: get_instance()->is_attached($object, 'version'))
            {
                return false;
            }
            $forbidden = array();
            $forbidden[] = $object->get_id();
        }
        else
        {
            if (self :: get_instance()->is_attached($object))
            {
                return false;
            }
            $children = array();
            //$children = self :: get_instance()->get_children_ids($object);
            $versions = array();
            $versions = self :: get_instance()->get_version_ids($object);
            $forbidden = array_merge($children, $versions);
        }

        if (self :: get_instance()->is_content_object_included($object))
        {
            return false;
        }

        $conditions = array();
        $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_REF, $object->get_id());
        $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $object->get_id(), ComplexContentObjectItem :: get_table_name());
        $condition = new OrCondition($conditions);
        $count_wrapper_items = self :: get_instance()->count_complex_content_object_items($condition);
        if ($count_wrapper_items > 0)
        {
            return false;
        }

        $count_portfolio_wrapper_items = self :: get_instance()->count_type_content_objects(PortfolioItem :: get_type_name(), new EqualityCondition(PortfolioItem :: PROPERTY_REFERENCE, $object->get_id(), PortfolioItem :: get_type_name()));
        if ($count_portfolio_wrapper_items > 0)
        {
            return false;
        }

        $count_learning_path_wrapper_items = self :: get_instance()->count_type_content_objects(LearningPathItem :: get_type_name(), new EqualityCondition(LearningPathItem :: PROPERTY_REFERENCE, $object->get_id(), LearningPathItem :: get_type_name()));
        if ($count_learning_path_wrapper_items > 0)
        {
            return false;
        }

        $count_children = self :: get_instance()->count_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $object->get_id(), ComplexContentObjectItem :: get_table_name()));
        if ($count_children > 0)
        {
            return false;
        }

        return ! self :: any_content_object_is_published($forbidden);
    }

    /**
     * Copies a complex learning object
     */
    public static function copy_complex_content_object($clo)
    {
        $clo->create_all();
        self :: copy_complex_children($clo);
        return $clo;
    }

    public static function copy_complex_children($clo)
    {
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $clo->get_id(), ComplexContentObjectItem :: get_table_name());
        $items = self :: get_instance()->retrieve_complex_content_object_items($condition);
        while ($item = $items->next_result())
        {
            $nitem = new ComplexContentObjectItem();
            $nitem->set_user_id($item->get_user_id());
            $nitem->set_display_order($item->get_display_order());
            $nitem->set_parent($clo->get_id());
            $nitem->set_ref($item->get_ref());
            $nitem->create();
            $lo = self :: get_instance()->retrieve_content_object($item->get_ref());
            if ($lo->is_complex_content_object())
            {
                $lo->create_all();
                $nitem->set_ref($lo->get_id());
                $nitem->update();
                self :: copy_complex_content_object($lo);
            }
        }
    }

    /**
     * Determines whether a version is revertable.
     * @param ContentObject $object
     * @return boolean True if the given learning object version can be reverted
     */
    public static function content_object_revert_allowed($object)
    {
        return ! self :: get_instance()->is_latest_version($object);
    }

    /**
     * Returns the number of learning objects that match the given criteria.
     * This method has the same limitations as retrieve_content_objects.
     * @param string $type The type of learning objects to search for, if any.
     * If you do not specify a type, or the type is not
     * known in advance, you will only be able to select
     * on default properties; also, there will be a
     * significant performance decrease.
     * @param Condition $condition The condition to use for learning object
     * selection, structured as a Condition
     * object. Please consult the appropriate
     * documentation.
     * @param int $state The state the learning objects should have. Any of
     * the ContentObject :: STATE_* constants. A negative
     * number means the state should be ignored. Defaults
     * to ContentObject :: STATE_NORMAL. You can just as
     * easily use your own condition for this; this
     * parameter is merely for convenience, and to ensure
     * that the function does not apply to recycled objects
     * by default.
     * @return int The number of matching learning objects.
     */
    public static function count_publication_attributes($user, $object_id, $condition = null)
    {
        $applications = self :: get_registered_applications();
        $info = 0;
        foreach ($applications as $index => $application_name)
        {
            $application = Application :: factory($application_name, $user);
            $info += $application->count_publication_attributes($user, $object_id, $condition);
        }

        $admin = new AdminManager($user);
        $info += $admin->count_publication_attributes($user, $object_id, $condition);

        return $info;
    }

    /**
     * Updates the given learning object publications learning object id.
     * @param ContentObjectPublicationAttribute $object The learning object publication attribute.
     * @return boolean True if the update succceeded, false otherwise.
     */
    public static function update_content_object_publication_id($publication_attr)
    {
        $application = Application :: factory($publication_attr->get_application());
        return $application->update_content_object_publication_id($publication_attr);
    }

    /**
     * Deletes all learning objects a user_id has:
     * Retrieves the learning object(s) a user has made,
     * deletes the publications made with these object(s),
     * and finally, deletes the object itself.
     */
    public static function delete_content_object_by_user($user_id)
    {
        $content_object = self :: get_instance()->retrieve_content_object_by_user($user_id);
        while ($object = $content_object->next_result())
        {
            if (! self :: delete_content_object_publications($object))
            {
                return false;
            }
            if (! $object->delete())
            {
                return false;
            }
        }
        return true;
    }

    public static function delete_content_object_publications($object)
    {
        $applications = self :: get_registered_applications();
        foreach ($applications as $index => $application_name)
        {
            $application = Application :: factory($application_name);
            $application->delete_content_object_publications($object->get_id());
        }

        $admin = AdminDataManager :: get_instance()->delete_content_object_publications($object->get_id());

        return true;
    }

    public static function delete_content_object_publication($application, $publication_id)
    {
        //require_once (Path :: get(SYS_PATH) . 'application/lib/' . $application . '/' . $application . '_manager/' . $application . '_manager.class.php');
        $application = Application :: factory($application, null, true);
        return $application->delete_content_object_publication($publication_id);
    }

    /**
     * Automagically loads all the available types of learning objects
     * and registers them with this data manager.
     * @todo This function now parses the XML-files of every learning object
     * type. There's probably a faster way to retrieve this information by
     * saving the types and their properties in the database when the learning
     * object type is installed on the system.
     */
    public static function load_types()
    {
        $path = Path :: get_repository_path() . 'lib/content_object/';

        foreach (self :: get_registered_types(true) as $content_object_type)
        {
            $content_object_path = $path . $content_object_type . '/' . $content_object_type . '.class.php';
            require_once $content_object_path;
        }
    }

    /**
     * Checks if an identifier is a valid name for a learning object type.
     * @param string $name The name.
     * @return boolean True if a valid learning object type name was passed,
     * false otherwise.
     */
    public static function is_content_object_type_name($name)
    {
        return (preg_match('/^[a-z][a-z_]+$/', $name) > 0);
    }

    /**
     * Returns the names of the applications known to this
     * repository datamanager.
     * @return array The applications.
     */
    public static function get_registered_applications()
    {
        if (! isset(self :: $applications) || count(self :: $applications) == 0)
        {
            self :: $applications = WebApplication :: load_all();
        }

        return self :: $applications;
    }

    public static function delete_clois_for_content_object($content_object)
    {
        $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_REF, $content_object->get_id());
        $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $content_object->get_id(), ComplexContentObjectItem :: get_table_name());
        $condition = new OrCondition($conditions);

        return self :: get_instance()->delete_complex_content_object_items($condition);
    }

    /**
     * Gets the number of categories the user has defined in his repository
     * @param int $user_id
     * @return int
     */
    public static function get_number_of_categories($user_id)
    {
        if (! isset(self :: $number_of_categories{$user_id}))
        {
            $condition = new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $user_id);
            //self :: get_instance()->number_of_categories{$user_id} = self :: get_instance()->count_type_content_objects('category', $condition);
            self :: $number_of_categories[$user_id] = self :: get_instance()->count_categories($condition);
        }
        return self :: $number_of_categories{$user_id};
    }

    public static function get_content_object_managers()
    {
        self :: load_types();
        $active_objects = self :: get_registered_types(true);
        $managers = array();

        foreach ($active_objects as $active_object)
        {
            $active_object_managers = call_user_func(array(ContentObject :: type_to_class($active_object), 'get_managers'));
            if (count($active_object_managers) > 0)
            {
                $managers[$active_object] = $active_object_managers;
            }
        }

        return $managers;
    }
}
?>