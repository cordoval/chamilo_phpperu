<?php
/**
 * $Id: content_object_pub_feedback.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */

class ContentObjectPubFeedback extends ContentObject
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_ID = 'id';
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_CLOI_ID = 'complex_content_object_id';
    const PROPERTY_FEEDBACK_ID = 'feedback_id';
    
    /**
     * Default properties of the content_object_feedback object, stored in an associative
     * array.
     */
    private $defaultProperties;

    function ContentObjectPubFeedback($publication_id = 0, $cloi_id = 0, $feedback_id = 0, $defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_PUBLICATION_ID, self :: PROPERTY_CLOI_ID, self :: PROPERTY_FEEDBACK_ID);
    }

    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    static function is_default_property_name($name)
    {
        return in_array($name, self :: get_default_property_names());
    }

    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    function get_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
    }

    function get_cloi_id()
    {
        return $this->get_default_property(self :: PROPERTY_CLOI_ID);
    }

    function get_feedback_id()
    {
        return $this->get_default_property(self :: PROPERTY_FEEDBACK_ID);
    }

    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    function set_publication_id($publication_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
    }

    function set_cloi_id($cloi_id)
    {
        return $this->set_default_property(self :: PROPERTY_CLOI_ID, $cloi_id);
    }

    function set_feedback_id($feedback_id)
    {
        return $this->set_default_property(self :: PROPERTY_FEEDBACK_ID, $feedback_id);
    }

    function delete()
    {
        return RepositoryDataManager :: get_instance()->delete_content_object_pub_feedback($this);
    }

    function create()
    {
        $gdm = RepositoryDataManager :: get_instance();
        $id = $gdm->get_next_content_object_pub_feedback_id();
        $this->set_id($id);
        
        return $gdm->create_content_object_pub_feedback($this);
    }

    function update()
    {
        $gdm = RepositoryDataManager :: get_instance();
        $success = $gdm->update_content_object_pub_feedback($this);
        if (! $success)
        {
            return false;
        }
        
        return true;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>
