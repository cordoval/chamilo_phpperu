<?php
/**
 * $Id: content_object_publication_course_group.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms
 */


/**
 * This class describes a ContentObjectPublicationCourseGroup data object
 *
 * @author Hans De Bisschop
 */
class ContentObjectPublicationCourseGroup extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * ContentObjectPublicationCourseGroup properties
     */
    const PROPERTY_PUBLICATION = 'publication_id';
    const PROPERTY_COURSE_GROUP_ID = 'course_group_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_PUBLICATION, self :: PROPERTY_COURSE_GROUP_ID);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    /**
     * Returns the publication of this ContentObjectPublicationCourseGroup.
     * @return the publication.
     */
    function get_publication()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION);
    }

    /**
     * Sets the publication of this ContentObjectPublicationCourseGroup.
     * @param publication
     */
    function set_publication($publication)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION, $publication);
    }

    /**
     * Returns the course_group_id of this ContentObjectPublicationCourseGroup.
     * @return the course_group_id.
     */
    function get_course_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_GROUP_ID);
    }

    /**
     * Sets the course_group_id of this ContentObjectPublicationCourseGroup.
     * @param course_group_id
     */
    function set_course_group_id($course_group_id)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_GROUP_ID, $course_group_id);
    }

    function create()
    {
        $dm = WeblcmsDataManager :: get_instance();
        return $dm->create_content_object_publication_course_group($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>