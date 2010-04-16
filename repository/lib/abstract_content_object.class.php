<?php
/**
 * $Id: abstract_content_object.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
/**
 * An abstract learning object
 */
class AbstractContentObject extends ContentObject
{
    /**
     * The type of the learning object
     */
    private $type;
    /**
     * Are attachments supported
     */
    private $attachments_supported;

    /**
     * Constructor
     * @param string $type
     * @param int $owner
     * @param int $parent
     */
    function AbstractContentObject($type, $owner, $parent = 0)
    {
        parent :: __construct();
        $this->type = $type;
        $this->attachments_supported = false;
        $this->set_owner_id($owner);
        $this->set_parent_id($parent);
    }

    /**
     * Gets the type of this abstract learning object
     * @return string
     */
    function get_type()
    {
        return $this->type;
    }

    /**
     * Determines if this object supports attachments
     * @return boolean
     */
    function supports_attachments()
    {
        $dummy_object = ContentObject :: factory($this->get_type());
        return $dummy_object->supports_attachments();
    }

    function is_versionable()
    {
        $dummy_object = ContentObject :: factory($this->get_type());
        return $dummy_object->is_versionable();
    }

    function is_versioning_required()
    {
        $dummy_object = ContentObject :: factory($this->get_type());
        return $dummy_object->is_versioning_required();
    }

    static function get_type_name()
    {
        return '';
    }
}
?>