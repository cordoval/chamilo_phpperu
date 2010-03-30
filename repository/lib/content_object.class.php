<?php
/**
 * $Id: content_object.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
/**
 *	This class represents a learning object in the repository. Every object
 *	that can be associated with a module is in fact a learning object.
 *
 *	Learning objects have a number of default properties:
 *	- id: the numeric ID of the learning object;
 *	- owner: the ID of the user who owns the learning object;
 *	- title: the title of the learning object;
 *	- description: a brief description of the learning object; may also be
 *	  used to store its content in select cases;
 *	- parent: the numeric ID of the parent object of this learning object;
 *    this is a learning object by itself, usually a category;
 *  - display_order: a number giving the learning object a position among its
 *    siblings; only applies if the learning object is ordered;
 *	- created: the date when the learning object was created, as returned by
 *	  PHP's time() function (UNIX time, seconds since the epoch);
 *	- modified: the date when the learning object was last modified, as
 *	  returned by PHP's time() function;
 *  - state: the state the learning object is in; currently only used to mark
 *    learning objects as "recycled", i.e. moved to the Recycle Bin.
 *
 *	Actual learning objects must be instances of extensions of this class.
 *	They may define additional properties which are specific to that
 *	particular type of learning object, e.g. the path to a document. This
 *	class provides a framework for that purpose.
 *
 * To access the values of the properties, this class and its subclasses
 * should provide accessor methods. The names of the properties should be
 * defined as class constants, for standardization purposes. It is recommended
 * that the names of these constants start with the string "PROPERTY_".
 *
 *	To create your own type of learning object, you should follow these steps:
 *	- Decide on a name for the type, e.g. "MyType".
 *	- Create a new subdirectory in /repository/lib/content_object. For
 *	  "MyType", it would be called "my_type".
 *	- Create two files in that subdirectory:
 *	  - The properties file (e.g. "my_type.properties") is a plain text list
 *	    of the names of all the properties of your type, one name per line.
 *	    This file may be omitted if your type does not require additional
 *	    properties.
 *	  - The class file (e.g. "my_type.class.php") is a PHP class that may
 *	    provide specific methods for the type. Even if the type does not
 *	    require additional methods, you must still define the class. Take
 *	    a look at the types that are already defined for examples.
 *	- The data manager will now automagically be aware of the type. All that's
 *	  left for you to do is create the physical storage for the type. This
 *	  will heavily depend on the type of data manager you are using. As MySQL
 *	  is the default, you will probably have to create a table named after the
 *	  type you are defining. This table should contain a numeric "id" column,
 *	  as well as columns for all the properties in the properties file. You do
 *	  not need columns for the default properties! These are stored elsewhere.
 *	When you've completed these steps, you should be able to instantiate the
 *	class and manipulate the objects at will.
 *
 *	@author Tim De Pauw
 *  @author Hans De Bisschop
 *  @author Dieter De Neef
 */

class ContentObject extends DataClass implements AccessibleContentObject
{
    const CLASS_NAME = __CLASS__;

    /**
     * Constant to define the normal state of a learning object
     */
    const STATE_NORMAL = 0;
    /**
     * Constant to define the recycled state of a learning object (= learning
     * object is moved to recycle bin)
     */
    const STATE_RECYCLED = 1;
    /**
     * Constant to define the backup state of a learning object
     */
    const STATE_BACKUP = 2;
    /**#@+
     * Property name of this learning object
     */
    const PROPERTY_TYPE = 'type';
    const PROPERTY_OWNER_ID = 'owner_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_PARENT_ID = 'parent_id';
    const PROPERTY_DISPLAY_ORDER_INDEX = 'display_order';
    const PROPERTY_CREATION_DATE = 'created';
    const PROPERTY_MODIFICATION_DATE = 'modified';
    const PROPERTY_OBJECT_NUMBER = 'object_number';
    const PROPERTY_STATE = 'state';
    const PROPERTY_COMMENT = 'comment';
    const PROPERTY_CONTENT_HASH = 'content_hash';
    /**#@-*/

    /**
     * Additional properties specific to this type of learning object, stored
     * in an associative array.
     */
    private $additionalProperties;

    /**
     * Learning objects attached to this learning object.
     */
    private $attachments;

    /**
     * Learning objects included into this learning object.
     */
    private $includes;

    /**
     * The state that this learning object had when it was retrieved. Used to
     * determine if the state of its children should be updated upon updating
     * the learning object.
     */
    private $oldState;

    /**
     * Creates a new learning object.
     * @param int $id The numeric ID of the learning object. May be omitted
     *                if creating a new object.
     * @param array $defaultProperties The default properties of the learning
     *                                 object. Associative array.
     * @param array $additionalProperties The properties specific for this
     *                                    type of learning object.
     *                                    Associative array. Null if they are
     *                                    unknown at construction of the
     *                                    object; in this case, they will be
     *                                    retrieved when needed.
     */
    function ContentObject($defaultProperties = array (), $additionalProperties = null)
    {
        parent :: __construct($defaultProperties);
        $this->additionalProperties = $additionalProperties;
        $this->oldState = $defaultProperties[self :: PROPERTY_STATE];
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return RepositoryDataManager :: get_instance();
    }

    /**
     * Returns a string representation of the type of this learning object.
     * @return string The type.
     */
    function get_type()
    {
        $type = $this->get_default_property(self :: PROPERTY_TYPE);
    	if($type)
        {
        	return $type;	
        }
        
    	return self :: class_to_type(get_class($this));
    }

    /**
     * Returns the state of this learning object.
     * @return int The state.
     */
    function get_state()
    {
        return $this->get_default_property(self :: PROPERTY_STATE);
    }

    /**
     * Returns the ID of this learning object's owner.
     * @return int The ID.
     */
    function get_owner_id()
    {
        return $this->get_default_property(self :: PROPERTY_OWNER_ID);
    }

    /**
     * Returns the title of this learning object.
     * @return string The title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the description of this learning object.
     * @return string The description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    function has_description()
    {
        $description = $this->get_description();
        return ($description != '<p>&#160;</p>' && count($description) > 0);
    }

    /**
     * Returns the difference of this learning object
     * with a given object based on it's id.
     * @param int $id The ID of the learning object to compare with.
     * @return Array The difference.
     */
    function get_difference($id)
    {
        $dm = RepositoryDataManager :: get_instance();
        $version = $dm->retrieve_content_object($id);

        $lod = ContentObjectDifference :: factory($this, $version);

        return $lod;
    }

    /**
     * Returns the comment of this learning object version.
     * @return string The version.
     */
    function get_comment()
    {
        return $this->get_default_property(self :: PROPERTY_COMMENT);
    }

    /**
     * Returns the numeric identifier of the learning object's parent learning
     * object.
     * @return int The identifier.
     */
    function get_parent_id()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT_ID);
    }

    /**
     * Returns the display order index of the learning object among its
     * siblings.
     * @return int The display order index.
     */
    function get_display_order_index()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER_INDEX);
    }

    /**
     * Returns the date when this learning object was created, as returned
     * by PHP's time() function.
     * @return int The creation date.
     */
    function get_creation_date()
    {
        return $this->get_default_property(self :: PROPERTY_CREATION_DATE);
    }

    /**
     * Returns the date when this learning object was last modified, as
     * returned by PHP's time() function.
     * @return int The modification time.
     */
    function get_modification_date()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFICATION_DATE);
    }

    /**
     * Returns the version number.
     * @return int The version number.
     */
    function get_object_number()
    {
        return $this->get_default_property(self :: PROPERTY_OBJECT_NUMBER);
    }

    /**
     * Returns the learning objects attached to this learning object.
     * @return array The learning objects.
     */
    function get_attached_content_objects()
    {
        if (! is_array($this->attachments))
        {
            $dm = RepositoryDataManager :: get_instance();
            $this->attachments = $dm->retrieve_attached_content_objects($this);
        }
        return $this->attachments;
    }

    /**
     * Returns the learning objects included into this learning object.
     * @return array The learning objects.
     */
    function get_included_content_objects()
    {
        if (! is_array($this->includes))
        {
            $dm = RepositoryDataManager :: get_instance();
            $this->includes = $dm->retrieve_included_content_objects($this);
        }
        return $this->includes;
    }

    function get_content_object_versions($include_last = true)
    {
        if (! is_array($this->versions))
        {
            $dm = RepositoryDataManager :: get_instance();
            $this->versions = $dm->retrieve_content_object_versions($this, $include_last);
        }
        return $this->versions;
    }

    function get_latest_version_id()
    {
        $dm = RepositoryDataManager :: get_instance();
        return $dm->get_latest_version_id($this);
    }

    function get_latest_version()
    {
        $dm = RepositoryDataManager :: get_instance();
        return $dm->retrieve_content_object($dm->get_latest_version_id($this));
    }

    /**
     * Returns the edition of this learning object
     * @return an int; the number of the version.
     */
    function get_content_object_edition()
    {
        $dm = RepositoryDataManager :: get_instance();
        return array_search($this->id, $dm->get_version_ids($this)) + 1;
    }

    /**
     * Returns the full URL where this learning object may be viewed.
     * @return string The URL.
     */
    function get_view_url()
    {
        return $this->get_path(WEB_PATH) . 'index_repository_manager.php?go=view&category=' . $this->get_parent_id() . '&object=' . $this->get_id();
    }

    /**
     * Sets this learning object's state to any of the STATE_* constants.
     * @param int $state The state.
     * @return boolean True upon success, false upon failure.
     */
    function set_state($state)
    {
        return $this->set_default_property(self :: PROPERTY_STATE, $state);
    }

    /**
     * Sets the ID of this learning object's owner.
     * @param int $id The ID.
     */
    function set_owner_id($owner)
    {
        $this->set_default_property(self :: PROPERTY_OWNER_ID, $owner);
    }

    /**
     * Sets the object number of this learning object.
     * @param int $object_number The Object Number.
     */
    function set_object_number($object_number)
    {
        $this->set_default_property(self :: PROPERTY_OBJECT_NUMBER, $object_number);
    }

    /**
     * Sets the title of this learning object.
     * @param string $title The title.
     */
    function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    /**
     * Sets the description of this learning object.
     * @param string $description The description.
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Sets the comment of this learning object version.
     * @param string $comment The comment.
     */
    function set_comment($comment)
    {
        $this->set_default_property(self :: PROPERTY_COMMENT, $comment);
    }

    /**
     * Sets the ID of this learning object's parent learning object.
     * @param int $parent The ID.
     */
    function set_parent_id($parent)
    {
        $this->set_default_property(self :: PROPERTY_PARENT_ID, $parent);
    }

    /**
     * Sets the display order index of the learning object among its siblings.
     * @param int $index The index.
     */
    function set_display_order_index($index)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAY_ORDER_INDEX, $index);
    }

    /**
     * Sets the date when this learning object was created.
     * @param int $created The creation date, as returned by time().
     */
    function set_creation_date($created)
    {
        $this->set_default_property(self :: PROPERTY_CREATION_DATE, $created);
    }

    /**
     * Sets the date when this learning object was modified.
     * @param int $modified The modification date, as returned by time().
     */
    function set_modification_date($modified)
    {
        $this->set_default_property(self :: PROPERTY_MODIFICATION_DATE, $modified);
    }
    
    function get_content_hash()
    {
    	return $this->get_default_property(self :: PROPERTY_CONTENT_HASH);
    }
    
    function set_content_hash($content_hash)
    {
    	$this->set_default_property(self :: PROPERTY_CONTENT_HASH, $content_hash);
    }

    /**
     * Returns whether or not this learning object is extended, i.e. whether
     * its type defines additional properties.
     * @return boolean True if the learning object is extended, false
     *                 otherwise.
     */
    function is_extended()
    {
        return self :: is_extended_type($this->get_type());
    }

    /**
     * Determines whether this learning object is ordered, i.e. whether its
     * order within its parent learning object is fixed. The order is stored
     * in the display order index property, which is automatically maintained
     * by the learning object class.
     * @return boolean True if the object is ordered, false otherwise.
     */
    function is_ordered()
    {
        return false;
    }

    /**
     * Determines whether this learning object can have versions.
     * @return boolean True if the object is versionable, false otherwise.
     */
    function is_versionable()
    {
        return true;
    }

    /**
     * Checks whether the current version of the object is the latest version
     */
    function is_latest_version()
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->is_latest_version($this);
    }

    /**
     * Returns the number of versions of the learning object
     */
    function get_version_count()
    {
        $rdm = RepositoryDataManager :: get_instance();
        return count($rdm->get_version_ids($this));

    }

    /**
     * Returns the remaining number of possible versions
     */
    function get_available_version_count()
    {
        $owner = UserDataManager :: get_instance()->retrieve_user($this->get_owner_id());
        $qm = new QuotaManager($owner);
        return $qm->get_max_versions($this->get_type()) - $this->get_version_count();

    }

    /**
     * Attaches the learning object with the given ID to this learning object.
     * @param int $id The ID of the learning object to attach.
     */
    function attach_content_object($id)
    {
        $dm = RepositoryDataManager :: get_instance();
        return $dm->attach_content_object($this, $id);
    }

    /**
     * Includes the learning object with the given ID in this learning object.
     * @param int $id The ID of the learning object to include.
     */
    function include_content_object($id)
    {
        $rdm = RepositoryDataManager :: get_instance();

        $is_already_included = $rdm->is_content_object_already_included($this, $id);

        if ($is_already_included)
        {
            return true;
        }
        else
        {
            return $rdm->include_content_object($this, $id);
        }
    }

    /**
     * Removes the learning object with the given ID from this learning
     * object's attachment list.
     * @param int $id The ID of the learning object to remove from the
     *                attachment list.
     * @return boolean True if the attachment was removed, false if it did not
     *                 exist.
     */
    function detach_content_object($id)
    {
        $dm = RepositoryDataManager :: get_instance();
        return $dm->detach_content_object($this, $id);
    }

    /**
     * Removes the learning object with the given ID from this learning
     * object's include list.
     * @param int $id The ID of the learning object to remove from the
     *                include list.
     * @return boolean True if the include was removed, false if it did not
     *                 exist.
     */
    function exclude_content_object($id)
    {
        $dm = RepositoryDataManager :: get_instance();
        return $dm->exclude_content_object($this, $id);
    }

    /**
     * Gets an additional (type-specific) property of this learning object by
     * name.
     * @param string $name The name of the property.
     */
    function get_additional_property($name)
    {
        $this->check_for_additional_properties();
        return $this->additionalProperties[$name];
    }

    /**
     * Sets an additional (type-specific) property of this learning object by
     * name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_additional_property($name, $value)
    {
        //$this->check_for_additional_properties();
        $this->additionalProperties[$name] = $value;
    }

    /**
     * Gets the additional (type-specific) properties of this learning
     * object.
     * @return array An associative array containing the properties.
     */
    function get_additional_properties()
    {
        $this->check_for_additional_properties();
        return $this->additionalProperties;
    }

    /**
     * Sets the additional (type-specific) properties of this learning
     * object.
     * @param array An associative array containing the properties.
     */
    function set_additional_properties($additional_properties)
    {
        $this->additionalProperties = $additional_properties;
    }

    /**
     * Assigns the learning object a display order index. Only applicable
     * if this type allows ordering. This also happens automatically upon
     * invocation of {@link #create()}.
     * @return int The learning object's display index, or -1 if not applicable.
     */
    function assign_display_order_index()
    {
        if ($this->is_ordered())
        {
            $index = $this->get_display_order_index();
            if (! $index)
            {
                $dm = RepositoryDataManager :: get_instance();
                return $dm->assign_content_object_display_order_index($this);
            }
            return $index;
        }
        return - 1;
    }

    /**
     * Instructs the data manager to create the learning object, making it
     * persistent. Also assigns a unique ID to the learning object and sets
     * the learning object's creation date to the current time.
     * @return boolean True if creation succeeded, false otherwise.
     */
    function create()
    {

        $dm = RepositoryDataManager :: get_instance();
        $now = time();

        $this->assign_display_order_index();
        $this->set_creation_date($now);
        $this->set_modification_date($now);
        $this->set_object_number($dm->get_next_content_object_number());
        
        if (! $dm->create_content_object($this, 'new'))
        {
            return false;
        }

        if ($this->get_owner_id() == 0)
            return true;

        $parent = $this->get_parent_id();
        if (! $parent)
        {
            $parent_id = RepositoryRights :: get_user_root_id($this->get_owner_id());
        }
        else
        {
            $parent_id = RepositoryRights :: get_location_id_by_identifier_from_user_subtree('repository_category', $this->get_parent_id(), $this->get_owner_id());
        }

    	if (!RepositoryRights :: create_location_in_user_tree($this->get_title(), 'content_object', $this->get_id(), $parent_id, $this->get_owner_id()))
        {
            return false;
        }

        return true;
    }

    function create_all()
    {

        $this->assign_display_order_index();
        $dm = RepositoryDataManager :: get_instance();
        $object_number = $dm->get_next_content_object_number();
        $this->set_object_number($object_number);

        if (! $dm->create_content_object($this, 'new'))
        {
            return false;
        }

        if ($this->get_owner_id() == 0)
            return true;

     	$parent = $this->get_parent_id();
        if (! $parent)
        {
            $parent_id = RepositoryRights :: get_user_root_id($this->get_owner_id());
        }
        else
        {
            $parent_id = RepositoryRights :: get_location_id_by_identifier_from_user_subtree('repository_category', $this->get_parent_id(), $this->get_owner_id());
        }

    	if (!RepositoryRights :: create_location_in_user_tree($this->get_title(), 'content_object', $this->get_id(), $parent_id, $this->get_owner_id()))
        {
            return false;
        }

        return true;
    }

    /**
     * Instructs the data manager to update the learning object, making any
     * modifications permanent. Also sets the learning object's modification
     * date to the current time if the update is a true update. A true update
     * is an update that implicates a change to a property that affects the
     * learning object itself; changing the learning object's category, for
     * instance, should not change the last modification date.
     * @param boolean $trueUpdate True if the update is a true update
     *                            (default), false otherwise.
     * @return boolean True if the update succeeded, false otherwise.
     */
    function update($trueUpdate = true)
    {
        if ($trueUpdate)
        {
            $this->set_modification_date(time());
        }
        $dm = RepositoryDataManager :: get_instance();
        $success = $dm->update_content_object($this);
        if (! $success)
        {
            return false;
        }
        $state = $this->get_state();
        if ($state == $this->oldState)
        {
            return true;
        }
        $child_ids = self :: get_child_ids($this->get_id());
        $dm->set_content_object_states($child_ids, $state);
        /*
		 * We return true here regardless of the result of the child update,
		 * since the object itself did get updated.
		 */
        return true;
    }

    function recycle()
    {
    	$this->set_modification_date(time());
    	$this->set_state(self :: STATE_RECYCLED);

    	$dm = RepositoryDataManager :: get_instance();
        return $dm->update_content_object($this);
    }

    function move($new_parent_id)
    {
    	$this->set_parent_id($new_parent_id);
    	$dm = RepositoryDataManager :: get_instance();
        return $dm->update_content_object($this);
    }

    function version($trueUpdate = true)
    {
        $now = time();
        $dm = RepositoryDataManager :: get_instance();

        $this->set_creation_date($now);
        $this->set_modification_date($now);

        $success = $dm->create_content_object($this, 'version');
        if (! $success)
        {
            return false;
        }
        $state = $this->get_state();
        if ($state == $this->oldState)
        {
            return true;
        }
        $child_ids = self :: get_child_ids($this->get_id());
        $dm->set_content_object_states($child_ids, $state);
        /*
		 * We return true here regardless of the result of the child update,
		 * since the object itself did get updated.
		 */
        return true;
    }

    private static function get_child_ids($id)
    {
        $cond = new EqualityCondition(self :: PROPERTY_PARENT_ID, $id);
        $children = RepositoryDataManager :: get_instance()->retrieve_content_objects($cond, array(), 0, - 1, - 1);
        $ids = array();
        while ($child = $children->next_result())
        {
            $child_id = $child->get_id();
            $ids[] = $child_id;
            $child_ids = self :: get_child_ids($child_id);
            if (count($child_ids))
            {
                $ids = array_merge($ids, $child_ids);
            }
        }
        return $ids;
    }

    /**
     * Instructs the data manager to delete the learning object.
     * @return boolean True if deletion succeeded, false otherwise.
     */
    function delete()
    {
        return RepositoryDataManager :: get_instance()->delete_content_object($this);
    }

    function delete_version()
    {
        return RepositoryDataManager :: get_instance()->delete_content_object_version($this);
    }

    function delete_links()
    {
        $rdm = RepositoryDataManager :: get_instance();

        if ($rdm->delete_content_object_publications($this) && $rdm->delete_content_object_attachments($this) &&
        	$rdm->delete_content_object_includes($this) && $rdm->delete_clois_for_content_object($this) && $rdm->delete_assisting_content_objects($this))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Retrieves this learning object's ancestors.
     * @return array The ancestors, all learning objects.
     */
    function get_ancestors()
    {
        $ancestors = array();
        $aid = $this->get_parent_id();
        while ($aid > 0)
        {
            $ancestor = RepositoryDataManager :: get_instance()->retrieve_content_object($aid);
            $ancestors[] = $ancestor;
            $aid = $ancestor->get_parent_id();
        }
        return $ancestors;
    }

    /**
     * Checks if the given ID is the ID of one of this learning object's
     * ancestors.
     * @param int $ancestor_id
     * @return boolean True if the ID belongs to an ancestor, false otherwise.
     */
    function has_ancestor($ancestor_id)
    {
        $aid = $this->get_parent_id();
        while ($aid > 0)
        {
            if ($aid == $ancestor_id)
            {
                return true;
            }
            $ancestor = RepositoryDataManager :: get_instance()->retrieve_content_object($aid);
            $aid = $ancestor->get_parent_id();
        }
        return false;
    }

    /**
     * Determines whether this learning object may be moved to the learning
     * object with the given ID. By default, a learning object may be moved
     * to another learning object if the other learning object is not the
     * learning object itself, the learning object is not an ancestor of the
     * other learning object, and the other learning object is a category.
     * @param int $target The ID of the target learning object.
     * @return boolean True if the move is allowed, false otherwise.
     */
    function move_allowed($target)
    {
        /*if ($target == $this->get_id())
		{
			return false;
		}
		$target_object = RepositoryDataManager :: get_instance()->retrieve_content_object($target);
		if ($target_object->get_type() != 'category')
		{
			return false;
		}
		return !$target_object->has_ancestor($this->get_id());*/
        return true;
    }

    // XXX: Keep this around? Override? Make useful?
    function __tostring()
    {
        return get_class($this) . '#' . $this->get_id() . ' (' . $this->get_title() . ')';
    }

    /**
     * Determines whether this learning object supports attachments, i.e.
     * whether other learning objects may be attached to it.
     * @return boolean True if attachments are supported, false otherwise.
     */
    function supports_attachments()
    {
        return false;
    }

    /**
     * Determines whether this learning object supports includes, i.e.
     * whether other learning objects may be included into it.
     * @return boolean True if includes are supported, false otherwise.
     */
    function supports_includes()
    {
        return true;
    }

    /**
     * Determines whether this learning object is a complex learning object
     * @return boolean True if the LO is a CLO
     */
    function is_complex_content_object()
    {
        //		$file = dirname(__FILE__) . '/content_object/' . $this->get_type() . '/complex_' . $this->get_type() . '.class.php';
        //
        //		if(file_exists($file))
        //		{
        //			require_once($file);
        //			$class = 'Complex' . $this->type_to_class($this->get_type());
        //			$object = new $class();
        //			return count($object->get_allowed_types()) > 0;
        //		}
        //		return false;


        return count($this->get_allowed_types()) > 0;
    }

    function get_allowed_types()
    {
        return array();
    }

    /**
     * Gets the name of the icon corresponding to this learning object.
     */
    function get_icon_name()
    {
        return $this->get_type();
    }

    function get_icon_image()
    {
        $src = Theme :: get_common_image_path() . 'content_object/' . $this->get_icon_name() . '.png';
        return '<img src="' . $src . '" alt="' . $this->get_icon_name() . '" />';
    }

    /**
     * Checks if the learning object's additional properties have already been
     * loaded, and requests them from the data manager if they have not.
     */
    private function check_for_additional_properties()
    {
        if (isset($this->additionalProperties))
        {
            return;
        }
        $dm = RepositoryDataManager :: get_instance();
        $this->additionalProperties = $dm->retrieve_additional_content_object_properties($this);
    }

    /**
     * Get the default properties of all learning objects.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_OWNER_ID, self :: PROPERTY_TYPE, 
        								self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_PARENT_ID, self :: PROPERTY_CREATION_DATE, 
        								self :: PROPERTY_MODIFICATION_DATE, self :: PROPERTY_OBJECT_NUMBER, self :: PROPERTY_STATE, 
        								self :: PROPERTY_DISPLAY_ORDER_INDEX, self :: PROPERTY_COMMENT, self :: PROPERTY_CONTENT_HASH));
    }

    static function get_additional_property_names()
    {
        return array();
    }

    /**
     * Checks if the given identifier is the name of a default learning object
     * property.
     * @param string $name The identifier.
     * @return boolean True if the identifier is a property name, false
     *                 otherwise.
     */
    static function is_default_property_name($name)
    {
        return in_array($name, self :: get_default_property_names());
    }

	static function is_additional_property_name($name)
    {
        return in_array($name, self :: get_additional_property_names());
    }

    /**
     * Get all properties of this type of learning object that should be taken
     * into account to calculate the used disk space.
     * @return mixed The property names. Either a string, an array of strings,
     *               or null if no properties affect disk quota.
     */
    static function get_disk_space_properties()
    {
        return null;
    }

    /**
     * Converts a learning object type name to the corresponding class name.
     * @param string $type The type name.
     * @return string The class name.
     */
    static function type_to_class($type)
    {
        return Utilities :: underscores_to_camelcase($type);
    }

    /**
     * Converts a class name to the corresponding learning object type name.
     * @param string $class The class name.
     * @return string The type name.
     */
    static function class_to_type($class)
    {
        return Utilities :: camelcase_to_underscores($class);
    }

    /**
     * Determines whether this learning object is a master type.
     *
     * This means it can exist on its own. This function can be called staticly.
     * By default this function returns true. If a certain learning object type
     * isn't a master type, this function should be overwritte in the
     * corresponding subclass of this class and the function should return
     * false.
     * @return boolean true if this is a master type.
     */
    function is_master_type()
    {
        return true;
    }

    /**
     * Invokes the constructor of the class that corresponds to the specified
     * type of learning object.
     * @param string $type The learning object type.
     * @param int $id The ID of the learning object.
     * @param array $defaultProperties An associative array containing the
     *                                 default properties of the learning
     *                                 object.
     * @param array $additionalProperties An associative array containing the
     *                                    additional (type-specific)
     *                                    properties of the learning object.
     *                                    Null if unknown; this implies JIT
     *                                    retrieval.
     * @return ContentObject The newly instantiated learning object.
     */
    static function factory($type, $defaultProperties = array(), $additionalProperties = array())
    {
        if(!AdminDataManager :: get_instance()->is_registered($type, 'content_object'))
        {
        	return null;
        }

    	$class = self :: type_to_class($type);
        require_once dirname(__FILE__) . '/content_object/' . $type . '/' . $type . '.class.php';
        return new $class($defaultProperties, $additionalProperties);
    }

    static function is_extended_type($type)
    {
        $class = self :: type_to_class($type);
        require_once dirname(__FILE__) . '/content_object/' . $type . '/' . $type . '.class.php';

        $properties = call_user_func(array($class, 'get_additional_property_names'));
        return !empty($properties);
    }

    function get_path($path_type)
    {
        return Path :: get($path_type);
    }

    /**
     * Determines whether an edit of a learning object requires a new version or not
     * @return false
     */
    function is_versioning_required()
    {
        return false;
    }

    function get_html_editors()
    {
        /*require_once dirname(__FILE__) . '/content_object_form.class.php';
		$form = ContentObjectForm :: factory($this->get_type(), $this, $this->get_type());
		return $form->get_html_editors();*/

        return array(self :: PROPERTY_DESCRIPTION);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    /**
     *
     * @param integer $content_object_id
     * @return ContentObject An object inheriting from ContentObject
     */
    public static function get_by_id($content_object_id)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_object($content_object_id);
    }
}
?>